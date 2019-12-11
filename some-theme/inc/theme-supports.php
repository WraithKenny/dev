<?php
/**
 * ACF Functions.
 *
 * @package Some_Theme
 */

add_action( 'after_setup_theme', function() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_editor_style( 'css/editor-style.css' );

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
