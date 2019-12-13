<?php
/**
 * Theme Supports.
 *
 * @package Some_Theme
 */

add_action( 'after_setup_theme', function() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 960;
	}

	set_post_thumbnail_size( 1200, 9999 );

	register_nav_menus( [
		'primary' => __( 'Primary', 'sometheme' ),
	] );

	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'script',
		'style',
	] );

	add_editor_style( 'css/editor-style.css' );
	add_editor_style();

} );

/**
 * Add basic metatags, first.
 */
add_action( 'wp_head', function() {
	?>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
}, -99 );

/**
 * Add Preconnect tags, early.
 */
add_action( 'wp_head', function() {
	?>
	<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
	<link rel="preconnect" href="https://fonts.googleapis.com/" crossorigin>
	<?php
}, 0 );

/**
 * Add "Skip to content" accessibility functionality, early.
 */
add_action( 'wp_body_open', function() {
	echo '<a class="skip-link screen-reader-text focus-visible" href="#site-content">' . esc_html__( 'Skip to the content', 'sometheme' ) . '</a>';
}, 5 );
