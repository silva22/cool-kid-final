<?php

namespace WPFormsUserRegistration\EmailNotifications;

use WP_User;
use WPForms\Emails\Mailer;
use WPForms\Emails\Helpers as EmailHelpers;
use WPForms_WP_Emails;
use WPFormsUserRegistration\Process\Helpers\UserRegistration;
use WPFormsUserRegistration\SmartTags\Helpers\Helper as SmartTagsHelper;

/**
 * Notifications class.
 *
 * @since 2.0.0
 */
class Notifications {

	/**
	 * List of submitted fields.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $fields = [];

	/**
	 * Form data.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private $form_data = [];

	/**
	 * Entry id.
	 *
	 * @since 2.0.0
	 *
	 * @var int
	 */
	private $entry_id;

	/**
	 * Send notifications.
	 *
	 * @since 2.0.0
	 *
	 * @param int   $user_id   The user id.
	 * @param array $user_data User data.
	 * @param array $form_data The information for the form.
	 * @param array $fields    The fields that have been submitted.
	 * @param int   $entry_id  The entry id.
	 */
	public function notification( $user_id, $user_data, $form_data, $fields, $entry_id ) {

		$this->form_data = $form_data;
		$this->fields    = $fields;
		$this->entry_id  = $entry_id;

		$user = $this->get_user( $user_id );

		if ( ! $user ) {
			return;
		}

		SmartTagsHelper::set_user( $user );

		$activation = $this->get_form_activation_type( $this->form_data['settings'] );

		// Send user email notification is enabled OR if user activation is
		// enabled and requires user activation.
		if ( ! empty( $this->form_data['settings']['registration_email_user'] ) ) {
			$this->user_notification( $user, $user_data['user_pass'], $activation );
		}

		if ( $activation === 'user' ) {
			$this->user_activation( $user );
		}

		// Hide password value for non user emails.
		UserRegistration::set_password( '**********' );

		// Send admin email notification is enabled OR if user activation is
		// enabled and requires the manual admin activation.
		if ( ! empty( $this->form_data['settings']['registration_email_admin'] ) ) {
			$this->admin_notification( $user, $user_data['user_pass'], $activation );
		}
	}

	/**
	 * Send notification after user activation.
	 *
	 * @since 2.0.0
	 *
	 * @param string $user_id User ID.
	 *
	 * @noinspection PhpUnused
	 */
	public function after_activation( $user_id ) {

		$user = $this->get_user( $user_id );

		if ( ! $user ) {
			return;
		}

		$this->form_data = $this->get_form_data_for_user( $user_id );
		$this->entry_id  = $this->get_user_entry_id( $user_id );
		$this->fields    = $this->get_fields_data_for_user();

		if ( ! $this->form_data ) {
			return;
		}

		if ( empty( $this->form_data['settings']['registration_email_user_after_activation'] ) ) {
			return;
		}

		$activation = $this->get_form_activation_type( $this->form_data['settings'] );

		if ( ! $activation ) {
			return;
		}

		$email = [
			'address' => $user->user_email,
			'subject' => $this->get_setting( 'subject', 'registration_email_user_after_activation', Helper::default_user_after_activation_subject() ),
			'message' => $this->get_setting( 'message', 'registration_email_user_after_activation', Helper::default_admin_message() ),
			'user'    => $user,
		];

		/**
		 * This filter allows overwriting activation user email data.
		 *
		 * @since 2.0.0
		 *
		 * @param array $email {
		 *     Email data.
		 *
		 *     @type string   $address User email.
		 *     @type string   $subject Email subject.
		 *     @type string   $message Email body.
		 *     @type WP_User $user    WP User object.
		 * }
		 */
		$email = apply_filters( 'wpforms_user_registration_email_notifications_after_activation_user_email', $email );

		$this->send( $email );

		delete_user_meta( $user_id, 'wpforms-form-id' );
		delete_user_meta( $user_id, 'wpforms-entry-id' );
	}

