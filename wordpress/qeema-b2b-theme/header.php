<?php
/**
 * Document head + site header (an Elementor "Site Part").
 *
 * @package QeemaB2B
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php qb_render_part( 'header' ); ?>
<main id="content" class="<?php echo esc_attr( qb_main_class() ); ?>">
