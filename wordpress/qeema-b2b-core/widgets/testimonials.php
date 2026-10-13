<?php
/**
 * Testimonials: heading and a vertical slider (photo, name, project, quote, stars).
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Testimonials extends QB_Widget_Base {

	public function get_name() {
		return 'qb-testimonials';
	}

	public function get_title() {
		return __( 'Testimonials Slider', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	public function get_style_depends() {
		return array( 'qb-home' );
	}

	protected function register_controls() {
		$this->sec( 'head', __( 'Heading', 'qeema-b2b' ) );
		$this->t( 'pill', __( 'Pill text', 'qeema-b2b' ), 'Testimonials' );
		$this->t( 'title', __( 'Title (HTML allowed: <strong>)', 'qeema-b2b' ), 'We give your project a <strong>stronger foundation</strong> for success', 'area' );
		$this->m( 'blob', __( 'Background blob', 'qeema-b2b' ), 'svg/testimonial-blob.svg' );
		$this->m( 'comma', __( 'Quote mark', 'qeema-b2b' ), 'svg/comma.svg' );
		$this->m( 'star_icon', __( 'Star icon', 'qeema-b2b' ), 'svg/icon.svg' );
		$this->m( 'play_icon', __( 'Play icon', 'qeema-b2b' ), 'svg/play.svg' );
		$this->t( 'prev_label', __( 'Previous button - accessible label', 'qeema-b2b' ), 'Previous testimonial' );
		$this->t( 'next_label', __( 'Next button - accessible label', 'qeema-b2b' ), 'Next testimonial' );
		$this->end();

		$this->sec( 'items', __( 'Testimonials', 'qeema-b2b' ) );
		$r = $this->rep();
		$this->ma( $r, 'photo', __( 'Photo', 'qeema-b2b' ), 'img/testimonial-heba.webp' );
		$r->add_control( 'name', array( 'label' => __( 'Name', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Client Name' ) );
		$r->add_control( 'project', array( 'label' => __( 'Project', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Project name' ) );
		$r->add_control( 'quote', array( 'label' => __( 'Quote (new line = line break)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => '' ) );
		$r->add_control( 'stars', array( 'label' => __( 'Stars (0-5)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::NUMBER, 'default' => 4, 'min' => 0, 'max' => 5 ) );
		$r->add_control( 'play', array( 'label' => __( 'Show play icon on photo', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ) );
		$r->add_control( 'video_url', array( 'label' => __( 'Video link (play button opens it)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '' ), 'placeholder' => 'https://' ) );
		$placeholder = 'Placeholder testimonial. Replace with a real client quote about the quality, communication and delivery of the project.';
		$mk          = function ( $name, $project, $quote ) {
			return array( 'photo' => qb_media_default( 'img/testimonial-heba.webp' ), 'name' => $name, 'project' => $project, 'quote' => $quote, 'stars' => 4, 'play' => 'yes' );
		};
		$this->add_control(
			'items',
			array(
				'label'       => __( 'Slides', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => array(
					$mk( 'Heba Yasser', 'Project name', "one of the best detecated and proffisional team i've worked with, in last hallwoen campaign they deliver to us B.Tech the campaign in short notice with great quality and no delays I really appreciate the commitment to deadlines, high quality and their followup is just amazing\nhighly recommended" ),
					$mk( 'Client Name', 'Project name', $placeholder ),
					$mk( 'Client Name', 'Project name', $placeholder ),
					$mk( 'Client Name', 'Project name', $placeholder ),
				),
				'title_field' => '{{{ name }}}',
			)
		);
		$this->end();

		$this->style_all(
			'.testi',
			array(
				array( 'pill', __( 'Pill', 'qeema-b2b' ), '.testi__head .pill' ),
				array( 'title', __( 'Title', 'qeema-b2b' ), '.testi__head .title' ),
				array( 'name', __( 'Client name', 'qeema-b2b' ), '.tslide__card h3' ),
				array( 'proj', __( 'Project name', 'qeema-b2b' ), '.tslide__project' ),
				array( 'quote', __( 'Quote', 'qeema-b2b' ), '.tslide__quote' ),
			)
		);

		$this->style_sec( 'qb_style_nav', __( 'Arrows & dots', 'qeema-b2b' ) );
		$ctl = array(
			'arrow_color'     => array( __( 'Arrow icon colour', 'qeema-b2b' ), '.testi__arrow', 'color' ),
			'arrow_bg'        => array( __( 'Arrow background', 'qeema-b2b' ), '.testi__arrow', 'background' ),
			'arrow_hover'     => array( __( 'Arrow hover background', 'qeema-b2b' ), '.testi__arrow:hover', 'background' ),
			'arrow_border'    => array( __( 'Arrow border colour', 'qeema-b2b' ), '.testi__arrow', 'border-color' ),
			'dot_color'       => array( __( 'Dot colour', 'qeema-b2b' ), '.testi__dots button', 'background' ),
			'dot_active'      => array( __( 'Active dot colour', 'qeema-b2b' ), '.testi__dots button.is-active', 'background' ),
		);
		foreach ( $ctl as $id => $c ) {
			$this->add_control(
				$id,
				array(
					'label'     => $c[0],
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} ' . $c[1] => $c[2] . ': {{VALUE}};' ),
				)
			);
		}
		$this->end();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = (array) $s['items'];
		$star  = $this->img( $s['star_icon'], array( 'alt' => '' ), 'full' );
		?>
		<section class="testi" data-qb="testi" aria-label="<?php echo esc_attr( wp_strip_all_tags( $s['title'] ) ); ?>">
			<div class="testi__bg" aria-hidden="true">
				<?php echo $this->img( $s['blob'], array( 'class' => 'testi__blob' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->img( $s['comma'], array( 'class' => 'testi__comma' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
			<div class="testi__inner">
				<div class="testi__head" data-reveal>
					<?php echo $this->el( 'span', 'pill', 'pill pill--grey', esc_html( $s['pill'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo $this->el( 'h2', 'title', 'title', $this->kses( $s['title'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
				<div class="testi__stage" data-active="0">
					<?php foreach ( $items as $i => $it ) : ?>
						<article class="tslide<?php echo 0 === $i ? ' is-active' : ''; ?>" data-i="<?php echo (int) $i; ?>">
							<div class="tslide__photo">
								<?php echo $this->img( $it['photo'], array( 'alt' => ! empty( $it['photo_alt'] ) ? $it['photo_alt'] : $it['name'], 'loading' => 'lazy' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								<?php if ( 'yes' === $it['play'] ) : ?>
									<?php $play = $this->img( $s['play_icon'], array( 'alt' => '' ), 'full' ); ?>
									<?php if ( ! empty( $it['video_url']['url'] ) ) : ?>
										<?php echo $this->a( $it['video_url'], 'play' . $i, 'tslide__play', $play, array( 'aria-label' => sprintf( /* translators: %s: client name */ __( 'Play video: %s', 'qeema-b2b' ), $it['name'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									<?php else : ?>
										<span class="tslide__play" aria-hidden="true"><?php echo $play; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									<?php endif; ?>
								<?php endif; ?>
							</div>
							<div class="tslide__text">
								<div class="tslide__card">
									<div>
										<?php echo $this->el( 'h3', $this->rk( 'name', 'items', $i ), '', esc_html( $it['name'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
										<?php echo $this->el( 'p', $this->rk( 'project', 'items', $i ), 'tslide__project', esc_html( $it['project'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
									</div>
									<?php
									$q = implode( '<br>', array_map( 'esc_html', preg_split( '/\r\n|\r|\n/', (string) $it['quote'] ) ) );
									echo $this->el( 'p', $this->rk( 'quote', 'items', $i ), 'tslide__quote', $q ); // phpcs:ignore WordPress.Security.EscapeOutput
									?>
								</div>
								<?php $n = max( 0, min( 5, (int) $it['stars'] ) ); ?>
								<div class="tslide__stars" role="img" aria-label="<?php echo esc_attr( sprintf( '%d star rating', $n ) ); ?>"><?php echo str_repeat( $star, $n ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
							</div>
						</article>
					<?php endforeach; ?>

					<div class="testi__nav">
						<button type="button" class="testi__arrow testi__arrow--up" aria-label="<?php echo esc_attr( $s['prev_label'] ); ?>"><svg viewBox="0 0 20 20" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12.5 5-5 5 5"/></svg></button>
						<div class="testi__dots" role="tablist" aria-label="Testimonials">
							<?php foreach ( $items as $i => $it ) : ?>
								<button type="button" role="tab" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" aria-label="Testimonial <?php echo (int) ( $i + 1 ); ?>"<?php echo 0 === $i ? ' class="is-active"' : ''; ?>></button>
							<?php endforeach; ?>
						</div>
						<button type="button" class="testi__arrow testi__arrow--down" aria-label="<?php echo esc_attr( $s['next_label'] ); ?>"><svg viewBox="0 0 20 20" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 7.5 5 5 5-5"/></svg></button>
					</div>
				</div>
			</div>
		</section>
		<?php
	}
}