	/**
	 * Send notification for user activation.
	 *
	 * @since 2.0.0
	 *
	 * @param string $user_id User ID.
	 *
	 * @noinspection PhpUnused
	 */
	public function resend_activation( $user_id ) {

		$user = $this->get_user( $user_id );

		if ( ! $user ) {
			return;
		}

		$this->form_data = $this->get_form_data_for_user( $user_id );
		$this->entry_id  = $this->get_user_entry_id( $user_id );
		$this->fields    = $this->get_fields_data_for_user();

		if ( ! $this->form_data ) {
			return;
		}

		$activation = $this->get_form_activation_type( $this->form_data['settings'] );

		if ( $activation !== 'user' ) {
			return;
		}

		$this->user_activation( $user );
	}

	/**
	 * Send reset password link.
	 *
	 * @since 2.0.0
	 *
	 * @param string $user_id   User ID.
	 * @param array  $form_data The information for the form.
	 * @param array  $fields    The fields that have been submitted.
	 * @param int    $entry_id  The entry id.
	 *
	 * @noinspection PhpUnused
	 */
	public function reset_password( $user_id, $form_data, $fields, $entry_id ) {

		$this->form_data = $form_data;
		$this->fields    = $fields;
		$this->entry_id  = $entry_id;

		$user = $this->get_user( $user_id );

		if ( ! $user ) {
			return;
		}

		$email = [
			'address' => $user->user_email,
			'subject' => $this->get_setting( 'subject', 'user_reset_email_user', Helper::default_user_reset_subject() ),
			'message' => $this->get_setting( 'message', 'user_reset_email_user', Helper::default_user_reset_message() ),
			'user'    => $user,
		];

		/**
		 * This filter allows overwriting reset password user email data.
		 *
		 * @since 2.0.0
		 *
		 * @param array $email {
		 *     Email data.
		 *
		 *     @type string   $address User email.
		 *     @type string   $subject Email subject.
		 *     @type string   $message Email body.
		 *     @type WP_User $user    WP User object.
		 * }
		 */
		$email = apply_filters( 'wpforms_user_registration_email_notifications_reset_password_user_email', $email );

		$this->send( $email );
	}

	/**
	 * Send Email.
	 *
	 * @since 2.0.0
	 *
	 * @param array $email Email data to send.
	 */
	private function send( $email ) {

		$email['message'] = wpforms_process_smart_tags( $email['message'], $this->form_data, $this->fields, $this->entry_id );
		$email['subject'] = wpforms_process_smart_tags( $email['subject'], $this->form_data, $this->fields, $this->entry_id );

		if ( get_option( Helper::LEGACY_EMAILS ) === '1' && wpforms_setting( 'user-registration-template', 'legacy' ) === 'legacy' ) {

			( new WPForms_WP_Emails() )->send(
				$email['address'],
				$email['subject'],
				$email['message']
			);

			return;
		}

		// Extract the email message.
		$message = $email['message'];

		// If it's not a plain text template, replace line breaks.
		if ( ! EmailHelpers::is_plain_text_template() ) {
			// Replace line breaks with <br/> tags.
			$message = str_replace( "\r\n", '<br/>', $message );
			// Wrap the message in a table row.
			$message = sprintf( '<tr><td class="field-name field-value">%1$s</td></tr>', $message );
		}

		$args = [
			'body' => [
				'message' => $message,
			],
		];

		/**
		 * Filter to customize the email template name independently of the global setting.
		 *
		 * @since 2.4.0
		 *
		 * @param string $template_name The template name to be used.
		 */
		$template_name  = apply_filters( 'wpforms_user_registration_email_notifications_template_name', EmailHelpers::get_current_template_name() );
		$template_class = EmailHelpers::get_current_template_class( $template_name, __NAMESPACE__ . '\Templates\General' );
		$template       = ( new $template_class() )->set_args( $args );

		/**
		 * This filter allows overwriting modern email template.
		 *
		 * @since 2.0.0
		 *
		 * @param object $template Template object.
		 * @param array  $email    Email data {
		 *     Email data.
		 *
		 *     @type string   $address Admin email.
		 *     @type string   $subject Email subject.
		 *     @type string   $message Email body.
		 *     @type WP_User  $user    WP User object.
		 * }
		 */
		$template = apply_filters( 'wpforms_user_registration_email_notifications_send_emails_template', $template, $email );

		$content = $template->get();

		if ( ! $content ) {
			return;
		}

		( new Mailer() )
			->template( $template )
			->subject( $email['subject'] )
			->to_email( $email['address'] )
			->send();
	}

