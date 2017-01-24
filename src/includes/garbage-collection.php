<?php

namespace PhilipNewcomer\PersistentTransients\Garbage_Collection;
use PhilipNewcomer\PersistentTransients as PersistentTransients;
use PhilipNewcomer\PersistentTransients\Helpers as Helpers;

/**
 * Schedules the garbage collection event, if it is not already scheduled.
 */
function maybe_schedule_event() {
	$hook = Helpers\get_garbage_collection_hook_name();

	if ( ! wp_next_scheduled( $hook ) ) {
		wp_schedule_event( time(), 'daily', $hook );
	}
}
add_action( 'init', __NAMESPACE__ . '\maybe_schedule_event' );

/**
 * Garbage collection function.
 */
function collect_garbage() {
	global $wpdb;

	$option_prefix = Helpers\get_option_prefix();
	$query         = sprintf( "SELECT option_name from {$wpdb->options} WHERE `option_name` LIKE '%s%%'", $option_prefix );

	$option_names  = $wpdb->get_col( $query );

	if ( empty( $option_names ) ) {
		return;
	}

	foreach ( $option_names as $option_name ) {

		if ( preg_match( '/_expiration$/', $option_name ) ) {
			continue;
		}

		$transient_name = preg_replace( "/^$option_prefix/", '', $option_name );

		if ( Helpers\is_transient_expired( $transient_name ) ) {
			PersistentTransients\delete( $transient_name );
		}
	}
}
add_action( Helpers\get_garbage_collection_hook_name(), __NAMESPACE__ . '\collect_garbage' );
