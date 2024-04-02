<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! User::query()->where('email', 'josefo727@gmail.com')->exists()) {
            $user = User::query()->create([
                'name' => 'José R. Gutierrez',
                'email' => 'josefo727@gmail.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $user->save();
        }
    }
}
