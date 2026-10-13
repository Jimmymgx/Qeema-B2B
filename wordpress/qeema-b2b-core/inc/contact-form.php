<?php
/**
 * Book-a-call submissions: AJAX handler, lead storage, e-mail notification, settings page.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Localise ajax URL + nonce for the front-end scripts. */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_localize_script(
			'qb-components',
			'qbData',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'qb_book_call' ),
			)
		);
	},
	21
);

/**
 * Per-form settings (recipient, server messages) are kept in an option, keyed by an opaque token
 * that the form carries as a hidden field. Visitors can only pick a token that a widget registered.
 *
 * @param array $cfg to, e_session, e_invalid.
 * @return string Token.
 */
function qb_form_register( $cfg ) {
	$token = substr( wp_hash( wp_json_encode( $cfg ) ), 0, 16 );
	$map   = get_option( 'qb_form_cfg', array() );
	if ( ! is_array( $map ) ) {
		$map = array();
	}
	if ( ! isset( $map[ $token ] ) ) {
		$map[ $token ] = $cfg;
		if ( count( $map ) > 100 ) {
			$map = array_slice( $map, -100, null, true );
		}
		update_option( 'qb_form_cfg', $map, false );
	}
	return $token;
}

function qb_form_cfg( $token ) {
	$map = get_option( 'qb_form_cfg', array() );
	return ( $token && is_array( $map ) && isset( $map[ $token ] ) ) ? (array) $map[ $token ] : array();
}

function qb_ajax_book_call() {
	$cfg = qb_form_cfg( isset( $_POST['qb_rt'] ) ? sanitize_key( wp_unslash( $_POST['qb_rt'] ) ) : '' );
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'qb_book_call' ) ) {
		wp_send_json_error( array( 'message' => ! empty( $cfg['e_session'] ) ? $cfg['e_session'] : __( 'Your session expired. Please reload the page and try again.', 'qeema-b2b' ) ), 403 );
	}
	// honeypot: bots fill every field
	if ( ! empty( $_POST['qb_hp'] ) ) {
		wp_send_json_success();
	}
	$first   = isset( $_POST['firstName'] ) ? sanitize_text_field( wp_unslash( $_POST['firstName'] ) ) : '';
	$last    = isset( $_POST['lastName'] ) ? sanitize_text_field( wp_unslash( $_POST['lastName'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$source  = isset( $_POST['source'] ) ? esc_url_raw( wp_unslash( $_POST['source'] ) ) : '';

	if ( '' === $first || '' === $last || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => ! empty( $cfg['e_invalid'] ) ? $cfg['e_invalid'] : __( 'Please fill in your name and a valid email address.', 'qeema-b2b' ) ), 422 );
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'qb_lead',
			'post_status' => 'publish',
			'post_title'  => $first . ' ' . $last . ' — ' . gmdate( 'Y-m-d H:i' ),
		)
	);
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta(
			$id,
			'_qb_lead',
			array(
				'First name' => $first,
				'Last name'  => $last,
				'Email'      => $email,
				'Phone'      => $phone,
				'Message'    => $message,
				'Page'       => $source,
			)
		);
	}

	$to      = ! empty( $cfg['to'] ) && is_email( $cfg['to'] ) ? $cfg['to'] : get_option( 'qb_lead_email', get_option( 'admin_email' ) );
	$subject = sprintf( '[%s] New "Book a call" request from %s %s', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $first, $last );
	$body    = "Name: $first $last\nEmail: $email\nPhone: $phone\nPage: $source\n\n$message\n";
	wp_mail( $to, $subject, $body, array( 'Reply-To: ' . $first . ' ' . $last . ' <' . $email . '>' ) );

	wp_send_json_success();
}
add_action( 'wp_ajax_qb_book_call', 'qb_ajax_book_call' );
add_action( 'wp_ajax_nopriv_qb_book_call', 'qb_ajax_book_call' );

/* ---------- Leads → Settings ---------- */
add_action(
	'admin_menu',
	function () {
		add_submenu_page( 'edit.php?post_type=qb_lead', __( 'Lead settings', 'qeema-b2b' ), __( 'Settings', 'qeema-b2b' ), 'manage_options', 'qb-lead-settings', 'qb_lead_settings_page' );
	}
);

function qb_lead_settings_page() {
	if ( isset( $_POST['qb_lead_email'] ) && check_admin_referer( 'qb_lead_settings' ) ) {
		update_option( 'qb_lead_email', sanitize_email( wp_unslash( $_POST['qb_lead_email'] ) ) );
		echo '<div class="updated"><p>' . esc_html__( 'Saved.', 'qeema-b2b' ) . '</p></div>';
	}
	echo '<div class="wrap"><h1>' . esc_html__( 'Lead settings', 'qeema-b2b' ) . '</h1><form method="post">';
	wp_nonce_field( 'qb_lead_settings' );
	echo '<table class="form-table"><tr><th><label for="qb_lead_email">' . esc_html__( 'Send "Book a call" notifications to', 'qeema-b2b' ) . '</label></th><td><input type="email" class="regular-text" id="qb_lead_email" name="qb_lead_email" value="' . esc_attr( get_option( 'qb_lead_email', get_option( 'admin_email' ) ) ) . '"></td></tr></table>';
	submit_button();
	echo '</form></div>';
}
