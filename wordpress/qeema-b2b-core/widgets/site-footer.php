<?php
/**
 * Site footer: headline, intro, CTA, social icons and three link columns.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Site_Footer extends QB_Widget_Base {

	public function get_name() {
		return 'qb-site-footer';
	}

	public function get_title() {
		return __( 'Site Footer', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-footer';
	}

	public function get_style_depends() {
		return array( 'qb-base' );
	}

	/** Repeater for "text + optional link" lines. */
	private function lines_repeater( $id, $label, $default ) {
		$r = $this->rep();
		$r->add_control( 'text', array( 'label' => __( 'Text', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Text' ) );
		$r->add_control( 'link', array( 'label' => __( 'Link (optional)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '' ) ) );
		$this->add_control(
			$id,
			array(
				'label'       => $label,
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => $default,
				'title_field' => '{{{ text }}}',
			)
		);
	}

	protected function register_controls() {
		$this->sec( 'top', __( 'Headline & call-to-action', 'qeema-b2b' ) );
		$this->t( 'h1', __( 'Headline, line 1', 'qeema-b2b' ), 'Let’s Work' );
		$this->t( 'h2', __( 'Headline, line 2', 'qeema-b2b' ), 'Together' );
		$this->t(
			'intro',
			__( 'Intro text (new line = line break)', 'qeema-b2b' ),
			"Building digital products, platforms, and experiences that help businesses move forward.\nOur company is one of the leading firms in website development and design in Egypt and the Arab world.",
			'area'
		);
		$this->t( 'cta_text', __( 'Button text', 'qeema-b2b' ), 'Book a call' );
		$this->l( 'cta_link', __( 'Button link', 'qeema-b2b' ), home_url( '/book-a-call/' ) );
		$this->end();

		$this->sec( 'brand', __( 'Logo & social icons', 'qeema-b2b' ) );
		$this->ma( $this, 'logo', __( 'Logo image (leave empty for the built-in white logo)', 'qeema-b2b' ), 'svg/logo-white.svg', 'Qeematech' );
		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => __( 'Logo width', 'qeema-b2b' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 60, 'max' => 500 ) ),
				'selectors'  => array( '{{WRAPPER}} .site-footer__logo' => 'width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->sw( 'show_glow', __( 'Show corner glow', 'qeema-b2b' ), 'yes' );
		$this->m( 'glow', __( 'Corner glow image', 'qeema-b2b' ), 'svg/ellipse.svg' );
		$this->t( 'logo_label', __( 'Logo accessible label', 'qeema-b2b' ), 'Qeematech' );
		$r = $this->rep();
		$r->add_control( 'label', array( 'label' => __( 'Name', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Facebook' ) );
		$r->add_control( 'icon', array( 'label' => __( 'Icon', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => qb_media_default( 'svg/facebook.svg' ) ) );
		$r->add_control( 'link', array( 'label' => __( 'Link', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '' ) ) );
		$this->add_control(
			'social',
			array(
				'label'       => __( 'Social links', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => array(
					array( 'label' => 'Facebook', 'icon' => qb_media_default( 'svg/facebook.svg' ), 'link' => array( 'url' => '#' ) ),
					array( 'label' => 'Instagram', 'icon' => qb_media_default( 'svg/instagram.svg' ), 'link' => array( 'url' => '#' ) ),
					array( 'label' => 'Behance', 'icon' => qb_media_default( 'svg/behance.svg' ), 'link' => array( 'url' => '#' ) ),
					array( 'label' => 'WhatsApp', 'icon' => qb_media_default( 'svg/whatsapp.svg' ), 'link' => array( 'url' => '#' ) ),
				),
				'title_field' => '{{{ label }}}',
			)
		);
		$this->end();

		$this->sec( 'col1', __( 'Column 1', 'qeema-b2b' ) );
		$this->t( 'c1_title', __( 'Title', 'qeema-b2b' ), 'Important Links' );
		$this->lines_repeater(
			'c1_items',
			__( 'Links', 'qeema-b2b' ),
			array(
				array( 'text' => 'Home', 'link' => array( 'url' => home_url( '/' ) ) ),
				array( 'text' => 'About Us', 'link' => array( 'url' => home_url( '/about-us/' ) ) ),
				array( 'text' => 'Our Services', 'link' => array( 'url' => home_url( '/services/' ) ) ),
				array( 'text' => 'Contact Us', 'link' => array( 'url' => home_url( '/book-a-call/' ) ) ),
			)
		);
		$this->end();

		$this->sec( 'col2', __( 'Column 2', 'qeema-b2b' ) );
		$this->t( 'c2_title', __( 'Title', 'qeema-b2b' ), 'Address' );
		$this->lines_repeater(
			'c2_items',
			__( 'Lines', 'qeema-b2b' ),
			array(
				array( 'text' => '3 Makram Ebeid, Nasr City, Cairo', 'link' => array( 'url' => '' ) ),
				array( 'text' => '59 Iran Street, off Mohi El Din Abu El Ezz, Dokki, Giza', 'link' => array( 'url' => '' ) ),
				array( 'text' => '51 Al Nahda Street, Maadi, Cairo', 'link' => array( 'url' => '' ) ),
			)
		);
		$this->end();

		$this->sec( 'col3', __( 'Column 3', 'qeema-b2b' ) );
		$this->t( 'c3_title', __( 'Title', 'qeema-b2b' ), 'Call Us' );
		$this->lines_repeater(
			'c3_items',
			__( 'Lines', 'qeema-b2b' ),
			array(
				array( 'text' => '01012804721', 'link' => array( 'url' => 'tel:01012804721' ) ),
				array( 'text' => 'Sales@qeematech.net', 'link' => array( 'url' => 'mailto:Sales@qeematech.net' ) ),
				array( 'text' => 'Support@qeematech.net', 'link' => array( 'url' => 'mailto:Support@qeematech.net' ) ),
			)
		);
		$this->end();

		$this->style_all(
			'.site-footer',
			array(
				array( 'headline1', __( 'Headline, line 1', 'qeema-b2b' ), '.site-footer__headline span:first-child' ),
				array( 'headline2', __( 'Headline, line 2', 'qeema-b2b' ), '.site-footer__headline span:last-child' ),
				array( 'intro', __( 'Intro text', 'qeema-b2b' ), '.site-footer__intro p' ),
				array( 'coltitle', __( 'Column titles', 'qeema-b2b' ), '.site-footer__col h3' ),
				array( 'collinks', __( 'Column links / lines', 'qeema-b2b' ), '.site-footer__col li, .site-footer__col a' ),
			)
		);
	}

	private function col( $s, $n, $extra_class = '' ) {
		echo '<div class="site-footer__col' . esc_attr( $extra_class ) . '">';
		echo $this->el( 'h3', 'c' . $n . '_title', '', esc_html( $s[ 'c' . $n . '_title' ] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<ul>';
		foreach ( (array) $s[ 'c' . $n . '_items' ] as $i => $it ) {
			$key = $this->rk( 'text', 'c' . $n . '_items', $i );
			$txt = $this->el( 'span', $key, '', esc_html( $it['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			if ( ! empty( $it['link']['url'] ) ) {
				echo '<li>' . $this->a( $it['link'], 'c' . $n . '_a' . $i, '', esc_html( $it['text'] ) ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
			} else {
				echo '<li>' . $txt . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}
		echo '</ul></div>';
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $s['intro'] ) ) );
		?>
		<footer class="site-footer">
			<?php if ( 'yes' === $s['show_glow'] ) : ?>
				<?php echo $this->img( $s['glow'], array( 'class' => 'site-footer__glow', 'alt' => '', 'aria-hidden' => 'true' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php endif; ?>
			<div class="site-footer__inner">
				<div class="site-footer__top">
					<p class="site-footer__headline" data-reveal><?php echo $this->el( 'span', 'h1', '', esc_html( $s['h1'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo $this->el( 'span', 'h2', '', esc_html( $s['h2'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<div class="site-footer__intro" data-reveal style="--d:.1s">
						<p><?php echo wp_kses_post( implode( '<br>', array_map( 'esc_html', $lines ) ) ); ?></p>
						<?php echo $this->a( $s['cta_link'], 'cta', 'btn btn--light', esc_html( $s['cta_text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</div>
				<div class="site-footer__cols">
					<div class="site-footer__brand">
						<?php if ( empty( $s['logo']['url'] ) ) : ?>
							<div class="site-footer__logo" role="img" aria-label="<?php echo esc_attr( $s['logo_label'] ); ?>"></div>
						<?php else : ?>
							<div class="site-footer__logo site-footer__logo--img"><?php echo $this->img( $s['logo'], array( 'alt' => ! empty( $s['logo_alt'] ) ? $s['logo_alt'] : $s['logo_label'], 'class' => 'site-footer__logo-img' ), 'medium' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
						<?php endif; ?>
						<div class="site-footer__social">
							<?php
							foreach ( (array) $s['social'] as $i => $it ) {
								$inner = $this->img( $it['icon'], array( 'alt' => '' ), 'thumbnail' );
								echo $this->a( $it['link'], 'soc' . $i, '', $inner, array( 'aria-label' => $it['label'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
							}
							?>
						</div>
					</div>
					<?php
					$this->col( $s, 1 );
					$this->col( $s, 2, ' site-footer__col--addr' );
					$this->col( $s, 3 );
					?>
				</div>
			</div>
		</footer>
		<?php
	}
}
