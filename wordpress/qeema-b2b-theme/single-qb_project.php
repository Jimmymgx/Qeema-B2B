<?php
/**
 * A single project: everything is the project's own Elementor content.
 *
 * @package QeemaB2B
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
while ( have_posts() ) {
	the_post();
	$qb_html = get_the_content();
	if ( '' === trim( wp_strip_all_tags( (string) $qb_html ) ) && ! get_post_meta( get_the_ID(), '_elementor_data', true ) && current_user_can( 'edit_post', get_the_ID() ) ) {
		echo '<section class="page-head" style="min-height:40vh"><p class="lead">' . sprintf(
			/* translators: %s: link to edit the project */
			esc_html__( 'This project has no layout yet. %s', 'qeema-b2b' ),
			'<a href="' . esc_url( get_edit_post_link() ) . '">' . esc_html__( 'Edit it', 'qeema-b2b' ) . '</a>'
		) . '</p></section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	the_content();
}
get_footer();
