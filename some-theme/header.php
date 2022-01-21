<?php
/**
 * The Header
 *
 * @package Some_Theme
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/site/header' ); ?>
