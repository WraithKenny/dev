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
		include_once get_theme_file_path( 'css/inline.css' ); // phpcs:disable
		$css = ob_get_contents();
		ob_end_clean();
		wp_register_style( 'sometheme-inline', false );
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

	wp_enqueue_style(
		'sometheme-fonts',
		'https://fonts.googleapis.com/css?family=Montserrat:600|Open+Sans:300,300i,400,400i,600,600i&display=swap',
		[],
		$version
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
 */
add_filter( 'style_loader_tag', function( $tag, $handle, $href, $media ) {
	if ( wp_styles()->get_data( $handle, 'async' ) ) {
		$tag = '<link rel="preload" id="' . $handle . '-css" media="' . $media . '" href="' . $href . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">
		<noscript>' . $tag . '</noscript>';
	}
	return $tag;
}, 10, 4 );

/**
 * From WP's TwentyTwenty Theme.
 *
 * Fix skip link focus in IE11.
 *
 * This does not enqueue the script because it is tiny and because it is only for IE11,
 * thus it does not warrant having an entire dedicated blocking script being loaded.
 *
 * @link https://git.io/vWdr2
 */
add_action( 'wp_print_footer_scripts', function() {
	// The following is minified via `terser --compress --mangle -- assets/js/skip-link-focus-fix.js`. (in TwentyTwenty).
	?>
	<script>/(trident|msie)/i.test(navigator.userAgent)&&document.getElementById&&window.addEventListener&&window.addEventListener("hashchange",function(){var t,e=location.hash.substring(1);/^[A-z0-9_-]+$/.test(e)&&(t=document.getElementById(e))&&(/^(?:a|select|input|button|textarea)$/i.test(t.tagName)||(t.tabIndex=-1),t.focus())},!1);</script>
	<?php
} );

/**
 * Add No-JS Class.
 * If we're missing JavaScript support, the HTML element will have a no-js class.
 */
add_action( 'wp_head', function() {
	?>
	<script>document.documentElement.className = document.documentElement.className.replace('no-js','js');</script>
	<?php
} );

/**
 * Include minified loadCSS. (Polyfil for Firefox.)
 * https://github.com/filamentgroup/loadCSS/blob/v2.1.0/src/cssrelpreload.js
 */
add_action( 'wp_head', function() {
	?>
	<script>/*! loadCSS. [c]2017 Filament Group, Inc. MIT License */!function(t){"use strict";t.loadCSS||(t.loadCSS=function(){});var e=loadCSS.relpreload={};if(e.support=function(){var e;try{e=t.document.createElement("link").relList.supports("preload")}catch(t){e=!1}return function(){return e}}(),e.bindMediaToggle=function(t){var e=t.media||"all";function a(){t.addEventListener?t.removeEventListener("load",a):t.attachEvent&&t.detachEvent("onload",a),t.setAttribute("onload",null),t.media=e}t.addEventListener?t.addEventListener("load",a):t.attachEvent&&t.attachEvent("onload",a),setTimeout(function(){t.rel="stylesheet",t.media="only x"}),setTimeout(a,3e3)},e.poly=function(){if(!e.support())for(var a=t.document.getElementsByTagName("link"),n=0;n<a.length;n++){var o=a[n];"preload"!==o.rel||"style"!==o.getAttribute("as")||o.getAttribute("data-loadcss")||(o.setAttribute("data-loadcss",!0),e.bindMediaToggle(o))}},!e.support()){e.poly();var a=t.setInterval(e.poly,500);t.addEventListener?t.addEventListener("load",function(){e.poly(),t.clearInterval(a)}):t.attachEvent&&t.attachEvent("onload",function(){e.poly(),t.clearInterval(a)})}"undefined"!=typeof exports?exports.loadCSS=loadCSS:t.loadCSS=loadCSS}("undefined"!=typeof global?global:this);</script>
	<?php
}, 11 );

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
