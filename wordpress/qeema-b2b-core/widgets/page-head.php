<?php
/**
 * Inner-page head: breadcrumb, title, lead, optional category filter chips and contact cards.
 * Used by Our Work and Book a call.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Page_Head extends QB_Widget_Base {

	public function get_name() {
		return 'qb-page-head';
	}

	public function get_title() {
		return __( 'Page Head', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-heading';
	}

	public function get_style_depends() {
		return array( 'qb-inner', 'qb-book' );
	}

	protected function register_controls() {
		$this->sec( 'crumb', __( 'Breadcrumb', 'qeema-b2b' ) );
		$this->crumb_controls(
			array(
				array( 'label' => 'Home', 'link' => array( 'url' => home_url( '/' ) ) ),
				array( 'label' => 'Our Work', 'link' => array( 'url' => '' ) ),
			)
		);
		$this->end();

		$this->sec( 'text', __( 'Text', 'qeema-b2b' ) );
		$this->sel( 'variant', __( 'Style', 'qeema-b2b' ), array( 'default' => __( 'Centred lead (Our Work)', 'qeema-b2b' ), 'book' => __( 'Left lead (Book a call)', 'qeema-b2b' ) ), 'default' );
		$this->t( 'title', __( 'Title (HTML allowed: <strong>)', 'qeema-b2b' ), 'Projects That Turn Ideas Into <strong>Impact</strong>', 'area' );
		$this->t( 'lead', __( 'Paragraph', 'qeema-b2b' ), 'A selection of digital products, platforms, and experiences we’ve built for ambitious businesses across different industries.', 'area' );
		$this->end();

		$this->sec( 'chips', __( 'Category filter chips', 'qeema-b2b' ) );
		$this->sw( 'chips', __( 'Show category chips (they filter the Work Archive on this page)', 'qeema-b2b' ), 'no' );
		$this->end();

		$this->sec( 'contact', __( 'Contact cards', 'qeema-b2b' ) );
		$this->sw( 'show_cards', __( 'Show contact cards', 'qeema-b2b' ), 'no' );
		$r = $this->rep();
		$this->ma( $r, 'icon', __( 'Icon', 'qeema-b2b' ), 'svg/contact-call.svg' );
		$r->add_control( 'text', array( 'label' => __( 'Text', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '' ) );
		$r->add_control( 'link', array( 'label' => __( 'Link', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '' ) ) );
		$this->add_control(
			'cards',
			array(
				'label'       => __( 'Cards', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => array(
					array( 'icon' => qb_media_default( 'svg/contact-call.svg' ), 'text' => '201012804721+', 'link' => array( 'url' => 'tel:+201012804721' ) ),
					array( 'icon' => qb_media_default( 'svg/contact-mail.svg' ), 'text' => 'sales@qeematech.net', 'link' => array( 'url' => 'mailto:sales@qeematech.net' ) ),
					array( 'icon' => qb_media_default( 'svg/contact-location.svg' ), 'text' => '3 Makram Ebeid, Nasr City, Cairo', 'link' => array( 'url' => 'https://maps.google.com/?q=3+Makram+Ebeid,+Nasr+City,+Cairo', 'is_external' => 'on' ) ),
				),
				'title_field' => '{{{ text }}}',
				'condition'   => array( 'show_cards' => 'yes' ),
			)
		);
		$this->end();

		$this->style_sec( 'cards_icon_style', __( 'Contact card icons', 'qeema-b2b' ) );
		$this->img_style( 'cicon', __( 'Icons', 'qeema-b2b' ), '.contact-card img' );
		$this->add_responsive_control( 'cicon_hh', array( 'label' => __( 'Height', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => array( 'px' ), 'range' => array( 'px' => array( 'min' => 10, 'max' => 600 ) ), 'selectors' => array( '{{WRAPPER}} .contact-card img' => 'height: {{SIZE}}{{UNIT}};' ) ) );
		$this->add_control( 'card_bg', array( 'label' => __( 'Card background', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::COLOR, 'selectors' => array( '{{WRAPPER}} .contact-card' => 'background-color: {{VALUE}};' ) ) );
		$this->end();

		$this->style_all(
			'.page-head',
			array(
				array( 'title', __( 'Title', 'qeema-b2b' ), '.title' ),
				array( 'lead', __( 'Paragraph', 'qeema-b2b' ), '.lead' ),
				array( 'chips', __( 'Chips', 'qeema-b2b' ), '.work__tags .tag' ),
				array( 'cards', __( 'Contact cards', 'qeema-b2b' ), '.contact-card span' ),
			)
		);
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$class = 'page-head' . ( 'book' === $s['variant'] ? ' page-head--book' : '' );
		$terms = ( 'yes' === $s['chips'] ) ? get_terms( array( 'taxonomy' => 'qb_project_cat', 'hide_empty' => false ) ) : array();
		?>
		<section class="<?php echo esc_attr( $class ); ?>">
			<?php echo $this->crumb_html( $s['crumb'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php echo $this->el( 'h1', 'title', 'title rise-in', $this->kses( $s['title'] ), 'basic', array( 'style' => '--d:.15s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php echo $this->el( 'p', 'lead', 'lead rise-in', esc_html( $s['lead'] ), 'basic', array( 'style' => '--d:.25s' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $terms && ! is_wp_error( $terms ) ) : ?>
				<div class="work__tags rise-in" data-qb="archive-filter" role="group" aria-label="<?php esc_attr_e( 'Filter projects', 'qeema-b2b' ); ?>" style="--d:.35s">
					<?php foreach ( $terms as $t ) : ?>
						<button type="button" class="tag" data-filter="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<?php if ( 'yes' === $s['show_cards'] ) : ?>
				<ul class="contact-trio rise-in" style="--d:.35s">
					<?php
					foreach ( (array) $s['cards'] as $i => $c ) {
						$inner = $this->img( $c['icon'], array( 'alt' => $c['icon_alt'], 'width' => '60', 'height' => '60' ), 'thumbnail' ) . $this->el( 'span', $this->rk( 'text', 'cards', $i ), '', esc_html( $c['text'] ) );
						echo '<li>' . $this->a( $c['link'], 'card' . $i, 'contact-card', $inner ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput
					}
					?>
				</ul>
			<?php endif; ?>
		</section>
		<?php
	}
}
