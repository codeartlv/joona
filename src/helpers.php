<?php

use Codeart\Joona\Facades\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;

/**
 * Returns list of defined localed
 *
 * @return array
 */
function get_admin_locales(): array
{
	$locales = (array) config('joona.locales');
	$map = (array) config('joona.locale_map');

	array_walk($locales, function (&$locale, $key) use (&$locales, $map) {
		$locale['enabled'] = $locale['enabled'] ?? false;

		if (!$locale['enabled']) {
			unset($locales[$key]);
			return;
		}

		$locale['url'] = route('joona.set-locale', ['locale' => $key]);
		$locale['active'] = Lang::locale() == $key;

		$locale_key = $map[$key] ?? $key;

		$locale['flag'] = '/vendor/joona/images/flags/'.$locale_key.'.svg';
	});


	return $locales;
}

/**
 * Merges arrays recursively.
 *
 * @param array $array1
 * @param array $array2
 * @return array
 */
function array_merge_deep(array $array1, array $array2)
{
	foreach ($array2 as $k => $v) {
		if (is_integer($k)) {
			$array1[] = $v;
		} elseif (is_array($v) && isset($array1[$k]) && is_array($array1[$k])) {
			$array1[$k] =array_merge_deep($array1[$k], $v);
		} else {
			$array1[$k] = $v;
		}
	}
	return $array1;
}

/**
 * Parses pssword policy string
 *
 * @param string $policy
 * @return array
 */
function parse_password_policy(string $policy): array
{
	$rules = explode(',', $policy);
	$mapped = [];

	foreach ($rules as $rule) {
		$parts = explode(':', $rule);
		$rule_name = $parts[0];
		$rule_value = $parts[1] ?? true;

		$mapped[$rule_name] = $rule_value;
	}

	return $mapped;
}

/**
 * Generates random password
 *
 * @param int $length
 * @return string
 */
function str_random_password($length = 10): string
{
	$upper_case = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$lower_case = 'abcdefghijklmnopqrstuvwxyz';
	$numbers = '0123456789';
	$special_chars = '!@#$%^&*()-_=+';

	// Ensure the password contains at least one character from each set
	$password = $upper_case[rand(0, strlen($upper_case) - 1)] .
				$lower_case[rand(0, strlen($lower_case) - 1)] .
				$numbers[rand(0, strlen($numbers) - 1)] .
				$special_chars[rand(0, strlen($special_chars) - 1)];

	// Fill the rest of the password length with random characters from all sets
	$all_characters = $upper_case . $lower_case . $numbers . $special_chars;

	for ($i = 4; $i < $length; $i++) {
		$password .= $all_characters[rand(0, strlen($all_characters) - 1)];
	}

	return str_shuffle($password);
}

/**
 * Returns main menu
 *
 * @return array
 */
function get_admin_menu(): array
{
	$user_menu = (array) config('joona.menu');

	$settings = [];

	$settings[] = [
		'caption' => __('joona::common.menu.settings_users'),
		'icon' => 'person',
		'badge' => 0,
		'route' => 'joona.user.list',
		'active' => ['joona.user.activities']
	];

	if (config('joona.use_permissions')) {
		$settings[] = [
			'caption' => __('joona::common.menu.settings_permissions'),
			'icon' => 'linked_services',
			'badge' => 0,
			'route' => 'joona.user.permission-groups',
		];
	}

	$default_menu = [
		'home' => [
			'caption' => __('joona::common.menu.home'),
			'icon' => 'dashboard',
			'route' => 'joona.dashboard',
		],
		'settings' => [
			'caption' => __('joona::common.menu.settings'),
			'icon' => 'tune',
			'badge' => 0,
			'childs' => $settings
		]
	];

	$menu = array_merge_deep($user_menu, $default_menu);

	// Filter out forbidden routes
	$filter_forbidden = function ($items) use (&$filter_forbidden) {
		foreach ($items as $index => $item) {
			$item += [
				'route' => ''
			];

			if (!empty($item['childs'])) {
				$item['childs'] = $filter_forbidden($item['childs']);
			}

			if (Permission::isDefined($item['route']) && Gate::denies($item['route'])) {
				unset($items[$index]);
				continue;
			}

			if (array_key_exists('childs', $item) && empty($item['childs'])) {
				unset($items[$index]);
				continue;
			}


			$items[$index] = $item;
		}

		return $items;
	};

	$menu = $filter_forbidden($menu);

	// Detect active status
	$active_route = request()->route();

	$detect_active = function ($items) use (&$detect_active, $active_route) {
		foreach ($items as $index => $item) {
			$item += [
				'route' => ''
			];

			$has_active_child = false;

			if (!empty($item['childs'])) {
				$item['childs'] = $detect_active($item['childs']);

				$active_children = array_filter($item['childs'], function ($child) {
					return !empty($child['active']);
				});

				$has_active_child = count($active_children) > 0;
			}

			$test_routes = [$item['route']];

			if (!empty($item['active']) && is_array($item['active'])) {
				$test_routes = array_merge($test_routes, $item['active']);
			}

			$is_active_route = is_object($active_route) && method_exists($active_route, 'getName') && in_array($active_route->getName(), $test_routes);
			$item['active'] = $is_active_route || $has_active_child;

			$items[$index] = $item;
		}

		return $items;
	};

	return $detect_active($menu);
}
