<?php

namespace WPFormsUserRegistration\Frontend;

use WPFormsUserRegistration\Process\Helpers\UserRegistration;

/**
 * Frontend actions class.
 *
 * @since 2.0.0
 */
class Frontend {

	/**
	 * Hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_filter( 'wpforms_frontend_load', [ $this, 'display_form' ], 999, 2 );
	}

	/**
	 * Maybe hide the form if the appropriate setting is selected.
	 *
	 * @since 2.0.0
	 *
	 * @param bool  $bool      Value to determine if form should be displayed.
	 * @param array $form_data The information for the form.
	 *
	 * @return bool
	 */
	public function display_form( $bool, $form_data ) {

		if ( $this->is_gb_editor() || ! is_user_logged_in() ) {
			return $bool;
		}

		if ( ! empty( $form_data['settings']['registration_hide'] ) && ! UserRegistration::is_registration_enabled( $form_data ) ) {
			return $bool;
		}

		foreach ( [ 'registration_hide', 'user_login_hide', 'user_reset_hide' ] as $setting ) {
			if ( empty( $form_data['settings'][ $setting ] ) ) {
				continue;
			}

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wpforms_process_smart_tags( wpautop( $form_data['settings'][ $setting . '_message' ] ), $form_data );

			return false;
		}

		return $bool;
	}

	/**
	 * Checking if is Gutenberg REST API call.
	 *
	 * @since 2.0.0
	 *
	 * @return bool True if is Gutenberg REST API call.
	 */
	private function is_gb_editor() {

		return defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && $_REQUEST['context'] === 'edit'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}


}
