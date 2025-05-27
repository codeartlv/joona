<?php

namespace Codeart\Joona;

use Codeart\Joona\Facades\Joona;
use Codeart\Joona\Facades\Permission;
use Illuminate\Support\Facades\Lang;
use Codeart\Joona\MetaData\Page;
use Codeart\Joona\MetaData\Locale;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

class Panel
{
	/**
	 * Panel base path
	 *
	 * @var string
	 */
	private $basePath = '/admin';

	/**
	 * Panel base domain
	 *
	 * @var string
	 */
	private $baseDomain = null;

	/**
	 * Uses roles and permission checks
	 *
	 * @var true
	 */
	private $useRoles = true;

	/**
	 * Enable locales
	 *
	 * @var array
	 */
	private $locales = [];

	/**
	 * Vite resources
	 *
	 * @var array
	 */
	private $viteResources = [];

	/**
	 * App logo
	 *
	 * @var string
	 */
	private ?string $appLogo = '/vendor/joona/images/example_logo.png';

	/**
	 * Dark mode app logo
	 *
	 * @var string
	 */
	private ?string $appLogoDark = '/vendor/joona/images/example_logo.png';

	/**
	 * Sidebar icon
	 *
	 * @var string
	 */
	private ?string $icon = null;

	/**
	 * Panel pages
	 *
	 * @var Page[]
	 */
	private $pages = [];

	/**
	 * Custom permissions
	 *
	 * @var Permission[]
	 */
	private $permissions = [];

	/**
	 * Javascript translation keys
	 *
	 * @var array
	 */
	private $jsTranslations = [];

	/**
	 * Custom permission loader
	 *
	 * @var null|string
	 */
	private ?string $permissionLoader = null;

	/**
	 * Add additional user levels
	 * @var string[]
	 */
	protected array $userClasses = [];

	/**
	 * Get permission loader class
	 *
	 * @return null|string
	 */
	public function getPermissionLoader(): ?string
	{
		return $this->permissionLoader;
	}

	/**
	 * Set custom permission loader class
	 *
	 * @param string $className
	 * @return void
	 */
	public function setPermissionLoader(string $className): self
	{
		$this->permissionLoader = $className;

		return $this;
	}

	/**
	 * Add custom routes
	 *
	 * @param callable $routesCallback
	 * @return void
	 */
	public function addRoutes(string $security, callable|string $routes): self
	{
		$security = in_array($security, ['secure', 'free']) ? $security : 'secure';
		$group = null;
		
		if (is_callable($routes)) {
			$group = $routes;
		} elseif (is_string($routes) && file_exists($routes)) {
			$group = function () use ($routes) {
				require $routes;
			};
		}

		if (!$group) {
			throw new InvalidArgumentException('Routes must be a callable or a valid file path.');
		}

		Route::domain(Joona::getBaseDomain())
			->prefix(Joona::getBasePath())
			->middleware(['web', 'admin.web'])
			->group(function () use ($security, $group) {

				if ($security == 'free') {
					$group();
					return;
				}

				Route::middleware(['admin.auth'])->group(function () use ($group) {
					$group();
				});
			});

		return $this;
	}

	/**
	 * Add additional user levels
	 *
	 * @param string[] $levels
	 * @return Panel
	 */
	public function addUserClasses(array $levels): self
	{
		$this->userClasses = $levels;

		return $this;
	}

	/**
	 * Returns additional user levels
	 *
	 * @return string[]
	 */
	public function getUserClasses(): array
	{
		return $this->userClasses;
	}

	/**
	 * Sets base path for application
	 *
	 * @param string $path Provide a starting point of admin panel. Use '/' or empty string to load panel with prefix.
	 * @return Panel
	 */
	public function setBasePath(string $path): self
	{
		$this->basePath = '/' . ltrim($path, '/');

		return $this;
	}

	/**
	 * Return base domain
	 *
	 * @return null|string
	 */
	public function getBaseDomain(): ?string
	{
		return $this->baseDomain;
	}

	/**
	 * Set base domain where panel resides
	 *
	 * @param string $domain
	 * @return Panel
	 */
	public function setBaseDomain(string $domain): self
	{
		$this->baseDomain = $domain;

		return $this;
	}

	/**
	 * Returns route base path
	 *
	 * @return string
	 */
	public function getBasePath(): string
	{
		return $this->basePath;
	}

