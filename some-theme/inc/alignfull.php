<?php
/**
 * Find Scrollbar width, necessary for sensible, perfect, Wide and Full Alignments.
 *
 * @package Some_Theme
 */

/**
 * Learned from code at
 * https://www.filamentgroup.com/lab/scrollbars/
 * and
 * https://davidwalsh.name/detect-scrollbar-width
 *
 * Notes: This is a progressive enhancement. No-JS and IE 11 will get
 * a "close enough" version.
 *
 * Supports "unobtrusive" auto-hiding scrollbars: the width will resolve to 0
 * and cancel out. The versions which have scrollbars with width will be accounted
 * for via CSS Custom Variables, adjusting the CSS Calculations of `100vw` and
 * related rules, progressively.
 *
 * Tested on latest Chrome (PC/Mac), Firefox (PC/Mac), Safari, Edge, and IE 11.
 *
 * Inlined for speed and non-blocking.
 *
 * See https://github.com/WordPress/gutenberg/issues/8289#issuecomment-565671066
 * for history.
 *
 * Dont' forget to add `add_theme_support( 'align-wide' )`.
 */
add_action( 'wp_body_open', function() {

	// Uncompressed verions.
	if ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) {
		?>
		<style>
		.alignwide {
			margin-left: calc((100% - 100vw + 17px) / 4);
			margin-right: calc((100% - 100vw + 17px) / 4);
			width: auto;
			max-width: none;
		}

		.alignfull {
			margin-left: calc((100% - 100vw + 17px) / 2);
			margin-right: calc((100% - 100vw + 17px) / 2);
			width: auto;
			max-width: none;
		}

		:root {
			--scrollbar-width: 17px;
			--white-space: (100% - 100vw + var(--scrollbar-width));
		}

		@supports (--scrollbar-width: 17px) {

			.alignwide {
				margin-left: calc(var(--white-space) / 4);
				margin-right: calc(var(--white-space) / 4);
			}

			.alignfull {
				margin-left: calc(var(--white-space) / 2);
				margin-right: calc(var(--white-space) / 2);
			}
		}
		</style>

		<script>
		( function() {
			var d = document,
				b = d.body,
				e = d.createElement( 'div' );
			e.setAttribute( 'style', 'width: 30px; overflow: scroll; position: absolute' );
			b.appendChild( e );
			d.documentElement.style.setProperty( '--scrollbar-width', e.offsetWidth - e.clientWidth + 'px' );
			b.removeChild( e );
		}() );
		</script>
		<?php
	} else {
		// Whitespace removed by hand.
		// phpcs:ignore Squiz.PHP.EmbeddedPhp.ContentAfterEnd,Squiz.PHP.EmbeddedPhp.ContentBeforeOpen
		?><style>.alignwide{margin-left:calc((100% - 100vw + 17px)/4);margin-right:calc((100% - 100vw + 17px)/4);width:auto;max-width:none}.alignfull{margin-left:calc((100% - 100vw + 17px)/2);margin-right:calc((100% - 100vw + 17px)/2);width:auto;max-width:none}:root{--scrollbar-width: 17px;--white-space:(100% - 100vw + var(--scrollbar-width))}@supports (--scrollbar-width:17px) {.alignwide{margin-left:calc(var(--white-space)/4);margin-right:calc(var(--white-space)/4)}.alignfull{margin-left:calc(var(--white-space)/2);margin-right:calc(var(--white-space)/2)}}</style><script>(function(){var d=document,b=d.body,e=d.createElement('div');e.setAttribute('style','width:30px;overflow:scroll;position:absolute');b.appendChild(e);d.documentElement.style.setProperty('--scrollbar-width',e.offsetWidth-e.clientWidth+'px');b.removeChild(e)}())</script><?php
	}
} );
