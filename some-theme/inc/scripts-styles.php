<?php
/**
 * Scripts and Styles enqueue Functions.
 *
 * @package Some_Theme
 */

/**
 * Enqueue main scripts and styles.
 */
add_action( 'wp_enqueue_scripts', function() {

	/**
	 * Note: wp_get_theme()->get( 'Version' ) has file read per
	 * page load, but object caching and page caching might help.
	 *
	 * The `filemtime( get_theme_file_path( '.../file' ) )` ensures
	 * cache busting when in development via WP_DEBUG. Makes
	 * live-reload more enjoyable.
	 */

	$debug   = defined( 'WP_DEBUG' ) && true === WP_DEBUG;
	$version = wp_get_theme()->get( 'Version' );

	if ( $debug ) {
		// With WP_Debug, use linked style instead of inlined.
		wp_enqueue_style(
			'sometheme-inline',
			get_theme_file_uri( 'css/inline.css' ),
			[],
			filemtime( get_theme_file_path( 'css/inline.css' ) )
		);
	} else {
		// Print the "Above the Fold" mobile CSS in the `head`.
		ob_start();
		include_once get_theme_file_path( 'css/inline.css' ); // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		$css = ob_get_contents();
		ob_end_clean();
		wp_register_style( 'sometheme-inline', false ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_style( 'sometheme-inline' );
		wp_add_inline_style( 'sometheme-inline', $css );
	}

	wp_enqueue_style(
		'sometheme-style',
		get_theme_file_uri( 'css/style.css' ),
		[],
		$debug ? filemtime( get_theme_file_path( 'css/style.css' ) ) : $version
	);
	wp_style_add_data( 'sometheme-style', 'async', true );

	// Needs null version, if more than 1 family is used.
	wp_enqueue_style( // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		'sometheme-fonts',
		'https://fonts.googleapis.com/css?family=Montserrat:600|Open+Sans:300,300i,400,400i,600,600i&display=swap',
		[],
		null
	);
	wp_style_add_data( 'sometheme-fonts', 'async', true );

	// Defer core and plugin css also.
	wp_style_add_data( 'wp-block-library', 'async', true );
	wp_style_add_data( 'gforms_reset_css', 'async', true );
	wp_style_add_data( 'gforms_formsmain_css', 'async', true );
	wp_style_add_data( 'gforms_ready_class_css', 'async', true );
	wp_style_add_data( 'gforms_browsers_css', 'async', true );

	wp_enqueue_script(
		'sometheme-js',
		get_theme_file_uri( 'js/bundle.min.js' ),
		[ 'jquery' ],
		$debug ? filemtime( get_theme_file_path( 'js/bundle.min.js' ) ) : $version,
		true
	);
	wp_script_add_data( 'sometheme-js', 'async', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
		wp_script_add_data( 'comment-reply', 'async', true );
	}
} );

/**
 * Defer, or Async, using `wp_script_add_data( 'handle', 'async', true );`.
 * Derived from Twentytwenty theme.
 */
add_filter( 'script_loader_tag', function( $tag, $handle ) {
	foreach ( [ 'async', 'defer' ] as $attr ) {
		if ( ! wp_scripts()->get_data( $handle, $attr ) ) {
			continue;
		}
		// Prevent adding attribute when already added in #12009.
		if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
			$tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
		}
		// Only allow async or defer, not both.
		break;
	}
	return $tag;
}, 10, 2 );

/**
 * "Defer" CSS using `wp_style_add_data( 'handle', 'async', true );`.
 * See: https://web.dev/defer-non-critical-css/#optimize
 * See: https://www.filamentgroup.com/lab/load-css-simpler/
 */
add_filter( 'style_loader_tag', function( $tag, $handle, $href, $media ) {
	if ( wp_styles()->get_data( $handle, 'async' ) ) {
		// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
		$tag = '<link rel="stylesheet" id="' . $handle . '-css" media="print" href="' . $href . '" onload="this.media=\'' . $media . '\';this.onload=null;">
		<noscript>' . $tag . '</noscript>';
	}
	return $tag;
}, 10, 4 );

/**
 * Add No-JS Class.
 * If we're missing JavaScript support, the Body element will have a no-js class.
 */
add_action( 'wp_body_open', function() {
	?>
	<script>document.body.classList.remove("no-js");</script>
	<?php
} );
add_filter( 'body_class', function( $classes ) {
	$classes[] = 'no-js';
	return $classes;
} );

/**
 * Support for `hamburgers` JavaScript portion.
 *
 * This might not belong inlined.
 */
add_action( 'wp_footer', function() {
	?>
	<script>
	document.querySelector( 'button.hamburger' ).addEventListener( 'click', function() {
		if ( this.classList.contains( 'is-active' ) ) {
			this.setAttribute( 'aria-expanded', 'false' );
			this.classList.remove( 'is-active' );
		} else {
			this.classList.add( 'is-active' );
			this.setAttribute( 'aria-expanded', 'true' );
		}
	});
	</script>
	<?php
} );
