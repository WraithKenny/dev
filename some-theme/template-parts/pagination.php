<?php
/**
 * Template Part: Pagination
 *
 * @package Some_Theme
 */

if ( $GLOBALS['wp_query']->max_num_pages <= 1 ) {
	return;
}
$prev_text = esc_html__( 'Newer', 'sometheme' );
$next_text = esc_html__( 'Older', 'sometheme' );

$links = '';
foreach ( paginate_links( [
	'type'      => 'array',
	'mid_size'  => 1,
	'prev_text' => $prev_text,
	'next_text' => $next_text,
] ) as $page_link ) {
	if ( strpos( $page_link, 'page-numbers current' ) ) {
		$links .= '<li class="page-item active">' . $page_link . '</li>';
	} elseif ( strpos( $page_link, 'page-numbers dots' ) ) {
		$links .= '<li class="page-item disabled">' . $page_link . '</li>';
	} else {
		$links .= '<li class="page-item">' . $page_link . '</li>';
	}
}

if ( strpos( $links, 'prev page-numbers' ) === false ) {
	$links = '<li class="page-item disabled"><span class="page-link">' . $prev_text . '</span></li>' . $links;
}
if ( strpos( $links, 'next page-numbers' ) === false ) {
	$links .= '<li class="page-item disabled"><span class="page-link">' . $next_text . '</span></li>';
}

$links = str_replace( 'page-numbers', 'page-link', $links );
?>
<nav class="navigation" role="navigation" aria-label="<?php esc_html_e( 'Posts', 'sometheme' ); ?>">
	<ul class="pagination justify-content-center">
		<?php echo $links; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</ul>
</nav>
