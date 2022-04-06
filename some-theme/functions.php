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
add_theme_support( 'admin-bar', [
	'callback' => function() {
		echo '<style id="admin-bar-style">#wpadminbar {opacity:0.25;transform:translateY(-50%);transition:opacity 0.3s, transform 0.3s;}#wpadminbar:hover {opacity:1;transform:translateY(0%);}</style>';
	},
]);

require_once get_theme_file_path( 'inc/update-check.php' );
require_once get_theme_file_path( 'inc/acf.php' );
require_once get_theme_file_path( 'inc/theme-supports.php' );
require_once get_theme_file_path( 'inc/alignfull.php' );
require_once get_theme_file_path( 'inc/scripts-styles.php' );
