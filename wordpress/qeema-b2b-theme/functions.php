<?php
/**
 * Qeema B2B theme setup.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'responsive-embeds' );
		register_nav_menus( array( 'primary' => __( 'Primary menu', 'qeema-b2b' ) ) );
	}
);

/** Render an Elementor-built Site Part ('header' | 'footer'). */
function qb_render_part( $type ) {
	if ( ! function_exists( 'qb_get_part_id' ) || ! class_exists( '\Elementor\Plugin' ) ) {
		return;
	}
	$id = qb_get_part_id( $type );
	if ( ! $id ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			echo '<div style="padding:12px 20px;background:#fff3cd;color:#664d03;font:14px/1.4 sans-serif">' . sprintf(
				/* translators: %s: header or footer */
				esc_html__( 'No published site %s found. Create one under Header & Footer (only editors see this message).', 'qeema-b2b' ),
				esc_html( 'footer' === $type ? __( 'footer', 'qeema-b2b' ) : __( 'header', 'qeema-b2b' ) )
			) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		return;
	}
	echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id, true ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/** Classes for the <main> wrapper, from the page's "Page style" setting. */
function qb_main_class() {
	$style = get_queried_object_id() ? get_post_meta( get_queried_object_id(), '_qb_page_style', true ) : '';
	if ( is_singular( 'qb_project' ) ) {
		return 'qb-main proj';
	}
	if ( 'home' === $style || is_front_page() ) {
		return 'qb-main';
	}
	return 'qb-main inner' . ( $style && 'inner' !== $style ? ' inner--' . sanitize_html_class( $style ) : '' );
}

/** Site parts are edited in Elementor but must never be indexed on their own. */
add_action(
	'wp_head',
	function () {
		if ( is_singular( 'qb_part' ) ) {
			echo '<meta name="robots" content="noindex,nofollow">' . "\n";
		}
	},
	1
);
