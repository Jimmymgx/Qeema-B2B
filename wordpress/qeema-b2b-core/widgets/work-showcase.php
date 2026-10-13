<?php
/**
 * "Our Work" showcase: heading, optional category chips, a row of project cards and a "see more" button.
 * Cards come from the Projects post type. Used on Home, Services and project pages.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Work_Showcase extends QB_Widget_Base {

	public function get_name() {
		return 'qb-work-showcase';
	}

	public function get_title() {
		return __( 'Work Showcase (projects)', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_style_depends() {
		return array( 'qb-home' );
	}

	protected function register_controls() {
		$this->sec( 'head', __( 'Heading', 'qeema-b2b' ) );
		$this->sel(
			'variant',
			__( 'Layout', 'qeema-b2b' ),
			array(
				'home'     => __( 'Centred (Home)', 'qeema-b2b' ),
				'services' => __( 'Left aligned (Services pages)', 'qeema-b2b' ),
				'project'  => __( 'Centred (project pages)', 'qeema-b2b' ),
			),
			'home'
		);
		$this->t( 'pill', __( 'Pill text', 'qeema-b2b' ), 'Our Work' );
		$this->t( 'title', __( 'Title (HTML allowed: <strong>)', 'qeema-b2b' ), 'Projects That Turn Ideas Into <strong>Impact</strong>', 'area' );
		$this->t( 'lead', __( 'Paragraph', 'qeema-b2b' ), 'A selection of digital products, platforms, and experiences we’ve built for ambitious businesses across different industries.', 'area' );
		$this->sw( 'chips', __( 'Show category filter chips', 'qeema-b2b' ), 'yes' );
		$this->end();

		$this->sec( 'cards', __( 'Project cards', 'qeema-b2b' ) );
		$this->num( 'count', __( 'Number of projects', 'qeema-b2b' ), 6, 1, 24 );
		$terms = array( '' => __( 'All categories', 'qeema-b2b' ) );
		$list  = get_terms( array( 'taxonomy' => 'qb_project_cat', 'hide_empty' => false ) );
		if ( $list && ! is_wp_error( $list ) ) {
			foreach ( $list as $t ) {
				$terms[ $t->slug ] = $t->name;
			}
		}
		$this->sel( 'only_cat', __( 'Only this category', 'qeema-b2b' ), $terms, '' );
		$this->t( 'link_text', __( 'Card link text', 'qeema-b2b' ), 'View Project' );
		$this->add_control(
			'cards_info',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					/* translators: %s: link to the Projects list */
					__( 'Each card’s title, text, image and category come from the project itself. %s', 'qeema-b2b' ),
					'<a href="' . esc_url( admin_url( 'edit.php?post_type=qb_project' ) ) . '" target="_blank" rel="noopener">' . esc_html__( 'Edit Projects', 'qeema-b2b' ) . '</a>'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);
		$this->m( 'fallback_image', __( 'Fallback image (used when a project has no Featured image)', 'qeema-b2b' ), 'img/project-screenshot.webp' );
		$this->m( 'arrow_icon', __( 'Card link arrow icon', 'qeema-b2b' ), 'svg/arrow-right.svg' );
		$this->end();

		$this->sec( 'more', __( '"See more" button', 'qeema-b2b' ) );
		$this->sw( 'show_more', __( 'Show button', 'qeema-b2b' ), 'yes' );
		$this->t( 'more_text', __( 'Text', 'qeema-b2b' ), 'See More' );
		$this->l( 'more_link', __( 'Link', 'qeema-b2b' ), home_url( '/our-work/' ) );
		$this->end();

		$this->style_all(
			'section',
			array(
				array( 'pill', __( 'Pill', 'qeema-b2b' ), '.pill' ),
				array( 'title', __( 'Title', 'qeema-b2b' ), '.work__head .title' ),
				array( 'lead', __( 'Paragraph', 'qeema-b2b' ), '.work__lead' ),
				array( 'ctitle', __( 'Card title', 'qeema-b2b' ), '.pcard h3' ),
				array( 'ctext', __( 'Card text', 'qeema-b2b' ), '.pcard p' ),
			)
		);
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$class   = array( 'home' => 'work', 'services' => 'svc-work', 'project' => 'proj-more' );
		$section = isset( $class[ $s['variant'] ] ) ? $class[ $s['variant'] ] : 'work';
		$args    = array( 'posts_per_page' => max( 1, (int) $s['count'] ) );
		if ( ! empty( $s['only_cat'] ) ) {
			$args['tax_query'] = array( array( 'taxonomy' => 'qb_project_cat', 'field' => 'slug', 'terms' => $s['only_cat'] ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		}
		$opts     = array(
			'fallback' => ! empty( $s['fallback_image']['url'] ) ? $s['fallback_image']['url'] : '',
			'arrow'    => ! empty( $s['arrow_icon']['url'] ) ? $s['arrow_icon']['url'] : '',
		);
		$projects = qb_get_projects( $args );
		$terms    = get_terms( array( 'taxonomy' => 'qb_project_cat', 'hide_empty' => false ) );
		?>
		<section class="<?php echo esc_attr( $section ); ?>" data-qb="work" aria-label="<?php echo esc_attr( wp_strip_all_tags( $s['title'] ) ); ?>">
			<div class="work__head">
				<?php echo $this->el( 'span', 'pill', 'pill pill--soft', esc_html( $s['pill'] ), 'none', array( 'data-reveal' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->el( 'h2', 'title', 'title', $this->kses( $s['title'] ), 'basic', array( 'data-reveal' => '', 'style' => '--d:.08s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->el( 'p', 'lead', 'lead work__lead', esc_html( $s['lead'] ), 'basic', array( 'data-reveal' => '', 'style' => '--d:.16s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( 'yes' === $s['chips'] && $terms && ! is_wp_error( $terms ) ) : ?>
					<div class="work__tags" role="group" aria-label="<?php esc_attr_e( 'Filter projects', 'qeema-b2b' ); ?>" data-reveal style="--d:.24s">
						<?php foreach ( $terms as $t ) : ?>
							<button type="button" class="tag" data-filter="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?></button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="work__grid">
				<?php
				if ( $projects ) {
					foreach ( $projects as $n => $p ) {
						echo qb_project_card_html( $p, $s['link_text'], ( $n % 3 ) * 0.1, false, $opts ); // phpcs:ignore WordPress.Security.EscapeOutput
					}
				} elseif ( $this->is_edit() ) {
					echo '<p style="grid-column:1/-1;text-align:center;color:#717171">' . esc_html__( 'No projects yet. Add some under Projects in the WordPress menu.', 'qeema-b2b' ) . '</p>';
				}
				?>
			</div>
			<?php if ( 'yes' === $s['show_more'] ) : ?>
				<?php echo $this->a( $s['more_link'], 'more', 'btn work__more', esc_html( $s['more_text'] ), array( 'data-reveal' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php endif; ?>
		</section>
		<?php
	}
}
