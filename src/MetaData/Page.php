<?php

namespace Codeart\Joona\MetaData;

/**
 * Represents a navigation item or page in a user interface.
 *
 * The Page class provides a fluent interface to define properties of a page or navigation item,
 * such as its caption, icon, and route. The class is designed for ease of use, allowing properties
 * to be set via method chaining. This class is particularly useful in building dynamic navigation
 * menus or page lists where each item may have associated text, icons, and navigation routes.
 *
 * Properties:
 * - $caption: The text label or caption associated with the page.
 * - $icon: The icon associated with the page, which could be a class name, a path to an image, or any other identifier.
 * - $route: The navigation route or URL associated with the page, defining where the user should be directed when interacting with this page.
 *
 */
class Page
{
	public ?string $caption = null;
	public ?string $icon = null;
	public ?string $route = null;
	public ?int $badge = 0;
	public array $activeRoutes = [];

	/**
	 * Constructs a new Page instance with a unique identifier.
	 *
	 * @param string $id The unique identifier for the page.
	 */
	private function __construct(public string $id)
	{
		$this->id = $id;
	}

	/**
	 * Sets the caption for the page.
	 *
	 * @param ?string $caption The caption for the page.
	 * @return self Returns the instance of this Page for method chaining.
	 */
	public function caption(?string $caption): self
	{
		$this->caption = $caption;
		return $this;
	}

	/**
	 * Sets the icon for the page.
	 *
	 * @param ?string $icon The icon for the page.
	 * @return self Returns the instance of this Page for method chaining.
	 */
	public function icon(?string $icon): self
	{
		$this->icon = $icon;
		return $this;
	}

	/**
	 * Sets the badge for the page.
	 *
	 * @param int $badge Badge number.
	 * @return self Returns the instance of this Page for method chaining.
	 */
	public function badge(int $badge): self
	{
		$this->badge = $badge;
		return $this;
	}

	/**
	 * Sets the route for the page.
	 *
	 * @param ?string $route The route for the page.
	 * @return self Returns the instance of this Page for method chaining.
	 */
	public function route(?string $route): self
	{
		$this->route = $route;
		return $this;
	}

	 /**
	 * Factory method to create a new Page instance.
	 *
	 * @param string $id The unique identifier for the new page.
	 * @return self Returns a new instance of Page.
	 */
	public static function make(string $id)
	{
		return new self($id);
	}

	/**
	 * Add additional routes on which page is considered selected in navigation
	 *
	 * @param array $routes
	 * @return self Returns the instance of this Page for method chaining.
	 */
	public function activeOn(array $routes = []): self
	{
		$this->activeRoutes = $routes;

		return $this;
	}
}
