<?php

namespace Codeart\Joona\Http\Controllers\Common;

use Codeart\Joona\Facades\Joona;

class ComponentsController
{
	/**
	 * Opens image cropper window for upload component
	 *
	 * @return void
	 */
	public function crop()
	{
		return view('joona::components.cropper');
	}
}
