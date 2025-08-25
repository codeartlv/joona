<?php

namespace Codeart\Joona\View\Components\Uploader;

class ReceivedFiles
{
	/**
	 * @var array<int,ReceivedFile>
	 */
	protected array $data;

	public function __construct($data)
	{
		if (!is_array($data)) {
			$data = [];
		}

		$filteredData = [];

		foreach ($data as $key => $value) {
			$filteredData[] = new ReceivedFile(
				id: (int) ($value['id'] ?? null),
				caption: $value['caption'] ?? null
			);
		}

		$this->data = $filteredData;
	}

	public static function fromPost(string $fieldName): self
	{
		$data = request()->post($fieldName);

		if (!is_array($data)) {
			$data = [];
		}

		return new self($data);
	}

	public function getIds(): array
	{
		return array_map(function($file){
			return $file->id;
		}, $this->data);
	}

	public function files(): array
	{
		return $this->data;
	}

	public function getFirstId(): ?int
	{
		if (empty($this->data)) {
			return null;
		}

		return $this->data[0]->id;
	}

	public function hasFiles(): bool
	{
		return !empty($this->data);
	}
}