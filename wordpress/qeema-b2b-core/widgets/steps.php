<?php
/**
 * Icon cards in a row: "Why Qeematech" (About, Services) and "Our approach" steps (service pages).
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Steps extends QB_Widget_Base {

	public function get_name() {
		return 'qb-steps';
	}

	public function get_title() {
		return __( 'Icon Cards / Steps', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-icon-box';
	}

	public function get_style_depends() {
		return array( 'qb-services' );
	}

	protected function register_controls() {
		$this->sec( 'head', __( 'Heading', 'qeema-b2b' ) );
		$this->sel( 'variant', __( 'Layout', 'qeema-b2b' ), array( 'why' => __( 'Why section (About, Services)', 'qeema-b2b' ), 'approach' => __( 'Approach steps (service pages)', 'qeema-b2b' ) ), 'why' );
		$this->t( 'anchor', __( 'Anchor ID (for in-page links, no #)', 'qeema-b2b' ), '' );
		$this->t( 'pill', __( 'Pill text', 'qeema-b2b' ), 'Why Qeematech' );
		$this->t( 'title', __( 'Title (HTML allowed: <strong>)', 'qeema-b2b' ), 'We give your project a <strong>stronger foundation</strong> for success', 'area' );
		$this->end();

		$this->sec( 'cards', __( 'Cards', 'qeema-b2b' ) );
		$r = $this->rep();
		$this->ma( $r, 'icon', __( 'Icon', 'qeema-b2b' ), 'img/why-icon.png' );
		$r->add_control( 'title', array( 'label' => __( 'Title', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Title', 'label_block' => true ) );
		$r->add_control( 'text', array( 'label' => __( 'Text', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => '' ) );
		$icon = qb_media_default( 'img/why-icon.png' );
		$this->add_control(
			'cards',
			array(
				'label'       => __( 'Cards', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => array(
					array( 'icon' => $icon, 'title' => 'Software Experts', 'text' => 'Practical experience in developing websites, systems, and applications to ensure professional, scalable implementation.' ),
					array( 'icon' => $icon, 'title' => 'Real Protection', 'text' => 'We apply best core practices in security and stability to enhance the safety of your digital project from the start.' ),
					array( 'icon' => $icon, 'title' => 'Ideas and Solutions', 'text' => 'We don’t just execute; we help you choose the right solution to achieve the best results for your business.' ),
					array( 'icon' => $icon, 'title' => 'Technical Support', 'text' => 'Ongoing technical follow-up to help you maintain the quality of your site or app and quickly address any feedback.' ),
				),
				'title_field' => '{{{ title }}}',
			)
		);
		$this->end();

		$this->style_sec( 'cards_style', __( 'Cards & icons', 'qeema-b2b' ) );
		$this->add_responsive_control(
			'cols',
			array(
				'label'     => __( 'Columns', 'qeema-b2b' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array( '' => __( 'Default', 'qeema-b2b' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5' ),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .why__grid' => 'display: grid; grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
					'{{WRAPPER}} .why__card' => 'width: auto;',
				),
			)
		);
		$this->add_control( 'card_bg', array( 'label' => __( 'Card background', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .why__card' => 'background-color: {{VALUE}};' ) ) );
		$this->add_group_control( \Elementor\Group_Control_Border::get_type(), array( 'name' => 'card_border', 'selector' => '{{WRAPPER}} .why__card' ) );
		$this->add_responsive_control( 'card_pad', array( 'label' => __( 'Card padding', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%', 'em' ), 'selectors' => array( '{{WRAPPER}} .why__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'card_radius', array( 'label' => __( 'Card corner radius', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%' ), 'selectors' => array( '{{WRAPPER}} .why__card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->img_style( 'icon', __( 'Icons', 'qeema-b2b' ), '.why__icon img' );
		$this->add_responsive_control( 'icon_hh', array( 'label' => __( 'Height', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 10, 'max' => 600 ) ), 'selectors' => array( '{{WRAPPER}} .why__icon img' => 'height: {{SIZE}}{{UNIT}};' ) ) );
		$this->end();

		$this->style_all(
			'section',
			array(
				array( 'pill', __( 'Pill', 'qeema-b2b' ), '.pill' ),
				array( 'title', __( 'Heading', 'qeema-b2b' ), 'h2.title' ),
				array( 'ctitle', __( 'Card title', 'qeema-b2b' ), '.why__card h3' ),
				array( 'ctext', __( 'Card text', 'qeema-b2b' ), '.why__card p' ),
			)
		);
	}

	protected function render() {
		$s        = $this->get_settings_for_display();
		$approach = 'approach' === $s['variant'];
		$cls      = $approach ? 'sd-approach' : 'why';
		$head     = $approach ? 'sd-approach__head' : 'why__head';
		$id       = $s['anchor'] ? ' id="' . esc_attr( $s['anchor'] ) . '"' : '';
		?>
		<section class="<?php echo esc_attr( $cls ); ?>"<?php echo $id; // phpcs:ignore WordPress.Security.EscapeOutput ?> aria-label="<?php echo esc_attr( wp_strip_all_tags( $s['title'] ) ); ?>">
			<div class="<?php echo esc_attr( $head ); ?>">
				<?php echo $this->el( 'span', 'pill', 'pill pill--grey', esc_html( $s['pill'] ), 'none', array( 'data-reveal' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->el( 'h2', 'title', 'title', $this->kses( $s['title'] ), 'basic', array( 'data-reveal' => '', 'style' => '--d:.08s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<ul class="why__grid">
				<?php foreach ( (array) $s['cards'] as $i => $c ) : ?>
					<li class="why__card" data-reveal<?php echo $i ? ' style="--d:.' . (int) $i . 's"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<span class="why__icon"><?php echo $this->img( $c['icon'], array( 'alt' => $c['icon_alt'], 'width' => '36', 'height' => '36' ), 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php echo $this->el( 'h3', $this->rk( 'title', 'cards', $i ), '', esc_html( $c['title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo $this->el( 'p', $this->rk( 'text', 'cards', $i ), '', esc_html( $c['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
		<?php
	}
}
