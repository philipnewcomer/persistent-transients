<?php

namespace PhilipNewcomer\PersistentTransients\Helpers;

/**
 * Returns the hook that garbage collection should be run on.
 *
 * @return string
 */
function get_garbage_collection_hook_name() {
	return 'persistent_transients_garbage_collection';
}

/**
 * Returns the maximum length an option name can be.
 *
 * @link https://core.trac.wordpress.org/changeset/34030
 *
 * @return int The maximum length.
 */
function get_option_name_max_length() {
	return 191;
}

/**
 * Returns the persistent transient option prefix.
 *
 * @return string
 */
function get_option_prefix() {
	return '_persistent_transient_';
}

/**
 * Returns the option name for the specified transient.
 *
 * @param string $transient The transient name.
 *
 * @return string The option name.
 */
function get_transient_option_name( $transient = '' ) {
	return get_option_prefix() . $transient;
}

/**
 * Returns the expiration option name for the specified transient.
 *
 * @param string $transient The transient name.
 *
 * @return string The option name.
 */
function get_transient_expiration_option_name( $transient = '' ) {
	return sprintf( '%s_expiration',
		get_transient_option_name( $transient )
	);
}

/**
 * Determines whether the specified transient is expired.
 *
 * @param string $transient
 *
 * @return bool
 */
function is_transient_expired( $transient = '' ) {

	if ( empty( $transient ) ) {
		return true;
	}

	$option_expiration_name = get_transient_expiration_option_name( $transient );
	$expiration_timestamp   = get_option( $option_expiration_name );

	if ( false === $expiration_timestamp ) {
		return true;
	}

	if ( current_time( 'timestamp' ) > intval( $expiration_timestamp ) ) {
		return true;
	}

	return false;
}
