<?php
/**
 * Service detail hero: breadcrumb, title, intro, tags, buttons, animated artwork (six variants) and strip.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Service_Hero extends QB_Widget_Base {

	public function get_name() {
		return 'qb-service-hero';
	}

	public function get_title() {
		return __( 'Service Hero', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-device-desktop';
	}

	public function get_style_depends() {
		return array( 'qb-service-art' );
	}

	/** Section shown only for the listed artwork variants. */
	private function vsec( $id, $label, $arts ) {
		$this->start_controls_section(
			$id . '_sec',
			array(
				'label'     => $label,
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => array( 'art' => $arts ),
			)
		);
	}

	private static function bar_defaults( $heights ) {
		return array_map(
			function ( $h ) {
				return array( 'height' => $h );
			},
			$heights
		);
	}

	/** Bar heights from a repeater value, as numbers 5..100. */
	private static function heights( $items ) {
		$out = array();
		foreach ( (array) $items as $it ) {
			$out[] = (float) max( 5, min( 100, (float) ( isset( $it['height'] ) ? $it['height'] : 50 ) ) );
		}
		return $out;
	}

	/** SVG polyline points that follow the bar heights inside a $w x $h box. */
	private static function line_points( $hs, $w, $h ) {
		$n   = count( $hs );
		$pts = array();
		foreach ( $hs as $i => $v ) {
			$x     = $n > 1 ? round( $i * $w / ( $n - 1 ) ) : 0;
			$y     = round( max( $h * 0.05, $h * ( 1 - $v / 100 ) ) );
			$pts[] = $x . ',' . $y;
		}
		return implode( ' ', $pts );
	}

	private function bars_repeater( $id, $label, $heights ) {
		$r = $this->rep();
		$r->add_control( 'height', array( 'label' => __( 'Bar height (%)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 50, 'min' => 5, 'max' => 100 ) );
		$this->add_control(
			$id,
			array(
				'label'       => $label,
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => self::bar_defaults( $heights ),
				'title_field' => '{{{ height }}}%',
			)
		);
	}

	protected function register_controls() {
		$this->sec( 'crumb', __( 'Breadcrumb', 'qeema-b2b' ) );
		$this->crumb_controls(
			array(
				array( 'label' => 'Home', 'link' => array( 'url' => home_url( '/' ) ) ),
				array( 'label' => 'Services', 'link' => array( 'url' => home_url( '/services/' ) ) ),
				array( 'label' => 'Website Design & Development', 'link' => array( 'url' => '' ) ),
			)
		);
		$this->end();

		$this->sec( 'text', __( 'Text & buttons', 'qeema-b2b' ) );
		$this->t( 'title', __( 'Title', 'qeema-b2b' ), 'Website Design & Development' );
		$this->t( 'intro', __( 'Intro', 'qeema-b2b' ), 'We design and develop modern, responsive websites that combine strong visual identity with seamless user experiences.', 'area' );
		$this->t( 'tags', __( 'Tags (comma separated)', 'qeema-b2b' ), 'UI Design, UX Design, Corporate Websites, Responsive', 'text' );
		$this->t( 'btn1_text', __( 'Button 1 text', 'qeema-b2b' ), 'Discuss your project' );
		$this->l( 'btn1_link', __( 'Button 1 link', 'qeema-b2b' ), home_url( '/book-a-call/' ) );
		$this->t( 'btn2_text', __( 'Button 2 text (outline)', 'qeema-b2b' ), 'Explore Our Approach' );
		$this->l( 'btn2_link', __( 'Button 2 link', 'qeema-b2b' ), '#approach' );
		$this->end();

		$this->sec( 'art', __( 'Artwork', 'qeema-b2b' ) );
		$this->sel(
			'art',
			__( 'Artwork style', 'qeema-b2b' ),
			array(
				'website'   => __( 'Website (laptop + phone)', 'qeema-b2b' ),
				'mobile'    => __( 'Mobile app (two phones)', 'qeema-b2b' ),
				'ecommerce' => __( 'E-commerce (store + cart)', 'qeema-b2b' ),
				'software'  => __( 'Custom software (dashboard)', 'qeema-b2b' ),
				'branding'  => __( 'Branding (tiles + swatches)', 'qeema-b2b' ),
				'marketing' => __( 'Marketing (growth chart)', 'qeema-b2b' ),
			),
			'website'
		);
		$this->t( 'cap', __( 'Caption', 'qeema-b2b' ), 'One experience. Every screen.' );
		$this->sw( 'show_rings', __( 'Orbit rings behind the artwork', 'qeema-b2b' ), 'yes' );
		$this->ma( $this, 'ring_a', __( 'Orbit ring A', 'qeema-b2b' ), 'svg/orbit-a.svg' );
		$this->ma( $this, 'ring_b', __( 'Orbit ring B', 'qeema-b2b' ), 'svg/orbit-b.svg' );
		$this->end();

		$this->vsec( 'art_laptop', __( 'Artwork: laptop', 'qeema-b2b' ), array( 'website', 'ecommerce', 'software' ) );
		$this->ma( $this, 'laptop_frame', __( 'Laptop frame', 'qeema-b2b' ), 'img/laptop-mockup.webp' );
		$this->end();

		$this->vsec( 'art_screen', __( 'Artwork: laptop screen', 'qeema-b2b' ), array( 'website', 'ecommerce' ) );
		$this->ma( $this, 'laptop_screen', __( 'Laptop screen image', 'qeema-b2b' ), 'img/laptop-screen.jpg' );
		$this->end();

		$this->vsec( 'art_phones', __( 'Artwork: phones', 'qeema-b2b' ), array( 'website', 'mobile' ) );
		$this->ma( $this, 'phone_frame', __( 'Phone frame', 'qeema-b2b' ), 'img/phone-mockup.png' );
		$this->ma( $this, 'phone_a', __( 'Phone screen image', 'qeema-b2b' ), 'img/phone-screen.jpg' );
		$this->end();

		$this->vsec( 'art_phone_b', __( 'Artwork: second phone', 'qeema-b2b' ), array( 'mobile' ) );
		$this->ma( $this, 'phone_b', __( 'Second phone screen image', 'qeema-b2b' ), 'img/project-screenshot1.webp' );
		$this->end();

		$this->vsec( 'art_software', __( 'Artwork: dashboard', 'qeema-b2b' ), array( 'software' ) );
		$this->ma( $this, 'dash_image', __( 'Dashboard image (replaces the drawn dashboard)', 'qeema-b2b' ) );
		$this->bars_repeater( 'dash_bars', __( 'Dashboard bars (used when no dashboard image is set)', 'qeema-b2b' ), array( 42, 64, 50, 78, 58, 90, 70, 96 ) );
		$this->end();

		$this->vsec( 'art_chips', __( 'Artwork: floating labels', 'qeema-b2b' ), array( 'mobile', 'ecommerce', 'software', 'marketing' ) );
		$this->t( 'chip1_a', __( 'Floating label 1 · bold', 'qeema-b2b' ), '' );
		$this->t( 'chip1_b', __( 'Floating label 1 · small', 'qeema-b2b' ), '' );
		$this->t( 'chip2_a', __( 'Floating label 2 · bold', 'qeema-b2b' ), '' );
		$this->t( 'chip2_b', __( 'Floating label 2 · small', 'qeema-b2b' ), '' );
		$this->t( 'chip3_a', __( 'Floating label 3 · badge (inside the blue pill)', 'qeema-b2b' ), '', 'text', array( 'condition' => array( 'art' => 'marketing' ) ) );
		$this->t( 'chip3_b', __( 'Floating label 3 · bold text', 'qeema-b2b' ), '', 'text', array( 'condition' => array( 'art' => 'marketing' ) ) );
		$this->t( 'tick_text', __( 'Tick badge text', 'qeema-b2b' ), '✓', 'text', array( 'condition' => array( 'art' => array( 'ecommerce', 'software' ) ) ) );
		$this->t( 'cart_btn', __( 'Cart button text', 'qeema-b2b' ), 'Checkout', 'text', array( 'condition' => array( 'art' => 'ecommerce' ) ) );
		$this->end();

		$this->vsec( 'art_marketing', __( 'Artwork: growth chart', 'qeema-b2b' ), array( 'marketing' ) );
		$this->t( 'chart_title', __( 'Chart title', 'qeema-b2b' ), 'Organic traffic' );
		$this->bars_repeater( 'chart_bars', __( 'Chart bars (the line follows them)', 'qeema-b2b' ), array( 30, 42, 38, 58, 52, 74, 92 ) );
		$this->end();

		$this->vsec( 'art_brand', __( 'Artwork: branding tiles', 'qeema-b2b' ), array( 'branding' ) );
		$this->t( 'type_big', __( 'Type tile · big letters', 'qeema-b2b' ), 'Aa' );
		$this->t( 'type_small', __( 'Type tile · font name', 'qeema-b2b' ), 'Roboto' );
		$this->t( 'card_url', __( 'Business card text', 'qeema-b2b' ), 'qeematech.net' );
		$this->ma( $this, 'brand_logo', __( 'Brand logo on white tile', 'qeema-b2b' ), 'svg/logo.svg' );
		$this->ma( $this, 'brand_logo_w', __( 'Brand logo on dark card', 'qeema-b2b' ), 'svg/logo-white.svg' );
		foreach ( array( 1 => '#00247e', 2 => '#0c5adb', 3 => '#00cedc', 4 => '#1e1e1e' ) as $n => $col ) {
			$this->add_control( 'sw' . $n, array( 'label' => sprintf( __( 'Swatch %d colour', 'qeema-b2b' ), $n ), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => $col ) );
		}
		$this->end();

		$this->sec( 'strip', __( 'Bottom strip', 'qeema-b2b' ) );
		$this->t( 'strip_l', __( 'Left text', 'qeema-b2b' ), 'A complete website, from idea to launch.' );
		$this->t( 'link1', __( 'Right link 1 text', 'qeema-b2b' ), 'What’s included' );
		$this->l( 'link1_url', __( 'Right link 1 URL', 'qeema-b2b' ), '#included' );
		$this->t( 'link2', __( 'Right link 2 text', 'qeema-b2b' ), 'Our approach' );
		$this->l( 'link2_url', __( 'Right link 2 URL', 'qeema-b2b' ), '#approach' );
		$this->t( 'link3', __( 'Right link 3 text', 'qeema-b2b' ), 'Deliverables' );
		$this->l( 'link3_url', __( 'Right link 3 URL', 'qeema-b2b' ), '#deliverables' );
		$this->end();

		$this->style_all(
			'.sd-hero',
			array(
				array( 'title', __( 'Title', 'qeema-b2b' ), '.sd-hero__copy h1' ),
				array( 'intro', __( 'Intro', 'qeema-b2b' ), '.sd-hero__copy > p' ),
				array( 'tags', __( 'Tags', 'qeema-b2b' ), '.svc-card__tags .tag' ),
				array( 'strip', __( 'Strip text', 'qeema-b2b' ), '.svc-strip p' ),
			)
		);
	}

	/* ---------- artwork variants (pure markup; styles in service-art.css) ---------- */

	private function laptop( $screen_html, $s ) {
		return '<div class="sd-laptop">' . $screen_html
			. $this->img( $s['laptop_frame'], array( 'class' => 'sd-laptop__frame', 'alt' => $s['laptop_frame_alt'], 'width' => '612', 'height' => '612', 'fetchpriority' => 'high' ), 'full' ) . '</div>';
	}

	private function phone( $cls, $media, $alt, $s ) {
		return '<div class="sd-phone ' . esc_attr( $cls ) . '">'
			. $this->img( $s['phone_frame'], array( 'class' => 'sd-phone__frame', 'alt' => $s['phone_frame_alt'], 'width' => '216', 'height' => '459' ), 'full' )
			. $this->img( $media, array( 'class' => 'sd-phone__screen', 'alt' => $alt, 'width' => '194', 'height' => '438' ), 'medium' ) . '</div>';
	}

	private function chip( $cls, $style, $html ) {
		return '<div class="sd-chip ' . esc_attr( $cls ) . '" style="' . esc_attr( $style ) . '">' . $html . '</div>';
	}

	private function bars_html( $hs ) {
		$out = '';
		foreach ( $hs as $h ) {
			$out .= '<i style="--h:' . esc_attr( $h + 0 ) . '%"></i>';
		}
		return $out;
	}

	private function art_html( $s ) {
		$a    = function ( $k ) use ( $s ) {
			return esc_html( $s[ $k ] );
		};
		$tick = '<span class="sd-tick">' . esc_html( '' !== (string) $s['tick_text'] ? $s['tick_text'] : '✓' ) . '</span>';
		$sc   = function ( $m, $alt ) {
			return $this->img( $m, array( 'class' => 'sd-laptop__screen', 'alt' => $alt, 'width' => '436', 'height' => '271', 'fetchpriority' => 'high' ), 'large' );
		};
		switch ( $s['art'] ) {
			case 'mobile':
				return $this->phone( 'sd-phone--a', $s['phone_a'], $s['phone_a_alt'], $s ) . $this->phone( 'sd-phone--b', $s['phone_b'], $s['phone_b_alt'], $s )
					. $this->chip( 'sd-chip--c', 'left:392px;top:70px', '<b>' . $a( 'chip1_a' ) . '</b><span>' . $a( 'chip1_b' ) . '</span>' )
					. $this->chip( 'sd-chip--d', 'left:20px;top:300px', '<span class="sd-dot"></span><b>' . $a( 'chip2_a' ) . '</b><span>' . $a( 'chip2_b' ) . '</span>' );
			case 'ecommerce':
				return $this->laptop( $sc( $s['laptop_screen'], $s['laptop_screen_alt'] ), $s )
					. $this->chip( 'sd-chip--cart', 'left:372px;top:210px', '<b>' . $a( 'chip1_a' ) . '</b><span class="sd-price">' . $a( 'chip1_b' ) . '</span><i class="sd-btn">' . $a( 'cart_btn' ) . '</i>' )
					. $this->chip( 'sd-chip--c', 'left:18px;top:70px', $tick . '<b>' . $a( 'chip2_a' ) . '</b>' );
			case 'software':
				if ( ! empty( $s['dash_image']['url'] ) || ! empty( $s['dash_image']['id'] ) ) {
					$dash = $this->img( $s['dash_image'], array( 'class' => 'sd-laptop__screen', 'alt' => $s['dash_image_alt'], 'width' => '436', 'height' => '271' ), 'large' );
				} else {
					$hs   = self::heights( $s['dash_bars'] );
					$pts  = ( $hs === array( 42.0, 64.0, 50.0, 78.0, 58.0, 90.0, 70.0, 96.0 ) ) ? '0,50 28,40 56,44 86,26 116,32 150,14 200,6' : self::line_points( $hs, 200, 60 );
					$dash = '<div class="sd-laptop__screen sd-dash" aria-hidden="true"><span class="sd-dash__side"></span><span class="sd-dash__kpi sd-dash__kpi--1"></span><span class="sd-dash__kpi sd-dash__kpi--2"></span><span class="sd-dash__kpi sd-dash__kpi--3"></span>'
						. '<span class="sd-dash__bars">' . $this->bars_html( $hs ) . '</span>'
						. '<svg class="sd-dash__line" viewBox="0 0 200 60" preserveAspectRatio="none"><polyline pathLength="1" points="' . esc_attr( $pts ) . '" /></svg></div>';
				}
				return $this->laptop( $dash, $s )
					. $this->chip( 'sd-chip--c', 'left:380px;top:96px', '<span class="sd-dot"></span><b>' . $a( 'chip1_a' ) . '</b><span>' . $a( 'chip1_b' ) . '</span>' )
					. $this->chip( 'sd-chip--d', 'left:30px;top:300px', $tick . '<b>' . $a( 'chip2_a' ) . '</b><span>' . $a( 'chip2_b' ) . '</span>' );
			case 'branding':
				$sw = '';
				foreach ( array( 1, 2, 3, 4 ) as $n ) {
					$sw .= '<i style="--c:' . esc_attr( $s[ 'sw' . $n ] ) . '"></i>';
				}
				return '<div class="sd-tile sd-tile--logo">' . $this->img( $s['brand_logo'], array( 'alt' => $s['brand_logo_alt'], 'width' => '150', 'height' => '76' ), 'medium' ) . '</div>'
					. '<div class="sd-tile sd-tile--type"><b>' . $a( 'type_big' ) . '</b><span>' . $a( 'type_small' ) . '</span></div>'
					. '<div class="sd-tile sd-tile--card">' . $this->img( $s['brand_logo_w'], array( 'alt' => $s['brand_logo_w_alt'], 'width' => '120', 'height' => '61' ), 'medium' ) . '<span>' . $a( 'card_url' ) . '</span></div>'
					. '<div class="sd-swatches">' . $sw . '</div>';
			case 'marketing':
				$hs  = self::heights( $s['chart_bars'] );
				$pts = ( $hs === array( 30.0, 42.0, 38.0, 58.0, 52.0, 74.0, 92.0 ) ) ? '0,100 50,88 100,92 150,66 200,72 250,36 300,12' : self::line_points( $hs, 300, 120 );
				return '<div class="sd-chart" aria-hidden="true"><span class="sd-chart__title">' . $a( 'chart_title' ) . '</span>'
					. '<span class="sd-chart__bars">' . $this->bars_html( $hs ) . '</span>'
					. '<svg class="sd-chart__line" viewBox="0 0 300 120" preserveAspectRatio="none"><polyline pathLength="1" points="' . esc_attr( $pts ) . '" /></svg></div>'
					. $this->chip( 'sd-chip--c', 'left:330px;top:44px', '<b>' . $a( 'chip1_a' ) . '</b><span>' . $a( 'chip1_b' ) . '</span>' )
					. $this->chip( 'sd-chip--d', 'left:10px;top:326px', '<b>' . $a( 'chip2_a' ) . '</b><span>' . $a( 'chip2_b' ) . '</span>' )
					. $this->chip( 'sd-chip--e', 'left:372px;top:340px', '<span class="sd-tick">' . $a( 'chip3_a' ) . '</span><b>' . $a( 'chip3_b' ) . '</b>' );
			default:
				return $this->laptop( $sc( $s['laptop_screen'], $s['laptop_screen_alt'] ), $s ) . $this->phone( '', $s['phone_a'], $s['phone_a_alt'], $s );
		}
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$tags = array_filter( array_map( 'trim', explode( ',', (string) $s['tags'] ) ) );
		if ( in_array( $s['art'], array( 'website', 'mobile' ), true ) ) {
			include QB_PATH . 'inc/phone-clip.php';
		}
		?>
		<section class="sd-hero" aria-label="<?php echo esc_attr( $s['title'] ); ?>">
			<?php echo $this->crumb_html( $s['crumb'], 'crumb rise-in', '.05s', true ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<div class="sd-hero__row">
				<div class="sd-hero__copy">
					<?php echo $this->el( 'h1', 'title', 'rise-in', esc_html( $s['title'] ), 'none', array( 'style' => '--d:.1s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo $this->el( 'p', 'intro', 'rise-in', esc_html( $s['intro'] ), 'basic', array( 'style' => '--d:.2s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<div class="svc-card__tags rise-in" style="--d:.3s"><?php foreach ( $tags as $t ) { echo '<span class="tag">' . esc_html( $t ) . '</span>'; } ?></div>
					<div class="sd-hero__actions rise-in" style="--d:.4s">
						<?php echo $this->a( $s['btn1_link'], 'b1', 'btn', esc_html( $s['btn1_text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo $this->a( $s['btn2_link'], 'b2', 'btn btn--outline', '<span>' . esc_html( $s['btn2_text'] ) . '</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</div>
				<div class="sd-art sd-art--<?php echo esc_attr( $s['art'] ); ?> rise-in" style="--d:.25s" aria-hidden="true">
					<?php if ( 'yes' === $s['show_rings'] ) : ?>
						<?php echo $this->img( $s['ring_a'], array( 'class' => 'sd-art__rings', 'alt' => $s['ring_a_alt'] ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo $this->img( $s['ring_b'], array( 'class' => 'sd-art__rings sd-art__rings--b', 'alt' => $s['ring_b_alt'] ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php endif; ?>
					<?php echo $this->art_html( $s ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo $this->el( 'span', 'cap', 'sd-art__cap', esc_html( $s['cap'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
			<div class="svc-strip rise-in" style="--d:.5s">
				<?php echo $this->el( 'p', 'strip_l', '', esc_html( $s['strip_l'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<p><?php echo $this->a( $s['link1_url'], 'sl1', '', esc_html( $s['link1'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> &nbsp;/&nbsp; <?php echo $this->a( $s['link2_url'], 'sl2', '', esc_html( $s['link2'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> &nbsp;/&nbsp; <?php echo $this->a( $s['link3_url'], 'sl3', '', esc_html( $s['link3'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			</div>
		</section>
		<?php
	}
}
