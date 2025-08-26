<?php

namespace Codeart\Joona\View\Components\Uploader;

use ArrayAccess;

class ReceivedFile implements ArrayAccess
{
	public function __construct(
		public int $id,
		public ?string $caption
	) {
		
	}

	public function offsetExists($offset): bool {
        return property_exists($this, $offset);
    }

    public function offsetGet($offset): mixed {
        return $this->$offset ?? null;
    }

    public function offsetSet($offset, $value): void {
        $this->$offset = $value;
    }

    public function offsetUnset($offset): void {
        unset($this->$offset);
    }
}