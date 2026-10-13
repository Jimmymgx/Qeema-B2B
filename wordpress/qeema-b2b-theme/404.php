<?php
/**
 * 404.
 *
 * @package QeemaB2B
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$qb_nf = (int) get_option( 'qb_404_page' );
if ( $qb_nf && get_post( $qb_nf ) && class_exists( '\Elementor\Plugin' ) ) {
	// Elementor-built "Page not found" (edit it under Pages; seeded by Tools → Qeema B2B setup).
	echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $qb_nf, true ); // phpcs:ignore WordPress.Security.EscapeOutput
} else {
	?>
<section class="page-head" style="min-height:60vh">
	<h1 class="title"><?php esc_html_e( 'Page not found', 'qeema-b2b' ); ?></h1>
	<p class="lead"><?php esc_html_e( 'The page you are looking for does not exist.', 'qeema-b2b' ); ?></p>
	<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'qeema-b2b' ); ?></a>
</section>
	<?php
}
get_footer();
