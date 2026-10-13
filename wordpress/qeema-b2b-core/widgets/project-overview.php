<?php
/**
 * Project overview: intro paragraphs, a heading and numbered steps.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Project_Overview extends QB_Widget_Base {

	public function get_name() {
		return 'qb-project-overview';
	}

	public function get_title() {
		return __( 'Project Overview', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-editor-list-ol';
	}

	public function get_style_depends() {
		return array( 'qb-project' );
	}

	protected function register_controls() {
		$this->sec( 'intro', __( 'Introduction', 'qeema-b2b' ) );
		$this->t( 'p1', __( 'Paragraph 1', 'qeema-b2b' ), 'A short introduction to the project: what it is, who it is for and the idea behind it. Replace this text with the real project story.', 'area' );
		$this->t( 'p2', __( 'Paragraph 2', 'qeema-b2b' ), 'Describe the goal of the project and the experience it gives to its users, in a few sentences.', 'area' );
		$this->t( 'heading', __( 'Steps heading', 'qeema-b2b' ), 'Main Focus / Mission Statement' );
		$this->ma( $this, 'image', __( 'Overview image (optional)', 'qeema-b2b' ) );
		$this->end();

		$this->sec( 'steps', __( 'Steps', 'qeema-b2b' ) );
		$r = $this->rep();
		$this->ma( $r, 'icon', __( 'Step image / icon (optional)', 'qeema-b2b' ) );
		$r->add_control( 'title', array( 'label' => __( 'Title', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Title', 'label_block' => true ) );
		$r->add_control( 'text', array( 'label' => __( 'Text', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'default' => '' ) );
		$this->add_control(
			'steps',
			array(
				'label'       => __( 'Steps', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => array(
					array( 'title' => 'The Challenge', 'text' => 'Explain the main problem or challenge this project had to solve.' ),
					array( 'title' => 'Implementation Mechanism', 'text' => 'Explain how the Qeema Tech team approached and built the solution.' ),
					array( 'title' => 'Customer journey with us', 'text' => 'Explain how we worked with the client from the first idea to the launch.' ),
				),
				'title_field' => '{{{ title }}}',
			)
		);
		$this->end();

		$this->style_all(
			'.proj-overview',
			array(
				array( 'copy', __( 'Intro paragraphs', 'qeema-b2b' ), '.proj-overview__copy' ),
				array( 'heading', __( 'Steps heading', 'qeema-b2b' ), '.proj-overview__title' ),
				array( 'stitle', __( 'Step title', 'qeema-b2b' ), '.proj-step h3' ),
				array( 'stext', __( 'Step text', 'qeema-b2b' ), '.proj-step p' ),
			)
		);
		$this->style_sec( 'qb_img_style', __( 'Images', 'qeema-b2b' ) );
		$this->img_style( 'oimg', __( 'Overview image', 'qeema-b2b' ), '.proj-overview__img' );
		$this->img_style( 'simg', __( 'Step image / icon', 'qeema-b2b' ), '.proj-step__img' );
		$this->end();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="proj-overview" aria-label="<?php esc_attr_e( 'Project overview', 'qeema-b2b' ); ?>">
			<div class="lead-row proj-overview__lead" data-reveal>
				<div class="proj-overview__copy">
					<?php echo $this->el( 'p', 'p1', '', esc_html( $s['p1'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php echo $this->el( 'p', 'p2', '', esc_html( $s['p2'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			</div>
			<?php if ( ! empty( $s['image']['url'] ) ) : ?>
				<div class="proj-overview__media" data-reveal><?php echo $this->img( $s['image'], array( 'class' => 'proj-overview__img', 'alt' => $s['image_alt'], 'loading' => 'lazy' ), 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
			<?php echo $this->el( 'h2', 'heading', 'proj-overview__title', esc_html( $s['heading'] ), 'none', array( 'data-reveal' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<ol class="proj-steps">
				<?php foreach ( (array) $s['steps'] as $i => $st ) : ?>
					<li class="proj-step" data-reveal<?php echo $i ? ' style="--d:' . ( 0.12 * $i ) . 's"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<span class="proj-step__num" aria-hidden="true"><?php echo (int) ( $i + 1 ); ?></span>
						<div>
							<?php if ( ! empty( $st['icon']['url'] ) ) : ?>
								<?php echo $this->img( $st['icon'], array( 'class' => 'proj-step__img', 'alt' => isset( $st['icon_alt'] ) ? $st['icon_alt'] : '', 'loading' => 'lazy' ), 'medium' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php endif; ?>
							<?php echo $this->el( 'h3', $this->rk( 'title', 'steps', $i ), '', esc_html( $st['title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php echo $this->el( 'p', $this->rk( 'text', 'steps', $i ), '', esc_html( $st['text'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		</section>
		<?php
	}
}
