<?php
/**
 * Template Part: Site Header
 *
 * @package Some_Theme
 */

?><header id="site-header" role="banner">
	<a href="/">Site Header</a>
	<button class="hamburger hamburger--3dx" aria-controls="primary-navigation" aria-expanded="false">
		<span class="hamburger-label"><?php esc_html_e( 'Menu', 'sometheme' ); ?></span>
		<span aria-hidden="true" class="hamburger-box"><span class="toggle-icon hamburger-inner"></span></span>
	</button>
	<nav id="primary-navigation" class="primary-nav" role="navigation">
		<?php if ( has_nav_menu( 'primary' ) ) { ?>
			<?php wp_nav_menu( [ 'theme_location' => 'primary' ] ); ?>
		<?php } ?>
	</nav>
</header>
