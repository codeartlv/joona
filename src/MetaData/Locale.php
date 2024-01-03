<?php

namespace Codeart\Joona\MetaData;

/**
 * Represents a locale, encapsulating details like caption, code, and mapping for a language.
 *
 * The Locale class is used to store and manage information related to a specific
 * locale (language). It includes functionalities to retrieve URLs for locale-related
 * resources, such as flag images and setup links.
 */
class Locale
{
	/**
	 * Constructs a new Locale instance with the provided details.
	 *
	 * @param string $caption The caption or name of the locale. This is the display name of the locale.
	 * @param string $code The locale's code, typically an ISO language code (e.g., en, fr).
	 * @param ?string $map An optional mapping string for the locale. It's used to link the locale with a specific
	 *                     resource or identifier, falling back to the locale code if not provided.
	 */
	public function __construct(public string $caption, public string $code, public ?string $map = null)
	{
	}

	/**
	 * Retrieves the URL for the locale's flag image.
	 *
	 * This method constructs a URL pointing to the flag image associated with the locale,
	 * using either the 'map' value if provided or the locale code.
	 *
	 * @return string The URL to the flag image.
	 */
	public function getFlagUrl(): string
	{
		$locale_key = $this->map ?? $this->code;
		return '/vendor/joona/images/flags/'.$locale_key.'.svg';
	}

	/**
	 * Generates a URL for setting the application's locale to this locale's code.
	 *
	 * This method constructs a URL that can be used to change the application's
	 * current locale to the one represented by this Locale instance.
	 *
	 * @return string The URL to change the application's locale.
	 */
	public function getSetupUrl(): string
	{
		return route('joona.set-locale', ['locale' => $this->code]);
	}
}
