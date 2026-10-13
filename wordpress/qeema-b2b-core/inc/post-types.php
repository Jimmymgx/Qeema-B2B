<?php
/**
 * Post types: projects (+ categories), site parts (header/footer), leads.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function qb_register_post_types() {
	register_post_type(
		'qb_project',
		array(
			'labels'       => array(
				'name'               => __( 'Projects', 'qeema-b2b' ),
				'singular_name'      => __( 'Project', 'qeema-b2b' ),
				'add_new_item'       => __( 'Add New Project', 'qeema-b2b' ),
				'edit_item'          => __( 'Edit Project', 'qeema-b2b' ),
				'menu_name'          => __( 'Projects', 'qeema-b2b' ),
				'all_items'          => __( 'All Projects', 'qeema-b2b' ),
				'featured_image'     => __( 'Project card image', 'qeema-b2b' ),
				'set_featured_image' => __( 'Set project card image', 'qeema-b2b' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-portfolio',
			'menu_position'=> 21,
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'project' ),
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'elementor' ),
		)
	);

	register_taxonomy(
		'qb_project_cat',
		'qb_project',
		array(
			'labels'       => array(
				'name'          => __( 'Project categories', 'qeema-b2b' ),
				'singular_name' => __( 'Project category', 'qeema-b2b' ),
			),
			'hierarchical' => true,
			'public'       => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'project-category' ),
		)
	);

	register_post_type(
		'qb_part',
		array(
			'labels'              => array(
				'name'          => __( 'Site Parts', 'qeema-b2b' ),
				'singular_name' => __( 'Site Part', 'qeema-b2b' ),
				'add_new_item'  => __( 'Add New Site Part', 'qeema-b2b' ),
				'edit_item'     => __( 'Edit Site Part (header / footer)', 'qeema-b2b' ),
				'menu_name'     => __( 'Header & Footer', 'qeema-b2b' ),
			),
			'public'              => true,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-align-wide',
			'menu_position'       => 22,
			'has_archive'         => false,
			'rewrite'             => array( 'slug' => 'site-part' ),
			'supports'            => array( 'title', 'editor', 'elementor' ),
		)
	);

	register_post_type(
		'qb_lead',
		array(
			'labels'          => array(
				'name'          => __( 'Leads', 'qeema-b2b' ),
				'singular_name' => __( 'Lead', 'qeema-b2b' ),
				'menu_name'     => __( 'Leads', 'qeema-b2b' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 23,
			'capability_type' => 'post',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
			'supports'        => array( 'title' ),
		)
	);
}
add_action( 'init', 'qb_register_post_types' );

/* ---------- meta boxes ---------- */
add_action(
	'add_meta_boxes',
	function () {
		add_meta_box( 'qb_project_meta', __( 'Project details', 'qeema-b2b' ), 'qb_project_meta_box', 'qb_project', 'side', 'default' );
		add_meta_box( 'qb_part_meta', __( 'Site part type', 'qeema-b2b' ), 'qb_part_meta_box', 'qb_part', 'side', 'high' );
		add_meta_box( 'qb_page_style', __( 'Page style', 'qeema-b2b' ), 'qb_page_style_box', 'page', 'side', 'default' );
		add_meta_box( 'qb_lead_meta', __( 'Submission', 'qeema-b2b' ), 'qb_lead_meta_box', 'qb_lead', 'normal', 'high' );
	}
);

