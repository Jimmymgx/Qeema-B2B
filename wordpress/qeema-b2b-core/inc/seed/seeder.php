<?php
/**
 * Seeder: builds the whole site (pages as Elementor data, projects, header/footer parts, settings).
 *
 * - Idempotent: running it again never duplicates anything.
 * - Never overwrites content you edited: a page is only refreshed while its Elementor data
 *   still equals what the seeder wrote (hash stored in _qb_seed_hash). Use --force to override.
 *
 * Run:  wp qb seed [--force]      or   Tools → Qeema B2B setup in wp-admin.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ------------------------------------------------------------------ Elementor data builders */

function qb_eid() {
	return substr( md5( uniqid( '', true ) . wp_rand() ), 0, 8 );
}

/** Container with no spacing of its own (the widgets carry the design's spacing). */
function qb_container( $children, $classes = '', $extra = array() ) {
	$settings = array_merge(
		array(
			'content_width'  => 'full',
			'flex_direction' => 'column',
			'flex_gap'       => array( 'column' => '0', 'row' => '0', 'isLinked' => true, 'unit' => 'px', 'size' => 0 ),
			'padding'        => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ),
			'css_classes'    => $classes,
		),
		$extra
	);
	return array( 'id' => qb_eid(), 'elType' => 'container', 'isInner' => false, 'settings' => $settings, 'elements' => $children );
}

/** Default item lists of a widget's repeater controls (computed once per widget type from a throw-away instance). */
function qb_repeater_defaults( $type ) {
	static $cache = array();
	if ( isset( $cache[ $type ] ) ) {
		return $cache[ $type ];
	}
	$cache[ $type ] = array();
	if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
		return array();
	}
	require_once QB_PATH . 'inc/class-qb-widget-base.php';
	foreach ( qb_widget_map() as $file => $class ) {
		if ( 'qb-' . $file !== $type ) {
			continue;
		}
		require_once QB_PATH . 'widgets/' . $file . '.php';
		$obj = new $class( array(), array() );
		foreach ( $obj->get_controls() as $id => $c ) {
			if ( ! empty( $c['type'] ) && 'repeater' === $c['type'] ) {
				$cache[ $type ][ $id ] = QB_Widget_Base::complete_repeater( $c );
			}
		}
	}
	return $cache[ $type ];
}

function qb_widget( $type, $settings = array() ) {
	// Store every repeater's default list in the data, so the Elementor panel shows the same items the page renders.
	foreach ( qb_repeater_defaults( $type ) as $id => $items ) {
		if ( ! array_key_exists( $id, $settings ) ) {
			$settings[ $id ] = $items;
		}
	}
	return array( 'id' => qb_eid(), 'elType' => 'widget', 'widgetType' => $type, 'settings' => $settings, 'elements' => array() );
}

/** Repeater items need a stable _id. */
function qb_items( $items ) {
	$out = array();
	foreach ( $items as $it ) {
		$it['_id'] = qb_eid();
		$out[]     = $it;
	}
	return $out;
}

function qb_crumb( $pairs ) {
	$items = array();
	foreach ( $pairs as $p ) {
		$items[] = array( 'label' => $p[0], 'link' => array( 'url' => isset( $p[1] ) ? $p[1] : '' ) );
	}
	return qb_items( $items );
}

/* ------------------------------------------------------------------ media */