	/**
	 * Get User.
	 *
	 * @since 2.0.0
	 *
	 * @param string $user_id User ID.
	 *
	 * @return false|WP_User
	 */
	private function get_user( $user_id ) {

		return get_user_by( 'id', $user_id );
	}

	/**
	 * Get form data used by user for registration.
	 *
	 * @param string $user_id User ID.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	private function get_form_data_for_user( $user_id ) {

		$form_id = get_user_meta( $user_id, 'wpforms-form-id', true );

		if ( ! $form_id ) {
			return [];
		}

		$form_obj = wpforms()->get( 'form' );

		return $form_obj ? $form_obj->get( $form_id, [ 'content_only' => true ] ) : [];
	}

	/**
	 * Get user submitted entry id.
	 *
	 * @param string $user_id User ID.
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	private function get_user_entry_id( $user_id ) {

		return absint( get_user_meta( $user_id, 'wpforms-entry-id', true ) );
	}

	/**
	 * Get submitted fields used by user for registration.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	private function get_fields_data_for_user() {

		if ( ! $this->entry_id ) {
			return [];
		}

		$entry_obj = wpforms()->get( 'entry' );

		if ( $entry_obj === null ) {
			return [];
		}

		$entry = $entry_obj->get( $this->entry_id );

		if ( empty( $entry ) || empty( $entry->fields ) ) {
			return [];
		}

		return wpforms_decode( $entry->fields );
	}

	/**
	 * Get form activation type.
	 *
	 * @param array $form_settings Form settings.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function get_form_activation_type( $form_settings ) {

		return UserRegistration::get_activation_type( $form_settings );
	}

	/**
	 * Get Setting.
	 *
	 * @since 2.0.0
	 *
	 * @param string $setting Setting to get.
	 * @param string $field   Field.
	 * @param string $default Default.
	 *
	 * @return string
	 */
	private function get_setting( $setting, $field, $default ) {

		$setting_key = $field . '_' . $setting;

		return isset( $this->form_data['settings'][ $setting_key ] ) ? $this->form_data['settings'][ $setting_key ] : $default;
	}

	/**
	 * Custom email we send to admin when new user registered.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_User $user           User.
	 * @param string  $plaintext_pass Password.
	 * @param string  $activation     Activation type.
	 */
	private function admin_notification( $user, $plaintext_pass, $activation ) {

		list( $subject, $message ) = $this->admin_activation(
			$this->get_setting( 'subject', 'registration_email_admin', Helper::default_admin_subject() ),
			$this->get_setting( 'message', 'registration_email_admin', Helper::default_admin_message() ),
			$activation
		);

		/* translators: %s - unapproved users list page URL. */
		$message .= "\r\n\r\n" . sprintf( esc_html__( 'Manage user activations: %s', 'wpforms-user-registration' ), admin_url( 'users.php' ) ) . "\r\n";

		/**
		 * This filter allows overwriting site admin email who will receive new user registration email.
		 *
		 * @since 2.0.0
		 *
		 * @param string $admin_email Admin email.
		 */
		$admin_email = apply_filters( 'wpforms_user_registration_email_notifications_admin_notification_email', get_option( 'admin_email' ) );

		$email = [
			'address'    => $admin_email,
			'subject'    => $subject,
			'message'    => $message,
			'user'       => $user,
			'password'   => $plaintext_pass,
			'activation' => $activation,
		];

		// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
		$email = apply_filters_deprecated(
			'wpforms_user_registration_email_admin',
			[ $email ],
			'2.0.0 of the WPForms User Registration plugin',
			'wpforms_user_registration_email_notifications_admin_notification'
		);

		/**
		 * This filter allows overwriting user registration admin email data.
		 *
		 * @since 2.0.0
		 *
		 * @param array $email {
		 *     Email data.
		 *
		 *     @type string   $address    Admin email.
		 *     @type string   $subject    Email subject.
		 *     @type string   $message    Email body.
		 *     @type WP_User $user       WP User object.
		 *     @type string   $password   Password.
		 *     @type string   $activation Activation link.
		 * }
		 */
		$email = apply_filters( 'wpforms_user_registration_email_notifications_admin_notification', $email );

		$this->send( $email );
	}

