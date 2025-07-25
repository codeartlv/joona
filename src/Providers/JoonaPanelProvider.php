<?php

namespace Codeart\Joona\Providers;

use Blade;
use Codeart\Joona\Auth\AdminUserProvider;
use Codeart\Joona\Auth\Permissions\PermissionGroup;
use Codeart\Joona\Auth\Permissions\RoutePermission;
use Codeart\Joona\Auth\Permissions\PermissionLoader;
use Codeart\Joona\Contracts\PermissionLoaderInterface;
use Codeart\Joona\Facades\Joona;
use Codeart\Joona\MetaData\Page;
use Codeart\Joona\Panel;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidatorObject;

abstract class JoonaPanelProvider extends JoonaProvider
{
	/**
	 * Configure the admin panel settings.
	 *
	 * @param Panel $panel
	 * @return void
	 */
	abstract protected function configure(Panel $panel): void;

	/**
	 * Register services.
	 *
	 * This method is called after all other service providers have been registered,
	 * meaning you have access to all other services that have been registered by the framework.
	 */
	public function register()
	{
		$this->app->singleton('codeartlv.joona', fn () => new Panel());

		$this->registerAuthGuard();
	}

	/**
	 * Bootstrap services.
	 *
	 * This method is called after all other service providers have been booted,
	 * meaning you have access to all other services that have been booted by the framework.
	 */
	public function boot()
	{
		$this->setupTranslations();

		$this->setupViews();

		$this->registerEventListeners();

		$this->registerPages();

		$this->configurePanel();

		$this->setupRoutes();

		$this->setupMiddlewares();

		$this->registerGate();
	}

	/**
	 * Registers the auth guard.
	 *
	 * Takes config/auth.php and appends the auth guard configuration.
	 *
	 * @return void
	 */
	private function registerAuthGuard(): void
	{
		// Merge "auth" config to add custom guard
		$authConfig = require $this->getPackageConfigPath() . 'auth.php';

		array_map(function ($key) use ($authConfig) {
			$config = config('auth.'.$key, []);

			config([
				'auth.'.$key => array_merge_recursive($config, $authConfig[$key])
			]);
		}, array_keys($authConfig));

		// Register user provider
		Auth::provider('joona', fn ($app, array $config) => new AdminUserProvider($app['hash'], $config['model']));

		// Add user guard facade
		$this->app->singleton('joona.auth', fn ($app) => Auth::guard('admin'));
	}

