<?php

namespace WPFormsUserRegistration\Process;

use WP_Error;
use WP_User;
use WPFormsUserRegistration\Process\Helpers\UserReset;

/**
 * Main class to init processes.
 *
 * @since 2.0.0
 */
class Process {

	/**
	 * Init Processes.
	 *
	 * @since 2.0.0
	 */
	public function init_processes() {

		( new Login() )->hooks();
		( new Registration() )->hooks();
		( new Reset() )->hooks();

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_action( 'wpforms_user_registration_process_registration_process_completed_after', [ $this, 'post_submissions_current_user' ] );
		add_filter( 'wpforms_process_before_form_data', [ UserReset::class, 'filter_form_fields' ], 999 );
		add_filter( 'wp_authenticate_user', [ $this, 'user_authenticate' ] );
	}

	/**
	 * Integration with Post Submissions add-on.
	 * Sets newly registered user as the author of the post.
	 *
	 * @since 2.0.0
	 *
	 * @param int $user_id User ID.
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function post_submissions_current_user( $user_id ) { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		add_filter(
			'wpforms_post_submissions_post_args',
			static function ( $post_args, $form_data, $fields ) use ( $user_id ) {

				if ( ! empty( $form_data['settings']['post_submissions_author'] ) && $form_data['settings']['post_submissions_author'] === 'current_user' ) {
					$post_args['post_author'] = $user_id;
				}

				return $post_args;
			},
			10,
			3
		);
	}

	/**
	 * Check whether the user is approved. Throws error if not.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_User|WP_Error $userdata User data.
	 *
	 * @return WP_User|WP_Error
	 */
	public function user_authenticate( $userdata ) {

		if (
			is_wp_error( $userdata ) ||
			empty( get_user_meta( $userdata->ID, 'wpforms-pending', true ) ) ||
			$userdata->user_email === get_bloginfo( 'admin_email' )
		) {
			return $userdata;
		}

		return new WP_Error(
			'wpforms_confirmation_error',
			wp_kses(
				__( '<strong>ERROR:</strong> Your account must be activated before you can log in.', 'wpforms-user-registration' ),
				[
					'strong' => [],
				]
			)
		);
	}
}
