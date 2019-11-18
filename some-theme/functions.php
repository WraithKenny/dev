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

require_once get_theme_file_path( 'tgmpa/class-tgm-plugin-activation.php' );
add_action( 'tgmpa_register', function() {
	tgmpa(
		[
			[
				'name'             => 'TGM Example Plugin',
				'slug'             => 'tgm-example-plugin',
				'source'           => get_template_directory() . '/tgmpa/plugins/tgm-example-plugin.zip',
				'required'         => true,
				'version'          => '1.0.1',
				'force_activation' => true,
			],
			[
				'name'     => 'Advanced Custom Fields Pro',
				'slug'     => 'advanced-custom-fields-pro',
				'source'   => 'https://github.com/wp-premium/advanced-custom-fields-pro/archive/master.zip',
				'required' => true,
			],
			[
				'name'     => 'Scripts n Styles',
				'slug'     => 'scripts-n-styles',
				'required' => false,
			],
			[
				'name'        => 'WordPress SEO by Yoast',
				'slug'        => 'wordpress-seo',
				'is_callable' => 'wpseo_init', // Checks for Plain or Premium.
			],
			[
				'name'     => 'Admin Cols',
				'slug'     => 'codepress-admin-columns',
				'required' => false,
			],
		],
		[
			'id'           => 'sometheme',             // Unique ID for hashing notices for multiple instances of TGMPA.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
		]
	);
} );
