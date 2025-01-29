<?php

namespace WPFormsUserRegistration\Process;

use WPFormsUserRegistration\Admin\Builder\Reset as BuilderReset;
use WPFormsUserRegistration\Process\Helpers\UserReset;
use WPFormsUserRegistration\SmartTags\Helpers\Helper as SmartTagHelper;

/**
 * Password Reset processing class.
 *
 * @since 2.0.0
 */
class Reset extends Base {

	/**
	 * Temporary storage for password.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	private $password = '';

	/**
	 * Temporary storage for user id.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	private $user_id = '';

	/**
	 * Hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		parent::hooks();
		add_action( 'wpforms_process_complete', [ $this, 'process_complete' ], 9, 4 );
	}

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

		if ( ! $this->is_reset_form( $form_data ) || empty( $form_data['id'] ) ) {
			return;
		}

		$login_field = wpforms_get_form_fields_by_meta( 'nickname', 'login', $form_data );
		$login_field = reset( $login_field );

		if ( ! $login_field || empty( $fields[ $login_field['id'] ]['value'] ) ) {

			if ( ! UserReset::is_reset_in_progress() ) {
				/**
				 * This filter allows overwriting a reset password no user provided error message.
				 *
				 * This message displayed on the front page if the user ot entered Username or Email Address.
				 *
				 * @param string $message Message text.
				 *
				 * @since 2.0.0
				 */
				$error_msg = apply_filters( 'wpforms_user_registration_process_reset_process_no_user_provided_error_message', esc_html__( 'Unable to reset user, Username or Email Address is missing.', 'wpforms-user-registration' ) );

				wpforms()->get( 'process' )->errors[ $form_data['id'] ]['header'] = $error_msg;
			}

			return;
		}

		$user_login = $fields[ $login_field['id'] ]['value'];

		$user = get_user_by( 'login', $user_login );

		// Logins can be emails.
		if ( ! $user ) {
			$user = get_user_by( 'email', $user_login );
		}

		if ( ! $user ) {

			/**
			 * This filter allows overwriting a "reset password no user found" error message.
			 *
			 * The message is displayed on the front page if a user tried to enter not existing user while resetting a password.
			 *
			 * @since 2.0.0
			 *
			 * @param string $message Message text.
			 */
			$error_msg = apply_filters( 'wpforms_user_registration_process_reset_process_no_user_found_error_message', esc_html__( 'Unable to reset a password. The user does not exist.', 'wpforms-user-registration' ) );

			wpforms_log(
				$error_msg,
				$user_login,
				[
					'type'    => [ 'error' ],
					'form_id' => $form_data['id'],
					'parent'  => $entry['id'],
				]
			);

			wpforms()->get( 'process' )->errors[ $form_data['id'] ]['header'] = $error_msg;

			return;
		}

		$this->user_id = $user->ID;
	}

	/**
	 * Process complete a form.
	 *
	 * @since 2.0.0
	 *
	 * @param array $fields    The fields that have been submitted.
	 * @param array $entry     The post data submitted by the form.
	 * @param array $form_data The information for the form.
	 * @param int   $entry_id  The entry ID.
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function process_complete( $fields, $entry, $form_data, $entry_id ) {

		if ( ! $this->is_reset_form( $form_data ) || empty( $form_data['id'] ) ) {
			return;
		}

		if ( ! UserReset::is_reset_in_progress() ) {

			if ( empty( $this->user_id ) ) {
				return;
			}

			SmartTagHelper::set_user( $this->user_id );

			wpforms_user_registration()->get( 'email_notifications' )->reset_password( $this->user_id, $form_data, $fields, $entry_id );
		}

		$this->reset_password( $fields, $form_data, $entry_id );
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
	 * @noinspection PhpUnused
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function process_after_filter( $fields, $entry, $form_data ) {

		if ( ! $this->is_reset_form( $form_data ) ) {
			return $fields;
		}

		$password_field = wpforms_get_form_fields_by_meta( 'nickname', 'password', $form_data );
		$password_field = reset( $password_field );
		$this->password = $fields[ $password_field['id'] ]['value_raw'] ?? '';

		return $this->hide_password_value( $fields );
	}

	/**
	 * Process password reset.
	 *
	 * @since 2.0.0
	 *
	 * @param array $fields    The fields that have been submitted.
	 * @param array $form_data The information for the form.
	 * @param int   $entry_id  The entry ID.
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	private function reset_password( $fields, $form_data, $entry_id ) {

		$user = UserReset::get_user();

		if ( ! $user ) {
			return;
		}

		UserReset::set_cookie( ' ', time() - YEAR_IN_SECONDS );

		if ( empty( $this->password ) ) {

			wpforms_log(
				'Unable to reset user password.',
				isset( $user->ID ) ? $user->ID : '',
				[
					'type'    => [ 'error' ],
					'form_id' => $form_data['id'],
					'parent'  => $entry_id,
				]
			);

			return;
		}

		reset_password( $user, $this->password );
	}

	/**
	 * Check if current form is reset one.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_data The information for the form.
	 *
	 * @return bool
	 */
	private function is_reset_form( $form_data ) {

		return ! empty( $form_data['meta']['template'] ) && $form_data['meta']['template'] === BuilderReset::TEMPLATE_SLUG;
	}
}
