<?php

namespace Codeart\Joona\Database\Seeders;

use Codeart\Joona\Enums\UserLevel;
use Codeart\Joona\Models\User\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
	/**
	 * Run the database seeder
	 *
	 * @return void
	 */
	public function run(): void
	{
		AdminUser::create([
			'password' => Hash::make('password'),
			'first_name' => 'Default',
			'last_name' => 'User',
			'email' => 'admin@localhost',
			'level' => UserLevel::Admin,
		]);
	}
}
