<?php
/**
 * Services grid: two columns of service cards (label, title, text, tags, two buttons).
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Service_Cards extends QB_Widget_Base {

	public function get_name() {
		return 'qb-service-cards';
	}

	public function get_title() {
		return __( 'Service Cards Grid', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_style_depends() {
		return array( 'qb-services' );
	}

	private static function defaults() {
		$s = home_url( '/services/' );
		$c = array(
			array( '01 / Website Design & Development', 'Website Design & Development', 'We design and develop modern, responsive websites that combine strong visual identity with seamless user experiences.', 'UI Design, UX Design, Corporate Websites, Responsive', 'website-design-development' ),
			array( '02 / DIGITAL SOLUTIONS', 'Mobile App Development', 'We build high-performance mobile applications designed to deliver smooth, scalable experiences across iOS and Android.', 'iOS & Android, Flutter, React Native, UI/UX', 'mobile-app-development' ),
			array( '03 / DIGITAL SOLUTIONS', 'E-Commerce Design & Development', 'We create conversion-focused online stores that make browsing, purchasing, and managing products simple and effective.', 'WooCommerce, Shopify, Checkout, Conversion', 'e-commerce-solutions' ),
			array( '04 / DIGITAL SOLUTIONS', 'Custom Software', 'Tailor-made systems and platforms that streamline operations and scale with your business.', 'Web Platforms, Dashboards, Integrations, APIs', 'custom-software' ),
			array( '05 / BRAND', 'Branding & Identity', 'Memorable brand systems, from logo and tone of voice to guidelines that keep every touchpoint consistent.', 'Logo, Brand Guidelines, Messaging, Collateral', 'branding-identity' ),
			array( '06 / GROWTH', 'Digital Marketing', 'Data-driven campaigns, SEO and content that bring the right audience to your product.', 'SEO, Social Media, Paid Ads, Analytics', 'digital-marketing' ),
		);
		$out = array();
		foreach ( $c as $x ) {
			$out[] = array(
				'label' => $x[0], 'title' => $x[1], 'text' => $x[2], 'tags' => $x[3],
				'btn1_text' => 'Discuss your project', 'btn1_link' => array( 'url' => home_url( '/book-a-call/' ) ),
				'btn2_text' => 'Show more details', 'btn2_link' => array( 'url' => $s . $x[4] . '/' ),
			);
		}
		return $out;
	}

	protected function register_controls() {
		$this->sec( 'cards', __( 'Cards', 'qeema-b2b' ) );
		$r = $this->rep();
		$this->ma( $r, 'image', __( 'Card image (optional)', 'qeema-b2b' ) );
		$this->ma( $r, 'icon', __( 'Card icon (optional)', 'qeema-b2b' ) );
		$r->add_control( 'label', array( 'label' => __( 'Small label', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '01 / Service' ) );
		$r->add_control( 'title', array( 'label' => __( 'Title', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Service', 'label_block' => true ) );
		$r->add_control( 'text', array( 'label' => __( 'Description', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => '' ) );
		$r->add_control( 'tags', array( 'label' => __( 'Tags (comma separated)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'label_block' => true ) );
		$r->add_control( 'btn1_text', array( 'label' => __( 'Button 1 text', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Discuss your project' ) );
		$r->add_control( 'btn1_link', array( 'label' => __( 'Button 1 link', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '' ) ) );
		$r->add_control( 'btn2_text', array( 'label' => __( 'Button 2 text (outline)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Show more details' ) );
		$r->add_control( 'btn2_link', array( 'label' => __( 'Button 2 link', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '' ) ) );
		$this->add_control(
			'cards',
			array(
				'label'       => __( 'Service cards', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => self::defaults(),
				'title_field' => '{{{ title }}}',
			)
		);
		$this->end();

		$this->style_sec( 'cards_style', __( 'Cards layout & tags', 'qeema-b2b' ) );
		$this->add_responsive_control(
			'cols',
			array(
				'label'     => __( 'Columns', 'qeema-b2b' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array( '' => __( 'Default', 'qeema-b2b' ), '1' => '1', '2' => '2', '3' => '3', '4' => '4' ),
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} .svc-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));' ),
			)
		);
		$this->add_responsive_control(
			'gap',
			array(
				'label'      => __( 'Column gap', 'qeema-b2b' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
				'selectors'  => array( '{{WRAPPER}} .svc-grid' => 'column-gap: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_control( 'card_bg', array( 'label' => __( 'Card background', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .svc-card' => 'background-color: {{VALUE}};' ) ) );
		$this->typo( 'tag', __( 'Tags', 'qeema-b2b' ), '.svc-card__tags .tag' );
		$this->add_control( 'tag_bg', array( 'label' => __( 'Tag background', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .svc-card__tags .tag' => 'background-color: {{VALUE}};' ) ) );
		$this->img_style( 'cimg', __( 'Card image', 'qeema-b2b' ), '.svc-card__media img' );
		$this->end();

		$this->style_all(
			'.svc-grid',
			array(
				array( 'label', __( 'Small label', 'qeema-b2b' ), '.svc-card .svc-card__label' ),
				array( 'title', __( 'Card title', 'qeema-b2b' ), '.svc-card h3' ),
				array( 'text', __( 'Card text', 'qeema-b2b' ), '.svc-card > p:not(.svc-card__label)' ),
			)
		);
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<section class="svc-grid" aria-label="' . esc_attr__( 'All services', 'qeema-b2b' ) . '">';
		foreach ( (array) $s['cards'] as $i => $c ) {
			$tags = array_filter( array_map( 'trim', explode( ',', (string) $c['tags'] ) ) );
			?>
			<article class="svc-card" data-reveal<?php echo ( $i % 2 ) ? ' style="--d:.1s"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<?php
				if ( ! empty( $c['image']['url'] ) || ! empty( $c['image']['id'] ) ) {
					echo '<div class="svc-card__media">' . $this->img( $c['image'], array( 'alt' => isset( $c['image_alt'] ) ? $c['image_alt'] : '', 'loading' => 'lazy' ), 'large' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				if ( ! empty( $c['icon']['url'] ) || ! empty( $c['icon']['id'] ) ) {
					echo '<span class="svc-card__icon">' . $this->img( $c['icon'], array( 'alt' => isset( $c['icon_alt'] ) ? $c['icon_alt'] : '', 'width' => '48', 'height' => '48' ), 'thumbnail' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
				}
				?>
				<?php echo $this->el( 'p', $this->rk( 'label', 'cards', $i ), 'svc-card__label', esc_html( $c['label'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->el( 'h3', $this->rk( 'title', 'cards', $i ), '', esc_html( $c['title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->el( 'p', $this->rk( 'text', 'cards', $i ), '', esc_html( $c['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="svc-card__tags"><?php foreach ( $tags as $t ) { echo '<span class="tag">' . esc_html( $t ) . '</span>'; } ?></div>
				<div class="svc-card__actions">
					<?php echo $this->a( $c['btn1_link'], 'c' . $i . 'a', 'btn', esc_html( $c['btn1_text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo $this->a( $c['btn2_link'], 'c' . $i . 'b', 'btn btn--outline', '<span>' . esc_html( $c['btn2_text'] ) . '</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</article>
			<?php
		}
		echo '</section>';
	}
}
