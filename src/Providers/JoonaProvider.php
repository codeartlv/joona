<?php

namespace Codeart\Joona\Providers;

use Codeart\Joona\Facades\Joona;
use Codeart\Joona\Listeners\AddProviderToBootstrapListener;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Events\VendorTagPublished;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
		if (!$this->app->runningInConsole()) {
			return;
		}

		$this->setupMigrations();

		$this->offerPublish();

		$this->addConsoleCommands();
	}

	/**
	 * Configure package commands
	 *
	 * @return void
	 */
	private function addConsoleCommands(): void
	{
		// Register commands
		$this->commands([
			\Codeart\Joona\Commands\Seed::class,
			\Codeart\Joona\Commands\PublishAssets::class,
			\Codeart\Joona\Commands\UpdateSession::class,
		]);

		// Schedule commands
		$this->app->booted(function () {
            $schedule = app(Schedule::class);
			$schedule->command('joona:update-session')->everyTenMinutes();
        });
	}

	/**
	 * Static asset publishing
	 *
	 * @return void
	 */
	private function offerPublish(): void
	{
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

		// Register the listener for when vendor:publish runs
		Event::listen(VendorTagPublished::class, AddProviderToBootstrapListener::class);
	}

	/**
	 * Configure package migrations
	 *
	 * @return void
	 */
	private function setupMigrations(): void
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom($this->getPackageMigrationsPath());
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
