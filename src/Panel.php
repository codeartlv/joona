<?php

namespace Codeart\Joona;

use Codeart\Joona\Facades\Permission;
use Illuminate\Support\Facades\Lang;
use Codeart\Joona\MetaData\Page;
use Codeart\Joona\MetaData\Locale;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
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
	private $appLogo = '/vendor/joona/images/example_logo.png';

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
		$this->viteResources = $resources;
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
	 * @param string $path URL to logo image file
	 * @return Panel
	 */
	public function setLogo(string $path): self
	{
		$this->appLogo = $path;

		return $this;
	}

	/**
	 * Returns app logo
	 *
	 * @return string
	 */
	public function getLogo(): string
	{
		return $this->appLogo;
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

			$path = explode('.', $page->id);
			$this->insertPage($path, $page);
		}

		return $this;
	}

	/**
	 * Insert menu item at specified position
	 *
	 * @param array $path
	 * @param Page $page
	 * @return void
	 */
	private function insertPage(array $path, Page $page)
	{
		if (count($path) == 1) {
			$this->insertAtDashboard($page);
		} else {
			$this->insertAtParent($path, $page);
		}
	}

	/**
	 * Insert page after the "Dashboard"
	 *
	 * @param Page $page
	 * @return void
	 */
	private function insertAtDashboard(Page $page)
	{
		$dashboardIndex = $this->findPageIndex('dashboard');

		if ($dashboardIndex !== -1) {
			$this->pages = array_merge(
				array_slice($this->pages, 0, $dashboardIndex + 1),
				[
					[
						'page' => $page,
						'children' => [],
					]
				],
				array_slice($this->pages, $dashboardIndex + 1)
			);
		} else {
			array_unshift($this->pages, [
				'page' => $page,
				'children' => [],
			]);
		}
	}

	/**
	 * Insert page at the parent page
	 *
	 * @param array $path
	 * @param Page $page
	 * @return void
	 */
	private function insertAtParent(array $path, Page $page)
	{
		$parentIndex = $this->findPageIndex($path[0]);

		if ($parentIndex === -1) {
			$this->pages[] = [
				'page' => $page,
				'children' => []
			];
		} else {
			$this->pages[$parentIndex]['children'][] = [
				'page' => $page,
				'children' => [],
			];
		}
	}

	/**
	 * Returns page index by ID
	 *
	 * @param mixed $id
	 * @return string|int
	 */
	private function findPageIndex($id)
	{
		foreach ($this->pages as $index => $element) {
			if ($element['page'] instanceof Page && $element['page']->id === $id) {
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
		return $this->pages;
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
