<?php

namespace PhilipNewcomer\PersistentTransients;
use PhilipNewcomer\PersistentTransients\Helpers as Helpers;

require_once( __DIR__ . '/includes/helpers.php' );
require_once( __DIR__ . '/includes/garbage-collection.php' );

/**
 * Deletes a persistent transient value.
 *
 * @param string $transient The transient name.
 *
 * @return bool Whether the deletion was successful.
 */
function delete( $transient = '' ) {

	if ( empty( $transient ) ) {
		return false;
	}

	$option_name            = Helpers\get_transient_option_name( $transient );
	$option_expiration_name = Helpers\get_transient_expiration_option_name( $transient );

	$delete_option_status            = delete_option( $option_name );
	$delete_option_expiration_status = delete_option( $option_expiration_name );

	if ( false === $delete_option_status || false === $delete_option_expiration_status ) {
		return false;
	}

	return true;
}

/**
 * Returns a persistent transient value.
 *
 * @param string $transient The transient name.
 *
 * @return mixed The transient value.
 */
function get( $transient = '' ) {

	if ( empty( $transient ) ) {
		return false;
	}

	if ( Helpers\is_transient_expired( $transient ) ) {
		return false;
	}

	$option_name = Helpers\get_transient_option_name( $transient );

	return get_option( $option_name );
}

/**
 * Saves a persistent transient.
 *
 * @param string $transient  The transient name.
 * @param string $value      The data to save.
 * @param string $expiration The time the transient should be valid for, in seconds.
 *
 * @return bool Whether the value was successfully saved.
 */
function set( $transient = '', $value = '', $expiration = '' ) {

	if ( empty( $transient ) || empty( $value ) || empty( $expiration ) ) {
		return false;
	}

	$expiration_timestamp = current_time( 'timestamp' ) + intval( $expiration );

	$option_name            = Helpers\get_transient_option_name( $transient );
	$option_expiration_name = Helpers\get_transient_expiration_option_name( $transient );

	// If the resulting option name is too long, there's no use we try to save it.
	if ( strlen( $option_expiration_name ) > Helpers\get_option_name_max_length() ) {
		return false;
	}

	if ( false === update_option( $option_name, $value, false ) ) {
		return false;
	}

	if ( false === update_option( $option_expiration_name, $expiration_timestamp, false ) ) {
		return false;
	}

	return true;
}