	/**
	 * Configure package views
	 *
	 * @return void
	 */
	private function setupViews(): void
	{
		$this->loadViewsFrom($this->getPackageViewsPath(), 'joona');

		// View composers
		View::composer([
			'joona::global',
			'joona::default',
			'joona::error',
			'joona::user.auth-form',
			'joona::user.invite-form',
			'joona::user.recover-form',
			'joona::user.recover-password',
		], \Codeart\Joona\View\Composers\GlobalViewComposer::class);

		// Blade helpers
		Blade::directive('icon', function ($name) {
			return "<i data-role=\"icon\" class=\"material-symbols-outlined\"><?={$name};?></i>";
		});

		Blade::directive('attributes', function ($expression) {
			return "<?php echo \Codeart\Joona\Helpers\HtmlHelper::attributes({$expression}); ?>";
		});

		$components = [
			'autocomplete' => \Codeart\Joona\View\Components\Autocomplete\Autocomplete::class,
			'button' => \Codeart\Joona\View\Components\Button::class,
			'form' => \Codeart\Joona\View\Components\Form\FormElement::class,
			'dialog' => \Codeart\Joona\View\Components\Dialog::class,
			'paginator' => \Codeart\Joona\View\Components\Paginator::class,
			'alert' => \Codeart\Joona\View\Components\Alert::class,
			'password-validator' => \Codeart\Joona\View\Components\PasswordValidator::class,
			'datepicker' => \Codeart\Joona\View\Components\Datepicker::class,
			'uploader' => \Codeart\Joona\View\Components\Uploader\Uploader::class,
			'select' => \Codeart\Joona\View\Components\Select\Select::class,
			'textarea' => \Codeart\Joona\View\Components\Textarea::class,
			'input' => \Codeart\Joona\View\Components\Input::class,
			'checkbox' => \Codeart\Joona\View\Components\Checkbox::class,
			'form-section-heading' => \Codeart\Joona\View\Components\FormSectionHeading::class,
			'navbar' => \Codeart\Joona\View\Components\Navbar::class,
			'colorpicker' => \Codeart\Joona\View\Components\Colorpicker::class,
			'range' => \Codeart\Joona\View\Components\Range::class,
			'accordion' => \Codeart\Joona\View\Components\Accordion::class,
			'content' => \Codeart\Joona\View\Components\Layout\Content::class,
			'container' => \Codeart\Joona\View\Components\Layout\Container::class,
			'offcanvas' => \Codeart\Joona\View\Components\Offcanvas::class,
			'map-picker' => \Codeart\Joona\View\Components\MapPicker::class,
			'multiselect' => \Codeart\Joona\View\Components\Multiselect::class,
			'gallery' => \Codeart\Joona\View\Components\Gallery\Gallery::class,
			'copy' => \Codeart\Joona\View\Components\Copy::class,
			'chart' => \Codeart\Joona\View\Components\Chart::class,
			'tree-editor' => \Codeart\Joona\View\Components\TreeEditor::class,
			'text-editor' => \Codeart\Joona\View\Components\TextEditor::class,
			'table-bulk-options' => \Codeart\Joona\View\Components\TableBulkOptions::class,
			'checkbox-group' => \Codeart\Joona\View\Components\CheckboxGroup::class,
			'page-footer-bar' => \Codeart\Joona\View\Components\PageFooterBar::class,
			'table' => \Codeart\Joona\View\Components\DataTable::class,
			'editor' => \Codeart\Joona\View\Components\Editor::class,
			'tags' => \Codeart\Joona\View\Components\Tag\Tags::class,
		];

		foreach ($components as $name => $class) {
			Blade::component($name, $class);
		}
	}

	/**
	 * Configure package routes
	 *
	 * @return void
	 */
	private function setupRoutes(): void
	{
		Joona::addRoutes('free', $this->getPackageRoutesPath() . 'web.php');
		Joona::addRoutes('secure', $this->getPackageRoutesPath() . 'secure.php');
	}

	/**
	 * Configure package event listeners
	 *
	 * @return void
	 */
	private function registerEventListeners(): void
	{
		$this->app['events']->listen(
			\Illuminate\Auth\Events\Logout::class,
			\Codeart\Joona\Listeners\UserLoggedOutListener::class
		);

		$this->app['events']->listen(
			\Illuminate\Auth\Events\Login::class,
			\Codeart\Joona\Listeners\UserLoggedInListener::class
		);
	}

	/**
	 * Configure package middlewares
	 *
	 * @return void
	 */
	private function setupMiddlewares(): void
	{
		$router = resolve('router');

		$authMiddlewares = [
			\Codeart\Joona\Http\Middleware\Authenticate::class,
		];

		if (Joona::usesRolesAndPermissions()) {
			$authMiddlewares[] = \Codeart\Joona\Http\Middleware\CheckPermissions::class;
		}

		$router->middlewareGroup('admin.auth', $authMiddlewares);

		$router->middlewareGroup('admin.web', [
			\Codeart\Joona\Http\Middleware\SetLocale::class,
			\Codeart\Joona\Http\Middleware\LogUserActions::class
		]);
	}

