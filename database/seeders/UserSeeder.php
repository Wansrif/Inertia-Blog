<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@role.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);

        $superAdmin->assignRole('super-admin');
        $role = Role::findByName('super-admin');
        $role->givePermissionTo('manage roles', 'manage permissions', 'manage users', 'manage posts');

        // $superAdmin->posts()->create([
        //     'title' => 'My First Post',
        //     'slug' => Str::slug('My First Post') . '-' . Str::random(5),
        //     'body' => 'This is my first post',
        // ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@role.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('admin');
        $role = Role::findByName('admin');
        $role->givePermissionTo('manage roles', 'manage permissions', 'manage users', 'manage posts');

        // $admin->posts()->create([
        //     'title' => 'My First Post',
        //     'slug' => Str::slug('My First Post') . '-' . Str::random(5),
        //     'body' => 'This is my first post',
        // ]);

        $author = User::create([
            'name' => 'Author',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $author->assignRole('author');
        $role = Role::findByName('author');
        $role->givePermissionTo('manage posts');

        $author->posts()->create([
            'title' => 'My First Post',
            'slug' => Str::slug('My First Post') . '-' . Str::random(5),
            'body' => 'This is my first post',
        ]);
        $author->posts()->create([
            'title' => 'My Second Post',
            'slug' => Str::slug('My Second Post') . '-' . Str::random(5),
            'body' => 'This is my second post',
        ]);

        $author2 = User::create([
            'name' => 'Author 2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
        ]);

        $author2->assignRole('author');
        $author2->posts()->create([
            'title' => 'My First Post',
            'slug' => Str::slug('My First Post') . '-' . Str::random(5),
            'body' => 'This is my first post',
        ]);
        $author2->posts()->create([
            'title' => 'My Second Post',
            'slug' => Str::slug('My Second Post') . '-' . Str::random(5),
            'body' => 'This is my second post',
        ]);
    }
}
