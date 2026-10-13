<?php
/**
 * About page hero: title, label box, two paragraphs and a row of photos.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_About_Hero extends QB_Widget_Base {

	public function get_name() {
		return 'qb-about-hero';
	}

	public function get_title() {
		return __( 'About Hero', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	public function get_style_depends() {
		return array( 'qb-about' );
	}

	protected function register_controls() {
		$text = 'Our company is one of the leading entities in the development and design of websites in Egypt and the Arab world. We also have strong expertise in digital marketing, web hosting, Android and iPhone app development, as well as implementing systems, control panels, and integrated ERP solutions.';

		$this->sec( 'text', __( 'Text', 'qeema-b2b' ) );
		$this->t( 'title', __( 'Title', 'qeema-b2b' ), 'About Us' );
		$this->t( 'box', __( 'Label box text', 'qeema-b2b' ), 'Get to know us up close' );
		$this->t( 'p1', __( 'Left paragraph', 'qeema-b2b' ), $text, 'area' );
		$this->sw( 'p1_rtl', __( 'Left paragraph is right-to-left (Arabic)', 'qeema-b2b' ), 'yes' );
		$this->t( 'p2', __( 'Right paragraph', 'qeema-b2b' ), $text, 'area' );
		$this->end();

		$this->sec( 'photos', __( 'Photos', 'qeema-b2b' ) );
		$r = $this->rep();
		$r->add_control( 'image', array( 'label' => __( 'Image', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => qb_media_default( 'img/about-office-1.jpg' ) ) );
		$r->add_control( 'alt', array( 'label' => __( 'Alt text', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
		$this->add_control(
			'photos',
			array(
				'label'       => __( 'Photos', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => array(
					array( 'image' => qb_media_default( 'img/about-office-1.jpg' ), 'alt' => 'Qeematech office' ),
					array( 'image' => qb_media_default( 'img/about-office-2.jpg' ), 'alt' => 'Team working at their desks' ),
					array( 'image' => qb_media_default( 'img/about-office-3.jpg' ), 'alt' => 'Team monitoring dashboards' ),
					array( 'image' => qb_media_default( 'img/about-office-1.jpg' ), 'alt' => 'Qeematech office' ),
				),
				'title_field' => '{{{ alt }}}',
			)
		);
		$this->end();

		$this->style_sec( 'photos_style', __( 'Photos', 'qeema-b2b' ) );
		$this->add_responsive_control( 'photo_h', array( 'label' => __( 'Photo height', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 60, 'max' => 600 ) ), 'selectors' => array( '{{WRAPPER}} .about-hero__photos li' => 'height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_responsive_control( 'photo_radius', array( 'label' => __( 'Photo corner radius', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::DIMENSIONS, 'size_units' => array( 'px', '%' ), 'selectors' => array( '{{WRAPPER}} .about-hero__photos li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ) ) );
		$this->add_control( 'photo_fit', array( 'label' => __( 'Image fit', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => array( '' => __( 'Default', 'qeema-b2b' ), 'cover' => 'Cover', 'contain' => 'Contain', 'fill' => 'Fill' ), 'default' => '', 'selectors' => array( '{{WRAPPER}} .about-hero__photos img' => 'object-fit: {{VALUE}};' ) ) );
		$this->add_responsive_control( 'photo_gap', array( 'label' => __( 'Gap between photos', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 0, 'max' => 80 ) ), 'selectors' => array( '{{WRAPPER}} .about-hero__photos' => 'gap: {{SIZE}}{{UNIT}};' ) ) );
		$this->end();

		$this->style_all(
			'.about-hero',
			array(
				array( 'title', __( 'Title', 'qeema-b2b' ), '.about-hero__title' ),
				array( 'box', __( 'Label box', 'qeema-b2b' ), '.about-hero__box' ),
				array( 'p', __( 'Paragraphs', 'qeema-b2b' ), '.about-hero__p' ),
			)
		);
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="about-hero" aria-label="<?php echo esc_attr( $s['title'] ); ?>">
			<?php echo $this->el( 'h1', 'title', 'about-hero__title rise-in', esc_html( $s['title'] ), 'none', array( 'style' => '--d:.05s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<p class="about-hero__box rise-in" style="--d:.2s"><?php echo $this->el( 'span', 'box', '', esc_html( $s['box'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
			<div class="about-hero__cols rise-in" style="--d:.35s">
				<?php
				$rtl = 'yes' === $s['p1_rtl'];
				echo $this->el( 'p', 'p1', 'about-hero__p' . ( $rtl ? ' about-hero__p--rtl' : '' ), esc_html( $s['p1'] ), 'basic', $rtl ? array( 'dir' => 'rtl' ) : array() ); // phpcs:ignore WordPress.Security.EscapeOutput
				?>
				<span class="about-hero__divider" aria-hidden="true"></span>
				<?php echo $this->el( 'p', 'p2', 'about-hero__p', esc_html( $s['p2'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<ul class="about-hero__photos">
				<?php foreach ( (array) $s['photos'] as $i => $p ) : ?>
					<li data-reveal<?php echo $i ? ' style="--d:.' . (int) $i . 's"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo $this->img( $p['image'], array( 'alt' => $p['alt'] ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
				<?php endforeach; ?>
			</ul>
		</section>
		<?php
	}
}
