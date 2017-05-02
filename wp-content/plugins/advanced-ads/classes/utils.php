<?php
class Advanced_Ads_Utils {
	/**
	* Merges multiple arrays, recursively, and returns the merged array.
	*
	* This function is similar to PHP's array_merge_recursive() function, but it
	* handles non-array values differently. When merging values that are not both
	* arrays, the latter value replaces the former rather than merging with it.
	*
	* Example:
	* $link_options_1 = array( 'fragment' => 'x', 'class' => array( 'a', 'b' ) );
	* $link_options_2 = array( 'fragment' => 'y', 'class' => array( 'c', 'd' ) );
	* // This results in array( 'fragment' => 'y', 'class' => array( 'a', 'b', 'c', 'd' ) ).
	*
	* @param array $arrays An arrays of arrays to merge.
	* @param bool $preserve_integer_keys (optional) If given, integer keys will be preserved and merged instead of appended.
	* @return array The merged array.
	* @copyright Copyright 2001 - 2013 Drupal contributors. License: GPL-2.0+. Drupal is a registered trademark of Dries Buytaert.
	*/
	public static function merge_deep_array( array $arrays, $preserve_integer_keys = FALSE ) {
		$result = array();
		foreach ( $arrays as $array ) {
			if ( ! is_array( $array ) ) { continue; }

			foreach ( $array as $key => $value ) {
				// Renumber integer keys as array_merge_recursive() does unless
				// $preserve_integer_keys is set to TRUE. Note that PHP automatically
				// converts array keys that are integer strings (e.g., '1') to integers.
				if ( is_integer( $key ) && ! $preserve_integer_keys ) {
					$result[] = $value;
				}
				// Recurse when both values are arrays.
				elseif ( isset( $result[ $key ] ) && is_array( $result[ $key ] ) && is_array( $value ) ) {
					$result[ $key ] = self::merge_deep_array( array( $result[ $key ], $value ), $preserve_integer_keys );
				}
				// Otherwise, use the latter value, overriding any previous value.
				else {
					$result[ $key ] = $value;
				}
			}
		}
		return $result;
	}
}
?>