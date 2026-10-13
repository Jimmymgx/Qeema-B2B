<?php
/**
 * Ambient background for inner pages: blurred blue gradient + two thin vertical lines
 * (or the silky project background). Place it first in a page container; it sits behind
 * the content and spans the page wrapper.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Glow extends QB_Widget_Base {

	public function get_name() {
		return 'qb-glow';
	}

	public function get_title() {
		return __( 'Page Background (glow)', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-background';
	}

	public function get_style_depends() {
		return array( 'qb-inner', 'qb-project' );
	}

	protected function register_controls() {
		$this->sec( 'bg', __( 'Background', 'qeema-b2b' ) );
		$this->sel(
			'variant',
			__( 'Style', 'qeema-b2b' ),
			array(
				'top'  => __( 'Glow, short (Our Work)', 'qeema-b2b' ),
				'full' => __( 'Glow, standard (service pages)', 'qeema-b2b' ),
				'tall' => __( 'Glow, tall (About)', 'qeema-b2b' ),
				'page' => __( 'Glow, full page height (Book a call)', 'qeema-b2b' ),
				'silk' => __( 'Silky images (project pages)', 'qeema-b2b' ),
			),
			'full'
		);
		$this->add_control(
			'height',
			array(
				'label'     => __( 'Custom glow height (px, optional)', 'qeema-b2b' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'min'       => 300,
				'max'       => 6000,
				'condition' => array( 'variant!' => array( 'silk', 'page' ) ),
				'selectors' => array( '{{WRAPPER}} .inner-glow' => 'height: {{VALUE}}px;' ),
			)
		);
		$this->sw( 'lines', __( 'Vertical lines', 'qeema-b2b' ), 'yes' );
		$this->m( 'silk_a', __( 'Silk image 1', 'qeema-b2b' ), 'img/proj-bg-c.jpg' );
		$this->m( 'silk_b', __( 'Silk image 2', 'qeema-b2b' ), 'img/proj-bg-b.jpg' );
		$this->m( 'silk_c', __( 'Silk image 3', 'qeema-b2b' ), 'img/proj-bg-a.jpg' );
		$tops = array(
			'top_1' => array( __( 'Silk image 1 - top position (px)', 'qeema-b2b' ), 0 ),
			'top_2' => array( __( 'Silk image 2 - top position (px)', 'qeema-b2b' ), 859 ),
			'top_3' => array( __( 'Silk image 3 - top position (px)', 'qeema-b2b' ), 1760 ),
			'top_4' => array( __( 'Silk image 2 (repeat) - top position (px)', 'qeema-b2b' ), 2644 ),
		);
		foreach ( $tops as $id => $t ) {
			$this->add_control( $id, array( 'label' => $t[0], 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => $t[1], 'min' => -2000, 'max' => 10000, 'condition' => array( 'variant' => 'silk' ) ) );
		}
		$this->sw( 'hide_mobile', __( 'Hide on mobile', 'qeema-b2b' ), '' );
		$this->end();

		$this->style_sec( 'qb_style_glow', __( 'Glow colours', 'qeema-b2b' ) );
		$this->add_control( 'glow_c1', array( 'label' => __( 'Colour 1 (top-left tint)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::COLOR, 'condition' => array( 'variant!' => 'silk' ), 'selectors' => array( '{{WRAPPER}} .inner-glow' => '--qb-glow-c1: {{VALUE}};' ) ) );
		$this->add_control( 'glow_c2', array( 'label' => __( 'Colour 2 (top-right tint)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::COLOR, 'condition' => array( 'variant!' => 'silk' ), 'selectors' => array( '{{WRAPPER}} .inner-glow' => '--qb-glow-c2: {{VALUE}};' ) ) );
		$this->add_control(
			'glow_opacity',
			array(
				'label'     => __( 'Glow opacity', 'qeema-b2b' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ),
				'condition' => array( 'variant!' => 'silk' ),
				'selectors' => array( '{{WRAPPER}} .inner-glow' => '--qb-glow-o: {{SIZE}};' ),
			)
		);
		$this->add_control( 'line_color', array( 'label' => __( 'Vertical line colour', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::COLOR, 'condition' => array( 'variant!' => 'silk', 'lines' => 'yes' ), 'selectors' => array( '{{WRAPPER}} .inner-glow__vline' => 'background: linear-gradient(to bottom, {{VALUE}} 70%, transparent);' ) ) );
		$this->add_control(
			'line_opacity',
			array(
				'label'     => __( 'Vertical line opacity', 'qeema-b2b' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ),
				'condition' => array( 'variant!' => 'silk', 'lines' => 'yes' ),
				'selectors' => array( '{{WRAPPER}} .inner-glow__vline' => 'opacity: {{SIZE}};' ),
			)
		);
		$this->add_control(
			'silk_opacity',
			array(
				'label'     => __( 'Silk images opacity', 'qeema-b2b' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0, 'max' => 1, 'step' => 0.05 ) ),
				'condition' => array( 'variant' => 'silk' ),
				'selectors' => array( '{{WRAPPER}} .proj-bg' => 'opacity: {{SIZE}};' ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$hide = 'yes' === $s['hide_mobile'] ? ' qb-glow--hide-mobile' : '';
		if ( 'silk' === $s['variant'] ) {
			?>
			<div class="proj-bg<?php echo $hide; ?>" aria-hidden="true">
				<?php
				echo $this->img( $s['silk_b'], array( 'class' => 'flip', 'style' => 'top:' . (int) $s['top_4'] . 'px' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput
				echo $this->img( $s['silk_b'], array( 'class' => 'flip', 'style' => 'top:' . (int) $s['top_2'] . 'px' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput
				echo $this->img( $s['silk_c'], array( 'style' => 'top:' . (int) $s['top_3'] . 'px' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput
				echo $this->img( $s['silk_a'], array( 'style' => 'top:' . (int) $s['top_1'] . 'px' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput
				?>
			</div>
			<?php
			return;
		}
		$field = 'top' === $s['variant'] ? 'glow-field--top' : 'glow-field--full';
		$mod   = array( 'tall' => ' inner-glow--tall', 'page' => ' inner-glow--full' );
		?>
		<div class="inner-glow<?php echo isset( $mod[ $s['variant'] ] ) ? esc_attr( $mod[ $s['variant'] ] ) : ''; ?><?php echo $hide; // phpcs:ignore WordPress.Security.EscapeOutput ?>" aria-hidden="true">
			<div class="inner-glow__stage">
				<div class="glow-field <?php echo esc_attr( $field ); ?>"></div>
				<div class="glow-drift"></div>
				<?php if ( 'yes' === $s['lines'] ) : ?>
					<span class="inner-glow__vline inner-glow__vline--l"></span>
					<span class="inner-glow__vline inner-glow__vline--r"></span>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
