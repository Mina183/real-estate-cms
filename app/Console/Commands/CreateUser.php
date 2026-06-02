<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    protected $signature = 'make:user
                            {email : Korisnikov email}
                            {password : Lozinka (plain text - automatski će biti hash-ovana)}
                            {--name= : Puno ime (default: ime iz email-a)}
                            {--role=admin : Rola: superadmin, admin, relationship_manager, operations, compliance_officer, fund_manager, auditor, data_room_administrator}
                            {--approved : Odmah odobri korisnika}';

    protected $description = 'Kreira novog korisnika sa ispravno hash-ovanom lozinkom';

    public function handle(): int
    {
        $email    = $this->argument('email');
        $password = $this->argument('password');
        $name     = $this->option('name') ?? explode('@', $email)[0];
        $role     = $this->option('role');
        $approved = $this->option('approved');

        $validRoles = [
            'superadmin', 'admin', 'relationship_manager', 'operations',
            'compliance_officer', 'fund_manager', 'auditor', 'data_room_administrator',
        ];

        if (! in_array($role, $validRoles)) {
            $this->error("Nevalidna rola: {$role}. Dozvoljeno: " . implode(', ', $validRoles));
            return 1;
        }

        if (User::where('email', $email)->exists()) {
            $this->error("Korisnik sa emailom {$email} već postoji.");
            return 1;
        }

        $user = User::create([
            'name'        => $name,
            'email'       => $email,
            'password'    => $password,   // 'hashed' cast automatski hash-uje
            'role'        => $role,
            'is_approved' => $approved ? true : false,
        ]);

        $this->info("✅ Korisnik kreiran:");
        $this->table(
            ['ID', 'Ime', 'Email', 'Rola', 'Odobren'],
            [[$user->id, $user->name, $user->email, $user->role, $user->is_approved ? 'Da' : 'Ne']]
        );

        if (! $approved) {
            $this->warn("⚠️  Korisnik nije odobren. Koristite: php artisan user:approve {$user->id}");
        }

        $this->info("   Login URL: /login");
        $this->info("   Email:     {$email}");
        $this->info("   Password:  (as provided)");

        return 0;
    }
}