	/**
	 * Add activation info to admin email.
	 *
	 * @since 2.0.0
	 *
	 * @param string $subject    Subject.
	 * @param string $message    Message.
	 * @param string $activation Activation type.
	 *
	 * @return array
	 */
	private function admin_activation( $subject, $message, $activation ) {

		if ( ! $activation ) {
			return [ $subject, $message ];
		}

		$subject .= ' ' . esc_html__( '(Activation Required)', 'wpforms-user-registration' );
		$message .= "\r\n\r\n" . esc_html__( 'Account activation is required before the user can log in.', 'wpforms-user-registration' ) . "\r\n";

		if ( $activation !== 'user' ) {

			$message .= esc_html__( 'You must manually activate their account.', 'wpforms-user-registration' );

			return [ $subject, $message ];
		}

		$message .= esc_html__( 'The user has been emailed a link to activate their account.', 'wpforms-user-registration' );

		return [ $subject, $message ];
	}

	/**
	 * Custom email we send to new users.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_User $user           User.
	 * @param string  $plaintext_pass Password.
	 * @param string  $activation     Activation type.
	 */
	private function user_notification( $user, $plaintext_pass, $activation ) {

		$message = $this->get_setting( 'message', 'registration_email_user', Helper::default_user_message() );

		if ( $activation === 'admin' ) {
			$message .= esc_html__( 'Site administrator must activate your account before you can log in.', 'wpforms-user-registration' );
		}

		$email = [
			'address'    => $user->user_email,
			'subject'    => $this->get_setting( 'subject', 'registration_email_user', Helper::default_user_subject() ),
			'message'    => $message,
			'user'       => $user,
			'password'   => $plaintext_pass,
			'activation' => $activation,
		];

		// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
		$email = apply_filters_deprecated(
			'wpforms_user_registration_email_user',
			[ $email ],
			'2.0.0 of the WPForms User Registration plugin',
			'wpforms_user_registration_email_notifications_admin_notification'
		);

		/**
		 * This filter allows overwriting user registration email data.
		 *
		 * @since 2.0.0
		 *
		 * @param array $email {
		 *     Email data.
		 *
		 *     @type string   $address    Admin email.
		 *     @type string   $subject    Email subject.
		 *     @type string   $message    Email body.
		 *     @type WP_User $user       WP User object.
		 *     @type string   $password   Password.
		 *     @type string   $activation Activation link.
		 * }
		 */
		$email = apply_filters( 'wpforms_user_registration_email_notifications_user_notification', $email );

		$this->send( $email );
	}

	/**
	 * Add activation info to user email.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_User $user User type.
	 */
	private function user_activation( $user ) {

		if ( ! get_user_meta( $user->ID, 'wpforms-activate', true ) ) {
			// Create activation link.
			$hash = $this->generate_hash();

			add_user_meta( $user->ID, 'wpforms-activate', $hash );
		}

		$email = [
			'address' => $user->user_email,
			'subject' => $this->get_setting( 'subject', 'registration_email_user_activation', Helper::default_user_activation_subject() ),
			'message' => $this->get_setting( 'message', 'registration_email_user_activation', Helper::default_user_activation_message() ),
			'user'    => $user,
		];

		/**
		 * This filter allows overwriting user activation email data.
		 *
		 * @since 2.0.0
		 *
		 * @param array $email {
		 *     Email data.
		 *
		 *     @type string   $address    Admin email.
		 *     @type string   $subject    Email subject.
		 *     @type string   $message    Email body.
		 *     @type WP_User $user       WP User object.
		 *     @type string   $activation Activation link.
		 * }
		 */
		$email = apply_filters( 'wpforms_user_registration_email_notifications_user_activation', $email );

		$this->send( $email );
	}

	/**
	 * Generate secure hash key.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function generate_hash() {

		return substr( md5( microtime( true ) . wp_rand( 0, 1000 ) ), 0, 20 ); // to fit in db field with 20 char limit.
	}
}
