<?php

namespace Codeart\Joona\Commands;

use Illuminate\Console\Command;

class PublishAssets extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'joona:publish';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Build and publish assets';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle(): void
	{
		$this->info('Installing dependencies...');
		shell_exec('npm --prefix vendor/codeartlv/joona install');

		$this->info('Publishing assets...');

		$this->call('vendor:publish', [
			'--tag' => 'joona-assets',
			'--force' => true,
		]);

		$this->call('vendor:publish', [
			'--tag' => 'joona-config',
		]);

		$this->call('vendor:publish', [
			'--tag' => 'joona-provider',
		]);
	}
}
