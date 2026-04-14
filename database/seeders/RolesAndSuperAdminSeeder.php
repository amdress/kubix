<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar cache de Spatie para evitar conflictos de permisos viejos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /**
         * 1. DEFINICIÓN DE ROLES (KUBIX Ecosystem)
         */
        $roles = [
            'superadmin',     // Dios del sistema
            'nomad',          // Usuario base (vecino/ciudadano)
            'branch_manager', // Admin de territorio (Estado, Ciudad, Barrio)
            'branch_staff',   // Operador de territorio
            'business_owner', // Dueño de negocio (Workspace Owner)
            'business_staff', // Empleado de negocio
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role, 'guard_name' => 'web']
            );
        }

        /**
         * 2. SUPER ADMIN (Amdress Stark)
         */
        $superAdmin = User::updateOrCreate(
            ['email' => 'amdress@gmail.com'],
            [
                'name'              => 'Amdress Stark',
                'cpf'               => '00000000000', // El admin sí tiene CPF por estructura
                'password'          => Hash::make('admin@123'),
                'email_verified_at' => now(),
                'terms_accepted_at' => now(),
                'terms_version'     => '1.0',
            ]
        );

        // Asegurar que Amdress sea SuperAdmin
        if (!$superAdmin->hasRole('superadmin')) {
            $superAdmin->assignRole('superadmin');
        }
        
        $this->command->info('Roles de KUBIX creados y SuperAdmin configurado.');
    }
}