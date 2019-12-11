<?php
/**
 * The default template
 *
 * @package Some_Theme
 */

get_header(); ?>

<?php if ( have_posts() ) { ?>
	<div class="container">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<?php
			if ( is_singular() ) {
				the_title( '<h1 class="entry-title">', '</h1>' );
			} else {
				the_title( '<h2 class="entry-title heading-size-1"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
			}
			?>
			<?php the_content(); ?>
		</article>
	<?php endwhile; ?>
	</div>
<?php } else { ?>
	Nothin'.
<?php } ?>

<?php
get_footer();
