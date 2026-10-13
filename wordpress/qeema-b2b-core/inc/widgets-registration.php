<?php
/**
 * Elementor widget registry. To add a widget: create widgets/<file>.php and add a row below.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** file (without .php) => class */
function qb_widget_map() {
	return array(
		'site-header'      => 'QB_Site_Header',
		'site-footer'      => 'QB_Site_Footer',
		'glow'             => 'QB_Glow',
		'home-hero'        => 'QB_Home_Hero',
		'clients-marquee'  => 'QB_Clients_Marquee',
		'services-wheel'   => 'QB_Services_Wheel',
		'work-showcase'    => 'QB_Work_Showcase',
		'testimonials'     => 'QB_Testimonials',
		'page-head'        => 'QB_Page_Head',
		'steps'            => 'QB_Steps',
		'about-hero'       => 'QB_About_Hero',
		'story'            => 'QB_Story',
		'services-hero'    => 'QB_Services_Hero',
		'service-cards'    => 'QB_Service_Cards',
		'service-hero'     => 'QB_Service_Hero',
		'service-rows'     => 'QB_Service_Rows',
		'deliverables'     => 'QB_Deliverables',
		'service-nav'      => 'QB_Service_Nav',
		'work-archive'     => 'QB_Work_Archive',
		'project-hero'     => 'QB_Project_Hero',
		'project-overview' => 'QB_Project_Overview',
		'book-form'        => 'QB_Book_Form',
	);
}

add_action(
	'elementor/elements/categories_registered',
	function ( $elements_manager ) {
		$elements_manager->add_category(
			'qeema-b2b',
			array(
				'title' => __( 'Qeema B2B', 'qeema-b2b' ),
				'icon'  => 'fa fa-plug',
			),
			1
		);
	}
);

add_action(
	'elementor/widgets/register',
	function ( $widgets_manager ) {
		require_once QB_PATH . 'inc/class-qb-widget-base.php';
		foreach ( qb_widget_map() as $file => $class ) {
			require_once QB_PATH . 'widgets/' . $file . '.php';
			$widgets_manager->register( new $class() );
		}
	}
);
