<?php
/**
 * The Functions file.
 *
 * @package Some_Theme
 */

if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page(array(
		'page_title' => 'Theme Settings',
		'menu_title' => 'Theme Settings',
		'menu_slug'  => 'theme-general-settings',
		'capability' => 'edit_posts',
		'redirect'   => true,
	));
}

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

/**
 * Enqueue scripts and styles.
 */
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'sometheme-theme-sass-style', get_theme_file_uri( 'css/style.css' ), [], filemtime( get_theme_file_path( 'css/style.css' ) ) );
	wp_enqueue_style( 'sometheme-theme-font-style', 'https://fonts.googleapis.com/css?family=Lato:300,400,400i,700,900', [], '20190114' );

	wp_enqueue_script( 'sometheme-theme-js', get_theme_file_uri( 'js/bundle.min.js' ), [], filemtime( get_theme_file_path( 'js/bundle.min.js' ) ), true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
} );

add_action( 'after_setup_theme', function() {
	add_editor_style( 'css/editor-style.css' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );

	register_nav_menus( [
		'primary' => __( 'Primary', 'sometheme' ),
	] );

	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	] );

	add_editor_style();

	add_theme_support( 'responsive-embeds' );
} );
