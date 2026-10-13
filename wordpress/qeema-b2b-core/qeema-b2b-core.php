<?php
/**
 * Plugin Name:       Qeema B2B Core
 * Description:       Elementor widgets, post types and seeder for the Qeematech B2B website (every section, text, image and background is editable in Elementor).
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 * Author:            Qeematech
 * Text Domain:       qeema-b2b
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'QB_VERSION', '1.0.0' );
define( 'QB_FILE', __FILE__ );
define( 'QB_PATH', plugin_dir_path( __FILE__ ) );
define( 'QB_URL', plugin_dir_url( __FILE__ ) );

require_once QB_PATH . 'inc/helpers.php';
require_once QB_PATH . 'inc/post-types.php';
require_once QB_PATH . 'inc/assets.php';
require_once QB_PATH . 'inc/contact-form.php';
require_once QB_PATH . 'inc/archive-ajax.php';
require_once QB_PATH . 'inc/seed/seeder.php';

/**
 * Elementor integration (only when Elementor is active).
 */
add_action(
	'plugins_loaded',
	function () {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-error"><p>' . esc_html__( 'Qeema B2B Core needs the Elementor plugin to be active.', 'qeema-b2b' ) . '</p></div>';
				}
			);
			return;
		}
		if ( version_compare( ELEMENTOR_VERSION, '3.20.0', '<' ) ) {
			add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-warning"><p>' . esc_html__( 'Qeema B2B Core works best with Elementor 3.20 or newer.', 'qeema-b2b' ) . '</p></div>';
				}
			);
		}
		require_once QB_PATH . 'inc/widgets-registration.php';
	}
);

/**
 * Flush rewrite rules once after activation (post types).
 */
register_activation_hook(
	__FILE__,
	function () {
		qb_register_post_types();
		flush_rewrite_rules();
	}
);
