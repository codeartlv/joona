<?php

namespace Codeart\Joona\View\Components\Autocomplete;

use Illuminate\Contracts\Support\Arrayable;

class Response implements Arrayable
{
	private $items = [];

	public function __construct(array $items)
	{
		$this->items = $items;
	}

	public function toArray()
	{
		return [
			'suggestions' => array_map(function($item){
				return $item->toArray();
			}, $this->items)
		];
	}
}
