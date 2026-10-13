<?php
/**
 * Our Work archive: projects grid with pagination (12 per page by default).
 * Works without JavaScript (server-rendered, plain pager links); with JavaScript the grid swaps without a reload.
 * Category chips come from the "Page Head" widget on the same page (or enable them below).
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Work_Archive extends QB_Widget_Base {

	public function get_name() {
		return 'qb-work-archive';
	}

	public function get_title() {
		return __( 'Work Archive (projects grid)', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_style_depends() {
		return array( 'qb-inner' );
	}

	protected function register_controls() {
		$this->sec( 'grid', __( 'Grid', 'qeema-b2b' ) );
		$this->num( 'per', __( 'Projects per page', 'qeema-b2b' ), 12, 3, 48 );
		$this->t( 'link_text', __( 'Card link text', 'qeema-b2b' ), 'View Project' );
		$this->add_control(
			'cards_info',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					/* translators: %s: link to the Projects list */
					__( 'Each card’s title, text, image and category come from the project itself. %s', 'qeema-b2b' ),
					'<a href="' . esc_url( admin_url( 'edit.php?post_type=qb_project' ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Edit Projects', 'qeema-b2b' ) . '</a>'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);
		$this->m( 'fallback_image', __( 'Fallback image (used when a project has no Featured image)', 'qeema-b2b' ), 'img/project-screenshot.webp' );
		$this->m( 'arrow_icon', __( 'Card link arrow icon', 'qeema-b2b' ), 'svg/arrow-right.svg' );
		$this->t( 'empty', __( 'Text when nothing matches the filter', 'qeema-b2b' ), 'No projects match this filter yet.' );
		$this->end();

		$this->style_all(
			'.archive',
			array(
				array( 'ctitle', __( 'Card title', 'qeema-b2b' ), '.pcard h3' ),
				array( 'ctext', __( 'Card text', 'qeema-b2b' ), '.pcard p' ),
				array( 'empty', __( 'Empty message', 'qeema-b2b' ), '.archive__empty' ),
			)
		);
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$per  = max( 3, (int) $s['per'] );
		$page = isset( $_GET['qb_page'] ) ? max( 1, (int) $_GET['qb_page'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification
		$opts = array(
			'fallback' => ! empty( $s['fallback_image']['url'] ) ? $s['fallback_image']['url'] : '',
			'arrow'    => ! empty( $s['arrow_icon']['url'] ) ? $s['arrow_icon']['url'] : '',
		);
		$res  = qb_archive_query( '', $page, $per, $s['link_text'], $opts );
		?>
		<section class="archive" data-qb="archive" data-per="<?php echo (int) $per; ?>" data-page="<?php echo (int) $res['page']; ?>" data-link="<?php echo esc_attr( $s['link_text'] ); ?>" data-fallback="<?php echo esc_url( $opts['fallback'] ); ?>" data-arrow="<?php echo esc_url( $opts['arrow'] ); ?>" aria-label="<?php esc_attr_e( 'Projects', 'qeema-b2b' ); ?>">
			<div class="work__grid archive__grid" aria-live="polite"><?php echo $res['cards']; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<p class="archive__empty"<?php echo $res['count'] ? ' hidden' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $s['empty'] ); ?></p>
			<div class="archive__pager"><?php echo $res['pager']; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		</section>
		<?php
	}
}
