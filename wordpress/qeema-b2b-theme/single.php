<?php
/**
 * Fallback template (pages, posts, archives).
 *
 * @package QeemaB2B
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	}
} else {
	echo '<section class="page-head"><h1 class="title">' . esc_html__( 'Nothing found', 'qeema-b2b' ) . '</h1></section>';
}
get_footer();
