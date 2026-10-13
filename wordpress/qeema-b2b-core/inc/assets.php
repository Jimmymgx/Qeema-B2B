<?php
/**
 * Asset registration. Core styles/scripts load everywhere; page-specific CSS and
 * heavy scripts are attached to the widgets that need them (get_style_depends /
 * get_script_depends), so a page only downloads what its widgets use.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function qb_register_assets() {
	$v    = QB_VERSION;
	$css  = QB_URL . 'assets/css/';
	$js   = QB_URL . 'assets/js/';
	$mt   = function ( $f ) {
		$p = QB_PATH . 'assets/' . $f;
		return file_exists( $p ) ? filemtime( $p ) : QB_VERSION;
	};

	wp_register_style( 'qb-fonts', 'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght,SOFT,WONK@9..144,300,0,1&family=Inter:wght@400;500&family=Roboto:wght@300;400;500;600;700;800&display=swap', array(), null );

	$styles = array(
		// loads after Elementor's own stylesheet so the design's rules win equal-specificity ties (e.g. img resets)
		'qb-tokens'      => array( 'tokens.css', array( 'qb-fonts', 'elementor-frontend' ) ),
		'qb-base'        => array( 'base.css', array( 'qb-tokens' ) ),
		'qb-components'  => array( 'components.css', array( 'qb-base' ) ),
		'qb-glow'        => array( 'glow.css', array( 'qb-components' ) ),
		'qb-home'        => array( 'home.css', array( 'qb-glow' ) ),
		'qb-inner'       => array( 'inner.css', array( 'qb-home' ) ),
		'qb-about'       => array( 'about.css', array( 'qb-inner' ) ),
		'qb-services'    => array( 'services.css', array( 'qb-about' ) ),
		'qb-service-art' => array( 'service-art.css', array( 'qb-services' ) ),
		'qb-book'        => array( 'book.css', array( 'qb-inner' ) ),
		'qb-project'     => array( 'project.css', array( 'qb-inner' ) ),
		'qb-elementor'   => array( 'elementor-bridge.css', array( 'qb-base' ) ),
	);
	foreach ( $styles as $handle => $def ) {
		wp_register_style( $handle, $css . $def[0], $def[1], $mt( 'css/' . $def[0] ) );
	}

	wp_register_script( 'qb-main', $js . 'qb-main.js', array(), $mt( 'js/qb-main.js' ), true );
	wp_register_script( 'qb-components', $js . 'qb-components.js', array( 'qb-main' ), $mt( 'js/qb-components.js' ), true );
	wp_register_script( 'qb-three', $js . 'vendor/three.min.js', array(), '0.128', true );
	wp_register_script( 'qb-sculpture', $js . 'sculpture.js', array( 'qb-three', 'qb-main' ), $mt( 'js/sculpture.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'qb_register_assets', 5 );
add_action( 'elementor/editor/before_enqueue_scripts', 'qb_register_assets', 5 );
add_action( 'elementor/preview/enqueue_styles', 'qb_register_assets', 5 );

/** Always-on core assets. */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'qb-base' );
		wp_enqueue_style( 'qb-components' );
		wp_enqueue_style( 'qb-glow' );
		wp_enqueue_style( 'qb-inner' );
		wp_enqueue_style( 'qb-elementor' );
		wp_enqueue_script( 'qb-main' );
		wp_enqueue_script( 'qb-components' );
	},
	20
);

/** Flag the document as JS-enabled before first paint (the CSS hides reveal items only when .js is set). */
add_action(
	'wp_head',
	function () {
		echo "<script>document.documentElement.classList.add('js');</script>\n";
		echo '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	},
	1
);

/** Body classes the static CSS relies on. */
add_filter(
	'body_class',
	function ( $classes ) {
		$classes[] = 'qb-site';
		if ( is_front_page() ) {
			$classes[] = 'page-home';
		}
		return $classes;
	}
);
