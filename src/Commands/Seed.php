<?php

namespace Codeart\Joona\Commands;

use Illuminate\Console\Command;

class Seed extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'joona:seed';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Seed the backend data';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle(): void
	{
		$this->call('db:seed', ['--class' => \Codeart\Joona\Database\Seeders\AdminUserSeeder::class]);
	}
}
