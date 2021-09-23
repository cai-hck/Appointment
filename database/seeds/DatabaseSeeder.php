<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
           // $this->call(UsersTableSeeder::class);
           DB::table('users')->insert([
            [
				'name'       => 'admin',
				'email'      => 'admin@gmail.com',
                'password'   => bcrypt('123456'),
                'role'       => 'admin',
				'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
				'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]
        ]);        
    }
}
