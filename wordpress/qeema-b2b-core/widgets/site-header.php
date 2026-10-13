<?php
/**
 * Site header: logo, navigation, call-to-action, mobile menu.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Site_Header extends QB_Widget_Base {

	public function get_name() {
		return 'qb-site-header';
	}

	public function get_title() {
		return __( 'Site Header', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-header';
	}

	public function get_style_depends() {
		return array( 'qb-base' );
	}

	protected function register_controls() {
		$this->sec( 'logo', __( 'Logo', 'qeema-b2b' ) );
		$this->ma( $this, 'logo', __( 'Logo image (leave empty for the built-in Qeematech logo)', 'qeema-b2b' ), 'svg/logo.svg', 'Qeematech' );
		$this->add_responsive_control(
			'logo_width',
			array(
				'label'      => __( 'Logo width', 'qeema-b2b' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array( 'px' => array( 'min' => 40, 'max' => 400 ) ),
				'selectors'  => array( '{{WRAPPER}} .site-logo' => 'width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->sw( 'show_progress', __( 'Scroll progress bar', 'qeema-b2b' ), 'yes' );
		$this->l( 'logo_link', __( 'Logo link', 'qeema-b2b' ), home_url( '/' ) );
		$this->t( 'logo_label', __( 'Logo accessible label', 'qeema-b2b' ), 'Qeematech home' );
		$this->end();

		$this->sec( 'nav', __( 'Navigation', 'qeema-b2b' ) );
		$r = $this->rep();
		$r->add_control( 'label', array( 'label' => __( 'Label', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Link' ) );
		$r->add_control( 'link', array( 'label' => __( 'Link', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '' ) ) );
		$this->add_control(
			'items',
			array(
				'label'       => __( 'Menu items', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => array(
					array( 'label' => 'Home', 'link' => array( 'url' => home_url( '/' ) ) ),
					array( 'label' => 'About Us', 'link' => array( 'url' => home_url( '/about-us/' ) ) ),
					array( 'label' => 'Services', 'link' => array( 'url' => home_url( '/services/' ) ) ),
					array( 'label' => 'Our Work', 'link' => array( 'url' => home_url( '/our-work/' ) ) ),
				),
				'title_field' => '{{{ label }}}',
			)
		);
		$this->end();

		$this->sec( 'cta', __( 'Call-to-action button', 'qeema-b2b' ) );
		$this->t( 'cta_text', __( 'Button text', 'qeema-b2b' ), 'Book a call' );
		$this->l( 'cta_link', __( 'Button link', 'qeema-b2b' ), home_url( '/book-a-call/' ) );
		$this->t( 'toggle_label', __( 'Mobile menu button label', 'qeema-b2b' ), 'Toggle menu' );
		$this->end();

		$this->style_all(
			'.site-header',
			array(
				array( 'nav', __( 'Menu links', 'qeema-b2b' ), '.site-nav a' ),
				array( 'cta', __( 'Button', 'qeema-b2b' ), '.site-header__cta' ),
			)
		);

		$this->style_sec( 'qb_style_scrolled', __( 'Scrolled state', 'qeema-b2b' ) );
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'scrolled_bg',
				'label'    => __( 'Background after scrolling', 'qeema-b2b' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .site-header.is-scrolled',
			)
		);
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<header class="site-header" id="siteHeader">
			<div class="site-header__inner">
				<?php
				echo $this->a( // phpcs:ignore WordPress.Security.EscapeOutput
					$s['logo_link'],
					'logo_a',
					empty( $s['logo']['url'] ) ? 'site-logo' : 'site-logo site-logo--img',
					empty( $s['logo']['url'] ) ? '<span class="site-logo__mark"></span>' : $this->img( $s['logo'], array( 'class' => 'site-logo__img', 'alt' => $s['logo_alt'] ), 'medium' ),
					array( 'aria-label' => $s['logo_label'] )
				);
				?>
				<nav class="site-nav" id="siteNav" aria-label="<?php esc_attr_e( 'Main', 'qeema-b2b' ); ?>">
					<?php
					foreach ( (array) $s['items'] as $i => $item ) {
						$extra = array();
						if ( qb_is_current_url( isset( $item['link']['url'] ) ? $item['link']['url'] : '' ) ) {
							$extra['aria-current'] = 'page';
						}
						echo $this->a( $item['link'], 'nav_' . $i, '', esc_html( $item['label'] ), $extra ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</nav>
				<?php echo $this->a( $s['cta_link'], 'cta_a', 'btn site-header__cta', esc_html( $s['cta_text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<button class="site-header__toggle" id="navToggle" aria-label="<?php echo esc_attr( $s['toggle_label'] ); ?>" aria-expanded="false" aria-controls="siteNav"><span></span></button>
			</div>
		</header>
		<?php if ( 'yes' === $s['show_progress'] ) : ?>
		<div class="scroll-progress" aria-hidden="true"></div>
		<?php endif; ?>
		<?php
	}
}
