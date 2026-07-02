<?php

namespace App\Filament\Pages\Auth;

use App\Models\Tenant;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Database\Models\Domain;

class Register extends BaseRegister
{
    public function hasLogo(): bool
    {
        return false;
    }

    public function getHeading(): string | Htmlable
    {
        return new HtmlString('
            <span style="display:block;">
                <span style="display:inline-flex;align-items:center;gap:6px;
                             background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9999px;
                             padding:5px 14px;font-size:12px;font-weight:500;color:#15803d;
                             margin-bottom:16px;line-height:1;vertical-align:middle;">
                    ✦ Free to start
                </span>
                <span style="display:block;">Create your workspace</span>
            </span>
        ');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return new HtmlString(
            'Already have an account? <a href="' . filament()->getLoginUrl() . '" style="color:#2563eb;font-weight:500;">Sign in</a>'
        );
    }

    // ── Form fields ────────────────────────────────────────────────────────────

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getCompanyNameFormComponent(),
            $this->getSubdomainFormComponent(),
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
        ]);
    }

    protected function getCompanyNameFormComponent(): Component
    {
        return TextInput::make('company_name')
            ->label('Company Name')
            ->placeholder('Acme Corp')
            ->prefixIcon('heroicon-o-building-office-2')
            ->required()
            ->maxLength(100)
            ->autofocus();
    }

    protected function getSubdomainFormComponent(): Component
    {
        return TextInput::make('subdomain')
            ->label('Workspace URL')
            ->placeholder('acme')
            ->prefixIcon('heroicon-o-globe-alt')
            ->suffix('.localhost')
            ->required()
            ->minLength(3)
            ->maxLength(50)
            ->rules([
                'alpha_dash',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $reserved = ['admin', 'api', 'www', 'mail', 'app', 'dashboard', 'ftp'];
                    if (in_array(Str::lower($value), $reserved)) {
                        $fail("The subdomain \"{$value}\" is reserved.");
                    }
                    if (Domain::where('domain', Str::lower($value))->exists()) {
                        $fail('This workspace URL is already taken.');
                    }
                },
            ]);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Your Name')
            ->placeholder('John Doe')
            ->prefixIcon('heroicon-o-user')
            ->required()
            ->maxLength(100);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email Address')
            ->email()
            ->placeholder('you@company.com')
            ->prefixIcon('heroicon-o-envelope')
            ->required()
            ->maxLength(255);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->prefixIcon('heroicon-o-lock-closed')
            ->required()
            ->rule(Password::default())
            ->same('passwordConfirmation');
            // No dehydrateStateUsing — User model's 'hashed' cast handles bcrypt
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Confirm Password')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->prefixIcon('heroicon-o-lock-closed')
            ->required()
            ->dehydrated(false);
    }

    // ── Registration logic ─────────────────────────────────────────────────────

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data      = $this->form->getState();
        $subdomain = Str::lower(Str::slug($data['subdomain'], '-'));
        $tenantId  = $subdomain;

        // 1. Create tenant in central DB → fires TenantCreated event
        //    → stancl/tenancy automatically runs CreateDatabase + MigrateDatabase
        $tenant = Tenant::create([
            'id'   => $tenantId,
            'name' => $data['company_name'],
        ]);

        // 2. Attach domain
        // For subdomain identification, store the subdomain only (not the full hostname).
        // stancl/tenancy's DomainTenantResolver looks up by this value directly.
        $tenant->domains()->create([
            'domain' => $subdomain,
        ]);

        // 3. Switch to tenant database
        tenancy()->initialize($tenant);

        try {
            // 4. Create the admin user inside the tenant DB
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'], // already hashed via dehydrateStateUsing
            ]);

            // 5. Ensure the admin role exists then assign it
            $role = Role::firstOrCreate(
                ['name' => 'admin', 'guard_name' => 'web']
            );
            $user->assignRole($role);

        } finally {
            // 6. Always revert to central context, even on failure
            tenancy()->end();
        }

        // 7. Redirect to tenant's admin login page
        $tenantUrl = "https://{$subdomain}.saas-app.test/admin";

        // Redirect to the tenant's admin panel on the subdomain.
        // We use Livewire's redirect() (returns void) and return null
        // to satisfy the ?RegistrationResponse return type.
        $this->redirect($tenantUrl, navigate: false);

        return null;
    }

    public function getRegisterFormAction(): Action
    {
        return Action::make('register')
            ->label('Create workspace →')
            ->submit('register');
    }
}
