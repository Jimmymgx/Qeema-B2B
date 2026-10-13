<?php
/**
 * Project details hero: breadcrumb, project name + tagline, phone or laptop mockup and two buttons.
 * On a Project page the name, tagline and device default to that project's own details.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Project_Hero extends QB_Widget_Base {

	public function get_name() {
		return 'qb-project-hero';
	}

	public function get_title() {
		return __( 'Project Hero', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-device-mobile';
	}

	public function get_style_depends() {
		return array( 'qb-project' );
	}

	protected function register_controls() {
		$this->sec( 'text', __( 'Text', 'qeema-b2b' ) );
		$this->t( 'crumb_root', __( 'Breadcrumb · first item', 'qeema-b2b' ), 'Our Work' );
		$this->l( 'crumb_link', __( 'Breadcrumb · first link', 'qeema-b2b' ), home_url( '/our-work/' ) );
		$this->t( 'crumb_name', __( 'Breadcrumb · current page (empty = project title)', 'qeema-b2b' ), '' );
		$this->t( 'name', __( 'Project name (empty = from the project)', 'qeema-b2b' ), '' );
		$this->sw( 'dot', __( 'Blue dot before the name', 'qeema-b2b' ), 'yes' );
		$this->t( 'tagline', __( 'Tagline (empty = from the project)', 'qeema-b2b' ), '' );
		$this->end();

		$this->sec( 'device', __( 'Mockup', 'qeema-b2b' ) );
		$this->sel( 'device', __( 'Device', 'qeema-b2b' ), array( 'auto' => __( 'From the project', 'qeema-b2b' ), 'phone' => __( 'Phone', 'qeema-b2b' ), 'laptop' => __( 'Laptop', 'qeema-b2b' ) ), 'auto' );
		$this->add_control( 'phone_screen', array( 'label' => __( 'Phone screen image (empty = project Hero screenshot / Featured image)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => array( 'url' => '' ), 'dynamic' => array( 'active' => true ), 'condition' => array( 'device!' => 'laptop' ) ) );
		$this->add_control( 'laptop_screen', array( 'label' => __( 'Laptop screen image (empty = project Hero screenshot / Featured image)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::MEDIA, 'default' => array( 'url' => '' ), 'dynamic' => array( 'active' => true ), 'condition' => array( 'device!' => 'phone' ) ) );
		$this->t( 'screen_alt', __( 'Screen alt text', 'qeema-b2b' ), 'Project screen' );
		$this->ma( $this, 'frame_phone', __( 'Phone frame', 'qeema-b2b' ), 'img/phone-mockup.png' );
		$this->ma( $this, 'frame_laptop', __( 'Laptop frame', 'qeema-b2b' ), 'img/laptop-mockup.webp' );
		$this->end();

		$this->sec( 'ctas', __( 'Buttons', 'qeema-b2b' ) );
		$this->t( 'cta1_text', __( 'Button 1 text', 'qeema-b2b' ), 'Request a Quote' );
		$this->l( 'cta1_link', __( 'Button 1 link', 'qeema-b2b' ), home_url( '/book-a-call/' ) );
		$this->t( 'cta2_text', __( 'Button 2 text (outline)', 'qeema-b2b' ), 'View Project' );
		$this->l( 'cta2_link', __( 'Button 2 link (empty = the project own page; hidden if there is none)', 'qeema-b2b' ), '' );
		$this->end();

		$this->style_all(
			'.proj-hero',
			array(
				array( 'name', __( 'Project name', 'qeema-b2b' ), '.proj-hero__name' ),
			)
		);
		$this->style_sec( 'qb_img_style', __( 'Device images', 'qeema-b2b' ) );
		$this->img_style( 'screen', __( 'Screen image', 'qeema-b2b' ), '.device__screen' );
		$this->img_style( 'frame', __( 'Device frame', 'qeema-b2b' ), '.device__frame' );
		$this->end();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$post = get_post();
		$is_p = $post && 'qb_project' === $post->post_type;
		$edit = $this->is_edit();
		$name = $s['name'] ?: ( $is_p ? ( get_post_meta( $post->ID, '_qb_display_name', true ) ?: $post->post_title ) : ( $edit ? 'Kingo' : '' ) );
		$tag  = $s['tagline'] ?: ( $is_p ? ( get_post_meta( $post->ID, '_qb_tagline', true ) ?: '' ) : ( $edit ? 'Bold Ideas' : '' ) );
		$dev  = $s['device'];
		if ( 'auto' === $dev ) {
			$dev = $is_p ? ( get_post_meta( $post->ID, '_qb_device', true ) ?: 'phone' ) : 'phone';
		}
		$crumb = $s['crumb_name'] ?: ( $is_p ? $post->post_title : __( 'Project name', 'qeema-b2b' ) );
		// Screen: widget image > project "Hero screenshot" > project Featured image > bundled default.
		$auto = array( 'url' => '' );
		if ( $is_p ) {
			$shot = get_post_meta( $post->ID, '_qb_hero_screen', true );
			if ( $shot ) {
				$auto = array( 'id' => (int) attachment_url_to_postid( $shot ), 'url' => $shot );
			} elseif ( has_post_thumbnail( $post ) ) {
				$auto = array( 'id' => get_post_thumbnail_id( $post ), 'url' => (string) get_the_post_thumbnail_url( $post, 'large' ) );
			}
		}
		$screen_for = function ( $own, $default_file ) use ( $auto ) {
			if ( ! empty( $own['url'] ) || ! empty( $own['id'] ) ) {
				return $own;
			}
			return ! empty( $auto['url'] ) ? $auto : qb_media_default( $default_file );
		};
		$cta2 = $s['cta2_link'];
		if ( empty( $cta2['url'] ) && $is_p ) {
			$cta2 = array( 'url' => get_permalink( $post ) );
		}
		include QB_PATH . 'inc/phone-clip.php';
		?>
		<section class="proj-hero" data-qb="project" aria-label="<?php echo esc_attr( $name ); ?>">
			<div class="proj-hero__stage">
				<nav class="crumb rise-in" aria-label="Breadcrumb" style="--d:.05s">
					<ol>
						<li><?php echo $this->a( $s['crumb_link'], 'crumb', '', esc_html( $s['crumb_root'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
						<li aria-current="page"><?php echo esc_html( $crumb ); ?></li>
					</ol>
				</nav>
				<h1 class="proj-hero__brand">
					<span class="proj-hero__name rise-in" style="--d:.15s"><?php echo 'yes' === $s['dot'] ? '<b>.</b>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $name ); ?></span>
					<span class="proj-hero__tag rise-in" style="--d:.3s"><?php echo esc_html( $tag ); ?></span>
				</h1>

				<?php if ( 'laptop' === $dev ) : ?>
					<div class="device device--laptop rise-in" style="--d:.2s">
						<div class="device__float">
							<?php echo $this->img( $screen_for( $s['laptop_screen'], 'img/laptop-screen.jpg' ), array( 'class' => 'device__screen', 'alt' => $s['screen_alt'], 'width' => '436', 'height' => '271' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php echo $this->img( $s['frame_laptop'], array( 'class' => 'device__frame', 'alt' => $s['frame_laptop_alt'], 'width' => '612', 'height' => '612' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
					</div>
				<?php else : ?>
					<div class="device device--phone rise-in" style="--d:.2s">
						<div class="device__float">
							<?php echo $this->img( $s['frame_phone'], array( 'class' => 'device__frame', 'alt' => $s['frame_phone_alt'], 'width' => '216', 'height' => '459' ), 'full' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php echo $this->img( $screen_for( $s['phone_screen'], 'img/phone-screen.jpg' ), array( 'class' => 'device__screen', 'alt' => $s['screen_alt'], 'width' => '194', 'height' => '438' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
					</div>
				<?php endif; ?>

				<div class="proj-cta proj-cta--quote rise-in" style="--d:.5s"><?php echo $this->a( $s['cta1_link'], 'cta1', 'btn', esc_html( $s['cta1_text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="proj-cta__line"></span></div>
				<?php if ( ! empty( $cta2['url'] ) || $edit ) : ?>
				<div class="proj-cta proj-cta--view rise-in" style="--d:.65s"><?php echo $this->a( $cta2, 'cta2', 'btn btn--outline', '<span>' . esc_html( $s['cta2_text'] ) . '</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span class="proj-cta__line"></span></div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}
}
