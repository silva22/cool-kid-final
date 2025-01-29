<?php

namespace WPFormsUserRegistration\Process;

use WPFormsUserRegistration\Admin\Builder\Login as BuilderLogin;
use WPFormsUserRegistration\Process\Helpers\UserLogin;
use WPFormsUserRegistration\Process\Helpers\UserProcess;

/**
 * Login processing class.
 *
 * @since 2.0.0
 */
class Login extends Base {

	/**
	 * Process a form.
	 *
	 * @since 2.0.0
	 *
	 * @param array $fields    The fields that have been submitted.
	 * @param array $entry     The post data submitted by the form.
	 * @param array $form_data The information for the form.
	 */
	public function process( $fields, $entry, $form_data ) {

		if ( ! $this->is_login_form( $form_data ) || empty( $form_data['id'] ) ) {
			return;
		}

		$login_fields = UserProcess::get_required_fields( $form_data, $fields, [ 'login', 'password', 'remember_me' ] );

		// If no login fields have been found, return early.
		if ( empty( $login_fields ) || empty( $login_fields['login'] ) || empty( $login_fields['password'] ) ) {

			/**
			 * This filter allows overwriting user login empty fields error message.
			 *
			 * @since 2.0.0
			 *
			 * @param string $message Error Message.
			 */
			wpforms()->get( 'process' )->errors[ $form_data['id'] ]['header'] = apply_filters( 'wpforms_user_registration_process_login_process_empty_fields_error_message', esc_html__( 'Username and Password are required.', 'wpforms-user-registration' ) );

			return;
		}

		$credentials = [
			'user_login'    => $login_fields['login'],
			'user_password' => $login_fields['password'],
			'remember'      => ! isset( $login_fields['remember_me'] ) || $login_fields['remember_me'],
		];

		// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
		$credentials = apply_filters_deprecated(
			'wpforms_user_registration_login_creds',
			[ $credentials, $fields, $entry, $form_data ],
			'2.0.0 of the WPForms User Registration plugin',
			'wpforms_user_registration_process_login_process_credentials'
		);

		/**
		 * This filter allows overwriting user login credentials.
		 *
		 * @since 2.0.0
		 *
		 * @param array $credentials {
		 *     Credentials data.
		 *
		 *     @type string $user_login    User login.
		 *     @type string $user_password Password.
		 *     @type bool   $remember      Is needs to be remembered.
		 * }
		 * @param array $fields      The fields that have been submitted.
		 * @param array $entry       The post data submitted by the form.
		 * @param array $form_data   The information for the form.
		 */
		$credentials = apply_filters( 'wpforms_user_registration_process_login_process_credentials', $credentials, $fields, $entry, $form_data );

		if ( empty( $credentials ) ) {
			return;
		}

		$user = wp_signon( $credentials, UserLogin::maybe_force_secure_cookie( $credentials ) );

		if ( ! is_wp_error( $user ) ) {
			return;
		}

		$error_code = $user->get_error_code();

		// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
		$error_message = apply_filters_deprecated(
			'wpforms_user_registration_login_error',
			[ $user->get_error_message(), $error_code ],
			'2.0.0 of the WPForms User Registration plugin',
			'wpforms_user_registration_process_login_process_wp_error_message'
		);

		/**
		 * This filter allows overwriting user login WP error message.
		 *
		 * @since 2.0.0
		 *
		 * @param string $message Error Message text.
		 * @param string $code    Error code.
		 */
		wpforms()->get( 'process' )->errors[ $form_data['id'] ]['header'] = apply_filters( 'wpforms_user_registration_process_login_process_wp_error_message', $error_message, $error_code );

		wpforms_log(
			'Unable to log in',
			$login_fields,
			[
				'type'    => [ 'error' ],
				'form_id' => $form_data['id'],
				'parent'  => $entry['id'],
			]
		);
	}

	/**
	 * After processing a from.
	 *
	 * @since 2.0.0
	 *
	 * @param array $fields    Fields.
	 * @param array $entry     Entry.
	 * @param array $form_data Form data.
	 *
	 * @return array
	 *
	 * @noinspection PhpUnused
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function process_after_filter( $fields, $entry, $form_data ) {

		if ( ! $this->is_login_form( $form_data ) ) {
			return $fields;
		}

		return $this->hide_password_value( $fields );
	}

	/**
	 * Check if current form is login one.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_data The information for the form.
	 *
	 * @return bool
	 */
	private function is_login_form( $form_data ) {

		return ! empty( $form_data['meta']['template'] ) && $form_data['meta']['template'] === BuilderLogin::TEMPLATE_SLUG;
	}
}