	private function registerPages(): void
	{
		$panel = resolve('codeartlv.joona');

		$default_menu = [];

		$default_menu[] =
			Page::make('dashboard')
				->route('joona.dashboard')
				->caption('joona::common.menu.home')
				->icon('dashboard');

		$default_menu[] =
			Page::make('settings')
				->caption('joona::common.menu.settings')
				->icon('tune');

		$default_menu[] =
			Page::make('settings.users')
				->route('joona.user.list')
				->caption('joona::common.menu.settings_users')
				->icon('person');

		$default_menu[] =
			Page::make('settings.users')
				->route('joona.user.activities')
				->caption('joona::common.menu.settings_users_activities')
				->icon('ballot');

		if (Joona::usesRolesAndPermissions()) {
			$default_menu[] =
				Page::make('settings.permissions')
					->route('joona.user.permission-groups')
					->caption('joona::common.menu.settings_permissions')
					->icon('linked_services');
		}

		$panel->addPages($default_menu);
	}

	/**
	 * Configure gate
	 *
	 * @return void
	 */
	private function registerGate(): void
	{
		$panel = resolve('codeartlv.joona');

		$panel->addPermissions([
			PermissionGroup::make('joona::user.elevated-permissions', [
				new RoutePermission(
					id: 'admin_manage_permissions',
					routes : [
						'joona.user.permission-groups',
						'joona.user.permission-save-role',
						'joona.user.permission-edit-role',
						'joona.user.permission-delete-role',
						'joona.user.permission-save',
					],
					label: 'joona::user.permission_manage_permissions',
					elevated: true
				)
			]),
			PermissionGroup::make('joona::user.admin-users-permissions', [
				new RoutePermission(
					id: 'admin_view_users',
					routes : [
						'joona.user.list',
					],
					label: 'joona::user.permission_view_admin_users',
				),
				new RoutePermission(
					id: 'admin_edit_users',
					routes : [
						'joona.user.edit',
						'joona.user.save'
					],
					label: 'joona::user.permission_manage_admin_users',
				),
				new RoutePermission(
					id: 'admin_view_userlog',
					routes : [
						'joona.user.activities'
					],
					label: 'joona::user.permission_user_activities',
				),
			])
		]);

		$this->app->singleton('joona.permission-loader', function () use ($panel) {

			$className = $panel->getPermissionLoader() ?? '\Codeart\Joona\Auth\Permissions\PermissionLoader';
			$instance = new $className($panel->getPermissions());

			if (!$instance instanceof PermissionLoaderInterface) {
				throw new Exception('PermissionLoader must implement PermissionLoaderInterface interface');
			}

			return $instance;
		});
	}


	/**
	 * Call panel configuration on user side
	 *
	 * @return void
	 */
	private function configurePanel(): void
	{
		$this->configure(resolve('codeartlv.joona'));
	}

	/**
	 * Configure package translations
	 *
	 * @return void
	 */
	private function setupTranslations(): void
	{
		$this->loadTranslationsFrom($this->getPackageTranslationsPath(), 'joona');

		// Use custom validation translations
		Validator::resolver(function ($translator, $data, $rules, $messages, $custom_attrib) {
			$packageMessages = $translator->get('joona::validation');
			$messages = array_merge($messages, $packageMessages);

			$custom_attrib = array_merge($custom_attrib, (array) $translator->get('joona::validation.attributes'));

			return new ValidatorObject($translator, $data, $rules, $messages, $custom_attrib);
		});

		$userKeys = (array) config('joona.js_translations');

		$translationKeys = array_merge([
			'joona::common.ok',
			'joona::common.cancel',
			'joona::common.error',
			'joona::common.delete',
			'joona::common.no_results_found',
			'joona::common.selected',
			'joona::common.copied',
			'joona::common.select_files',
			'joona::common.confirm',
			'joona::common.delete_node_confirm',
			'joona::common.editorjs.tools.list.ordered',
			'joona::common.editorjs.tools.list.unordered',
			'joona::common.editorjs.tools.list.checklist',
			'joona::common.editorjs.tools.heading.name',
			'joona::common.editorjs.tools.gallery.name',
			'joona::common.editorjs.tools.text.name',
			'joona::common.editorjs.tools.quote.name',
			'joona::common.editorjs.tools.table.name',
		], $userKeys);

		$panel = resolve('codeartlv.joona');
		$panel->setJavascriptTranslations($translationKeys);
	}
}
