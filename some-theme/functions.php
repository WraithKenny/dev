<?php
/**
 * The Functions file.
 *
 * @package Some_Theme
 */

/**
 * Makes Admin Bar less intrusive on CSS techniques.
 *
 * Notes: It removes default styling, i.e., the "bump" which
 * pushes the page with margins, interfering with some CSS
 * techniques that require clean height calculations.
 *
 * The result is an admin bar that doesn't take space but moves
 * itself out of the way visually.
 */
// phpcs:disable
add_theme_support( 'admin-bar', [ 'callback' => function() {
	?><style id="admin-bar-style">
	#wpadminbar {
		opacity: 0.25;
		transform: translateY(-50%);
		transition: opacity 0.3s, transform 0.3s;
	}
	#wpadminbar:hover {
		opacity: 1;
		transform: translateY(0%);
	}
	</style><?php
} ] );
// phpcs:enable

require_once get_theme_file_path( 'inc/update-check.php' );
require_once get_theme_file_path( 'inc/acf.php' );
require_once get_theme_file_path( 'inc/theme-supports.php' );
require_once get_theme_file_path( 'inc/alignfull.php' );
require_once get_theme_file_path( 'inc/scripts-styles.php' );
require_once get_theme_file_path( 'inc/plugins.php' );

// Fix loopback on local development.
add_action( 'init', function() {
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		return;
	}

	// Only run if the site is on a local TLD.
	$tld = end( explode( '.', wp_parse_url( site_url() )['host'] ) );
	if ( ! in_array( $tld, [ 'test', 'example', 'invalid', 'localhost' ], true ) ) {
		return;
	}

	add_filter( 'http_request_args', function( $parsed_args, $url ) {

		$request_domain = wp_parse_url( $url )['host'];
		$site_domain    = wp_parse_url( get_site_url() )['host'];

		// Loopback request.
		if ( $site_domain === $request_domain ) {
			$parsed_args['sslverify'] = false;
		}

		return $parsed_args;
	}, 10, 2 );
});
