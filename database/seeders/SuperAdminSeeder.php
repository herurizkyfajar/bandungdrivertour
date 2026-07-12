<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrNew(['email' => 'herurizkyfajar@gmail.com']);
        $user->name = 'Super Admin';
        $user->password = Hash::make('224589herU!');
        $user->role = 'super_admin';
        $user->save();
    }
}
