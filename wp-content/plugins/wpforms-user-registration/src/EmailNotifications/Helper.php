<?php

namespace WPFormsUserRegistration\EmailNotifications;

/**
 * Notifications helper class.
 *
 * @since 2.0.0
 */
class Helper {

	/**
	 * Option name to store the legacy emails usage.
	 *
	 * @since 2.0.0
	 */
	const LEGACY_EMAILS = 'wpforms_user_registration_legacy_emails';

	/**
	 * Output settings HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param array  $form_data       The information for the form.
	 * @param string $field           Field to append.
	 * @param string $default_subject Default subject.
	 * @param string $default_message Default message.
	 * @param bool   $show            Show settings.
	 *
	 * @return string
	 */
	public static function settings_html( $form_data, $field, $default_subject, $default_message, $show ) {

		$display = $show ? '' : 'display:none;';

		$output = '<div id="wpforms-notifications-block-' . esc_attr( $field ) . '" class="wpforms-user-registration-email-notifications-template" style="' . esc_attr( $display ) . '">';

		$output .= wpforms_panel_field(
			'text',
			'settings',
			$field . '_subject',
			$form_data,
			esc_html__( 'Email Subject', 'wpforms-user-registration' ),
			[
				'default'       => $default_subject,
				'smarttags'     => [
					'type' => 'all',
				],
				'after_tooltip' => '&nbsp;<span class="required">*</span>',
				'input_class'   => 'wpforms-required',
			],
			false
		);

		$output .= wpforms_panel_field(
			'textarea',
			'settings',
			$field . '_message',
			$form_data,
			esc_html__( 'Message', 'wpforms-user-registration' ),
			[
				'rows'          => 6,
				'default'       => $default_message,
				'smarttags'     => [
					'type' => 'all',
				],
				'after_tooltip' => '&nbsp;<span class="required">*</span>',
				'input_class'   => 'wpforms-required',
			],
			false
		);

		$output .= '</div>';

		return $output;
	}

	/**
	 * Get default user activation subject.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function default_user_activation_subject() {

		return sprintf( /* translators: %s - {site_name} smart tag. */
			esc_html__( '%s Activation Required', 'wpforms-user-registration' ),
			'{site_name}'
		);
	}

	/**
	 * Get default user activation message.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function default_user_activation_message() {

		$default_message  = esc_html__( 'IMPORTANT: You must activate your account before you can log in.', 'wpforms-user-registration' ) . "\r\n";
		$default_message .= esc_html__( 'Please visit the link below.', 'wpforms-user-registration' ) . "\r\n\r\n";
		$default_message .= '{url_user_activation}';

		return $default_message;
	}

	/**
	 * Get default admin subject.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function default_admin_subject() {

		return sprintf( /* translators: %s - {site_name} smart tag. */
			esc_html__( '%s New User Registration', 'wpforms-user-registration' ),
			'{site_name}'
		);
	}

	/**
	 * Get default admin message.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function default_admin_message() {

		$default_message  = sprintf( /* translators: %s - {site_name} smart tag. */
			esc_html__( 'New user registration on your site %s:', 'wpforms-user-registration' ),
			'{site_name}'
		);
		$default_message .= "\r\n\r\n";
		$default_message .= sprintf( /* translators: %s - {user_registration_login} smart tag. */
			esc_html__( 'Username: %s', 'wpforms-user-registration' ),
			'{user_registration_login}'
		);
		$default_message .= "\r\n";
		$default_message .= sprintf( /* translators: %s - {user_registration_email} smart tag. */
			esc_html__( 'Email: %s', 'wpforms-user-registration' ),
			'{user_registration_email}'
		);

		return $default_message;
	}

	/**
	 * Get default admin subject.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function default_user_subject() {

		return sprintf( /* translators: %s - {site_name} smart tag. */
			esc_html__( '%s Your username and password info', 'wpforms-user-registration' ),
			'{site_name}'
		);
	}

	/**
	 * Get default admin message.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function default_user_message() {

		$default_message  = sprintf( /* translators: %s - {user_registration_login} smart tag. */
			esc_html__( 'Username: %s', 'wpforms-user-registration' ),
			'{user_registration_login}'
		);
		$default_message .= "\r\n";
		$default_message .= sprintf( /* translators: %s - {user_registration_password} smart tag. */
			esc_html__( 'Password: %s', 'wpforms-user-registration' ),
			'{user_registration_password}'
		);
		$default_message .= "\r\n";
		$default_message .= '{url_login}' . "\r\n\r\n";

		return $default_message;
	}

	/**
	 * Get default admin subject.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function default_user_after_activation_subject() {

		return sprintf( /* translators: %s - {site_name} smart tag. */
			esc_html__( '%s Your account was successfully activated', 'wpforms-user-registration' ),
			'{site_name}'
		);
	}

	/**
	 * Get default admin message.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function default_user_after_activation_message() {

		$default_message  = esc_html__( 'You can log in with your credentials now.', 'wpforms-user-registration' ) . "\r\n\r\n";
		$default_message .= '{url_login}';

		return $default_message;
	}

	/**
	 * Get default reset password user subject.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function default_user_reset_subject() {

		return esc_html__( 'Reset Your Password', 'wpforms-user-registration' );
	}

	/**
	 * Get default reset password user message.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function default_user_reset_message() {

		$default_message  = esc_html__( 'Someone has requested a password reset for the following account:', 'wpforms-user-registration' ) . "\r\n\r\n";
		$default_message .= sprintf( /* translators: %s - {site_name} smart tag. */
			esc_html__( 'Site Name: %s', 'wpforms-user-registration' ),
			'{site_name}'
		);
		$default_message .= "\r\n\r\n";
		$default_message .= sprintf( /* translators: %s - {user_registration_login} smart tag. */
			esc_html__( 'Username: %s', 'wpforms-user-registration' ),
			'{user_registration_login}'
		);
		$default_message .= "\r\n\r\n";
		$default_message .= esc_html__( 'If this was a mistake, just ignore this email and nothing will happen.', 'wpforms-user-registration' ) . "\r\n\r\n";
		$default_message .= '{user_registration_password_reset}';

		return $default_message;
	}
}
