<?php
/**
 * ACF Functions.
 *
 * @package Some_Theme
 */

/**
 * Enqueue scripts and styles.
 */
add_action( 'wp_enqueue_scripts', function() {

	// Don't use wp_get_theme()->get( 'Version' ) because file access is expensive.
	$verion = '1.0.0';

	// Always bust cache when debugging/developing.
	$debug = defined( 'WP_DEBUG' ) && true === WP_DEBUG;

	wp_enqueue_style(
		'sometheme-theme-sass-style',
		get_theme_file_uri( 'css/style.css' ),
		[],
		$debug ? filemtime( get_theme_file_path( 'css/style.css' ) ) : $verion
	);

	wp_enqueue_style(
		'sometheme-theme-font-style',
		'https://fonts.googleapis.com/css?family=Montserrat:600|Open+Sans:300,300i,400,400i,600,600i&display=swap',
		[],
		$verion
	);

	wp_enqueue_script(
		'sometheme-theme-js',
		get_theme_file_uri( 'js/bundle.min.js' ),
		[],
		$debug ? filemtime( get_theme_file_path( 'js/bundle.min.js' ) ) : $verion,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
} );
