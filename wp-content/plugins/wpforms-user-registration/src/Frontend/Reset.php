<?php

namespace WPFormsUserRegistration\Frontend;

use WPFormsUserRegistration\Admin\Builder\Reset as BuilderReset;
use WPFormsUserRegistration\Process\Helpers\UserReset;

/**
 * Frontend reset password actions class.
 *
 * @since 2.0.0
 */
class Reset {

	/**
	 * Hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_filter( 'wpforms_frontend_form_data', [ UserReset::class, 'filter_form_fields' ], 999 );
		add_filter( 'wpforms_frontend_confirmation_message', [ $this, 'confirmation_message' ], 999, 2 );
		add_action( 'wpforms_frontend_output', [ $this, 'invalid_link_message' ], 9, 5 );
		add_action( 'init', [ $this, 'maybe_set_cookie' ] );
	}

	/**
	 * Maybe set reset password cookie.
	 *
	 * @since 2.0.0
	 **/
	public function maybe_set_cookie() {

		if ( empty( $_GET['action'] ) || $_GET['action'] !== 'wpforms_rp' || empty( $_GET['login'] ) || empty( $_GET['key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$login = sanitize_user( wp_unslash( $_GET['login'] ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
		$key   = sanitize_text_field( wp_unslash( $_GET['key'] ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended

		UserReset::set_cookie( sprintf( '%s:%s', $login, $key ), 0 );

		$url = remove_query_arg( [ 'action', 'key', 'login' ] );

		if ( is_wp_error( check_password_reset_key( $key, $login ) ) ) {
			$url = add_query_arg( 'wpforms_rp_invalid', true, $url );
		}

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Display custom confirmation message depending on the stage of resetting password.
	 *
	 * @since 2.0.0
	 *
	 * @param string $message   Confirmation message.
	 * @param array  $form_data Form data and settings.
	 *
	 * @return string
	 */
	public function confirmation_message( $message, $form_data ) {

		if ( empty( $form_data['meta']['template'] ) || $form_data['meta']['template'] !== BuilderReset::TEMPLATE_SLUG ) {
			return $message;
		}

		// phpcs:disable
		if ( UserReset::is_reset_in_progress() ) {
			/**
			 * This filter allows overwriting a reset password completed confirmation message.
			 *
			 * This message displays on the reset password form after the user submitted new password.
			 *
			 * @since 2.0.0
			 *
			 * @param string $message   Confirmation message.
			 * @param array  $form_data Form data and settings.
			 */
			return apply_filters( 'wpforms_user_registration_frontend_reset_confirmation_message_password_completed', esc_html__( 'Your password has been reset.', 'wpforms-user-registration' ), $form_data );
		}
		// phpcs:enable

		/**
		 * This filter allows overwriting a reset password confirmation message.
		 *
		 * This message displays on the reset password form after the user submitted a username.
		 *
		 * @since 2.0.0
		 *
		 * @param string $message   Confirmation message.
		 * @param array  $form_data Form data and settings.
		 */
		return apply_filters( 'wpforms_user_registration_frontend_reset_confirmation_message_password_link', esc_html__( 'Check your email for the confirmation link.', 'wpforms-user-registration' ), $form_data );
	}

	/**
	 * Display invalid link message.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_data   Form data and settings.
	 * @param null  $deprecated  Deprecated.
	 * @param bool  $title       Whether to display form title.
	 * @param bool  $description Whether to display form description.
	 * @param array $errors      List of all errors filled in WPForms_Process::process().
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function invalid_link_message( $form_data, $deprecated, $title, $description, $errors ) {

		if ( empty( $_GET['wpforms_rp_invalid'] ) || empty( $_COOKIE[ UserReset::get_cookie_name() ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		UserReset::set_cookie( ' ', time() - YEAR_IN_SECONDS );

		$message = wp_kses(
			__( '<strong>Error:</strong> Your password reset link appears to be invalid. Please request a new link below.', 'wpforms-user-registration' ),
			[
				'strong' => [],
			]
		);

		/**
		 * This filter allows overwriting an invalid reset password link message.
		 *
		 * This message displays on the reset password form if link is invalid.
		 *
		 * @since 2.0.0
		 *
		 * @param string $message Message.
		 */
		$message = apply_filters( 'wpforms_user_registration_frontend_reset_invalid_link_message', $message );

		$frontend_obj = wpforms()->get( 'frontend' );

		if ( ! $frontend_obj ) {
			return;
		}

		$frontend_obj->form_error( 'header', $message );
	}
}
