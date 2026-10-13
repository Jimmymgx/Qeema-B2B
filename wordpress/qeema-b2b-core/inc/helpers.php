<?php
/**
 * Small shared helpers (no Elementor dependency).
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** URL of a bundled asset, e.g. qb_asset( 'img/card.webp' ). */
function qb_asset( $path ) {
	return QB_URL . 'assets/' . ltrim( $path, '/' );
}

/** Default value for an Elementor MEDIA control pointing at a bundled image. */
function qb_media_default( $path ) {
	return array( 'url' => qb_asset( $path ) );
}

/**
 * Is $url the page being viewed (or, for section roots like /services/, an ancestor of it)?
 */
function qb_is_current_url( $url ) {
	if ( ! $url || '#' === $url[0] ) {
		return false;
	}
	$target  = trailingslashit( wp_parse_url( $url, PHP_URL_PATH ) ?: '/' );
	$current = trailingslashit( wp_parse_url( home_url( add_query_arg( array() ) ), PHP_URL_PATH ) ?: '/' );
	$home    = trailingslashit( wp_parse_url( home_url( '/' ), PHP_URL_PATH ) ?: '/' );
	if ( $target === $home ) {
		return $current === $home;
	}
	return 0 === strpos( $current, $target );
}

/** Projects shown in cards: returns normalised arrays so widgets stay simple. */
function qb_get_projects( $args = array() ) {
	$defaults = array(
		'post_type'      => 'qb_project',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	);
	$q     = new WP_Query( wp_parse_args( $args, $defaults ) );
	$items = array();
	foreach ( $q->posts as $p ) {
		$items[] = qb_project_card_data( $p );
	}
	return $items;
}

/** Data used to draw a project card. */
function qb_project_card_data( $post ) {
	$post   = get_post( $post );
	$terms  = get_the_terms( $post, 'qb_project_cat' );
	$slugs  = ( $terms && ! is_wp_error( $terms ) ) ? wp_list_pluck( $terms, 'slug' ) : array();
	$img_id = get_post_thumbnail_id( $post );
	$text   = has_excerpt( $post ) ? get_the_excerpt( $post ) : '';
	return array(
		'id'       => $post->ID,
		'title'    => get_the_title( $post ),
		'text'     => $text,
		'url'      => get_permalink( $post ),
		'cats'     => $slugs,
		'image_id' => $img_id,
		'image'    => $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : qb_asset( 'img/project-screenshot.webp' ),
	);
}

/** HTML for one project card (same markup as the static design). */
function qb_project_card_html( $p, $link_text = 'View Project', $delay = 0, $eager = false, $opts = array() ) {
	$d     = $delay ? ' style="--d:' . esc_attr( $delay ) . 's"' : '';
	$arrow = ! empty( $opts['arrow'] ) ? $opts['arrow'] : qb_asset( 'svg/arrow-right.svg' );
	$fb    = ! empty( $opts['fallback'] ) ? $opts['fallback'] : $p['image'];
	$img   = $p['image_id']
		? wp_get_attachment_image( $p['image_id'], 'large', false, array( 'loading' => $eager ? 'eager' : 'lazy', 'decoding' => 'async' ) )
		: '<img decoding="async" src="' . esc_url( $fb ) . '" alt="' . esc_attr( $p['title'] ) . '" loading="' . ( $eager ? 'eager' : 'lazy' ) . '" width="960" height="701">';
	$html = '<article class="pcard" data-cat="' . esc_attr( implode( ' ', $p['cats'] ) ) . '" data-reveal' . $d . '>'
		. '<a class="pcard__shot" href="' . esc_url( $p['url'] ) . '">' . $img . '</a>'
		. '<h3>' . esc_html( $p['title'] ) . '</h3>'
		. ( $p['text'] ? '<p>' . esc_html( $p['text'] ) . '</p>' : '' )
		. '<a class="pcard__link" href="' . esc_url( $p['url'] ) . '"><span>' . esc_html( $link_text ) . '</span><img decoding="async" src="' . esc_url( $arrow ) . '" alt=""></a>'
		. '</article>';
	return $html;
}