/** Copy a bundled asset into the Media Library once; returns attachment ID (0 on failure). */
function qb_import_asset( $rel ) {
	$map = get_option( 'qb_media_map', array() );
	if ( ! empty( $map[ $rel ] ) && get_post( $map[ $rel ] ) ) {
		return (int) $map[ $rel ];
	}
	$src = QB_PATH . 'assets/' . $rel;
	if ( ! file_exists( $src ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( basename( $src ) );
	copy( $src, $tmp );
	$svg_ok = function ( $m ) {
		$m['svg'] = 'image/svg+xml';
		return $m;
	};
	$svg_ft = function ( $d, $file, $filename ) {
		if ( 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
			$d = array( 'ext' => 'svg', 'type' => 'image/svg+xml', 'proper_filename' => false );
		}
		return $d;
	};
	add_filter( 'upload_mimes', $svg_ok );
	add_filter( 'wp_check_filetype_and_ext', $svg_ft, 10, 3 );
	$id = media_handle_sideload( array( 'name' => basename( $src ), 'tmp_name' => $tmp ), 0 );
	remove_filter( 'upload_mimes', $svg_ok );
	remove_filter( 'wp_check_filetype_and_ext', $svg_ft, 10 );
	if ( is_wp_error( $id ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		return 0;
	}
	if ( 'svg' !== strtolower( pathinfo( $src, PATHINFO_EXTENSION ) ) ) {
		update_post_meta( $id, '_wp_attachment_image_alt', pathinfo( $src, PATHINFO_FILENAME ) );
	}
	$map[ $rel ] = $id;
	update_option( 'qb_media_map', $map, false );
	return (int) $id;
}

/**
 * MEDIA control value for a bundled asset: the Media Library item (imported once, id cached in the
 * qb_media_map option) or, if the import fails, the plain plugin URL.
 */
function qb_media_item( $rel ) {
	$id = qb_import_asset( $rel );
	if ( $id ) {
		$url = wp_get_attachment_url( $id );
		if ( $url ) {
			return array( 'id' => $id, 'url' => $url );
		}
	}
	return qb_media_default( $rel );
}

/** Give a new, empty project the default project layout (used on first save in wp-admin). */
function qb_apply_default_project_layout( $post_id ) {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return;
	}
	$json = wp_json_encode( qb_page_data( 'project' ) );
	update_post_meta( $post_id, '_elementor_data', wp_slash( $json ) );
	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $post_id, '_elementor_template_type', 'wp-post' );
	update_post_meta( $post_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.35.0' );
	update_post_meta( $post_id, '_wp_page_template', 'default' );
	delete_post_meta( $post_id, '_elementor_css' );
}

/* ------------------------------------------------------------------ persistence */

/**
 * Create or refresh a post that holds Elementor data.
 *
 * @return int Post ID.
 */
function qb_upsert( $key, $post_type, $title, $slug, $data, $meta = array(), $parent = 0, $force = false, $extra = array() ) {
	$found = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'meta_key'       => '_qb_seed_key', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $key, // phpcs:ignore WordPress.DB.SlowDBQuery
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	$json = wp_json_encode( $data );
	$hash = md5( $json );
	if ( $found ) {
		$id      = (int) $found[0];
		$current = get_post_meta( $id, '_elementor_data', true );
		$stored  = get_post_meta( $id, '_qb_seed_hash', true );
		$edited  = $stored && $current && md5( is_string( $current ) ? $current : wp_json_encode( $current ) ) !== $stored;
		if ( $edited && ! $force ) {
			return $id;
		}
		if ( $stored === $hash && ! $force ) {
			return $id;
		}
		wp_update_post( array_merge( array( 'ID' => $id, 'post_title' => $title ), $extra ) );
	} else {
		$id = wp_insert_post(
			array_merge(
				array(
					'post_type'   => $post_type,
					'post_status' => 'publish',
					'post_title'  => $title,
					'post_name'   => $slug,
					'post_parent' => $parent,
				),
				$extra
			)
		);
		if ( is_wp_error( $id ) || ! $id ) {
			return 0;
		}
		update_post_meta( $id, '_qb_seed_key', $key );
	}
	update_post_meta( $id, '_elementor_data', wp_slash( $json ) );
	update_post_meta( $id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $id, '_elementor_template_type', 'page' === $post_type ? 'wp-page' : 'wp-post' );
	update_post_meta( $id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.35.0' );
	update_post_meta( $id, '_wp_page_template', 'default' );
	update_post_meta( $id, '_qb_seed_hash', md5( $json ) );
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, $k, $v );
	}
	delete_post_meta( $id, '_elementor_css' ); // regenerate the CSS file on next view
	return $id;
}

/* ------------------------------------------------------------------ page definitions */

function qb_page_data( $key ) {
	$S = home_url( '/services/' );
	switch ( $key ) {
		case 'home':
			return array(
				qb_container(
					array(
						qb_container( array( qb_widget( 'qb-home-hero' ), qb_widget( 'qb-clients-marquee' ) ), 'zone' ),
						qb_widget( 'qb-services-wheel' ),
						qb_widget( 'qb-work-showcase', array( 'variant' => 'home', 'count' => 6, 'chips' => 'yes' ) ),
						qb_widget( 'qb-testimonials' ),
					),
					'qb-page'
				),
			);
		case 'about':
			return array(
				qb_container(
					array(
						qb_widget( 'qb-glow', array( 'variant' => 'tall' ) ),
						qb_widget( 'qb-about-hero' ),
						qb_widget( 'qb-services-wheel' ),
						qb_widget( 'qb-story' ),
						qb_widget( 'qb-steps', array( 'variant' => 'why' ) ),
						qb_widget( 'qb-clients-marquee' ),
					),
					'qb-page qb-clip'
				),
			);
		case 'services':
			return array(
				qb_container(
					array(
						qb_widget( 'qb-glow', array( 'variant' => 'full' ) ),
						qb_widget( 'qb-services-hero' ),
						qb_widget( 'qb-services-wheel' ),
						qb_widget( 'qb-service-cards' ),
						qb_widget( 'qb-steps', array( 'variant' => 'why' ) ),
						qb_widget( 'qb-work-showcase', array( 'variant' => 'services', 'count' => 3, 'chips' => '', 'more_text' => 'See more' ) ),
					),
					'qb-page qb-clip'
				),
			);
		case 'our-work':
			return array(
				qb_container(
					array(
						qb_widget( 'qb-glow', array( 'variant' => 'top' ) ),
						qb_widget( 'qb-page-head', array( 'chips' => 'yes' ) ),
						qb_widget( 'qb-work-archive' ),
					),
					'qb-page qb-clip'
				),
			);
		case 'book-a-call':
			return array(
				qb_container(
					array(
						qb_widget( 'qb-glow', array( 'variant' => 'page' ) ),
						qb_widget(
							'qb-page-head',
							array(
								'variant'    => 'book',
								'crumb'      => qb_crumb( array( array( 'Home', home_url( '/' ) ), array( 'Book a call' ) ) ),
								'title'      => 'Ready to discuss your <strong>next project</strong>',
								'lead'       => 'The Qeema Tech team is ready to listen to your idea and turn it into a professional digital solution. Leave your details, and our team will contact you as soon as possible to discuss the best options for your needs.',
								'show_cards' => 'yes',
							)
						),
						qb_widget( 'qb-book-form' ),
					),
					'qb-page qb-clip'
				),
			);
		case '404':
			return array(
				qb_container(
					array(
						qb_widget( 'qb-glow', array( 'variant' => 'page' ) ),
						qb_widget(
							'qb-page-head',
							array(
								'crumb' => qb_crumb( array( array( 'Home', home_url( '/' ) ), array( 'Page not found' ) ) ),
								'title' => 'Page <strong>not found</strong>',
								'lead'  => 'The page you are looking for does not exist or has been moved. Use the menu or go back to the home page.',
							)
						),
					),
					'qb-page qb-clip'
				),
			);
		case 'project':
			return array(
				qb_container(
					array(
						qb_widget( 'qb-glow', array( 'variant' => 'silk' ) ),
						qb_widget(
							'qb-project-hero',
							array(
								'frame_phone'  => qb_media_item( 'img/phone-mockup.png' ),
								'frame_laptop' => qb_media_item( 'img/laptop-mockup.webp' ),
							)
						),
						qb_widget( 'qb-project-overview' ),
						qb_widget( 'qb-work-showcase', array( 'variant' => 'project', 'count' => 6, 'chips' => 'yes' ) ),
					),
					'qb-page qb-clip'
				),
			);
	}
	return array();
}

/** Floating-label texts per hero art style (service pages). */
function qb_art_chips( $art ) {
	$map = array(
		'mobile'    => array( 'chip1_a' => 'New order', 'chip1_b' => '#2481 · confirmed', 'chip2_a' => 'Push', 'chip2_b' => 'Your order is on its way' ),
		'ecommerce' => array( 'chip1_a' => 'Cart · 3 items', 'chip1_b' => '$120.00', 'chip2_a' => 'Order confirmed', 'cart_btn' => 'Checkout', 'laptop_screen' => qb_media_item( 'img/project-screenshot.webp' ) ),
		'software'  => array( 'chip1_a' => '99.9%', 'chip1_b' => 'uptime', 'chip2_a' => 'API', 'chip2_b' => 'connected' ),
		'branding'  => array(),
		'marketing' => array( 'chart_title' => 'Organic traffic', 'chip1_a' => '+128%', 'chip1_b' => 'organic traffic', 'chip2_a' => '4.8×', 'chip2_b' => 'return on ad spend', 'chip3_a' => '#1', 'chip3_b' => 'Ranking' ),
		'website'   => array(),
	);
	return isset( $map[ $art ] ) ? $map[ $art ] : array();
}

function qb_service_page_data( $i, $svc, $all ) {
	$n   = count( $all );
	$prv = $all[ ( $i + $n - 1 ) % $n ];
	$nxt = $all[ ( $i + 1 ) % $n ];
	$S   = home_url( '/services/' );

	$rows = array();
	foreach ( $svc['rows'] as $r ) {
		$rows[] = array( 'title' => $r['title'], 'text' => $r['text'], 'image' => qb_media_default( 'img/' . $r['thumb'] ) );
	}
	$steps = array();
	foreach ( $svc['steps'] as $st ) {
		$steps[] = array( 'icon' => qb_media_default( 'img/why-icon.png' ), 'title' => $st['title'], 'text' => $st['text'] );
	}

	$hero = array_merge(
		array(
			'crumb'    => qb_crumb( array( array( 'Home', home_url( '/' ) ), array( 'Services', $S ), array( $svc['name'] ) ) ),
			'title'    => $svc['name'],
			'intro'    => $svc['intro'],
			'tags'     => implode( ', ', $svc['tags'] ),
			'art'      => $svc['art'],
			'cap'      => $svc['cap'],
			'strip_l'  => $svc['strip'],
		),
		qb_art_chips( $svc['art'] )
	);

	return array(
		qb_container(
			array(
				qb_widget( 'qb-glow', array( 'variant' => 'full' ) ),
				qb_widget( 'qb-service-hero', $hero ),
				qb_widget( 'qb-service-rows', array( 'title' => $svc['list_title'], 'lead' => $svc['list_lead'], 'rows' => qb_items( $rows ) ) ),
				qb_widget( 'qb-steps', array( 'variant' => 'approach', 'anchor' => 'approach', 'pill' => 'Our approach', 'title' => $svc['approach_title'], 'cards' => qb_items( $steps ) ) ),
				qb_widget(
					'qb-deliverables',
					array(
						'title'  => $svc['deliver_title'],
						'lead1'  => $svc['deliver_leads'][0],
						'lead2'  => $svc['deliver_leads'][1],
						'checks' => qb_items( $svc['checks'] ),
					)
				),
				qb_widget(
					'qb-service-nav',
					array(
						'prev_title' => $prv['name'],
						'prev_link'  => array( 'url' => $S . $prv['slug'] . '/' ),
						'next_title' => $nxt['name'],
						'next_link'  => array( 'url' => $S . $nxt['slug'] . '/' ),
					)
				),
			),
			'qb-page qb-clip'
		),
	);
}

/* ------------------------------------------------------------------ the run */

function qb_seed_all( $force = false, $log = null ) {
	$GLOBALS['qb_seeding'] = true;
	$say = function ( $m ) use ( $log ) {
		if ( $log ) {
			call_user_func( $log, $m );
		}
	};

	// --- site settings
	update_option( 'blogname', 'Qeematech B2B' );
	update_option( 'blogdescription', 'We create digital products' );
	update_option( 'permalink_structure', '/%postname%/' );
	update_option( 'elementor_cpt_support', array( 'page', 'post', 'qb_project', 'qb_part' ) );
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );
	update_option( 'elementor_unfiltered_files_upload', '1' );        // allow SVG logos/icons in the media library
	register_taxonomy( 'qb_project_cat', 'qb_project' );
	$say( 'Settings saved.' );

	// --- Site Icon from the logo (only when none is set)
	if ( ! get_option( 'site_icon' ) ) {
		$icon = qb_import_asset( 'svg/logo.svg' );
		if ( $icon ) {
			update_option( 'site_icon', $icon );
		}
	}

	// --- project categories
	$cats = array(
		'mobile'    => 'Mobile Applications',
		'corporate' => 'Corporate Websites',
		'ecommerce' => 'E-Commerce',
		'travel'    => 'Travel Websites',
		'education' => 'Educational Websites',
		'custom'    => 'Custom Software',
	);
	foreach ( $cats as $slug => $name ) {
		if ( ! term_exists( $slug, 'qb_project_cat' ) ) {
			wp_insert_term( $name, 'qb_project_cat', array( 'slug' => $slug ) );
		}
	}
	$say( 'Project categories ready.' );

	// --- header / footer parts
	$hdr = qb_upsert( 'part-header', 'qb_part', 'Site header', 'site-header', array( qb_container( array( qb_widget( 'qb-site-header', array( 'logo' => qb_media_item( 'svg/logo.svg' ) ) ) ), 'qb-part qb-part--header' ) ), array( '_qb_part_type' => 'header' ), 0, $force );
	$ftr = qb_upsert( 'part-footer', 'qb_part', 'Site footer', 'site-footer', array( qb_container( array( qb_widget( 'qb-site-footer', array( 'logo' => qb_media_item( 'svg/logo-white.svg' ) ) ) ), 'qb-part qb-part--footer' ) ), array( '_qb_part_type' => 'footer' ), 0, $force );
	$say( "Header part #$hdr, footer part #$ftr." );

	// --- pages
	$ids   = array();
	$pages = array(
		'home'        => array( 'Home', 'home', 'home' ),
		'about'       => array( 'About Us', 'about-us', 'about' ),
		'services'    => array( 'Services', 'services', 'svc' ),
		'our-work'    => array( 'Our Work', 'our-work', '' ),
		'book-a-call' => array( 'Book a call', 'book-a-call', 'book' ),
	);
	foreach ( $pages as $key => $def ) {
		$ids[ $key ] = qb_upsert( 'page-' . $key, 'page', $def[0], $def[1], qb_page_data( $key ), array( '_qb_page_style' => $def[2] ), 0, $force );
		$say( "Page {$def[0]} #{$ids[$key]}" );
	}
	// 404 page (Elementor-built, private so it is not listed; the theme renders it for unknown URLs)
	$nf = qb_upsert( 'page-404', 'page', 'Page not found', 'page-not-found', qb_page_data( '404' ), array( '_qb_page_style' => '' ), 0, $force, array( 'post_status' => 'private' ) );
	if ( $nf ) {
		update_option( 'qb_404_page', $nf );
	}
	$say( "404 page #$nf" );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['home'] );

	// --- service pages (children of Services)
	$services = json_decode( file_get_contents( QB_PATH . 'inc/data/services.json' ), true );
	foreach ( $services as $i => $svc ) {
		$id = qb_upsert(
			'service-' . $svc['slug'],
			'page',
			$svc['name'],
			$svc['slug'],
			qb_service_page_data( $i, $svc, $services ),
			array( '_qb_page_style' => 'svc', '_yoast_wpseo_metadesc' => $svc['meta'] ),
			(int) $ids['services'],
			$force
		);
		$say( "Service page {$svc['name']} #$id" );
	}

	// --- projects
	$images = array();
	foreach ( array( 'project-screenshot.webp', 'project-screenshot1.webp', 'project-screenshot2.webp' ) as $f ) {
		$images[] = qb_import_asset( 'img/' . $f );
	}
	$names   = array( 'Rivermark', 'Kingo', 'Jazarti', 'Orionix', 'Lumina Labs', 'Horizon', 'NovaTech', 'Aurora', 'Cedar & Co', 'Bluepeak', 'Vertex', 'Solace', 'Nimbus', 'Atlas Group', 'Quill', 'Meridian', 'Pulse', 'Harbor', 'Zenith', 'Embark', 'Tandem', 'Foundry', 'Cascade', 'Ember' );
	$cat_set = array( array( 'corporate', 'travel' ), array( 'mobile', 'custom' ), array( 'ecommerce', 'corporate' ), array( 'education', 'travel' ), array( 'custom', 'education' ), array( 'mobile', 'ecommerce' ) );
	$total   = 36;
	for ( $i = 0; $i < $total; $i++ ) {
		$title = ( $names[ $i % count( $names ) ] ) . ( $i >= count( $names ) ? ' ' . ( floor( $i / count( $names ) ) + 1 ) : '' );
		$pid   = qb_upsert(
			'project-' . $i,
			'qb_project',
			$title,
			sanitize_title( $title ),
			qb_page_data( 'project' ),
			array(
				'_qb_device'       => ( $i % 2 ) ? 'laptop' : 'phone',
				'_qb_tagline'      => 'Bold Ideas',
				'_qb_display_name' => $title,
			),
			0,
			$force,
			array( 'post_excerpt' => 'High-Performance Websites And Platforms Built To Deliver Seamless Digital Experiences.', 'menu_order' => $i )
		);
		if ( $pid ) {
			set_post_thumbnail( $pid, $images[ ( $i + (int) floor( $i / 12 ) ) % count( $images ) ] );
			wp_set_object_terms( $pid, $cat_set[ $i % count( $cat_set ) ], 'qb_project_cat' );
		}
	}
	$say( "$total projects ready." );

	// --- primary menu (for themes/plugins that use it; the header widget has its own list)
	if ( ! wp_get_nav_menu_object( 'Primary' ) ) {
		$menu = wp_create_nav_menu( 'Primary' );
		if ( ! is_wp_error( $menu ) ) {
			foreach ( array( 'home' => 'Home', 'about' => 'About Us', 'services' => 'Services', 'our-work' => 'Our Work' ) as $k => $label ) {
				wp_update_nav_menu_item( $menu, 0, array( 'menu-item-title' => $label, 'menu-item-object' => 'page', 'menu-item-object-id' => $ids[ $k ], 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
			}
			$locs            = get_theme_mod( 'nav_menu_locations', array() );
			$locs['primary'] = $menu;
			set_theme_mod( 'nav_menu_locations', $locs );
		}
	}

	flush_rewrite_rules();
	if ( class_exists( '\Elementor\Plugin' ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	update_option( 'qb_seeded_version', QB_VERSION );
	$GLOBALS['qb_seeding'] = false;
	$say( 'Done.' );
}

/* ------------------------------------------------------------------ WP-CLI + admin entry points */

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'qb seed',
		function ( $args, $assoc ) {
			qb_seed_all( ! empty( $assoc['force'] ), function ( $m ) {
				WP_CLI::log( $m );
			} );
			WP_CLI::success( 'Qeema B2B content is ready.' );
		}
	);
}

add_action(
	'admin_menu',
	function () {
		add_management_page( 'Qeema B2B setup', 'Qeema B2B setup', 'manage_options', 'qb-setup', 'qb_setup_page' );
	}
);

function qb_setup_page() {
	$out = array();
	if ( isset( $_POST['qb_seed'] ) && check_admin_referer( 'qb_seed' ) ) {
		qb_seed_all( ! empty( $_POST['qb_force'] ), function ( $m ) use ( &$out ) {
			$out[] = $m;
		} );
	}
	echo '<div class="wrap"><h1>Qeema B2B setup</h1><p>Creates (or refreshes) the pages, service pages, projects, header and footer. Pages you have edited in Elementor are left alone unless you tick the box below.</p><form method="post">';
	wp_nonce_field( 'qb_seed' );
	echo '<p><label><input type="checkbox" name="qb_force" value="1"> Also overwrite pages I have edited</label></p>';
	submit_button( 'Create / refresh demo content', 'primary', 'qb_seed' );
	echo '</form>';
	if ( $out ) {
		echo '<pre>' . esc_html( implode( "\n", $out ) ) . '</pre>';
	}
	echo '</div>';
}
