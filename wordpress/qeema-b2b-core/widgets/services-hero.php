<?php
/**
 * Services page hero: title, promise, buttons, 3D sculpture (image fallback) and strip.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Services_Hero extends QB_Widget_Base {

	public function get_name() {
		return 'qb-services-hero';
	}

	public function get_title() {
		return __( 'Services Hero (3D)', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_style_depends() {
		return array( 'qb-services' );
	}

	public function get_script_depends() {
		return array( 'qb-sculpture' );
	}

	protected function register_controls() {
		$this->sec( 'crumb', __( 'Breadcrumb', 'qeema-b2b' ) );
		$this->crumb_controls(
			array(
				array( 'label' => 'Home', 'link' => array( 'url' => home_url( '/' ) ) ),
				array( 'label' => 'Services', 'link' => array( 'url' => '' ) ),
			)
		);
		$this->end();

		$this->sec( 'text', __( 'Text & buttons', 'qeema-b2b' ) );
		$this->t( 'title1', __( 'Title line 1 (light)', 'qeema-b2b' ), 'Services Built' );
		$this->t( 'title2', __( 'Title line 2 (bold blue)', 'qeema-b2b' ), 'for Growth.' );
		$this->t( 'promise', __( 'Promise paragraph', 'qeema-b2b' ), 'We create end-to-end digital solutions that help businesses build stronger products, improve operations, and grow with confidence.', 'area' );
		$this->t( 'btn1_text', __( 'Button 1 text', 'qeema-b2b' ), 'Order your project' );
		$this->l( 'btn1_link', __( 'Button 1 link', 'qeema-b2b' ), home_url( '/book-a-call/' ) );
		$this->t( 'btn2_text', __( 'Button 2 text (outline)', 'qeema-b2b' ), 'Explore our services' );
		$this->l( 'btn2_link', __( 'Button 2 link', 'qeema-b2b' ), '#services' );
		$this->end();

		$this->sec( 'art', __( 'Artwork', 'qeema-b2b' ) );
		$this->sw( 'three', __( 'Interactive 3D sculpture (draggable)', 'qeema-b2b' ), 'yes' );
		$this->add_control( 'three_note', array( 'type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => esc_html__( 'When the 3D sculpture is on, the 3D object replaces this image after it loads. The image is still shown while loading, on devices without WebGL, or when 3D is switched off.', 'qeema-b2b' ), 'content_classes' => 'elementor-descriptor' ) );
		$this->ma( $this, 'sculpt', __( 'Sculpture image (shown while loading, or if 3D is off)', 'qeema-b2b' ), 'img/growth-sculpture.webp' );
		$this->sw( 'show_orbits', __( 'Orbit rings', 'qeema-b2b' ), 'yes' );
		$this->ma( $this, 'orbit_a', __( 'Orbit ring A', 'qeema-b2b' ), 'svg/orbit-a.svg' );
		$this->ma( $this, 'orbit_b', __( 'Orbit ring B', 'qeema-b2b' ), 'svg/orbit-b.svg' );
		$this->t( 'cap', __( 'Caption', 'qeema-b2b' ), 'STRATEGY × DESIGN × TECHNOLOGY' );
		$this->t( 'hint', __( 'Drag hint', 'qeema-b2b' ), 'Drag to rotate' );
		$this->end();

		$this->sec( 'strip', __( 'Bottom strip', 'qeema-b2b' ) );
		$this->t( 'strip_l', __( 'Left text', 'qeema-b2b' ), 'From the first idea to what comes next.' );
		$this->t( 'strip_r', __( 'Right text', 'qeema-b2b' ), 'Websites  /  Mobile apps  /  E-Commerce  /  SEO  /  Custom software' );
		$this->end();

		$this->style_all(
			'.svc-hero',
			array(
				array( 'title', __( 'Title', 'qeema-b2b' ), '.svc-hero__title' ),
				array( 'promise', __( 'Promise paragraph', 'qeema-b2b' ), '.svc-hero__promise p' ),
				array( 'strip', __( 'Strip text', 'qeema-b2b' ), '.svc-strip p' ),
			)
		);
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="svc-hero" aria-label="<?php echo esc_attr( $s['title1'] . ' ' . $s['title2'] ); ?>">
			<div class="svc-hero__row">
				<div class="svc-hero__copy">
					<?php echo $this->crumb_html( $s['crumb'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<h1 class="svc-hero__title rise-in" style="--d:.1s"><?php echo $this->el( 'span', 'title1', '', esc_html( $s['title1'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <?php echo $this->el( 'strong', 'title2', '', esc_html( $s['title2'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
					<div class="svc-hero__promise rise-in" style="--d:.25s"><?php echo $this->el( 'p', 'promise', '', esc_html( $s['promise'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<div class="svc-hero__actions rise-in" style="--d:.4s">
						<?php echo $this->a( $s['btn1_link'], 'b1', 'btn', esc_html( $s['btn1_text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo $this->a( $s['btn2_link'], 'b2', 'btn btn--outline', '<span>' . esc_html( $s['btn2_text'] ) . '</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</div>
				<div class="svc-hero__art rise-in" style="--d:.2s" aria-hidden="true">
					<?php if ( 'yes' === $s['show_orbits'] ) : ?>
						<?php echo $this->img( $s['orbit_a'], array( 'class' => 'svc-hero__orbit--a', 'alt' => $s['orbit_a_alt'], 'width' => '620', 'height' => '118' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php echo $this->img( $s['orbit_b'], array( 'class' => 'svc-hero__orbit--b', 'alt' => $s['orbit_b_alt'], 'width' => '550', 'height' => '115' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php endif; ?>
					<span class="svc-hero__shadow"></span>
					<div class="svc-hero__sculpt"<?php echo 'yes' === $s['three'] ? ' data-qb="sculpt" data-hint="' . esc_attr( $s['hint'] ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<?php echo $this->img( $s['sculpt'], array( 'alt' => $s['sculpt_alt'], 'width' => '600', 'height' => '510', 'fetchpriority' => 'high' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
					<?php echo $this->el( 'span', 'cap', 'svc-hero__cap', esc_html( $s['cap'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
			<div class="svc-strip rise-in" style="--d:.5s">
				<?php echo $this->el( 'p', 'strip_l', '', esc_html( $s['strip_l'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->el( 'p', 'strip_r', '', esc_html( $s['strip_r'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</section>
		<?php
	}
}
