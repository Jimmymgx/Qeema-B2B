<?php
/**
 * "Trusted by" section: pill, heading and three auto-scrolling logo rows.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Clients_Marquee extends QB_Widget_Base {

	public function get_name() {
		return 'qb-clients-marquee';
	}

	public function get_title() {
		return __( 'Clients Logo Marquee', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_style_depends() {
		return array( 'qb-home' );
	}

	private static function defaults() {
		return array(
			1 => array( array( 'svg/logo-shutterframe-1.svg', 179 ), array( 'svg/logo-convergence.svg', 174 ), array( 'svg/logo-w.svg', 137 ) ),
			2 => array( array( 'img/logo-white-01 (1) (1).png', 164 ), array( 'svg/logo-coreos.svg', 121 ), array( 'svg/logo-shutterframe-2.svg', 167 ) ),
			3 => array( array( 'svg/logo-shutterframe-1.svg', 179 ), array( 'svg/logo-layers-a.svg', 109 ), array( 'svg/logo-layers-b.svg', 170 ) ),
		);
	}

	protected function register_controls() {
		$this->sec( 'head', __( 'Heading', 'qeema-b2b' ) );
		$this->t( 'pill', __( 'Pill text', 'qeema-b2b' ), 'Our Clients' );
		$this->t( 'title', __( 'Title (HTML allowed: <strong>, <br>)', 'qeema-b2b' ), 'Trusted by <strong>world-<br>leading</strong> enterprises', 'area' );
		$this->end();

		$speeds = array( 1 => 38, 2 => 44, 3 => 50 );
		$dirs   = array( 1 => 'l', 2 => 'r', 3 => 'l' );
		foreach ( self::defaults() as $n => $logos ) {
			$this->sec( 'row' . $n, sprintf( __( 'Logo row %d', 'qeema-b2b' ), $n ) );
			$this->num( 'speed' . $n, __( 'Loop duration (seconds, lower = faster)', 'qeema-b2b' ), $speeds[ $n ], 10, 120 );
			$this->sel( 'dir' . $n, __( 'Scroll direction', 'qeema-b2b' ), array( 'l' => __( 'Left', 'qeema-b2b' ), 'r' => __( 'Right', 'qeema-b2b' ) ), $dirs[ $n ] );
			$r = $this->rep();
			$this->ma( $r, 'logo', __( 'Logo', 'qeema-b2b' ) );
			$r->add_control( 'link', array( 'label' => __( 'Link (optional)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '' ) ) );
			$r->add_control( 'width', array( 'label' => __( 'Width (px)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 150, 'min' => 40, 'max' => 400 ) );
			$default = array();
			foreach ( $logos as $l ) {
				$default[] = array( 'logo' => qb_media_default( $l[0] ), 'logo_alt' => '', 'width' => $l[1] );
			}
			$this->add_control(
				'row' . $n,
				array(
					'label'       => __( 'Logos', 'qeema-b2b' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $r->get_controls(),
					'default'     => $default,
					'title_field' => 'Logo · {{{ width }}}px',
				)
			);
			$this->end();
		}

		$this->style_sec( 'qb_style_logos', __( 'Logos', 'qeema-b2b' ) );
		$this->add_control(
			'logo_opacity',
			array(
				'label'     => __( 'Logo opacity', 'qeema-b2b' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array( 'px' => array( 'min' => 0.1, 'max' => 1, 'step' => 0.05 ) ),
				'selectors' => array( '{{WRAPPER}} .marquee__track img' => 'opacity: {{SIZE}};' ),
			)
		);
		$this->end();

		$this->style_all(
			'.clients',
			array(
				array( 'pill', __( 'Pill', 'qeema-b2b' ), '.clients__head .pill' ),
				array( 'title', __( 'Title', 'qeema-b2b' ), '.clients__head .title' ),
			)
		);
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="clients" aria-label="<?php echo esc_attr( wp_strip_all_tags( $s['title'] ) ); ?>">
			<div class="clients__inner">
				<div class="clients__head" data-reveal="left">
					<?php echo $this->el( 'span', 'pill', 'pill', esc_html( $s['pill'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo $this->el( 'h2', 'title', 'title', $this->kses( $s['title'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<div class="clients__logos" aria-label="Client logos" data-reveal="zoom" data-qb="marquee">
					<?php
					foreach ( array( 1, 2, 3 ) as $n ) {
						$dir = 'r' === $s[ 'dir' . $n ] ? 'r' : 'l';
						echo '<div class="marquee marquee--' . esc_attr( $dir ) . '" style="--speed:' . (int) $s[ 'speed' . $n ] . 's"><ul class="marquee__track">';
						foreach ( (array) $s[ 'row' . $n ] as $k => $it ) {
							if ( empty( $it['logo']['url'] ) ) {
								continue;
							}
							$img = $this->img( $it['logo'], array( 'alt' => isset( $it['logo_alt'] ) ? $it['logo_alt'] : '', 'style' => 'width:' . (int) $it['width'] . 'px' ), 'medium' );
							if ( ! empty( $it['link']['url'] ) ) {
								$img = $this->a( $it['link'], 'm' . $n . '_' . $k, '', $img );
							}
							echo '<li>' . $img . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
						}
						echo '</ul></div>';
					}
					?>
				</div>
			</div>
		</section>
		<?php
	}
}
