<?php

namespace Codeart\Joona\Helpers;

use Illuminate\Support\Facades\Cache;

class FloodCheck
{
	/**
	 * User identity string
	 *
	 * @var string
	 */
	private string $identity;

	/**
	 * Category of performed action
	 *
	 * @var string
	 */
	private string $action;

	public function __construct(string $action = 'default', string $identity = 'ip')
	{
		if ($identity == 'ip') {
			$identity = request()->ip() ?? 'unknown-ip';
		}

		$this->identity = $identity;
		$this->action = $action;
	}

	/**
	 * Check for flood without adding attempt
	 *
	 * @param integer $maxAttempts
	 * @return bool
	 */
	public function checkOnly(int $maxAttempts = 5): bool
	{
		$key = $this -> getCacheKey();

		if (!Cache::has($key)) {
			return true;
		}

		$cache_data = Cache::get($key);

		if (!is_array($cache_data)) {
			$cache_data = [
				'attempts' => 0,
			];
		}

		$count = $cache_data['attempts'];

		if ($count < $maxAttempts) {
			return true;
		}

		return false;
	}

	/**
	 * Returns cache key
	 *
	 * @return string
	 */
	private function getCacheKey(): string
	{
		return 'fc:'.$this->identity.':'.$this->action;
	}

	/**
	 * Checks for flood
	 *
	 * @param integer $seconds
	 * @param integer $maxAttempts
	 * @return bool
	 */
	public function check(int $seconds = 10, int $maxAttempts = 5): bool
	{
		$this -> addAttempt($seconds);
		return $this -> checkOnly($maxAttempts);
	}

	/**
	 * Return timestamp when lock will be lifted
	 *
	 * @return int
	 */
	public function getUnlockTime(): int
	{
		$key = $this -> getCacheKey();

		if (Cache::has($key)) {
			$cache_data = Cache::get($key);
			return (int) $cache_data['expiration'];
		}

		return 0;
	}

	/**
	 * Adds flood attempt
	 *
	 * @param integer $seconds
	 * @return void
	 */
	public function addAttempt(int $seconds = 10) : void
	{
		$data = [
			'action' => $this->action,
			'attempts' => 1,
			'expiration' => strtotime('+'.$seconds.' Seconds')
		];

		$key = $this -> getCacheKey();

		if (Cache::has($key)) {
			$cache_data = Cache::get($key);

			if (!is_array($cache_data)) {
				$cache_data = [];
			}

			$updated_data = [
				'action' => $this->action,
				'attempts' => $cache_data['attempts'] + 1,
				'expiration' => $cache_data['expiration']
			];

			Cache::put($key, $updated_data, $cache_data['expiration'] - time());
			return;
		}

		Cache::put($key, $data, $seconds);
	}
}