	/**
	 * Controls whether to allow creating user roles and check permissions on
	 * them.
	 *
	 * @param bool $state
	 * @return Panel
	 */
	public function useRolesAndPermissions(bool $state): self
	{
		$this->useRoles = $state;

		return $this;
	}

	/**
	 * Returns whether role and permission control is enabled
	 *
	 * @return bool
	 */
	public function usesRolesAndPermissions(): bool
	{
		return $this->useRoles;
	}

	/**
	 * Set list of available locales within admin panel
	 *
	 * @param Locale[] $locales
	 * @return Panel
	 */
	public function setLocales(array $locales): self
	{
		$this->locales = $locales;

		return $this;
	}

	/**
	 * Return defined locales, groupped by language code
	 *
	 * @return Locale[]
	 */
	public function getLocales(): array
	{
		return array_combine(
			array_map(fn($locale) => $locale->code, $this->locales),
			array_map(fn($locale) => $locale, $this->locales)
		);
	}

	/**
	 * Returns formatted list of locales
	 *
	 * @return array
	 */
	public function getLocaleList(): array
	{
		return array_map(function ($locale) {
			return [
				'code' => $locale->code,
				'url' => $locale->getSetupUrl(),
				'title' => $locale->caption,
				'image' => $locale->caption,
				'active' => Lang::locale() == $locale->code,
				'flag' => $locale->getFlagUrl(),
			];
		}, $this->getLocales());
	}

	/**
	 * Return available color themes
	 *
	 * @return array
	 */
	public function getColorThemes(): array
	{
		return ['light', 'dark'];
	}

	/**
	 * Add Vite resources
	 *
	 * @param string[] $resources
	 * @return Panel
	 */
	public function addViteResources(array $resources): self
	{
		$this->viteResources = array_merge($resources, $this->viteResources);
		
		return $this;
	}

	/**
	 * Returns Vite resources
	 *
	 * @return array
	 */
	public function getViteResources(): array
	{
		return $this->viteResources;
	}

	/**
	 * Set custom logo to be used within the admin panel
	 *
	 * @param string $light URL to logo image file
	 * @param string $dark URL to dark logo image file
	 * @param string $icon URL to icon
	 * @return Panel
	 */
	public function setLogo(string $light, ?string $dark = null, ?string $icon = null): self
	{
		$this->appLogo = $light;
		$this->appLogoDark = $dark;
		$this->icon = $icon;

		return $this;
	}

	/**
	 * Returns app logo
	 *
	 * @return string
	 */
	public function getLogo(string $mode = 'light'): string
	{
		$key = 'appLogo';

		if ($mode == 'dark' && $this->appLogoDark) {
			$key = 'appLogoDark';
		}

		return $this->$key;
	}

	/**
	 * Return icon
	 *
	 * @return string
	 */
	public function getIcon(): string
	{
		return $this->icon ?? $this->getLogo('dark');
	}

	/**
	 * Add pages to menu
	 *
	 * @param Page[] $pages
	 * @return self
	 */
	public function addPages(array $pages)
	{
		foreach ($pages as $page) {
			if (!$page instanceof Page) {
				continue;
			}

			$this->pages[] = $page;
		}

		return $this;
	}

	/**
	 * Insert menu item at specified position
	 *
	 * @param Page $page
	 * @return void
	 */
	private function insertPage(Page $page, array &$pages = []): void
	{
		$path = explode('.', $page->id);

		if (count($path) == 1) {
			$this->insertAtDashboard($page, $pages);
			return;
		}

		$this->insertAtParent($page, $pages);
	}

	/**
	 * Insert page after the "Dashboard"
	 *
	 * @param Page $page
	 * @return void
	 */
	private function insertAtDashboard(Page $page, array &$pages = []): void
	{
		$dashboardIndex = $this->findPageIndex('dashboard', $pages);

		if ($dashboardIndex !== -1) {
			$pages = array_merge(
				array_slice($pages, 0, $dashboardIndex + 1),
				[
					[
						'page' => $page,
						'children' => [],
					]
				],
				array_slice($pages, $dashboardIndex + 1)
			);

			return;
		}

		array_unshift($pages, [
			'page' => $page,
			'children' => [],
		]);
	}

