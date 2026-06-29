<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
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
                             background:#eff6ff;border:1px solid #bfdbfe;border-radius:9999px;
                             padding:5px 14px;font-size:12px;font-weight:500;color:#1d4ed8;
                             margin-bottom:16px;line-height:1;vertical-align:middle;">
                    <svg style="width:13px;height:13px;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Admin Sign In
                </span>
                <span style="display:block;">Welcome back</span>
            </span>
        ');
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Sign in to manage your workspace.';
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::auth/pages/login.form.email.label'))
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->prefixIcon('heroicon-o-envelope')
            ->placeholder('you@example.com');
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::auth/pages/login.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->prefixIcon('heroicon-o-lock-closed')
            ->placeholder('Enter your password');
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Sign in →')
            ->submit('authenticate');
    }
}
