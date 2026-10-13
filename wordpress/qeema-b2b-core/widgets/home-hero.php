<?php
/**
 * Home hero: animated title, format toolbar, three-card fan, statement and CTA rows.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Home_Hero extends QB_Widget_Base {

	/** Fan layout per card (matches the Figma positions). */
	private static $fan = array(
		array( 'dx' => '-289.68px', 'dy' => '30.29px', 'r' => '-14deg', 'k' => '1' ),
		array( 'dx' => '0px', 'dy' => '0px', 'r' => '0deg', 'k' => '.5' ),
		array( 'dx' => '293.12px', 'dy' => '21.37px', 'r' => '10deg', 'k' => '-1' ),
	);

	public function get_name() {
		return 'qb-home-hero';
	}

	public function get_title() {
		return __( 'Home Hero', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_style_depends() {
		return array( 'qb-home' );
	}

	protected function register_controls() {
		$this->sec( 'title', __( 'Title', 'qeema-b2b' ) );
		$this->t( 'line1', __( 'Title line 1', 'qeema-b2b' ), 'We Create' );
		$this->t( 'line2', __( 'Title line 2 (boxed)', 'qeema-b2b' ), 'Digital Products' );
		$this->sw( 'toolbar', __( 'Show the text format toolbar', 'qeema-b2b' ), 'yes' );
		$this->sw( 'show_ticks', __( 'Show the left decorative lines', 'qeema-b2b' ), 'yes' );
		$this->sw( 'show_glow', __( 'Show the ambient glow background', 'qeema-b2b' ), 'yes' );
		$this->end();

		$this->sec( 'cards', __( 'Card fan (3 cards)', 'qeema-b2b' ) );
		$r = $this->rep();
		$r->add_control( 'image', array( 'label' => __( 'Image', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => qb_media_default( 'img/hero-card-build.webp' ) ) );
		$r->add_control( 'title', array( 'label' => __( 'Title', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Card title', 'label_block' => true ) );
		$r->add_control( 'text', array( 'label' => __( 'Description', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => '' ) );
		$r->add_control( 'alt', array( 'label' => __( 'Image alt text', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
		$this->add_control(
			'cards',
			array(
				'label'       => __( 'Cards (left, centre, right)', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => array(
					array( 'image' => qb_media_default( 'img/hero-card-build.webp' ), 'title' => 'Build Your Product', 'text' => 'Turn your idea into a website, mobile app, or custom platform—from planning to launch.', 'alt' => 'Floating phones, wireframes and laptop assembling into a product' ),
					array( 'image' => qb_media_default( 'img/hero-card-team.webp' ), 'title' => 'Extend Your Team', 'text' => 'Add development expertise to your team and keep your projects moving.', 'alt' => 'Interlocking puzzle pieces forming one team' ),
					array( 'image' => qb_media_default( 'img/hero-card-improve.webp' ), 'title' => 'Improve Your Software', 'text' => 'Upgrade existing systems, resolve technical issues, and develop new features.', 'alt' => 'Code editor and dashboard with gears orbiting an upgrade' ),
				),
				'title_field' => '{{{ title }}}',
				'max_items'   => 3,
			)
		);
		$this->m( 'rings', __( 'Rings behind the cards', 'qeema-b2b' ), 'svg/hero-rings.svg' );
		$this->end();

		$this->sec( 'statement', __( 'Statement', 'qeema-b2b' ) );
		$this->t( 'big1', __( 'Big text, line 1', 'qeema-b2b' ), 'We Shape' );
		$this->t( 'big2', __( 'Big text, line 2', 'qeema-b2b' ), 'Bold Ideas' );
		$this->t( 'lead', __( 'Paragraph', 'qeema-b2b' ), 'Strategy, design and technology come together to create digital experiences that move businesses forward.', 'area' );
		$this->end();

		$this->sec( 'ctas', __( 'Buttons', 'qeema-b2b' ) );
		$this->sw( 'show_cta1', __( 'Show button 1', 'qeema-b2b' ), 'yes' );
		$this->t( 'cta1_text', __( 'Button 1 text', 'qeema-b2b' ), 'Order your project' );
		$this->l( 'cta1_link', __( 'Button 1 link', 'qeema-b2b' ), home_url( '/book-a-call/' ) );
		$this->sw( 'show_cta2', __( 'Show button 2', 'qeema-b2b' ), 'yes' );
		$this->t( 'cta2_text', __( 'Button 2 text (outline)', 'qeema-b2b' ), 'Company Profile' );
		$this->l( 'cta2_link', __( 'Button 2 link', 'qeema-b2b' ), home_url( '/about-us/' ) );
		$this->end();

		// background / padding / margin go on the outer section, not on the scaled 1440px stage
		$this->style_all(
			'.hero',
			array(
				array( 'title', __( 'Title', 'qeema-b2b' ), '.hero__title-line, .hero__title-box' ),
				array( 'cardtitle', __( 'Card titles', 'qeema-b2b' ), '.tcard__title' ),
				array( 'carddesc', __( 'Card descriptions', 'qeema-b2b' ), '.tcard__desc' ),
				array( 'big', __( 'Big statement', 'qeema-b2b' ), '.hero__big' ),
				array( 'lead', __( 'Paragraph', 'qeema-b2b' ), '.hero__lead .lead' ),
			)
		);
	}

	private function toolbar() {
		?>
		<div class="ftool" role="toolbar" aria-label="Hero title formatting">
			<div class="ftool__wrap">
				<button type="button" class="ftool__btn ftool__btn--heading" aria-haspopup="listbox" aria-expanded="false">
					<span>Heading 1</span>
					<svg class="ftool__chev" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M5.333 6.667 7.529 8.862a.6.6 0 0 0 .942 0l2.196-2.195" stroke="currentColor" stroke-width="1.17" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<ul class="ftool__menu" role="listbox" aria-label="Text size" hidden>
					<li role="option" tabindex="-1" data-size="h1" aria-selected="true">Heading 1</li>
					<li role="option" tabindex="-1" data-size="h2" aria-selected="false">Heading 2</li>
					<li role="option" tabindex="-1" data-size="h3" aria-selected="false">Heading 3</li>
					<li role="option" tabindex="-1" data-size="p" aria-selected="false">Paragraph</li>
				</ul>
			</div>
			<span class="ftool__div" aria-hidden="true"></span>
			<button type="button" class="ftool__icon" data-style="bold" aria-pressed="false" aria-label="Bold"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6c0 1.104.895 2 2 2h2.666a2.667 2.667 0 0 0 0-5.334H6A1.333 1.333 0 0 0 4 4.666V6Z" stroke="currentColor" stroke-width="1.17" stroke-linecap="square" stroke-linejoin="round"/><path d="M8.666 8h.667a2.667 2.667 0 0 1 0 5.334H6A1.333 1.333 0 0 1 4 12V8" stroke="currentColor" stroke-width="1.17" stroke-linecap="square" stroke-linejoin="round"/></svg></button>
			<button type="button" class="ftool__icon" data-style="italic" aria-pressed="false" aria-label="Italic"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6.666 2.666h6M9.667 2.667 6.334 13.333M3.334 13.333h6" stroke="currentColor" stroke-width="1.17" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
			<button type="button" class="ftool__icon" data-style="underline" aria-pressed="false" aria-label="Underline"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 14h8M4 2.667V8a4 4 0 0 0 8 0V2.667" stroke="currentColor" stroke-width="1.17" stroke-linecap="round"/></svg></button>
			<span class="ftool__div" aria-hidden="true"></span>
			<div class="ftool__wrap">
				<button type="button" class="ftool__btn ftool__btn--color" aria-haspopup="true" aria-expanded="false" aria-label="Text colour">
					<svg class="ftool__a" width="14" height="16" viewBox="0 0 14 16" aria-hidden="true"><path d="M0 16 5.517 0H8.4L14 16h-2.551L8.441 7.087a35 35 0 0 1-.777-2.545A52 52 0 0 1 6.689 1.02h.498a65 65 0 0 1-.965 3.544A56 56 0 0 1 5.486 7.087L2.551 16H0Zm2.997-4.209V9.75h8.006v2.041H2.997Z" fill="currentColor"/><rect class="ftool__a-bar" x="0" y="14.5" width="14" height="1.5" rx=".75"/></svg>
					<svg class="ftool__chev" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M5.333 6.667 7.529 8.862a.6.6 0 0 0 .942 0l2.196-2.195" stroke="currentColor" stroke-width="1.17" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<div class="ftool__swatches" role="group" aria-label="Text colour" hidden>
					<button type="button" style="--c:#1e1e1e" data-color="default" aria-label="Default" class="is-active"></button>
					<button type="button" style="--c:#00247e" data-color="navy" aria-label="Navy"></button>
					<button type="button" style="--c:#0c5adb" data-color="blue" aria-label="Blue"></button>
					<button type="button" style="--c:#00cedc" data-color="cyan" aria-label="Cyan"></button>
					<button type="button" style="--c:linear-gradient(90deg,#00247e,#0c5adb)" data-color="gradient" aria-label="Brand gradient"></button>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="hero" data-qb="hero" aria-label="Introduction">
			<div class="hero__stage">
				<?php if ( 'yes' === $s['show_glow'] ) : ?>
				<div class="hero__glows" aria-hidden="true">
					<div class="glow-field glow-field--full"></div>
					<div class="glow-drift"></div>
					<div class="hero__band"></div>
					<span class="hero__vline hero__vline--l"></span>
					<span class="hero__vline hero__vline--r"></span>
				</div>
				<?php endif; ?>
				<?php if ( 'yes' === $s['show_ticks'] ) : ?>
				<div class="hero__ticks" aria-hidden="true">
					<span class="tick tick--blue" style="top:617px;--x:116px"></span>
					<span class="tick" style="top:633px;--x:98px"></span>
					<span class="tick" style="top:649px;--x:69px"></span>
					<span class="tick" style="top:665px;--x:47px"></span>
				</div>
				<?php endif; ?>

				<h1 class="hero__title" data-size="h1">
					<span class="hero__title-line"><?php echo $this->el( 'span', 'line1', 'rise', esc_html( $s['line1'] ), 'none', array( 'style' => '--d:.1s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="hero__title-box rise" style="--d:.28s"><?php echo $this->el( 'span', 'line2', '', esc_html( $s['line2'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</h1>

				<?php
				if ( 'yes' === $s['toolbar'] ) {
					$this->toolbar();
				}
				echo $this->img( $s['rings'], array( 'class' => 'hero__rings', 'aria-hidden' => 'true' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput
				?>

				<div class="fan">
					<?php
					foreach ( array_slice( (array) $s['cards'], 0, 3 ) as $i => $c ) {
						$f   = self::$fan[ $i ];
						$alt = isset( $c['alt'] ) ? $c['alt'] : '';
						?>
						<article class="tcard tcard--<?php echo (int) ( $i + 1 ); ?>" style="--dx:<?php echo esc_attr( $f['dx'] ); ?>;--dy:<?php echo esc_attr( $f['dy'] ); ?>;--r:<?php echo esc_attr( $f['r'] ); ?>;--k:<?php echo esc_attr( $f['k'] ); ?>;--i:<?php echo (int) $i; ?>">
							<div class="tcard__float">
								<div class="tcard__wrap">
									<div class="tcard__bg"><?php echo $this->img( $c['image'], array( 'alt' => '' ), 'medium' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
									<div class="tcard__photo"><?php echo $this->img( $c['image'], array( 'alt' => $alt, 'fetchpriority' => 'high' ), 'medium' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
									<div class="tcard__body">
										<?php
										echo $this->el( 'h3', $this->rk( 'title', 'cards', $i ), 'tcard__title', esc_html( $c['title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
										echo '<hr>';
										echo $this->el( 'p', $this->rk( 'text', 'cards', $i ), 'tcard__desc', esc_html( $c['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
										?>
									</div>
								</div>
							</div>
						</article>
						<?php
					}
					?>
				</div>

				<div class="hero__statement" data-reveal>
					<p class="hero__big hero__big--1"><span class="hero__dot">.</span><?php echo $this->el( 'span', 'big1', '', esc_html( $s['big1'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<p class="hero__big hero__big--2"><?php echo $this->el( 'span', 'big2', '', esc_html( $s['big2'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
					<div class="lead-row hero__lead">
						<?php echo $this->el( 'p', 'lead', 'lead', esc_html( $s['lead'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				</div>

				<?php if ( 'yes' === $s['show_cta1'] ) : ?>
				<div class="hero__cta hero__cta--1" data-reveal="right"><span class="cta-line"></span><?php echo $this->a( $s['cta1_link'], 'cta1', 'btn', esc_html( $s['cta1_text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php endif; ?>
				<?php if ( 'yes' === $s['show_cta2'] ) : ?>
				<div class="hero__cta hero__cta--2" data-reveal="right" style="--d:.15s"><span class="cta-line"></span><?php echo $this->a( $s['cta2_link'], 'cta2', 'btn btn--outline', '<span>' . esc_html( $s['cta2_text'] ) . '</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
