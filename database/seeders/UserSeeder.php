<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder
 *
 * Crea todos los usuarios del sistema de prueba.
 *
 * GRUPOS:
 *   Branch staff  → managers y staff de cada territorio
 *   Business      → dueños de empresas
 *
 * PASSWORD DEFAULT: password123 para todos
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        $users = [
            // ── Paraná ────────────────────────────────────────────────────────
            ['name' => 'Carlos Oliveira',  'email' => 'manager.parana@kubix.com',     'cpf' => '11111111111'],
            ['name' => 'Fernanda Lima',    'email' => 'staff.parana@kubix.com',       'cpf' => '11111111112'],

            // ── Curitiba (Juan el socio franquiciado) ─────────────────────────
            ['name' => 'Juan García',      'email' => 'juan@kubix.com',               'cpf' => '22222222221'],
            ['name' => 'Ana Souza',        'email' => 'staff.curitiba@kubix.com',     'cpf' => '22222222222'],

            // ── Pinheirinho ───────────────────────────────────────────────────
            ['name' => 'Roberto Mendes',   'email' => 'manager.pinheirinho@kubix.com','cpf' => '33333333331'],
            ['name' => 'Patrícia Costa',   'email' => 'staff.pinheirinho@kubix.com',  'cpf' => '33333333332'],

            // ── Centro Curitiba ───────────────────────────────────────────────
            ['name' => 'Lucas Ferreira',   'email' => 'manager.centro.ctba@kubix.com','cpf' => '44444444441'],
            ['name' => 'Camila Santos',    'email' => 'staff.centro.ctba@kubix.com',  'cpf' => '44444444442'],

            // ── São José dos Pinhais ──────────────────────────────────────────
            ['name' => 'Marcos Rocha',     'email' => 'manager.sjp@kubix.com',        'cpf' => '55555555551'],
            ['name' => 'Juliana Alves',    'email' => 'staff.sjp@kubix.com',          'cpf' => '55555555552'],

            // ── Centro São José ───────────────────────────────────────────────
            ['name' => 'Diego Pereira',    'email' => 'manager.centro.sjp@kubix.com', 'cpf' => '66666666661'],
            ['name' => 'Larissa Nunes',    'email' => 'staff.centro.sjp@kubix.com',   'cpf' => '66666666662'],

            // ── Business Owners ───────────────────────────────────────────────
            ['name' => 'Pedro Helados',    'email' => 'pedro@email.com',              'cpf' => '77777777771'],
            ['name' => 'Maria Modas',      'email' => 'maria@email.com',              'cpf' => '77777777772'],
            ['name' => 'José Tech',        'email' => 'jose@email.com',               'cpf' => '77777777773'],
            ['name' => 'Sandra Café',      'email' => 'sandra@email.com',             'cpf' => '77777777774'],
            ['name' => 'Bruno Auto',       'email' => 'bruno@email.com',              'cpf' => '77777777775'],
        ];

        foreach ($users as $data) {
            User::create([
                ...$data,
                'password'          => $password,
                'email_verified_at' => now(),
            ]);
        }
    }
}