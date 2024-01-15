<?php

namespace Codeart\Joona\Commands;

use Codeart\Joona\Models\User\AdminSession;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Routing\Router;

class UpdateAdminSession extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'joona:update-session';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update admin user session data';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$min = 15;

		$entries = AdminSession::whereNull('ended')
			->whereRaw('NOW() > DATE_ADD(last_action, INTERVAL ? MINUTE)', [$min])
			->get();

		$entries->each(function ($entry) {
			$ended = $entry->last_action ? $entry->last_action : now();

			$entry->update([
				'ended' => $ended,
				'end_reason' => 'stopped',
			]);
		});
	}
}
