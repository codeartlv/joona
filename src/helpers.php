<?php

/**
 * Parses pssword policy string
 *
 * @param string $policy
 * @return array
 */
function parse_password_policy(string $policy): array
{
	$rules = explode(',', $policy);
	$mapped = [];

	foreach ($rules as $rule) {
		$parts = explode(':', $rule);
		$rule_name = $parts[0];
		$rule_value = $parts[1] ?? true;

		$mapped[$rule_name] = $rule_value;
	}

	return $mapped;
}
