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
	 * live-reload enjoyable.
	 */

	$debug   = defined( 'WP_DEBUG' ) && true === WP_DEBUG;
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'sometheme-style',
		get_theme_file_uri( 'css/style.css' ),
		[],
		$debug ? filemtime( get_theme_file_path( 'css/style.css' ) ) : $version
	);

	wp_enqueue_style(
		'sometheme-fonts',
		'https://fonts.googleapis.com/css?family=Montserrat:600|Open+Sans:300,300i,400,400i,600,600i&display=swap',
		[],
		$version
	);

	wp_enqueue_script(
		'sometheme-js',
		get_theme_file_uri( 'js/bundle.min.js' ),
		[],
		$debug ? filemtime( get_theme_file_path( 'js/bundle.min.js' ) ) : $version,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
} );

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
 * Find Scrollbar width, necessary for sensible, perfect, Wide and Full Alignments.
 *
 * Code learned from https://www.filamentgroup.com/lab/scrollbars/
 *
 * Notes: This is a progressive enhancement. No-JS and IE 11 will get
 * a "close enough" version.
 *
 * The "unobtrusive" version will resolve to 0 and cancel out. The versions
 * which have width will be accounted for via CSS Custom Variables, adjusting
 * the CSS Calculations of `100vw` and related rules, progressively.
 *
 * Inlined for speed and non-blocking.
 */
add_action( 'wp_body_open', function() {
	?>
	<script>
	(function(){
		var parent = document.createElement('div');
		parent.setAttribute('style', 'width:30px;height:30px;overscroll-behavior:contain;overflow-y:auto;-webkit-overflow-scrolling:touch;-ms-overflow-style:-ms-autohiding-scrollbar;position:absolute');
		var child = document.createElement('div');
		child.setAttribute('style', 'width:100%;height:40px');
		parent.appendChild(child);
		document.body.appendChild(parent);
		var scrollbarWidth = 30 - parent.firstChild.clientWidth;
		if ( scrollbarWidth ) {
			document.documentElement.style.setProperty('--scrollbar-width', scrollbarWidth + 'px');
		}
		document.body.removeChild(parent);
	}());
	</script>
	<?php
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
