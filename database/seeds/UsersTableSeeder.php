<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Role::create(['name' => 'votante']);
        Role::create(['name' => 'admin']);

        $user = \App\User::create([
            'name' => 'IVAN ALFONSO MANGONES NIEVES',
            'email' => 'ivan.mangones@comfamiliar.org.co',
            'cedula' => 1067403004,
            'password' => Hash::make('Iv@n890504'),
        ]);
        $user->assignRole('admin');

    }
}