function qb_project_meta_box( $post ) {
	wp_nonce_field( 'qb_project_meta', 'qb_project_nonce' );
	$device  = get_post_meta( $post->ID, '_qb_device', true ) ?: 'phone';
	$tagline = get_post_meta( $post->ID, '_qb_tagline', true );
	$name    = get_post_meta( $post->ID, '_qb_display_name', true );
	echo '<p><label><strong>' . esc_html__( 'Hero device', 'qeema-b2b' ) . '</strong><br><select name="qb_device" style="width:100%">';
	foreach ( array( 'phone' => 'Phone', 'laptop' => 'Laptop' ) as $v => $l ) {
		echo '<option value="' . esc_attr( $v ) . '"' . selected( $device, $v, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select></label></p>';
	echo '<p><label><strong>' . esc_html__( 'Hero name', 'qeema-b2b' ) . '</strong><br><input type="text" name="qb_display_name" value="' . esc_attr( $name ) . '" style="width:100%" placeholder="Kingo"></label></p>';
	echo '<p><label><strong>' . esc_html__( 'Hero tagline', 'qeema-b2b' ) . '</strong><br><input type="text" name="qb_tagline" value="' . esc_attr( $tagline ) . '" style="width:100%" placeholder="Bold Ideas"></label></p>';
	$shot = get_post_meta( $post->ID, '_qb_hero_screen', true );
	echo '<p><label><strong>' . esc_html__( 'Hero screenshot', 'qeema-b2b' ) . '</strong><br><input type="url" id="qb_hero_screen" name="qb_hero_screen" value="' . esc_attr( $shot ) . '" style="width:100%" placeholder="https://"></label>';
	echo '<button type="button" class="button" id="qb_hero_screen_pick" style="margin-top:4px">' . esc_html__( 'Choose from Media Library', 'qeema-b2b' ) . '</button><br><span class="description">' . esc_html__( 'Shown inside the phone/laptop on the project page. Empty = the Featured image.', 'qeema-b2b' ) . '</span></p>';
	wp_enqueue_media();
	echo '<script>jQuery(function($){var f;$("#qb_hero_screen_pick").on("click",function(e){e.preventDefault();if(!f){f=wp.media({title:"Hero screenshot",multiple:false,library:{type:"image"}});f.on("select",function(){$("#qb_hero_screen").val(f.state().get("selection").first().toJSON().url);});}f.open();});});</script>';
	echo '<p class="description">' . esc_html__( 'The card image on the Our Work grid is the Featured image. Everything else on the project page is edited in Elementor.', 'qeema-b2b' ) . '</p>';
}

function qb_part_meta_box( $post ) {
	wp_nonce_field( 'qb_part_meta', 'qb_part_nonce' );
	$type = get_post_meta( $post->ID, '_qb_part_type', true ) ?: 'header';
	echo '<select name="qb_part_type" style="width:100%">';
	foreach ( array( 'header' => 'Site header', 'footer' => 'Site footer' ) as $v => $l ) {
		echo '<option value="' . esc_attr( $v ) . '"' . selected( $type, $v, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select><p class="description">' . esc_html__( 'The newest published part of each type is shown on every page.', 'qeema-b2b' ) . '</p>';
}

function qb_page_style_box( $post ) {
	wp_nonce_field( 'qb_page_style', 'qb_page_style_nonce' );
	$cur  = get_post_meta( $post->ID, '_qb_page_style', true );
	$opts = array(
		''         => __( 'Standard inner page', 'qeema-b2b' ),
		'home'     => __( 'Home (no inner wrapper)', 'qeema-b2b' ),
		'about'    => __( 'About', 'qeema-b2b' ),
		'svc'      => __( 'Services / service pages', 'qeema-b2b' ),
		'book'     => __( 'Book a call', 'qeema-b2b' ),
	);
	echo '<select name="qb_page_style" style="width:100%">';
	foreach ( $opts as $v => $l ) {
		echo '<option value="' . esc_attr( $v ) . '"' . selected( $cur, $v, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select><p class="description">' . esc_html__( 'Picks the spacing and background rules the page wrapper uses. Normally set automatically.', 'qeema-b2b' ) . '</p>';
}

function qb_lead_meta_box( $post ) {
	$data = get_post_meta( $post->ID, '_qb_lead', true );
	echo '<table class="widefat striped"><tbody>';
	foreach ( (array) $data as $k => $v ) {
		echo '<tr><th style="width:160px">' . esc_html( $k ) . '</th><td>' . nl2br( esc_html( $v ) ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

add_action(
	'save_post',
	function ( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( isset( $_POST['qb_project_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['qb_project_nonce'] ) ), 'qb_project_meta' ) ) {
			$device = isset( $_POST['qb_device'] ) && 'laptop' === $_POST['qb_device'] ? 'laptop' : 'phone';
			update_post_meta( $post_id, '_qb_device', $device );
			update_post_meta( $post_id, '_qb_tagline', isset( $_POST['qb_tagline'] ) ? sanitize_text_field( wp_unslash( $_POST['qb_tagline'] ) ) : '' );
			update_post_meta( $post_id, '_qb_hero_screen', isset( $_POST['qb_hero_screen'] ) ? esc_url_raw( wp_unslash( $_POST['qb_hero_screen'] ) ) : '' );
			update_post_meta( $post_id, '_qb_display_name', isset( $_POST['qb_display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['qb_display_name'] ) ) : '' );
		}
		if ( isset( $_POST['qb_page_style_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['qb_page_style_nonce'] ) ), 'qb_page_style' ) ) {
			update_post_meta( $post_id, '_qb_page_style', isset( $_POST['qb_page_style'] ) ? sanitize_key( wp_unslash( $_POST['qb_page_style'] ) ) : '' );
		}
		if ( isset( $_POST['qb_part_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['qb_part_nonce'] ) ), 'qb_part_meta' ) ) {
			$type = isset( $_POST['qb_part_type'] ) && 'footer' === $_POST['qb_part_type'] ? 'footer' : 'header';
			update_post_meta( $post_id, '_qb_part_type', $type );
		}
	}
);

/** Newest published site part of a type ('header' | 'footer'), or 0. */
function qb_get_part_id( $type ) {
	$q = get_posts(
		array(
			'post_type'      => 'qb_part',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_key'       => '_qb_part_type',
			'meta_value'     => $type,
			'fields'         => 'ids',
		)
	);
	return $q ? (int) $q[0] : 0;
}

/** New projects created in wp-admin get the default project layout (hero, overview, more work) instead of a blank page. */
add_action(
	'save_post_qb_project',
	function ( $post_id, $post ) {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! empty( $GLOBALS['qb_seeding'] ) ) {
			return;
		}
		if ( ! $post || in_array( $post->post_status, array( 'auto-draft', 'trash', 'inherit' ), true ) ) {
			return;
		}
		if ( get_post_meta( $post_id, '_elementor_data', true ) || get_post_meta( $post_id, '_qb_seed_key', true ) ) {
			return;
		}
		if ( function_exists( 'qb_apply_default_project_layout' ) ) {
			qb_apply_default_project_layout( $post_id );
		}
	},
	20,
	2
);

/** Admin notice: the header / footer are Site Parts; warn when one is missing. */
add_action(
	'admin_notices',
	function () {
		if ( ! current_user_can( 'edit_posts' ) || ! function_exists( 'qb_get_part_id' ) ) {
			return;
		}
		$missing = array();
		foreach ( array( 'header' => __( 'header', 'qeema-b2b' ), 'footer' => __( 'footer', 'qeema-b2b' ) ) as $type => $label ) {
			if ( ! qb_get_part_id( $type ) ) {
				$missing[] = $label;
			}
		}
		if ( $missing ) {
			echo '<div class="notice notice-warning"><p>' . sprintf(
				/* translators: %s: header / footer */
				esc_html__( 'No published site %s was found, so it is not shown on the website. Create one under Header & Footer (Site Parts) or run Tools → Qeema B2B setup.', 'qeema-b2b' ),
				esc_html( implode( ' / ', $missing ) )
			) . '</p></div>';
		}
	}
);
