<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Paginator extends Component
{
	public int $total_pages;
	public int $current_page;

	public function __construct(
		public int $total = 0,
		public int $size = 25,
		public int $page = 25,
		public int $range = 3,
		public string $param = 'page',
	) {
		$this->total_pages = $size > 0 ? max((int) ceil($total / $size), 1) : 0;
		$this->current_page = request()->input($param, 1);
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		$pages = [];
		$start = max($this->current_page - $this->range, 1);
		$end = min($this->current_page + $this->range, $this->total_pages);

		for ($page = $start; $page <= $end; $page++) {
			$pages[] = [
				'number' => $page,
				'active' => $this->current_page == $page,
			];
		}

		if (count($pages) == 1) {
			return '';
		}

		return view('joona::components.paginator', [
			'pages' => $pages,
		]);
	}
}
