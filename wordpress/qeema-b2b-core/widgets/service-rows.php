<?php
/**
 * "What is included" list: numbered rows; the active row reveals a thumbnail.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Service_Rows extends QB_Widget_Base {

	public function get_name() {
		return 'qb-service-rows';
	}

	public function get_title() {
		return __( 'Service Rows (what is included)', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-editor-list-ol';
	}

	public function get_style_depends() {
		return array( 'qb-service-art' );
	}

	protected function register_controls() {
		$this->sec( 'head', __( 'Heading', 'qeema-b2b' ) );
		$this->t( 'anchor', __( 'Anchor ID (no #)', 'qeema-b2b' ), 'included' );
		$this->t( 'pill', __( 'Pill text', 'qeema-b2b' ), 'Our services' );
		$this->t( 'title', __( 'Title (HTML allowed: <strong>)', 'qeema-b2b' ), 'Every detail <strong>Working together.</strong>', 'area' );
		$this->t( 'lead', __( 'Paragraph', 'qeema-b2b' ), 'Design and development, connected from the start. Built around what your business needs.', 'area' );
		$this->end();

		$this->sec( 'rows', __( 'Rows', 'qeema-b2b' ) );
		$r = $this->rep();
		$r->add_control( 'title', array( 'label' => __( 'Title', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Title', 'label_block' => true ) );
		$r->add_control( 'text', array( 'label' => __( 'Text', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => '' ) );
		$this->ma( $r, 'image', __( 'Thumbnail (shown on hover)', 'qeema-b2b' ), 'img/results-hand.webp' );
		$r->add_control( 'number', array( 'label' => __( 'Number override (empty = automatic)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
		$mk = function ( $t, $x, $img ) {
			return array( 'title' => $t, 'text' => $x, 'image' => qb_media_default( 'img/' . $img ) );
		};
		$this->add_control(
			'rows',
			array(
				'label'       => __( 'Rows', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => array(
					$mk( 'Strategy & structure', 'We start with your goals, audience and content, then map the pages and journeys that will move visitors toward action.', 'results-hand.webp' ),
					$mk( 'UX & interface design', 'Clear layouts, strong typography and a consistent visual system that reflect your brand on every screen size.', 'project-screenshot.webp' ),
					$mk( 'Responsive development', 'Clean, fast front-end code that adapts smoothly from large desktops to the smallest phones.', 'project-screenshot1.webp' ),
					$mk( 'Content management', 'An easy-to-use CMS so your team can edit pages, publish news and manage media without a developer.', 'project-screenshot2.webp' ),
					$mk( 'Performance & SEO', 'Optimised images, lean code and technical SEO foundations so the site loads fast and gets found.', 'results-hand.webp' ),
					$mk( 'Accessibility & quality', 'Tested across browsers and devices, with accessible markup, contrast and keyboard support built in.', 'project-screenshot1.webp' ),
				),
				'title_field' => '{{{ title }}}',
			)
		);
		$this->sw( 'show_numbers', __( 'Row numbers', 'qeema-b2b' ), 'yes' );
		$this->end();

		$this->style_sec( 'thumb_style', __( 'Thumbnails', 'qeema-b2b' ) );
		$this->add_responsive_control( 'thumb_w', array( 'label' => __( 'Width', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 60, 'max' => 600 ) ), 'selectors' => array( '{{WRAPPER}} .sd-row__img' => 'width: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'thumb_h', array( 'label' => __( 'Height', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 40, 'max' => 400 ) ), 'selectors' => array( '{{WRAPPER}} .sd-row__img' => 'height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'thumb_radius', array( 'label' => __( 'Corner radius', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%' ), 'selectors' => array( '{{WRAPPER}} .sd-row__img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_control( 'thumb_fit', array( 'label' => __( 'Image fit', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => array( '' => __( 'Default', 'qeema-b2b' ), 'cover' => 'Cover', 'contain' => 'Contain', 'fill' => 'Fill' ), 'default' => '', 'selectors' => array( '{{WRAPPER}} .sd-row__img img' => 'object-fit: {{VALUE}};' ) ) );
		$this->end();

		$this->style_all(
			'.sd-list',
			array(
				array( 'title', __( 'Heading', 'qeema-b2b' ), 'h2.title' ),
				array( 'lead', __( 'Heading paragraph', 'qeema-b2b' ), '.sd-list__head .lead' ),
				array( 'rtitle', __( 'Row title', 'qeema-b2b' ), '.sd-row h3' ),
				array( 'rtext', __( 'Row text', 'qeema-b2b' ), '.sd-row p' ),
				array( 'num', __( 'Row number', 'qeema-b2b' ), '.sd-row__num' ),
			)
		);
	}

	protected function render() {
		$s  = $this->get_settings_for_display();
		$id = $s['anchor'] ? ' id="' . esc_attr( $s['anchor'] ) . '"' : '';
		?>
		<section class="sd-list"<?php echo $id; // phpcs:ignore WordPress.Security.EscapeOutput ?> aria-label="<?php echo esc_attr( wp_strip_all_tags( $s['title'] ) ); ?>">
			<div class="sd-list__head">
				<div>
					<?php echo $this->el( 'span', 'pill', 'pill pill--grey', esc_html( $s['pill'] ), 'none', array( 'data-reveal' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo $this->el( 'h2', 'title', 'title', $this->kses( $s['title'] ), 'basic', array( 'data-reveal' => '', 'style' => '--d:.08s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<div class="lead-row" data-reveal="right"><?php echo $this->el( 'p', 'lead', 'lead', esc_html( $s['lead'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			</div>
			<ol class="sd-rows" data-qb="rows">
				<?php foreach ( (array) $s['rows'] as $i => $row ) : ?>
					<li class="sd-row<?php echo 0 === $i ? ' is-active' : ''; ?>" data-reveal<?php echo $i ? ' style="--d:.05s"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<div class="sd-row__text">
							<?php echo $this->el( 'h3', $this->rk( 'title', 'rows', $i ), '', esc_html( $row['title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php echo $this->el( 'p', $this->rk( 'text', 'rows', $i ), '', esc_html( $row['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
						<span class="sd-row__img"><?php echo $this->img( $row['image'], array( 'alt' => $row['image_alt'], 'loading' => 'lazy', 'width' => '228', 'height' => '120' ), 'medium' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php if ( 'yes' === $s['show_numbers'] ) : ?>
							<span class="sd-row__num" aria-hidden="true"><?php echo esc_html( '' !== trim( (string) $row['number'] ) ? $row['number'] : sprintf( '%02d', $i + 1 ) ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</section>
		<?php
	}
}
