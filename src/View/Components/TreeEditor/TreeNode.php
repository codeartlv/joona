<?php

namespace Codeart\Joona\View\Components\TreeEditor;

readonly class TreeNode
{
	public function __construct(
		public int $id,
		public ?int $parentId,
		public string $title,
		public array $class = [],
		public array $data = [],
	) {

	}
}
