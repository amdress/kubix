<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Affiliation;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MainScenarioSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. SOLUCIONES
        |--------------------------------------------------------------------------
        */
        $libera  = Solution::firstOrCreate(['slug' => 'libera-juros'], ['name' => 'Libera Juros']);
        $alugapp = Solution::firstOrCreate(['slug' => 'alugapp'],      ['name' => 'AlugApp']);
        $menuapp = Solution::firstOrCreate(['slug' => 'menuapp'],      ['name' => 'MenuApp']);

        /*
        |--------------------------------------------------------------------------
        | 2. BRANCHES
        |--------------------------------------------------------------------------
        */
        $curitiba = Branch::firstOrCreate(['slug' => 'curitiba'], ['name' => 'Curitiba', 'code' => 'CWB']);
        $manaus   = Branch::firstOrCreate(['slug' => 'manaus'],   ['name' => 'Manaus',   'code' => 'MAO']);
        $china    = Branch::firstOrCreate(['slug' => 'china'],    ['name' => 'Shenzhen', 'code' => 'SZX']);

        // Branches inventados
        $eglis = Branch::firstOrCreate(['slug' => 'eglis'], ['name' => 'Eglis', 'code' => 'EGL']);
        $kju   = Branch::firstOrCreate(['slug' => 'kju'],   ['name' => 'Kju',   'code' => 'KJU']);

        /*
        |--------------------------------------------------------------------------
        | 3. EMPRESAS
        |--------------------------------------------------------------------------
        */
        $pedroCo = Company::firstOrCreate(
            ['slug' => 'pedro-asoc'],
            ['name' => 'Pedro & Asociados', 'trade_name' => 'Pedro & Asociados']
        );

        $anaCo = Company::firstOrCreate(
            ['slug' => 'ana-sol'],
            ['name' => 'Ana Solutions', 'trade_name' => 'Ana Solutions']
        );

        /*
        |--------------------------------------------------------------------------
        | 4. OWNERS
        |--------------------------------------------------------------------------
        */
        $pedroOwner = $this->createUser(
            'Pedro Owner',
            'pedro@biz.com',
            'business_owner',
            'adventurer/svg?seed=Pedro'
        );

        $anaOwner = $this->createUser(
            'Ana Owner',
            'ana@biz.com',
            'business_owner',
            'adventurer/svg?seed=Ana'
        );

        /*
        |--------------------------------------------------------------------------
        | 5. CUENTAS
        |--------------------------------------------------------------------------
        */
        $pedroCWB = $this->createAccount($pedroCo, $curitiba, $libera, '#0f172a', 'PA-CWB');
        $this->affiliate($pedroOwner, Account::class, $pedroCWB->id, 'business_owner');

        $pedroMAO = $this->createAccount($pedroCo, $manaus, $alugapp, '#0f172a', 'PA-MAO');
        $this->affiliate($pedroOwner, Account::class, $pedroMAO->id, 'business_owner');

        $anaMAO = $this->createAccount($anaCo, $manaus, $menuapp, '#701a75', 'AS-MAO');
        $this->affiliate($anaOwner, Account::class, $anaMAO->id, 'business_owner');

        $anaSZX = $this->createAccount($anaCo, $china, $alugapp, '#701a75', 'AS-SZX');
        $this->affiliate($anaOwner, Account::class, $anaSZX->id, 'business_owner');

        /*
        |--------------------------------------------------------------------------
        | 6. BRANCH MANAGERS
        |--------------------------------------------------------------------------
        */
        $managerCWB = $this->createUser(
            'Lucas Curitiba',
            'lucas.cwb@corp.com',
            'branch_manager',
            'bottts/svg?seed=Lucas'
        );

        $managerMAO = $this->createUser(
            'Marina Manaus',
            'marina.mao@corp.com',
            'branch_manager',
            'bottts/svg?seed=Marina'
        );

        $managerSZX = $this->createUser(
            'Wei Shenzhen',
            'wei.szx@corp.com',
            'branch_manager',
            'bottts/svg?seed=Wei'
        );

        $managerEGL = $this->createUser(
            'Eglis Manager',
            'manager.eglis@corp.com',
            'branch_manager',
            'bottts/svg?seed=Eglis'
        );

        $managerKJU = $this->createUser(
            'Kju Manager',
            'manager.kju@corp.com',
            'branch_manager',
            'bottts/svg?seed=Kju'
        );

        // Afiliación de managers a su branch
        $this->affiliate($managerCWB, Branch::class, $curitiba->id, 'branch_manager');
        $this->affiliate($managerMAO, Branch::class, $manaus->id,   'branch_manager');
        $this->affiliate($managerSZX, Branch::class, $china->id,    'branch_manager');
        $this->affiliate($managerEGL, Branch::class, $eglis->id,    'branch_manager');
        $this->affiliate($managerKJU, Branch::class, $kju->id,      'branch_manager');

        /*
        |--------------------------------------------------------------------------
        | 7. STAFF
        |--------------------------------------------------------------------------
        */
        $carlos = $this->createUser(
            'Carlos Staff',
            'carlos@pedro.com',
            'business_staff',
            'bottts/svg?seed=Carlos'
        );

        $this->affiliate($carlos, Account::class, $pedroCWB->id, 'business_staff');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    private function createAccount($company, $branch, $solution, $color, $seed): Account
    {
        return Account::create([
            'branch_id'        => $branch->id,
            'accountable_id'   => $company->id,
            'accountable_type' => Company::class,
            'solution_id'      => $solution->id,
            'solution_slug'    => $solution->slug,
            'is_active'        => true,
            'branding'         => [
                'primary_color' => $color,
                'logo'          => "https://api.dicebear.com/7.x/identicon/svg?seed={$seed}",
                'watermark'     => "https://images.unsplash.com/photo-1614850523296-d8c1af93d400?w=800&q=40&blur=10",
                'context_label' => $company->name
            ]
        ]);
    }

    private int $cpfCounter = 500;

    private function createUser($name, $email, $role, $avatarSeed): User
    {
        $user = User::firstOrCreate(['email' => $email], [
            'name'     => $name,
            'cpf'      => str_pad((string) $this->cpfCounter++, 11, '0', STR_PAD_LEFT),
            'password' => Hash::make('password@123'),
        ]);

        $user->assignRole($role);

        if ($user->media()->count() === 0) {
            try {
                $user->addMediaFromUrl("https://api.dicebear.com/7.x/{$avatarSeed}")
                     ->toMediaCollection('avatar');
            } catch (\Exception $e) {}
        }

        return $user;
    }

    private function affiliate($user, $type, $id, $role): void
    {
        Affiliation::create([
            'user_id'           => $user->id,
            'affiliatable_type' => $type,
            'affiliatable_id'   => $id,
            'role'              => $role
        ]);
    }
}
