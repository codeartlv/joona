<?php

namespace Codeart\Joona\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Codeart\Joona\Models\User\AdminUser;

class AdminUserSeeder extends Seeder
{
	public function run(): void
	{
		AdminUser::create([
			'username' => 'admin',
			'password' => Hash::make('password'),
			'first_name' => 'Default',
			'last_name' => 'User',
			'email' => 'admin@localhost',
			'status' => AdminUser::STATUS_ACTIVE,
			'level' => AdminUser::LEVEL_ADMIN,
		]);
	}
}
