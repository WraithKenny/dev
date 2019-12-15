<?php
/**
 * Template Part: Entry Content
 *
 * @package Some_Theme
 */

?>
<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
	<?php
	if ( is_singular() ) {
		the_title( '<h1 class="entry-title">', '</h1>' );
	} else {
		the_title( '<h2 class="entry-title h1"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
	}
	?>
	<?php the_content(); ?>
</article>
