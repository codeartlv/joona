<?php

namespace Codeart\Joona\Providers;

use Blade;
use Codeart\Joona\Facades\Permission;
use Codeart\Joona\Models\User\Access\PermissionLoader;
use Codeart\Joona\Models\User\AdminUser;
use Codeart\Joona\Models\User\AdminUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorObject;
use Illuminate\Translation\Translator;

class JoonaServiceProvider extends \Illuminate\Support\ServiceProvider
{
	private function getPackageRoot(): string
	{
		return __DIR__.'/../../';
	}

	private function getPackageConfigPath(): string
	{
		return $this->getPackageRoot().'config/';
	}

	private function getPackageRoutesPath(): string
	{
		return $this->getPackageRoot().'routes/';
	}

	private function getPackageTranslationsPath(): string
	{
		return $this->getPackageRoot().'lang/';
	}

	private function getPackageMigrationsPath(): string
	{
		return $this->getPackageRoot().'database/migrations/';
	}

	private function getPackageViewsPath(): string
	{
		return $this->getPackageRoot().'resources/views/';
	}

	private function getPackageDistAssetPath(): string
	{
		return $this->getPackageRoot().'resources/assets/dist/';
	}

	private function offerPublish(): void
	{
		// Provide an application config
		$this->publishes([
			$this->getPackageConfigPath() . 'joona.php' => config_path('joona.php'),
		], 'joona-config');

		// Publish assets
		$this->publishes([
			$this->getPackageDistAssetPath() => public_path('vendor/joona'),
		], 'joona-assets');
	}

	private function handleConfigurations(): void
	{
		// Merge "auth" config to add custom guard
		$auth_config = require $this->getPackageConfigPath() . 'auth.php';

		array_map(function ($key) use ($auth_config) {
			$config = config('auth.'.$key, []);

			config([
				'auth.'.$key => array_merge_recursive($config, $auth_config[$key])
			]);
		}, array_keys($auth_config));

		// Merge "permissions" config with user configuration
		$package_config = require $this->getPackageConfigPath() . 'permissions.php';
		$user_config = (array) config('permissions', []);

		config([
			'permissions' => array_merge_recursive($user_config, $package_config)
		]);
	}

	private function setupMiddlewares(): void
	{
		$router = $this->app['router'];

		$router->middlewareGroup('admin.auth', [
			\Codeart\Joona\Middleware\Authenticate::class,
			\Codeart\Joona\Middleware\CheckPermissions::class,
		]);

		$router->middlewareGroup('admin.web', [
			\Codeart\Joona\Middleware\Locale::class,
			\Codeart\Joona\Middleware\LogActions::class
		]);
	}

	private function setupRoutes(): void
	{
		$this->loadRoutesFrom($this->getPackageRoutesPath() . 'web.php');
	}

	private function setupMigrations(): void
	{
		if ($this->app->runningInConsole()) {
			$this->loadMigrationsFrom($this->getPackageMigrationsPath());
		}
	}

	private function setupTranslations(): void
	{
		$this->loadTranslationsFrom($this->getPackageTranslationsPath(), 'joona');
	}

	private function setupViews(): void
	{
		$this->loadViewsFrom($this->getPackageViewsPath(), 'joona');

		// View composers
		View::composer([
			'joona::global',
			'joona::default',
			'joona::user.auth-form'
		], \Codeart\Joona\Composers\GlobalViewComposer::class);

		// Blade helpers
		Blade::directive('icon', function ($name) {
			return "<i class=\"material-symbols-outlined\"><?={$name};?></i>";
		});

		// Components
		Blade::component('joona-password-validator', \Codeart\Joona\View\Components\PasswordValidator::class);
		Blade::component('joona-paginator', \Codeart\Joona\View\Components\Paginator::class);
		Blade::component('joona-checkbox', \Codeart\Joona\View\Components\Checkbox::class);
		Blade::component('joona-form', \Codeart\Joona\View\Components\Form\FormElement::class);
		Blade::component('joona-form-group', \Codeart\Joona\View\Components\Form\Group::class);
		Blade::component('joona-button', \Codeart\Joona\View\Components\Form\Button::class);
	}

	private function registerGate(): void
	{
		$permissions = Permission::getPlainPermissions();

		foreach ($permissions as $permission) {
			Gate::define($permission, function ($user) use ($permission) {
				return Permission::validate($user, $permission);
			});
		}

		$elevated_routes = Permission::getElevatedPermissions();

		foreach ($elevated_routes as $route) {
			Gate::define($route, function ($user) use ($route) {
				return Permission::validate($user, $route);
			});
		}
	}

	private function addConsoleCommands(): void
	{
		if ($this->app->runningInConsole()) {
			$this->commands([
				\Codeart\Joona\Commands\PublishAssets::class,
				\Codeart\Joona\Commands\UpdateAdminSession::class,
				\Codeart\Joona\Commands\Seed::class,
			]);
		}
	}

	/**
	 * Register services.
	 *
	 * This method is called after all other service providers have been registered,
	 * meaning you have access to all other services that have been registered by the framework.
	 */
	public function register()
	{
		$this->handleConfigurations();

		$this->app->singleton('JoonaPermissionLoader', function () {
			return new PermissionLoader();
		});
	}

	/**
	 * Bootstrap services.
	 *
	 * This method is called after all other service providers have been booted,
	 * meaning you have access to all other services that have been booted by the framework.
	 */
	public function boot()
	{
		$this->offerPublish();

		$this->setupMiddlewares();

		$this->setupRoutes();

		$this->setupMigrations();

		$this->setupTranslations();

		$this->setupViews();

		$this->addConsoleCommands();

		// Register user provider
		Auth::provider('joona', function ($app, array $config) {
			return new AdminUserProvider($app['hash'], $config['model']);
		});

		// Add user guard facade
		$this->app->singleton('AdminAuth', function ($app) {
			return Auth::guard('admin');
		});

		// Use custom validation translations
		Validator::resolver(function ($translator, $data, $rules, $messages, $custom_attrib) {
			$packageMessages = $translator->get('joona::validation');
			$messages = array_merge($messages, $packageMessages);

			$custom_attrib = array_merge($custom_attrib, (array) $translator->get('joona::validation.attributes'));

			return new ValidatorObject($translator, $data, $rules, $messages, $custom_attrib);
		});

		$this->registerGate();
	}
}
