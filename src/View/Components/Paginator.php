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
		public array $links = [],
	) {
		$this->total_pages = $size > 0 ? max((int) ceil($total / $size), 1) : 0;
		$this->current_page = (int) request()->input($param, 1);

		if ($this->current_page <= 0) {
			$this->current_page = 1;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		$pages = [];
		$start = max($this->current_page - $this->range, 1);
		$end = min($this->current_page + $this->range, $this->total_pages);

		$parseUrl = function($page) {
			$linkData = $this->links;

			foreach ($linkData as $index => $link) {
				$linkData[$index] = sprintf(trim($link), $page);
			}

			if (!isset($linkData['href'])) {
				$linkData['href'] = request()->fullUrlWithQuery([
					$this->param => $page,
				]);
			}

			return $linkData;
		};

		$backLink = [];
		$forwardLink = [];

		for ($page = $start; $page <= $end; $page++) {
			$pages[] = [
				'number' => $page,
				'active' => $this->current_page == $page,
				'attr' => $parseUrl($page),
			];
		}

		if ($this->current_page > 1) {
			$backLink = $parseUrl(0);
		}

		if ($this->current_page < $this->total_pages) {
			$forwardLink = $parseUrl($this->current_page + 1);
		}

		if (count($pages) == 1) {
			return '';
		}

		return view('joona::components.paginator', [
			'pages' => $pages,
			'back_link' => $backLink,
			'forward_link' => $forwardLink,
		]);
	}
}
