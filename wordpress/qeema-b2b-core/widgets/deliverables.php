<?php
/**
 * "What you walk away with": heading, two paragraphs and a ticked list.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Deliverables extends QB_Widget_Base {

	public function get_name() {
		return 'qb-deliverables';
	}

	public function get_title() {
		return __( 'Deliverables Checklist', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-check-circle';
	}

	public function get_style_depends() {
		return array( 'qb-service-art' );
	}

	protected function register_controls() {
		$this->sec( 'text', __( 'Text', 'qeema-b2b' ) );
		$this->t( 'anchor', __( 'Anchor ID (no #)', 'qeema-b2b' ), 'deliverables' );
		$this->t( 'pill', __( 'Pill text', 'qeema-b2b' ), 'What you walk away with' );
		$this->t( 'title', __( 'Title (HTML allowed: <strong>)', 'qeema-b2b' ), 'Built To Launch, <strong>Ready To Evolve.</strong>', 'area' );
		$this->t( 'lead1', __( 'Paragraph 1', 'qeema-b2b' ), 'More than a finished website. You get the tools, ownership, and confidence to keep moving forward.', 'area' );
		$this->t( 'lead2', __( 'Paragraph 2', 'qeema-b2b' ), 'Your exact scope and deliverables are agreed together at the start of the project.', 'area' );
		$this->end();

		$this->sec( 'media', __( 'Image & check icon', 'qeema-b2b' ) );
		$this->sel( 'image_pos', __( 'Image position', 'qeema-b2b' ), array( 'hidden' => __( 'Hidden', 'qeema-b2b' ), 'left' => __( 'Left', 'qeema-b2b' ), 'right' => __( 'Right', 'qeema-b2b' ), 'top' => __( 'Top', 'qeema-b2b' ) ), 'hidden' );
		$this->ma( $this, 'image', __( 'Image', 'qeema-b2b' ) );
		$this->ma( $this, 'check_icon', __( 'Check icon (replaces the drawn tick)', 'qeema-b2b' ) );
		$this->end();

		$this->sec( 'checks', __( 'Checklist', 'qeema-b2b' ) );
		$r = $this->rep();
		$r->add_control( 'title', array( 'label' => __( 'Title', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Title', 'label_block' => true ) );
		$r->add_control( 'text', array( 'label' => __( 'Text', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => '' ) );
		$this->add_control(
			'checks',
			array(
				'label'       => __( 'Items', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => array(
					array( 'title' => 'Design files & component library', 'text' => 'Organized Figma files, responsive layouts, and reusable UI.' ),
					array( 'title' => 'Production-ready website', 'text' => 'Tested front-end code, CMS setup, and agreed integrations.' ),
					array( 'title' => 'Launch essentials', 'text' => 'Technical SEO, analytics setup, and a go-live checklist.' ),
					array( 'title' => 'Documentation & handover', 'text' => 'Editing guidance, team training, and a plan for ongoing care.' ),
				),
				'title_field' => '{{{ title }}}',
			)
		);
		$this->end();

		$this->style_sec( 'check_style', __( 'Check icon & image', 'qeema-b2b' ) );
		$this->add_control( 'check_color', array( 'label' => __( 'Check colour', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .sd-checks li::before' => 'border-color: {{VALUE}};' ) ) );
		$this->add_control( 'check_hover', array( 'label' => __( 'Check colour (hover)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .sd-checks li:hover::before' => 'border-color: {{VALUE}};' ) ) );
		$this->add_responsive_control(
			'check_size',
			array(
				'label'      => __( 'Check size', 'qeema-b2b' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 8, 'max' => 48 ) ),
				'selectors'  => array(
					'{{WRAPPER}} .sd-checks li::before' => 'height: {{SIZE}}{{UNIT}}; width: calc({{SIZE}}{{UNIT}} * 0.64);',
					'{{WRAPPER}} .sd-check-img'        => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				),
			)
		);
		$this->img_style( 'dimg', __( 'Image', 'qeema-b2b' ), '.sd-deliver__img img' );
		$this->end();

		$this->style_all(
			'.sd-deliver',
			array(
				array( 'title', __( 'Title', 'qeema-b2b' ), 'h2.title' ),
				array( 'lead', __( 'Paragraphs', 'qeema-b2b' ), '.sd-deliver__copy .lead' ),
				array( 'ctitle', __( 'Check titles', 'qeema-b2b' ), '.sd-checks h3' ),
				array( 'ctext', __( 'Check text', 'qeema-b2b' ), '.sd-checks p' ),
			)
		);
	}

	protected function render() {
		$s  = $this->get_settings_for_display();
		$has_img = 'hidden' !== $s['image_pos'] && ( ! empty( $s['image']['url'] ) || ! empty( $s['image']['id'] ) );
		$img     = $has_img ? '<div class="sd-deliver__img">' . $this->img( $s['image'], array( 'alt' => $s['image_alt'], 'loading' => 'lazy' ), 'large' ) . '</div>' : '';
		$tick    = ! empty( $s['check_icon']['url'] ) || ! empty( $s['check_icon']['id'] );
		$id = $s['anchor'] ? ' id="' . esc_attr( $s['anchor'] ) . '"' : '';
		?>
		<section class="sd-deliver<?php echo $has_img ? ' sd-deliver--img-' . esc_attr( $s['image_pos'] ) : ''; ?>"<?php echo $id; // phpcs:ignore WordPress.Security.EscapeOutput ?> aria-label="<?php echo esc_attr( wp_strip_all_tags( $s['title'] ) ); ?>">
			<?php echo in_array( $s['image_pos'], array( 'left', 'top' ), true ) ? $img : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="sd-deliver__copy">
				<?php echo $this->el( 'span', 'pill', 'pill pill--grey', esc_html( $s['pill'] ), 'none', array( 'data-reveal' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->el( 'h2', 'title', 'title', $this->kses( $s['title'] ), 'basic', array( 'data-reveal' => '', 'style' => '--d:.08s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->el( 'p', 'lead1', 'lead', esc_html( $s['lead1'] ), 'basic', array( 'data-reveal' => '', 'style' => '--d:.14s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->el( 'p', 'lead2', 'lead', esc_html( $s['lead2'] ), 'basic', array( 'data-reveal' => '', 'style' => '--d:.2s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<ul class="sd-checks">
				<?php foreach ( (array) $s['checks'] as $i => $c ) : ?>
					<li<?php echo $tick ? ' class="has-check-img"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?> data-reveal<?php echo $i ? ' style="--d:.' . (int) $i . 's"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<?php echo $tick ? $this->img( $s['check_icon'], array( 'class' => 'sd-check-img', 'alt' => $s['check_icon_alt'], 'width' => '18', 'height' => '18' ), 'thumbnail' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo $this->el( 'h3', $this->rk( 'title', 'checks', $i ), '', esc_html( $c['title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo $this->el( 'p', $this->rk( 'text', 'checks', $i ), '', esc_html( $c['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php echo 'right' === $s['image_pos'] ? $img : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</section>
		<?php
	}
}
