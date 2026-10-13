<?php
/**
 * Pinned services section: numbered wheel + slides driven by scroll.
 * Used on Home, About and Services.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Services_Wheel extends QB_Widget_Base {

	public function get_name() {
		return 'qb-services-wheel';
	}

	public function get_title() {
		return __( 'Services Wheel (scroll)', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-circle';
	}

	public function get_style_depends() {
		return array( 'qb-home' );
	}

	private static function items() {
		$base = home_url( '/services/' );
		return array(
			array( 'title' => 'Website Design & Development', 'text' => 'We design and develop modern, responsive websites that combine strong visual identity with seamless user experiences.', 'tags' => 'UI Design, UX Design, Corporate Websites, Responsive', 'slug' => 'website-design-development' ),
			array( 'title' => 'Mobile App Development', 'text' => 'Native and cross-platform apps built for performance, polish and growth on iOS and Android.', 'tags' => 'iOS, Android, Flutter, App Store Launch', 'slug' => 'mobile-app-development' ),
			array( 'title' => 'E-Commerce Solutions', 'text' => 'Fast, conversion-focused online stores with secure payments, smart catalogs and smooth checkout.', 'tags' => 'Online Stores, Payments, Catalog, Conversion', 'slug' => 'e-commerce-solutions' ),
			array( 'title' => 'Custom Software', 'text' => 'Tailor-made systems and platforms that streamline operations and scale with your business.', 'tags' => 'Web Platforms, Dashboards, Integrations, APIs', 'slug' => 'custom-software' ),
			array( 'title' => 'Branding & Identity', 'text' => 'Memorable brand systems, from logo and tone of voice to guidelines that keep every touchpoint consistent.', 'tags' => 'Logo, Brand Guidelines, Messaging, Collateral', 'slug' => 'branding-identity' ),
			array( 'title' => 'Digital Marketing', 'text' => 'Data-driven campaigns, SEO and content that bring the right audience to your product.', 'tags' => 'SEO, Social Media, Paid Ads, Analytics', 'slug' => 'digital-marketing' ),
		);
	}

	protected function register_controls() {
		$this->sec( 'head', __( 'Heading', 'qeema-b2b' ) );
		$this->t( 'pill', __( 'Pill text', 'qeema-b2b' ), 'Our services' );
		$this->t( 'title', __( 'Title (HTML allowed: <strong>)', 'qeema-b2b' ), 'Services Built <strong>for Growth</strong>', 'area' );
		$this->t( 'lead', __( 'Paragraph', 'qeema-b2b' ), 'We create end-to-end digital solutions that help businesses build stronger products, improve operations, and grow with confidence.', 'area' );
		$this->end();

		$this->sec( 'items', __( 'Services (one slide each)', 'qeema-b2b' ) );
		$r = $this->rep();
		$r->add_control( 'title', array( 'label' => __( 'Title', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Service', 'label_block' => true ) );
		$r->add_control( 'text', array( 'label' => __( 'Description', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => '' ) );
		$r->add_control( 'tags', array( 'label' => __( 'Tags (comma separated)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '', 'label_block' => true ) );
		$r->add_control( 'link', array( 'label' => __( 'Page link', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '' ) ) );
		$this->ma( $r, 'image', __( 'Illustration for this service (optional; empty = shared illustration)', 'qeema-b2b' ) );
		$default = array();
		foreach ( self::items() as $it ) {
			$default[] = array(
				'title' => $it['title'],
				'text'  => $it['text'],
				'tags'  => $it['tags'],
				'link'  => array( 'url' => home_url( '/services/' . $it['slug'] . '/' ) ),
			);
		}
		$this->add_control(
			'items',
			array(
				'label'       => __( 'Services', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => $default,
				'title_field' => '{{{ title }}}',
			)
		);
		$this->m( 'art', __( 'Shared illustration (used when a service has no image of its own)', 'qeema-b2b' ), 'img/service-illustration.webp' );
		$this->t( 'art_alt', __( 'Shared illustration - alt text (empty = decorative)', 'qeema-b2b' ), '' );
		$this->end();

		$this->style_all(
			'.services',
			array(
				array( 'title', __( 'Heading title', 'qeema-b2b' ), '.services__head .title' ),
				array( 'lead', __( 'Heading paragraph', 'qeema-b2b' ), '.services__head .lead' ),
				array( 'stitle', __( 'Service title', 'qeema-b2b' ), '.sslide h3' ),
				array( 'stext', __( 'Service description', 'qeema-b2b' ), '.sslide p' ),
				array( 'tag', __( 'Tags', 'qeema-b2b' ), '.sslide__tags .tag' ),
			)
		);

		$this->style_sec( 'qb_style_tags', __( 'Tag chips', 'qeema-b2b' ) );
		$this->add_control(
			'tag_bg',
			array(
				'label'     => __( 'Tag background', 'qeema-b2b' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sslide__tags .tag' => 'background-color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'tag_border',
			array(
				'label'     => __( 'Tag border colour', 'qeema-b2b' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sslide__tags .tag' => 'border-color: {{VALUE}};' ),
			)
		);
		$this->end();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = (array) $s['items'];
		?>
		<section class="services" id="services" data-qb="wheel" aria-label="<?php echo esc_attr( wp_strip_all_tags( $s['title'] ) ); ?>">
			<div class="services__head">
				<div class="services__head-left" data-reveal="left">
					<?php echo $this->el( 'span', 'pill', 'pill pill--grey', esc_html( $s['pill'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo $this->el( 'h2', 'title', 'title', $this->kses( $s['title'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<div class="lead-row services__lead" data-reveal="right">
					<?php echo $this->el( 'p', 'lead', 'lead', esc_html( $s['lead'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>

			<div class="services__track">
				<div class="services__panel" data-active="0">
					<div class="wheel">
						<div class="wheel__ring" aria-hidden="true"></div>
						<ol class="wheel__list">
							<?php foreach ( $items as $i => $it ) : ?>
								<li class="wheel__item" data-i="<?php echo (int) $i; ?>"><button type="button"><span class="wheel__dot"></span><span class="wheel__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span></button></li>
							<?php endforeach; ?>
						</ol>
					</div>

					<div class="services__slides" aria-live="polite">
						<?php
						foreach ( $items as $i => $it ) {
							$h    = esc_html( $it['title'] );
							$tags = array_filter( array_map( 'trim', explode( ',', (string) $it['tags'] ) ) );
							echo '<article class="sslide' . ( 0 === $i ? ' is-active' : '' ) . '" data-i="' . (int) $i . '">';
							$inner = ! empty( $it['link']['url'] ) ? $this->a( $it['link'], 'sl' . $i, '', $h ) : $h;
							echo $this->el( 'h3', $this->rk( 'title', 'items', $i ), '', $inner ); // phpcs:ignore WordPress.Security.EscapeOutput
							echo $this->el( 'p', $this->rk( 'text', 'items', $i ), '', esc_html( $it['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
							echo '<div class="sslide__tags">';
							foreach ( $tags as $t ) {
								echo '<span class="tag">' . esc_html( $t ) . '</span>';
							}
							echo '</div></article>';
						}
						?>
					</div>

					<div class="services__art"<?php echo empty( $s['art_alt'] ) ? ' aria-hidden="true"' : ''; ?>>
						<?php
						echo $this->img( $s['art'], array( 'alt' => $s['art_alt'], 'class' => 'services__art-shared' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput
						foreach ( $items as $i => $it ) {
							if ( ! empty( $it['image']['url'] ) ) {
								echo $this->img( $it['image'], array( 'alt' => isset( $it['image_alt'] ) ? $it['image_alt'] : '', 'class' => 'services__art-i is-off', 'data-i' => (int) $i ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput
							}
						}
						?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
