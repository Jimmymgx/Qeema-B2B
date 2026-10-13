<?php
/**
 * Project archive query + pager markup + AJAX endpoint.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cards + pager for one page of projects.
 *
 * @return array{cards:string,pager:string,count:int,page:int,pages:int}
 */
function qb_archive_query( $cat, $page, $per, $link_text = 'View Project', $opts = array() ) {
	$args = array(
		'post_type'      => 'qb_project',
		'post_status'    => 'publish',
		'posts_per_page' => $per,
		'paged'          => max( 1, (int) $page ),
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	);
	if ( $cat ) {
		$args['tax_query'] = array( array( 'taxonomy' => 'qb_project_cat', 'field' => 'slug', 'terms' => sanitize_title( $cat ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	$q     = new WP_Query( $args );
	$pages = max( 1, (int) $q->max_num_pages );
	$page  = min( max( 1, (int) $page ), $pages );
	$cards = '';
	foreach ( $q->posts as $n => $p ) {
		$cards .= qb_project_card_html( qb_project_card_data( $p ), $link_text, ( $n % 3 ) * 0.1, $n < 3, $opts );
	}
	return array(
		'cards' => $cards,
		'pager' => qb_pager_html( $page, $pages ),
		'count' => count( $q->posts ),
		'page'  => $page,
		'pages' => $pages,
	);
}

/** Pager markup identical to the static design (arrows + a window of 4 numbered buttons). */
function qb_pager_html( $page, $pages ) {
	if ( $pages <= 1 ) {
		return '';
	}
	$window = 4;
	$start  = min( max( $page - 1, 1 ), max( 1, $pages - $window + 1 ) );
	$end    = min( $pages, $start + $window - 1 );
	$arrow_l = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M13 8H3m0 0 4-4M3 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	$arrow_r = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10m0 0-4-4m4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
	$html    = '<nav class="pager" aria-label="' . esc_attr__( 'Pagination', 'qeema-b2b' ) . '">';
	$html   .= '<button type="button" class="pager__arrow" data-page="' . max( 1, $page - 1 ) . '" aria-label="' . esc_attr__( 'Previous page', 'qeema-b2b' ) . '"' . ( $page <= 1 ? ' disabled' : '' ) . '>' . $arrow_l . '</button>';
	$html   .= '<ol class="pager__list">';
	for ( $n = $start; $n <= $end; $n++ ) {
		$html .= '<li><button type="button" data-page="' . $n . '"' . ( $n === $page ? ' aria-current="page"' : '' ) . ' aria-label="' . esc_attr( sprintf( /* translators: %d: page number */ __( 'Page %d', 'qeema-b2b' ), $n ) ) . '">' . $n . '</button></li>';
	}
	if ( $end < $pages ) {
		$html .= '<li><button type="button" data-page="' . ( $end + 1 ) . '" aria-label="' . esc_attr__( 'More pages', 'qeema-b2b' ) . '">…</button></li>';
	}
	$html .= '</ol>';
	$html .= '<button type="button" class="pager__arrow" data-page="' . min( $pages, $page + 1 ) . '" aria-label="' . esc_attr__( 'Next page', 'qeema-b2b' ) . '"' . ( $page >= $pages ? ' disabled' : '' ) . '>' . $arrow_r . '</button>';
	return $html . '</nav>';
}

function qb_ajax_archive() {
	$cat  = isset( $_POST['cat'] ) ? sanitize_text_field( wp_unslash( $_POST['cat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$page = isset( $_POST['page'] ) ? (int) $_POST['page'] : 1; // phpcs:ignore WordPress.Security.NonceVerification
	$per  = isset( $_POST['per'] ) ? min( 48, max( 3, (int) $_POST['per'] ) ) : 12; // phpcs:ignore WordPress.Security.NonceVerification
	$link = isset( $_POST['link'] ) ? sanitize_text_field( wp_unslash( $_POST['link'] ) ) : 'View Project'; // phpcs:ignore WordPress.Security.NonceVerification
	$opts = array(
		'fallback' => isset( $_POST['fallback'] ) ? esc_url_raw( wp_unslash( $_POST['fallback'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification
		'arrow'    => isset( $_POST['arrow'] ) ? esc_url_raw( wp_unslash( $_POST['arrow'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification
	);
	wp_send_json_success( qb_archive_query( $cat, $page, $per, $link, $opts ) );
}
add_action( 'wp_ajax_qb_archive', 'qb_ajax_archive' );
add_action( 'wp_ajax_nopriv_qb_archive', 'qb_ajax_archive' );