	/**
	 * Insert page at the parent page
	 *
	 * @param Page $page
	 * @return void
	 */
	private function insertAtParent(Page $page, array &$pages = []): void
	{
		$path = explode('.', $page->id);
		$parentIndex = $this->findPageIndex($path[0], $pages);

		if ($parentIndex === -1) {
			$pages[] = [
				'page' => $page,
				'children' => []
			];

			return;
		}

		$pages[$parentIndex]['children'][] = [
			'page' => $page,
			'children' => [],
		];
	}

	/**
	 * Returns page index by ID
	 *
	 * @param mixed $pageId
	 * @return string|int
	 */
	private function findPageIndex($pageId, array $pages = [])
	{
		foreach ($pages as $index => $element) {
			if ($element['page'] instanceof Page && $element['page']->id === $pageId) {
				return $index;
			}
		}

		return -1;
	}

	/**
	 * Return registered pages
	 *
	 * @return Page[]
	 */
	public function getPages(): array
	{
		$pages = [];
		$topPages = [];
		$secondPages = [];

		foreach ($this->pages as $i => $page) {
			$path = $page->getPath();

			if (count($path) == 1) {
				$topPages[] = $page;
				continue;
			}

			$secondPages[] = $page;
		}

		$cyclePages = array_merge($topPages, $secondPages);

		foreach ($cyclePages as $page) {
			$this->insertPage($page, $pages);
		}

		return $pages;
	}

	/**
	 * Returns navigation array
	 *
	 * @return array
	 */
	public function getNavigation(): array
	{
		// Filter out forbidden routes
		$filter_forbidden = function ($items) use (&$filter_forbidden) {
			foreach ($items as $index => $item) {
				$page = $item['page'];

				if (!$page instanceof Page) {
					unset($items[$index]);
					continue;
				}

				if (!empty($item['children'])) {
					$item['children'] = $filter_forbidden($item['children']);
				}


				if (Permission::isDefined($page->route) && Gate::denies($page->route)) {
					unset($items[$index]);
					continue;
				}

				$items[$index] = $item;
			}

			return $items;
		};

		$pages = $filter_forbidden($this->getPages());

		// Detect active page and convert to array
		$active_route = request()->route();

		$to_array = function ($items) use (&$to_array, $active_route) {
			foreach ($items as $index => $item) {
				$has_active_child = false;
				$page = $item['page'];

				if (!empty($item['children'])) {
					$item['children'] = $to_array($item['children']);

					$active_children = array_filter($item['children'], function ($child) {
						return !empty($child['active']);
					});

					$has_active_child = count($active_children) > 0;
				}

				$test_routes = array_merge($page->activeRoutes, [
					$page->route
				]);

				$is_active_route = is_object($active_route) && method_exists($active_route, 'getName') && in_array($active_route->getName(), $test_routes);

				$items[$index] = [
					'caption' => $page->caption,
					'url' => $page->route ? route($page->route) : null,
					'active' => $is_active_route || $has_active_child,
					'icon' => $page->icon,
					'badge' => $page->badge,
					'childs' => $item['children'],
				];
			}

			return $items;
		};

		return $to_array($pages);
	}

	/**
	 * Add custom permissions
	 *
	 * @param array $permissions
	 * @return Panel
	 */
	public function addPermissions(array $permissions): self
	{
		$this->permissions = array_merge($permissions, $this->permissions);

		return $this;
	}

	/**
	 * Returns list of registered permissions
	 *
	 * @return array
	 */
	public function getPermissions(): array
	{
		return $this->permissions;
	}

	/**
	 * Returns formatted array of permissions to use in Lang.js
	 *
	 * @return array
	 * @throws BindingResolutionException
	 * @throws NotFoundExceptionInterface
	 * @throws ContainerExceptionInterface
	 */
	public function getJavascriptTranslations(): array
	{
		$translations = [];
		$locale = App::getLocale();

		foreach ($this->jsTranslations as $key) {
			$parts = explode('.', $key, 2) + ['', ''];
			$translations[$locale.'.'.$parts[0]][$parts[1]] = __($key);
		}

		return $translations;
	}

	/**
	 * Sets custom translations keys
	 *
	 * @param array $translations
	 * @return Panel
	 */
	public function setJavascriptTranslations(array $translations = []): self
	{
		$this->jsTranslations = $translations;

		return $this;
	}
}
