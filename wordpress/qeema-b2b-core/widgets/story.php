<?php
/**
 * "Our story": heading, intro, mission / vision rows and the QT artwork.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Story extends QB_Widget_Base {

	public function get_name() {
		return 'qb-story';
	}

	public function get_title() {
		return __( 'Our Story', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-text-area';
	}

	public function get_style_depends() {
		return array( 'qb-about' );
	}

	protected function register_controls() {
		$this->sec( 'text', __( 'Text', 'qeema-b2b' ) );
		$this->t( 'pill', __( 'Pill text', 'qeema-b2b' ), 'Our story' );
		$this->t( 'title', __( 'Title (HTML allowed: <strong>)', 'qeema-b2b' ), 'We build trust through a <strong>clear vision</strong> and <strong>a strong message</strong>', 'area' );
		$this->t( 'lead', __( 'Intro paragraph', 'qeema-b2b' ), 'Qeema Tech is your digital partner for success, leveraging extensive experience to deliver balanced tech solutions that combine quality, development, and a true understanding of market needs.', 'area' );
		$this->end();

		$this->sec( 'mv', __( 'Mission & vision', 'qeema-b2b' ) );
		$r = $this->rep();
		$this->ma( $r, 'icon', __( 'Icon', 'qeema-b2b' ), 'svg/mission.svg' );
		$r->add_control( 'title', array( 'label' => __( 'Title', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Title' ) );
		$r->add_control( 'text', array( 'label' => __( 'Text', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => '' ) );
		$this->add_control(
			'mv',
			array(
				'label'       => __( 'Rows', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => array(
					array( 'icon' => qb_media_default( 'svg/mission.svg' ), 'title' => 'Our Mission', 'text' => 'Our goal is to build a long-term relationship with our clients by providing reliable services and tangible results that help them grow and continuously strengthen their digital presence.' ),
					array( 'icon' => qb_media_default( 'svg/vision.svg' ), 'title' => 'Our Vision', 'text' => 'Our vision is to provide advanced technology services that enhance the value of our clients\' businesses, with continuous development of our capabilities and resources to achieve the highest level of quality and innovation.' ),
				),
				'title_field' => '{{{ title }}}',
			)
		);
		$this->m( 'art', __( 'Artwork', 'qeema-b2b' ), 'svg/story-qt.svg' );
		$this->t( 'art_alt', __( 'Artwork alt text', 'qeema-b2b' ), 'Qeematech logo with a chess rook' );
		$this->end();

		$this->style_sec( 'story_img_style', __( 'Images', 'qeema-b2b' ) );
		$this->img_style( 'mvicon', __( 'Mission / vision icons', 'qeema-b2b' ), '.story__mv img' );
		$this->add_responsive_control( 'mvicon_hh', array( 'label' => __( 'Height', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 10, 'max' => 600 ) ), 'selectors' => array( '{{WRAPPER}} .story__mv img' => 'height: {{SIZE}}{{UNIT}};' ) ) );
		$this->img_style( 'artimg', __( 'Artwork', 'qeema-b2b' ), '.story__art' );
		$this->end();

		$this->style_all(
			'.story',
			array(
				array( 'title', __( 'Title', 'qeema-b2b' ), '.story__title' ),
				array( 'lead', __( 'Intro', 'qeema-b2b' ), '.story__lead p' ),
				array( 'mvt', __( 'Row titles', 'qeema-b2b' ), '.story__mv h3' ),
				array( 'mvp', __( 'Row text', 'qeema-b2b' ), '.story__mv p' ),
			)
		);
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="story" aria-label="<?php echo esc_attr( wp_strip_all_tags( $s['title'] ) ); ?>">
			<div class="story__text">
				<?php echo $this->el( 'span', 'pill', 'pill pill--grey', esc_html( $s['pill'] ), 'none', array( 'data-reveal' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->el( 'h2', 'title', 'title story__title', $this->kses( $s['title'] ), 'basic', array( 'data-reveal' => '', 'style' => '--d:.08s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="lead-row story__lead" data-reveal style="--d:.16s">
					<?php echo $this->el( 'p', 'lead', '', esc_html( $s['lead'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<ul class="story__mv">
					<?php foreach ( (array) $s['mv'] as $i => $row ) : ?>
						<li data-reveal style="--d:.<?php echo (int) ( 2 + $i ); ?>s">
							<?php echo $this->img( $row['icon'], array( 'alt' => $row['icon_alt'], 'width' => '93', 'height' => '93' ), 'medium' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<div>
								<?php echo $this->el( 'h3', $this->rk( 'title', 'mv', $i ), '', esc_html( $row['title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php echo $this->el( 'p', $this->rk( 'text', 'mv', $i ), '', esc_html( $row['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php echo $this->img( $s['art'], array( 'class' => 'story__art', 'alt' => $s['art_alt'], 'width' => '476', 'height' => '392', 'data-reveal' => 'right' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</section>
		<?php
	}
}
