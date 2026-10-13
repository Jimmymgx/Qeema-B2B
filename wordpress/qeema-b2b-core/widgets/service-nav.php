<?php
/**
 * Previous / next service links at the bottom of a service page.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Service_Nav extends QB_Widget_Base {

	public function get_name() {
		return 'qb-service-nav';
	}

	public function get_title() {
		return __( 'Previous / Next Service', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-navigation-horizontal';
	}

	public function get_style_depends() {
		return array( 'qb-service-art' );
	}

	protected function register_controls() {
		$this->sec( 'prev', __( 'Previous', 'qeema-b2b' ) );
		$this->t( 'prev_label', __( 'Small label', 'qeema-b2b' ), 'Previous service' );
		$this->t( 'prev_title', __( 'Service name', 'qeema-b2b' ), 'Digital Marketing' );
		$this->l( 'prev_link', __( 'Link', 'qeema-b2b' ), home_url( '/services/digital-marketing/' ) );
		$this->t( 'prev_arrow', __( 'Arrow icon text (empty hides it)', 'qeema-b2b' ), '←' );
		$this->ma( $this, 'prev_image', __( 'Image (optional)', 'qeema-b2b' ) );
		$this->end();

		$this->sec( 'next', __( 'Next', 'qeema-b2b' ) );
		$this->t( 'next_label', __( 'Small label', 'qeema-b2b' ), 'Next service' );
		$this->t( 'next_title', __( 'Service name', 'qeema-b2b' ), 'Mobile App Development' );
		$this->l( 'next_link', __( 'Link', 'qeema-b2b' ), home_url( '/services/mobile-app-development/' ) );
		$this->t( 'next_arrow', __( 'Arrow icon text (empty hides it)', 'qeema-b2b' ), '→' );
		$this->ma( $this, 'next_image', __( 'Image (optional)', 'qeema-b2b' ) );
		$this->end();

		$this->style_sec( 'nav_style', __( 'Arrows & images', 'qeema-b2b' ) );
		$this->add_control( 'arrow_color', array( 'label' => __( 'Arrow colour', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .sd-next__arrow' => 'color: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'arrow_size', array( 'label' => __( 'Arrow size', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 10, 'max' => 80 ) ), 'selectors' => array( '{{WRAPPER}} .sd-next__arrow' => 'font-size: {{SIZE}}{{UNIT}};' ) ) );
		$this->img_style( 'nimg', __( 'Images', 'qeema-b2b' ), '.sd-next__img' );
		$this->end();

		$this->style_all(
			'.sd-next',
			array(
				array( 'label', __( 'Small labels', 'qeema-b2b' ), '.sd-next__link span' ),
				array( 'name', __( 'Service names', 'qeema-b2b' ), '.sd-next__link b' ),
			)
		);
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<nav class="sd-next" aria-label="<?php esc_attr_e( 'More services', 'qeema-b2b' ); ?>">
			<?php
			foreach ( array( 'prev', 'next' ) as $d ) {
				$arrow = '' !== (string) $s[ $d . '_arrow' ] ? '<i class="sd-next__arrow" aria-hidden="true">' . esc_html( $s[ $d . '_arrow' ] ) . '</i>' : '';
				$image = $this->img( $s[ $d . '_image' ], array( 'class' => 'sd-next__img', 'alt' => $s[ $d . '_image_alt' ], 'loading' => 'lazy' ), 'medium' );
				$inner = ( 'prev' === $d ? $arrow : '' ) . $image . '<span>' . esc_html( $s[ $d . '_label' ] ) . '</span><b>' . esc_html( $s[ $d . '_title' ] ) . '</b>' . ( 'next' === $d ? $arrow : '' );
				echo $this->a( $s[ $d . '_link' ], $d, 'sd-next__link sd-next__link--' . $d, $inner ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
			?>
		</nav>
		<?php
	}
}
