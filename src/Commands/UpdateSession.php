<?php

namespace Codeart\Joona\Commands;

use Codeart\Joona\Models\User\AdminSession;
use Illuminate\Console\Command;

class UpdateSession extends Command
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
	protected $description = 'Update admin user sessions';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle(): void
	{
		$min = 15;
		$check = AdminSession::whereRaw('ended IS NULL AND NOW() > DATE_ADD(last_action, INTERVAL '.$min.' MINUTE)')->get();

		if ($check->count()) {
			foreach ($check as $session) {
				$ended = $session->last_action ?: date('Y-m-d H:i:s');

				$session->update([
					'ended' => $ended,
					'end_reason' => 'stopped',
				]);
			}
		}
	}
}
