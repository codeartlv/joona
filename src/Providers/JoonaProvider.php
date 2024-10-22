<?php

namespace Codeart\Joona\Providers;

use Codeart\Joona\Facades\Joona;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class JoonaProvider extends ServiceProvider
{
	/**
	 * Bootstrap services.
	 *
	 * This method is called after all other service providers have been booted,
	 * meaning you have access to all other services that have been booted by the framework.
	 */
	public function boot()
	{
		$this->offerPublish();

		$this->addConsoleCommands();

		$this->app->booted(function () {
            $schedule = app(Schedule::class);
			$schedule->command('joona:update-session')->everyTenMinutes();
        });
	}

	/**
	 * Configure package commands
	 *
	 * @return void
	 */
	private function addConsoleCommands(): void
	{
		if ($this->app->runningInConsole()) {
			$this->commands([
				\Codeart\Joona\Commands\Seed::class,
				\Codeart\Joona\Commands\PublishAssets::class,
				\Codeart\Joona\Commands\UpdateSession::class,
			]);
		}
	}

	/**
	 * Static asset publishing
	 *
	 * @return void
	 */
	private function offerPublish(): void
	{
		if ($this->app->runningInConsole()) {
			// Provide an application config
			$this->publishes([
				$this->getPackageConfigPath() . 'joona.php' => config_path('joona.php'),
			], 'joona-config');

			// Publish assets
			$this->publishes([
				$this->getPackageDistAssetPath() => public_path('vendor/joona/images'),
			], 'joona-assets');

			// Publish provider
			$this->publishes([
				$this->getPackageExportPath() . 'JoonaServiceProvider.php' => app_path('Providers/JoonaServiceProvider.php'),
			], 'joona-provider');
		}
	}

	/**
	 * Returns package root directory
	 *
	 * @return string
	 */
	protected function getPackageRoot(): string
	{
		return __DIR__.'/../../';
	}

	/**
	 * Returns path to package routes directory
	 *
	 * @return string
	 */
	protected function getPackageRoutesPath(): string
	{
		return $this->getPackageRoot().'routes/';
	}

	/**
	 * Returns path to package migrations directory
	 *
	 * @return string
	 */
	protected function getPackageMigrationsPath(): string
	{
		return $this->getPackageRoot().'database/migrations/';
	}

	/**
	 * Returns path to package transations directory
	 *
	 * @return string
	 */
	protected function getPackageTranslationsPath(): string
	{
		return $this->getPackageRoot().'lang/';
	}

	/**
	 * Returns path to package views directory
	 *
	 * @return string
	 */
	protected function getPackageViewsPath(): string
	{
		return $this->getPackageRoot().'resources/views/';
	}

	/**
	 * Returns path to package config directory
	 *
	 * @return string
	 */
	protected function getPackageConfigPath(): string
	{
		return $this->getPackageRoot().'config/';
	}

	/**
	 * Asset directory
	 *
	 * @return string
	 */
	protected function getPackageDistAssetPath(): string
	{
		return $this->getPackageRoot().'resources/assets/images/';
	}

	/**
	 * Export directory
	 *
	 * @return string
	 */
	protected function getPackageExportPath(): string
	{
		return $this->getPackageRoot().'export/';
	}
}
