<?php
/**
 * Disable the update check for this theme.
 *
 * @package Some_Theme
 */

add_filter( 'http_request_args', function( $r, $url ) {
	if ( 0 !== strpos( $url, 'https://api.wordpress.org/themes/update-check' ) ) {
		return $r; // Not a theme update request. Bail immediately.
	}

	$themes = json_decode( $r['body']['themes'], true );
	unset( $themes['themes'][ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = wp_json_encode( $themes );

	return $r;
}, 5, 2 );
