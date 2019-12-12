<?php
/**
 * Add TGM and require ACF-Pro. Buy a license.
 *
 * @package Some_Theme
 */

add_action( 'tgmpa_register', function() {
	tgmpa(
		[
			[
				'name'             => 'Advanced Custom Fields Pro',
				'slug'             => 'advanced-custom-fields-pro',
				'source'           => 'https://github.com/wp-premium/advanced-custom-fields-pro/archive/master.zip',
				'required'         => true,
				'is_callable'      => 'acf',
				'force_activation' => true,
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
require_once get_theme_file_path( 'tgmpa/class-tgm-plugin-activation.php' ); // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
