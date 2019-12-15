<?php
/**
 * Template Part: Site Main
 *
 * @package Some_Theme
 */

?>
<main id="site-content" role="main" class="container">
<?php if ( have_posts() ) { ?>
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php get_template_part( 'template-parts/entry/content', get_post_type() ); ?>
	<?php endwhile; ?>
<?php } else { ?>
	Nothin'.
<?php } ?>
	<?php get_template_part( 'template-parts/pagination' ); ?>
</main>
