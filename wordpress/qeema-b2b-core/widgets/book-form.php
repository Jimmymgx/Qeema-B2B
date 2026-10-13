<?php
/**
 * "Book a call" form: floating-label fields, validation messages and a success screen.
 * Submissions are stored under Leads and e-mailed to the address set in Leads → Settings.
 *
 * @package QeemaB2B
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QB_Book_Form extends QB_Widget_Base {

	public function get_name() {
		return 'qb-book-form';
	}

	public function get_title() {
		return __( 'Book a Call Form', 'qeema-b2b' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_style_depends() {
		return array( 'qb-book' );
	}

	protected function register_controls() {
		$this->sec( 'head', __( 'Heading', 'qeema-b2b' ) );
		$this->t( 'pill', __( 'Pill text', 'qeema-b2b' ), 'Get in touch' );
		$this->end();

		$this->sec( 'fields', __( 'Field labels', 'qeema-b2b' ) );
		$this->t( 'l_first', __( 'First name', 'qeema-b2b' ), 'First Name *' );
		$this->t( 'l_last', __( 'Last name', 'qeema-b2b' ), 'Last Name *' );
		$this->t( 'l_email', __( 'Email', 'qeema-b2b' ), 'Email Address *' );
		$this->t( 'l_phone', __( 'Phone', 'qeema-b2b' ), 'Phone number' );
		$this->t( 'l_message', __( 'Message', 'qeema-b2b' ), 'Tell us more ...' );
		$this->t( 'submit', __( 'Submit button text', 'qeema-b2b' ), 'Submit Form' );
		$this->end();

		$this->sec( 'msgs', __( 'Validation messages', 'qeema-b2b' ) );
		$this->t( 'm_first', __( 'First name missing', 'qeema-b2b' ), 'Please enter your first name.' );
		$this->t( 'm_last', __( 'Last name missing', 'qeema-b2b' ), 'Please enter your last name.' );
		$this->t( 'm_email', __( 'Email missing', 'qeema-b2b' ), 'Please enter your email address.' );
		$this->t( 'm_email_bad', __( 'Email invalid', 'qeema-b2b' ), 'That email address does not look right.' );
		$this->t( 'm_phone', __( 'Phone invalid', 'qeema-b2b' ), 'Use digits only, for example +20 101 280 4721.' );
		$this->t( 'm_fail', __( 'Sending failed', 'qeema-b2b' ), 'Sorry, something went wrong. Please try again.' );
		$this->t( 'e_session', __( 'Session expired (server message)', 'qeema-b2b' ), 'Your session expired. Please reload the page and try again.' );
		$this->t( 'e_invalid', __( 'Name / email invalid (server message)', 'qeema-b2b' ), 'Please fill in your name and a valid email address.' );
		$this->end();

		$this->sec( 'success', __( 'Success screen', 'qeema-b2b' ) );
		$this->t( 's_title', __( 'Title (HTML allowed: <strong>)', 'qeema-b2b' ), 'Thank <strong>you!</strong>', 'area' );
		$this->t( 's_text', __( 'Text', 'qeema-b2b' ), 'We received your details and our team will contact you as soon as possible.', 'area' );
		$this->t( 's_btn', __( 'Button text', 'qeema-b2b' ), 'Back to Home' );
		$this->l( 's_link', __( 'Button link', 'qeema-b2b' ), home_url( '/' ) );
		$this->end();

		$this->sec( 'notify', __( 'Notification', 'qeema-b2b' ) );
		$this->t( 'notify_to', __( 'Send notification to (empty = Leads → Settings address)', 'qeema-b2b' ), '', 'text', array( 'description' => __( 'Kept on the server; the address is never printed in the page.', 'qeema-b2b' ) ) );
		$this->end();

		$this->style_sec( 'tick', __( 'Success tick', 'qeema-b2b' ) );
		$this->add_control( 'tick_c1', array( 'label' => __( 'Tick colour (start)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#00247E' ) );
		$this->add_control( 'tick_c2', array( 'label' => __( 'Tick colour (end)', 'qeema-b2b' ), 'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#0C5ADB' ) );
		$this->end();

		$this->style_all(
			'.book',
			array(
				array( 'label', __( 'Field labels', 'qeema-b2b' ), '.field label' ),
				array( 'submit', __( 'Submit button', 'qeema-b2b' ), '.book-form__submit' ),
				array( 'stitle', __( 'Success title', 'qeema-b2b' ), '.book-success .title' ),
				array( 'stext', __( 'Success text', 'qeema-b2b' ), '.book-success .lead' ),
			)
		);
	}

	private function field( $id, $name, $type, $label, $auto = '', $required = false ) {
		?>
		<div class="field">
			<input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $type ); ?>"<?php echo $auto ? ' autocomplete="' . esc_attr( $auto ) . '"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?> placeholder=" "<?php echo $required ? ' required' : ''; ?> aria-describedby="<?php echo esc_attr( $id ); ?>-err">
			<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
			<span class="field__line" aria-hidden="true"></span>
			<p class="field__error" id="<?php echo esc_attr( $id ); ?>-err" role="alert"></p>
		</div>
		<?php
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$uid  = 'qb' . $this->get_id();
		$data = array(
			'data-msg-first'     => $s['m_first'],
			'data-msg-last'      => $s['m_last'],
			'data-msg-email'     => $s['m_email'],
			'data-msg-email-bad' => $s['m_email_bad'],
			'data-msg-phone'     => $s['m_phone'],
			'data-msg-fail'      => $s['m_fail'],
		);
		$c1 = ! empty( $s['tick_c1'] ) ? $s['tick_c1'] : '#00247E';
		$c2 = ! empty( $s['tick_c2'] ) ? $s['tick_c2'] : '#0C5ADB';
		// Recipient + server messages stay on the server; the page only carries an opaque token.
		$token = qb_form_register(
			array(
				'to'        => sanitize_email( (string) $s['notify_to'] ),
				'e_session' => (string) $s['e_session'],
				'e_invalid' => (string) $s['e_invalid'],
			)
		);
		$attrs = '';
		foreach ( $data as $k => $v ) {
			$attrs .= ' ' . $k . '="' . esc_attr( $v ) . '"';
		}
		?>
		<section class="book" data-qb="book" aria-label="<?php echo esc_attr( $s['pill'] ); ?>">
			<span class="pill pill--soft book-pill" data-reveal><?php echo esc_html( $s['pill'] ); ?></span>
			<form class="book-form" novalidate data-reveal style="--d:.1s"<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
				<div class="book-form__grid">
					<?php
					$this->field( $uid . 'fn', 'firstName', 'text', $s['l_first'], 'given-name', true );
					$this->field( $uid . 'ln', 'lastName', 'text', $s['l_last'], 'family-name', true );
					$this->field( $uid . 'em', 'email', 'email', $s['l_email'], 'email', true );
					$this->field( $uid . 'ph', 'phone', 'tel', $s['l_phone'], 'tel', false );
					?>
					<div class="field field--wide">
						<textarea id="<?php echo esc_attr( $uid ); ?>msg" name="message" rows="1" placeholder=" "></textarea>
						<label for="<?php echo esc_attr( $uid ); ?>msg"><?php echo esc_html( $s['l_message'] ); ?></label>
						<span class="field__line" aria-hidden="true"></span>
					</div>
				</div>
				<input type="text" name="qb_hp" class="qb-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
				<input type="hidden" name="qb_rt" value="<?php echo esc_attr( $token ); ?>">
				<input type="hidden" name="source" value="<?php echo esc_attr( get_permalink() ); ?>">
				<div class="book-form__actions">
					<button class="btn book-form__submit" type="submit"><span class="btn__label"><?php echo esc_html( $s['submit'] ); ?></span><span class="btn__spinner" aria-hidden="true"></span></button>
				</div>
				<p class="book-form__error" role="alert" hidden></p>
			</form>

			<div class="book-success" hidden tabindex="-1">
				<svg class="book-success__check" width="72" height="72" viewBox="0 0 72 72" fill="none" aria-hidden="true"><circle cx="36" cy="36" r="34" stroke="url(#qbg<?php echo esc_attr( $this->get_id() ); ?>)" stroke-width="3"/><path d="M22 37.5 31.5 47 50 26" stroke="url(#qbg<?php echo esc_attr( $this->get_id() ); ?>)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="qbg<?php echo esc_attr( $this->get_id() ); ?>" x1="0" y1="0" x2="72" y2="0" gradientUnits="userSpaceOnUse"><stop stop-color="<?php echo esc_attr( $c1 ); ?>"/><stop offset="1" stop-color="<?php echo esc_attr( $c2 ); ?>"/></linearGradient></defs></svg>
				<?php echo $this->el( 'h2', 's_title', 'title', $this->kses( $s['s_title'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->el( 'p', 's_text', 'lead', esc_html( $s['s_text'] ), 'basic' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo $this->a( $s['s_link'], 's_btn', 'btn', esc_html( $s['s_btn'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</section>
		<?php
	}
}
