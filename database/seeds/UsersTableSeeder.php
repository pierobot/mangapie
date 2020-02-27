<?php

use Illuminate\Database\Seeder;

Use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::count() > 0) {
            $this->command->getOutput()->writeln('Users table is not empty, skipping...');

            return;
        }

        /** @var User $dev */
        $dev = User::create([
            'name' => 'dev',
            'email' => 'fake@email.com',
            'password' => Hash::make('dev'),
        ]);

        $dev->grantRole('Administrator');
    }
}
