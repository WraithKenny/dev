<?php
/**
 * ACF Functions.
 *
 * @package Some_Theme
 */

add_action( 'acf/init', function() {

	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page([
		'page_title' => 'Theme Settings',
		'menu_title' => 'Theme Settings',
		'menu_slug'  => 'theme-general-settings',
		'capability' => 'edit_theme_options',
		'icon_url'   => 'dashicons-layout',
		'autoload'   => true,
		'position'   => '2.145',
	]);
});
