<?php

namespace Codeart\Joona\Helpers;

class HtmlHelper
{
	/**
	 * Map attribute array to HTML attributes
	 *
	 * @param array $attributes
	 * @return string
	 */
    public static function attributes(array $attributes)
    {
        return implode(' ', array_map(function ($key) use ($attributes) {
            if(is_bool($attributes[$key])) {
                return $attributes[$key] ? $key : '';
            }
            return $key.'="'.htmlspecialchars($attributes[$key], ENT_QUOTES, 'UTF-8').'"';
        }, array_keys($attributes)));
    }
}
