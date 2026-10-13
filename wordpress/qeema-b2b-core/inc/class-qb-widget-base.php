<?php
/**
 * Shared base for every Qeema B2B widget. It removes the repetition from control
 * registration (content + style tabs) and rendering (inline editing, links, images),
 * so each widget file only describes its own section.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

abstract class QB_Widget_Base extends \Elementor\Widget_Base {

	public function get_categories() {
		return array( 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-site-identity';
	}

	public function get_keywords() {
		return array( 'qeema', 'b2b' );
	}

	/**
	 * Repeater defaults: Elementor only stores a repeater once it has been edited, so a widget that was
	 * created from data (the seeder) would otherwise render nothing. When the key was never saved, use the
	 * control's default list (each item completed with its fields' defaults).
	 */
	public function get_settings_for_display( $setting_key = null ) {
		$s = parent::get_settings_for_display( $setting_key );
		if ( null !== $setting_key ) {
			return $s;
		}
		$raw = (array) $this->get_data( 'settings' );
		foreach ( $this->get_controls() as $id => $c ) {
			if ( empty( $c['type'] ) || 'repeater' !== $c['type'] || array_key_exists( $id, $raw ) ) {
				continue;
			}
			$s[ $id ] = self::complete_repeater( $c );
		}
		return $s;
	}

	/** Default items of a repeater control, each completed with the defaults of its fields. */
	public static function complete_repeater( $control ) {
		$items = array();
		foreach ( (array) ( isset( $control['default'] ) ? $control['default'] : array() ) as $i => $item ) {
			foreach ( (array) $control['fields'] as $f ) {
				if ( isset( $f['name'] ) && ! array_key_exists( $f['name'], $item ) && array_key_exists( 'default', $f ) ) {
					$item[ $f['name'] ] = $f['default'];
				}
			}
			$item['_id'] = substr( md5( $control['name'] . $i ), 0, 7 );
			$items[]     = $item;
		}
		return $items;
	}

	/* ================= CONTENT controls ================= */

	protected function sec( $id, $label ) {
		// section ids get a suffix so they can never clash with a control of the same name (e.g. 'items')
		$this->start_controls_section( $id . '_sec', array( 'label' => $label, 'tab' => Controls_Manager::TAB_CONTENT ) );
	}

	protected function end() {
		$this->end_controls_section();
	}

	/** Text / textarea / WYSIWYG control. $type: text | area | rich. */
	protected function t( $id, $label, $default = '', $type = 'text', $extra = array() ) {
		$map  = array(
			'text' => Controls_Manager::TEXT,
			'area' => Controls_Manager::TEXTAREA,
			'rich' => Controls_Manager::WYSIWYG,
		);
		$args = array_merge(
			array(
				'label'       => $label,
				'type'        => $map[ $type ],
				'default'     => $default,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			),
			$extra
		);
		$this->add_control( $id, $args );
	}

	protected function m( $id, $label, $file = '' ) {
		$this->add_control(
			$id,
			array(
				'label'   => $label,
				'type'    => Controls_Manager::MEDIA,
				'default' => $file ? qb_media_default( $file ) : array( 'url' => '' ),
				'dynamic' => array( 'active' => true ),
			)
		);
	}

	/**
	 * Image + its alt text. $o is $this or a Repeater (both have add_control).
	 * Adds controls "$id" (MEDIA) and "{$id}_alt" (TEXT). Read the alt back with $s["{$id}_alt"].
	 */
	protected function ma( $o, $id, $label, $file = '', $alt = '' ) {
		$o->add_control(
			$id,
			array(
				'label'   => $label,
				'type'    => Controls_Manager::MEDIA,
				'default' => $file ? qb_media_default( $file ) : array( 'url' => '' ),
				'dynamic' => array( 'active' => true ),
			)
		);
		$o->add_control(
			$id . '_alt',
			array(
				'label'       => sprintf( /* translators: %s: image label */ __( '%s - alt text', 'qeema-b2b' ), $label ),
				'type'        => Controls_Manager::TEXT,
				'default'     => $alt,
				'label_block' => true,
			)
		);
	}

	/** Size / radius / fit controls for images matched by $selector (relative to {{WRAPPER}}). */
	protected function img_style( $id, $label, $selector ) {
		$this->add_control( $id . '_h', array( 'label' => $label, 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_responsive_control(
			$id . '_w',
			array(
				'label'      => __( 'Width', 'qeema-b2b' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array( 'px' => array( 'min' => 10, 'max' => 1200 ), '%' => array( 'min' => 5, 'max' => 100 ) ),
				'selectors'  => array( '{{WRAPPER}} ' . $selector => 'width: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			$id . '_radius',
			array(
				'label'      => __( 'Corner radius', 'qeema-b2b' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array( '{{WRAPPER}} ' . $selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_control(
			$id . '_fit',
			array(
				'label'     => __( 'Image fit', 'qeema-b2b' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array( '' => __( 'Default', 'qeema-b2b' ), 'cover' => 'Cover', 'contain' => 'Contain', 'fill' => 'Fill' ),
				'default'   => '',
				'selectors' => array( '{{WRAPPER}} ' . $selector => 'object-fit: {{VALUE}};' ),
			)
		);
	}

	protected function l( $id, $label, $url = '' ) {
		$this->add_control(
			$id,
			array(
				'label'       => $label,
				'type'        => Controls_Manager::URL,
				'default'     => array( 'url' => $url ),
				'placeholder' => 'https://',
				'dynamic'     => array( 'active' => true ),
			)
		);
	}

	protected function sel( $id, $label, $options, $default ) {
		$this->add_control( $id, array( 'label' => $label, 'type' => Controls_Manager::SELECT, 'options' => $options, 'default' => $default ) );
	}

	protected function sw( $id, $label, $default = 'yes' ) {
		$this->add_control(
			$id,
			array(
				'label'        => $label,
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'qeema-b2b' ),
				'label_off'    => __( 'Hide', 'qeema-b2b' ),
				'return_value' => 'yes',
				'default'      => $default,
			)
		);
	}

	protected function num( $id, $label, $default, $min = 0, $max = 100 ) {
		$this->add_control( $id, array( 'label' => $label, 'type' => Controls_Manager::NUMBER, 'default' => $default, 'min' => $min, 'max' => $max ) );
	}

	/** A new Repeater pre-filled by the caller. */
	protected function rep() {
		return new \Elementor\Repeater();
	}

	/* ================= STYLE controls ================= */

	protected function style_sec( $id, $label ) {
		$this->start_controls_section( $id, array( 'label' => $label, 'tab' => Controls_Manager::TAB_STYLE ) );
	}

	/** Typography + colour for one text element. $selector is relative to {{WRAPPER}}. */
	protected function typo( $id, $label, $selector, $color_prop = 'color' ) {
		$this->add_control( $id . '_h', array( 'label' => $label, 'type' => Controls_Manager::HEADING, 'separator' => 'before' ) );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array( 'name' => $id . '_typo', 'selector' => '{{WRAPPER}} ' . $selector )
		);
		$this->add_control(
			$id . '_color',
			array(
				'label'     => __( 'Colour', 'qeema-b2b' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} ' . $selector => $color_prop . ': {{VALUE}};' ),
			)
		);
	}

	/**
	 * "Section" style tab: background, padding and margin of the widget's root element.
	 * Pass a list of [id, label, selector] text elements to also get a typography block for each.
	 */
	protected function style_all( $root, $texts = array() ) {
		$this->style_sec( 'qb_style_section', __( 'Section background & spacing', 'qeema-b2b' ) );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'qb_bg',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} ' . $root,
			)
		);
		$this->add_responsive_control(
			'qb_padding',
			array(
				'label'      => __( 'Padding', 'qeema-b2b' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'vw' ),
				'selectors'  => array( '{{WRAPPER}} ' . $root => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'qb_margin',
			array(
				'label'      => __( 'Margin', 'qeema-b2b' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'vw' ),
				'selectors'  => array( '{{WRAPPER}} ' . $root => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();

		if ( $texts ) {
			$this->style_sec( 'qb_style_text', __( 'Text styles', 'qeema-b2b' ) );
			foreach ( $texts as $t ) {
				$this->typo( $t[0], $t[1], $t[2], isset( $t[3] ) ? $t[3] : 'color' );
			}
			$this->end_controls_section();
		}
	}

	/* ================= RENDER helpers ================= */

	/**
	 * Build an element whose content is click-to-edit in the Elementor canvas.
	 *
	 * @param string $tag     HTML tag.
	 * @param string $key     Setting key (or repeater key from get_repeater_setting_key()).
	 * @param string $class   CSS classes.
	 * @param string $content ALREADY ESCAPED inner HTML.
	 * @param string $toolbar 'none' (plain text), 'basic' (bold/italic/link) or 'advanced'.
	 * @param array  $attrs   Extra attributes.
	 */
	protected function el( $tag, $key, $class, $content, $toolbar = 'none', $attrs = array() ) {
		$this->add_render_attribute( $key, 'class', $class );
		foreach ( $attrs as $n => $v ) {
			$this->add_render_attribute( $key, $n, $v );
		}
		$this->add_inline_editing_attributes( $key, $toolbar );
		return '<' . $tag . ' ' . $this->get_render_attribute_string( $key ) . '>' . $content . '</' . $tag . '>';
	}

	/** Repeater setting key helper. */
	protected function rk( $field, $repeater, $i ) {
		return $this->get_repeater_setting_key( $field, $repeater, $i );
	}

	/** Allowed inline markup for headings / paragraphs. */
	protected function kses( $html ) {
		return wp_kses(
			$html,
			array(
				'strong' => array(), 'b' => array(), 'em' => array(), 'i' => array(), 'u' => array(),
				'br' => array(), 'span' => array( 'class' => true ), 'a' => array( 'href' => true, 'target' => true, 'rel' => true ),
			)
		);
	}

	/** <a> from a URL control. $inner is escaped HTML. */
	protected function a( $link, $key, $class, $inner, $extra = array() ) {
		$this->add_render_attribute( $key, 'class', $class );
		foreach ( $extra as $n => $v ) {
			$this->add_render_attribute( $key, $n, $v );
		}
		if ( ! empty( $link['url'] ) ) {
			$this->add_link_attributes( $key, $link );
		} else {
			$this->add_render_attribute( $key, 'href', '#' );
		}
		return '<a ' . $this->get_render_attribute_string( $key ) . '>' . $inner . '</a>';
	}

	/** <img> from a MEDIA control value ([ 'id' =>, 'url' => ]). */
	protected function img( $media, $attrs = array(), $size = 'large' ) {
		if ( empty( $media['url'] ) && empty( $media['id'] ) ) {
			return '';
		}
		$attrs = array_merge( array( 'alt' => '', 'decoding' => 'async' ), $attrs );
		if ( ! empty( $media['id'] ) && '' === $attrs['alt'] ) {
			unset( $attrs['alt'] ); // let WordPress use the alt text saved on the Media Library item
		}
		if ( ! empty( $media['id'] ) ) {
			$html = wp_get_attachment_image( (int) $media['id'], $size, false, $attrs );
			if ( $html ) {
				return $html;
			}
		}
		$out = '<img src="' . esc_url( $media['url'] ) . '"';
		foreach ( $attrs as $n => $v ) {
			$out .= ' ' . esc_attr( $n ) . '="' . esc_attr( $v ) . '"';
		}
		return $out . '>';
	}

	/** Repeater control for breadcrumb items (label + link; the last item is the current page). */
	protected function crumb_controls( $default ) {
		$r = $this->rep();
		$r->add_control( 'label', array( 'label' => __( 'Label', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => 'Page' ) );
		$r->add_control( 'link', array( 'label' => __( 'Link (not used on the last item)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::URL, 'default' => array( 'url' => '' ) ) );
		$this->add_control(
			'crumb',
			array(
				'label'       => __( 'Breadcrumb items', 'qeema-b2b' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $r->get_controls(),
				'default'     => $default,
				'title_field' => '{{{ label }}}',
			)
		);
	}

	/** Breadcrumb pill markup. */
	protected function crumb_html( $items, $class = 'crumb rise-in', $delay = '.05s', $three = false ) {
		$out  = '<nav class="' . esc_attr( $class . ( $three ? ' crumb--3' : '' ) ) . '" aria-label="Breadcrumb" style="--d:' . esc_attr( $delay ) . '"><ol>';
		$last = count( (array) $items ) - 1;
		foreach ( (array) $items as $i => $it ) {
			$label = esc_html( $it['label'] );
			if ( $i === $last ) {
				$out .= '<li aria-current="page">' . $label . '</li>';
			} else {
				$out .= '<li>' . $this->a( $it['link'], 'crumb' . $i, '', $label ) . '</li>';
			}
		}
		return $out . '</ol></nav>';
	}

	/** Is the widget being edited right now? */
	protected function is_edit() {
		return \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode();
	}

	/** data-reveal attribute string (animation on scroll; delay in seconds). */
	protected function reveal( $dir = '', $delay = 0 ) {
		$out = ' data-reveal' . ( $dir ? '="' . esc_attr( $dir ) . '"' : '' );
		if ( $delay ) {
			$out .= ' style="--d:' . esc_attr( $delay ) . 's"';
		}
		return $out;
	}
}
