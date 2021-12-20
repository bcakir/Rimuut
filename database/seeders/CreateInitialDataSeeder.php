<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Validation\Rules\In;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Currency;
use App\Models\Invoice;

class CreateInitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return bool
     */
    public function run()
    {
        if (User::all()->count() > 0)
        {
            return false;
        }

        $this->createRoles();
        $this->createUsers();
        $this->syncUserRoles();
        $this->createCurrencies();
        $this->createStatuses();
    }

    /**
     * Create Users
     *
     * @return void
     */
    private function createUsers()
    {
        $users = [
            [
                'name' => 'Bünyamin',
                'surname' => 'Çakır',
                'email' => 'bunyamincakir61@gmail.com',
                'password' => bcrypt('BC123456'),
                'address' => 'Güngören/İstanbul'
            ],
            [
                'name' => 'Mehmet',
                'surname' => 'Metiner',
                'email' => 'mehmet.metiner@gmail.com',
                'password' => bcrypt('MH123456'),
                'address' => 'Etiler/İstanbul'
            ],
            [
                'name' => 'Selami',
                'surname' => 'Şahin',
                'email' => 'selami.sahin@gmail.com',
                'password' => bcrypt('SH123456'),
                'address' => 'Tarabya/İstanbul'
            ],
            [
                'name' => 'Mustafa',
                'surname' => 'Altınok',
                'email' => 'mustafa.alitinok@gmail.com',
                'password' => bcrypt('MA123456'),
                'address' => 'Beşiktaş/İstanbul'
            ],
            [
                'name' => 'Hasan',
                'surname' => 'Altınok',
                'email' => 'hasan.alitinok@gmail.com',
                'password' => bcrypt('HA123456'),
                'address' => 'Beşiktaş/İstanbul'
            ],
        ];

        User::insert($users);
    }

    /**
     * Create Roles
     *
     * @return void
     */
    private function createRoles()
    {
        $roles = [
            ['name' => 'Freelancer'],
            ['name' => 'Business'],
            ['name' => 'Instructor'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }

    /**
     * Assign a role for users
     *
     * @return void
     */
    private function syncUserRoles()
    {
        $users = User::all();
        $roles = Role::all();

        foreach ($users as $user) {
            $random = rand(0, 2);
            $user->syncRoles($roles[$random]);
        }
    }

    private function createCurrencies()
    {
        $currencies = [
            ['code' =>'TRY' , 'name' => 'Turkish Lira', 'symbol' => 'TL'],
            ['code' =>'USD' , 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' =>'EUR' , 'name' => 'Euro', 'symbol' => '€'],
        ];

        Currency::insert($currencies);
    }

    private function createStatuses()
    {
        $statuses = [
            ['code' =>'ACT' , 'name' => 'Active'],
            ['code' =>'CNL' , 'name' => 'Cancelled'],
            ['code' =>'EXP' , 'Expired'],
        ];

        Status::insert($statuses);
    }
}
