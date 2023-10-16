<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Model\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      // Account::factory(10)->create();
      DB::table('accounts')->insert([
        'first_name'=> 'Mohamed',
        'last_name'=> "kandad",
        'password'=> 'Sika@@123',
        'email'=> 'kandad.tersea@gmail.com',
        'profile'=> 'admin',
        'status'=> 'invite'
      ]);
    }
}
