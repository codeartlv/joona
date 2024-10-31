<?php

namespace Codeart\Joona\Listeners;

use Codeart\Joona\Providers\JoonaProvider;
use Illuminate\Foundation\Events\VendorTagPublished;
use Illuminate\Support\Str;

/**
 * Event listener for package publishing. When the service provider is
 * published, try to add service provider into app's bootstrap file. Works on
 * Laravel >= 11 *
 *
 * @package Codeart\Sonora\Listeners
 * @author Deniss Kozlovs <deniss@codeart.lv>
 */
class AddProviderToBootstrapListener
{
	public function handle(VendorTagPublished $event)
	{
		// Check if any of the published paths contain the service provider file
		foreach ($event->paths as $path) {
			if (strpos($path, 'JoonaServiceProvider') !== false) {
				$this->addProviderToBootstrapFile();
				break;
			}
		}
	}

	private function addProviderToBootstrapFile()
	{
		if (method_exists(JoonaProvider::class, 'addProviderToBootstrapFile')) {
			$namespace = Str::replaceLast('\\', '', app()->getNamespace());
			JoonaProvider::addProviderToBootstrapFile("{$namespace}\\Providers\\JoonaServiceProvider");
		}
	}
}
