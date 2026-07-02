<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class TenantProvision extends Command
{
    protected $signature = 'tenant:provision
                            {subdomain  : Subdomain slug (e.g. acme)}
                            {company    : Company / workspace name}
                            {name       : Admin user full name}
                            {email      : Admin user email}
                            {password   : Admin user password}';

    protected $description = 'Provision a new tenant: create DB, run migrations, seed admin user.';

    public function handle(): int
    {
        $subdomain = strtolower($this->argument('subdomain'));
        $company   = $this->argument('company');
        $name      = $this->argument('name');
        $email     = $this->argument('email');
        $password  = $this->argument('password');

        // ── 1. Guard ────────────────────────────────────────────────────────────
        if (Tenant::find($subdomain)) {
            $this->error("Tenant \"{$subdomain}\" already exists.");

            return self::FAILURE;
        }

        // ── 2. Create tenant → triggers CreateDatabase + MigrateDatabase ────────
        $this->info("Creating tenant [{$subdomain}] ...");

        $tenant = Tenant::create([
            'id'   => $subdomain,
            'name' => $company,
        ]);

        // Store only the subdomain — DomainTenantResolver looks up by this value.
        $tenant->domains()->create([
            'domain' => $subdomain,
        ]);

        $this->info('Tenant DB provisioned and migrations applied.');

        // ── 3. Switch to tenant context ─────────────────────────────────────────
        tenancy()->initialize($tenant);

        try {
            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => $password, // User model's 'hashed' cast handles bcrypt
            ]);

            $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            $user->assignRole($role);

            $this->info("Admin user [{$email}] created and assigned role: admin");
        } finally {
            tenancy()->end();
        }

        // ── 4. Summary ──────────────────────────────────────────────────────────
        $this->newLine();
        $this->table(
            ['Key', 'Value'],
            [
                ['Tenant ID',     $subdomain],
                ['Database',      "tenant{$subdomain}"],
                ['Domain',        "{$subdomain}.saas-app.test"],
                ['Admin login',   "https://{$subdomain}.saas-app.test/admin"],
                ['Email',         $email],
            ]
        );

        return self::SUCCESS;
    }
}
