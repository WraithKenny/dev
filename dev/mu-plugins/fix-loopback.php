<?php
// Fix loopback on local development.
add_action( 'init', function() {
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		return;
	}

	// Only run if the site is on a local TLD.
	$host = wp_parse_url( site_url() )['host'];
	$parts = explode( '.', $host );
	$tld = end( $parts );
	if ( ! in_array( $tld, [ 'test', 'example', 'invalid', 'localhost' ], true ) ) {
		return;
	}

	add_filter( 'http_request_args', function( $parsed_args, $url ) {

		$request_host = wp_parse_url( $url )['host'];
		$host         = wp_parse_url( site_url() )['host'];

		// Loopback request.
		if ( $host === $request_host ) {
			$parsed_args['sslverify'] = false;
		}

		return $parsed_args;
	}, 10, 2 );
});
