<?php

namespace WPFormsUserRegistration\Process;

use WPFormsUserRegistration\Process\Helpers\UserProcess;
use WPFormsUserRegistration\Process\Helpers\UserRegistration;
use WPFormsUserRegistration\Process\Helpers\UserLogin;

/**
 * Registration processing class.
 *
 * @since 2.0.0
 */
class Registration extends Base {

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
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function process( $fields, $entry, $form_data ) {

		/**
		 * Bail out in several cases:
		 * 1) if it is not a registration form.
		 * 2) if the conditional logic for registration is not met.
		 * 3) if form contains errors.
		 */
		if (
			! UserRegistration::is_registration_enabled( $form_data ) ||
			! $this->is_registration_needed( $fields, $form_data ) ||
			! empty( wpforms()->get( 'process' )->errors[ $form_data['id'] ] )
		) {
			return;
		}

		$reg_fields = $this->get_required_fields( $fields, $form_data );

		// Check that we have all the required fields, if not abort.
		if ( empty( $reg_fields['email'] ) ) {
			wpforms()->get( 'process' )->errors[ $form_data['id'] ]['header'] = esc_html__( 'Email address is required.', 'wpforms-user-registration' );

			return;
		}

		// Check that username does not already exist.
		if ( username_exists( $reg_fields['username'] ) ) {

			// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation, WPForms.Comments.SinceTagHooks.MissingSinceTag
			$message = apply_filters_deprecated(
				'wpforms_user_registration_username_exists',
				[ esc_html__( 'A user with that username already exists.', 'wpforms-user-registration' ) ],
				'2.0.0 of the WPForms User Registration plugin',
				'wpforms_user_registration_process_registration_process_username_exists_error_message'
			);

			/**
			 * This filter allows overwriting username exists error message.
			 *
			 * @since 2.0.0
			 *
			 * @param string $message Error Message text.
			 */
			wpforms()->get( 'process' )->errors[ $form_data['id'] ]['header'] = apply_filters( 'wpforms_user_registration_process_registration_process_username_exists_error_message', $message );

			return;
		}

		// Check if username is valid.
		if ( ! validate_username( $reg_fields['username'] ) ) {

			/**
			 * This filter allows overwriting username invalid error message.
			 *
			 * @since 2.0.0
			 *
			 * @param string $message Error Message text.
			 */
			wpforms()->get( 'process' )->errors[ $form_data['id'] ]['header'] = apply_filters( 'wpforms_user_registration_process_registration_process_username_invalid_error_message', esc_html__( 'This username is invalid because it uses illegal characters. Please enter a valid username.', 'wpforms-user-registration' ) );

			return;
		}

		// Check that email does not already exist.
		if ( ! email_exists( $reg_fields['email'] ) ) {
			return;
		}

		// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
		$message = apply_filters_deprecated(
			'wpforms_user_registration_email_exists',
			[ esc_html__( 'A user with that email already exists.', 'wpforms-user-registration' ) ],
			'2.0.0 of the WPForms User Registration plugin',
			'wpforms_user_registration_process_registration_process_user_email_exists_error_message'
		);

		/**
		 * This filter allows overwriting user email exists error message.
		 *
		 * @since 2.0.0
		 *
		 * @param string $message Error Message text.
		 */
		wpforms()->get( 'process' )->errors[ $form_data['id'] ]['header'] = apply_filters( 'wpforms_user_registration_process_registration_process_user_email_exists_error_message', $message );
	}

	/**
	 * Generates an error message for a username created from an email address with illegal characters.
	 *
	 * @since 2.1.1
	 *
	 * @see Registration::get_required_fields for usage.
	 *
	 * @param string $error Default error message.
	 *
	 * @return string Substituted error message.
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function username_from_email_message( $error ) {

		return esc_html__( 'Your email has been used to create a username but it contains illegal characters. Please use an email address without special characters.', 'wpforms-user-registration' );
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

		if ( ! UserRegistration::is_registration_enabled( $form_data ) ) {
			return;
		}

		if ( ! empty( wpforms()->get( 'process' )->errors[ $form_data['id'] ] ) ) {

			wpforms_log(
				'User registration stopped by error.',
				wpforms()->get( 'process' )->errors[ $form_data['id'] ] ,
				[
					'type'    => [ 'error' ],
					'form_id' => $form_data['id'],
					'parent'  => $entry_id,
				]
			);

			return;
		}

		if ( ! $this->is_registration_needed( $fields, $form_data ) ) {

			wpforms_log(
				'User registration stopped by conditional logic.',
				$fields,
				[
					'type'    => [ 'conditional_logic' ],
					'form_id' => $form_data['id'],
					'parent'  => $entry_id,
				]
			);

			return;
		}

		$user_data = $this->get_data( $fields, $form_data );

		// Create user.
		$user_id = wp_insert_user( $user_data );

		// Something's wrong with user created.
		if ( is_wp_error( $user_id ) ) {

			wpforms()->get( 'process' )->errors[ $form_data['id'] ]['header'] = $user_id->get_error_message();

			wpforms_log(
				'Unable to create user account',
				$user_id,
				[
					'type'    => [ 'error' ],
					'form_id' => $form_data['id'],
					'parent'  => $entry_id,
				]
			);

			return;
		}

		$this->add_custom_meta( $user_id, $fields, $form_data );

		$this->set_author_id_for_uploaded_media( $user_id, $fields );

		$this->set_pending( $user_id, $form_data, $entry_id );

		wpforms_user_registration()->get( 'email_notifications' )->notification( $user_id, $user_data, $form_data, $fields, $entry_id );

		// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
		do_action_deprecated(
			'wpforms_user_registered',
			[ $user_id, $fields, $form_data, $user_data ],
			'2.0.0 of the WPForms User Registration plugin',
			'wpforms_user_registration_process_registration_process_completed_after'
		);

		/**
		 * This action fires after user registered account.
		 *
		 * @since 2.0.0
		 *
		 * @param int   $user_id   User id.
		 * @param array $fields    The fields that have been submitted.
		 * @param array $form_data The information for the form.
		 * @param array $user_data User data.
		 */
		do_action( 'wpforms_user_registration_process_registration_process_completed_after', $user_id, $fields, $form_data, $user_data );

		$this->maybe_auto_login( $form_data['settings'], $user_data );
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

		if ( ! UserRegistration::is_registration_enabled( $form_data ) ) {
			return $fields;
		}

		$password_field_id = isset( $form_data['settings']['registration_password'] ) && $form_data['settings']['registration_password'] !== '' ? absint( $form_data['settings']['registration_password'] ) : '';

		UserRegistration::set_password( $fields[ $password_field_id ]['value_raw'] ?? '' );

		return $this->hide_password_value( $fields );
	}

	/**
	 * Maybe auto log in user after registration.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_settings Form settings.
	 * @param array $user_data     User data.
	 */
	private function maybe_auto_login( $form_settings, $user_data ) {

		if (
			empty( $form_settings['registration_auto_log_in'] ) ||
			! empty( $form_settings['registration_activation'] ) ||
			is_user_logged_in()
		) {
			return;
		}

		$credentials = [
			'user_login'    => $user_data['user_email'],
			'user_password' => $user_data['user_pass'],
			'remember'      => true,
		];

		wp_signon( $credentials, UserLogin::maybe_force_secure_cookie( $credentials ) );
	}

	/**
	 * Set user to pending if user activation is enabled.
	 *
	 * @since 2.0.0
	 *
	 * @param int    $user_id   The user id.
	 * @param array  $form_data The information for the form.
	 * @param string $entry_id  The information for the form.
	 */
	private function set_pending( $user_id, $form_data, $entry_id ) {

		if ( ! UserRegistration::get_activation_type( $form_data['settings'] ) ) {
			return;
		}

		add_user_meta( $user_id, 'wpforms-pending', true );
		add_user_meta( $user_id, 'wpforms-role', $this->get_role( $form_data ) );
		add_user_meta( $user_id, 'wpforms-form-id', $form_data['id'] );
		add_user_meta( $user_id, 'wpforms-entry-id', $entry_id );

		if ( ! empty( $form_data['settings']['registration_activation_confirmation'] ) ) {
			add_user_meta( $user_id, 'wpforms-confirmation', $form_data['settings']['registration_activation_confirmation'] );
		}
	}

	/**
	 * Get user role.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_data The information for the form.
	 *
	 * @return mixed
	 */
	private function get_role( $form_data ) {

		return ! empty( $form_data['settings']['registration_role'] ) ? $form_data['settings']['registration_role'] : get_option( 'default_role' );
	}


	/**
	 * Check conditional logic if registration needed.
	 *
	 * @since 2.0.0
	 *
	 * @param array $fields    The fields that have been submitted.
	 * @param array $form_data The information for the form.
	 *
	 * @return bool
	 */
	private function is_registration_needed( $fields, $form_data ) {

		if ( ! isset( $form_data['settings']['user_registration']['registration_conditional'] ) ) {
			return true;
		}

		$settings = $form_data['settings']['user_registration']['registration_conditional'];

		if (
			empty( $settings['conditional_logic'] ) ||
			empty( $settings['conditional_type'] ) ||
			empty( $settings['conditionals'] )
		) {
			return true;
		}

		$process = wpforms_conditional_logic()->process( $fields, $form_data, $settings['conditionals'] );

		return $settings['conditional_type'] === 'create' ? $process : ! $process;
	}

	/**
	 * Get user data.
	 *
	 * @since 2.0.0
	 *
	 * @param array $fields    The fields that have been submitted.
	 * @param array $form_data The information for the form.
	 *
	 * @return array
	 */
	private function get_data( $fields, $form_data ) {

		$reg_fields = array_merge( $this->get_required_fields( $fields, $form_data ), $this->get_optional_fields( $fields, $form_data ) );

		// Required user information.
		$user_data = [
			'user_login' => $reg_fields['username'],
			'user_email' => $reg_fields['email'],
			'user_pass'  => $reg_fields['password'],
			'role'       => ! UserRegistration::get_activation_type( $form_data['settings'] ) ? $reg_fields['role'] : false,
		];

		// Optional user information.
		if ( ! empty( $reg_fields['website'] ) ) {
			$user_data['user_url'] = $reg_fields['website'];
		}
		if ( ! empty( $reg_fields['first_name'] ) ) {
			$user_data['first_name'] = $reg_fields['first_name'];
		}
		if ( ! empty( $reg_fields['last_name'] ) ) {
			$user_data['last_name'] = $reg_fields['last_name'];
		}
		if ( ! empty( $reg_fields['bio'] ) ) {
			$user_data['description'] = $reg_fields['bio'];
		}

		/**
		 * Filter user data before using them in wp_insert_user.
		 *
		 * @since 2.1.0
		 *
		 * @param array $user_data User data.
		 * @param array $fields    The fields that have been submitted.
		 * @param array $form_data The information for the form.
		 */
		return (array) apply_filters( 'wpforms_user_registration_process_registration_get_data', $user_data, $fields, $form_data );
	}

	/**
	 * Get optional user fields.
	 *
	 * @since 2.0.0
	 *
	 * @param array $fields    The fields that have been submitted.
	 * @param array $form_data The information for the form.
	 *
	 * @return array
	 */
	private function get_optional_fields( $fields, $form_data ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$optional        = [ 'name', 'bio', 'website' ];
		$form_settings   = $form_data['settings'];
		$optional_fields = [];

		foreach ( $optional as $opt ) {

			$key = 'registration_' . $opt;
			$id  = isset( $form_settings[ $key ] ) && $form_settings[ $key ] !== '' ? absint( $form_settings[ $key ] ) : '';

			if ( empty( $fields[ $id ]['value'] ) ) {
				continue;
			}

			if ( $opt === 'name' ) {

				$nkey                          = $form_data['fields'][ $id ]['format'] === 'simple' ? 'value' : 'first';
				$optional_fields['first_name'] = ! empty( $fields[ $id ][ $nkey ] ) ? $fields[ $id ][ $nkey ] : '';
				$optional_fields['last_name']  = ! empty( $fields[ $id ]['last'] ) ? $fields[ $id ]['last'] : '';
			} else {
				$optional_fields[ $opt ] = $fields[ $id ]['value'];
			}
		}

		$optional_fields['password'] = UserRegistration::get_password();
		$optional_fields['role']     = $this->get_role( $form_data );

		return $optional_fields;
	}

	/**
	 * Get required user fields.
	 *
	 * @since 2.0.0
	 *
	 * @param array $fields    The fields that have been submitted.
	 * @param array $form_data The information for the form.
	 *
	 * @return array
	 */
	private function get_required_fields( $fields, $form_data ) { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		$form_settings   = $form_data['settings'];
		$required_fields = UserProcess::get_required_fields( $form_data, $fields, [ 'username', 'email' ] );

		// If a username was not set by field meta method then check for the mapped field.
		if ( isset( $form_settings['registration_username'] ) && ! empty( $fields[ $form_settings['registration_username'] ]['value'] ) ) {
			$required_fields['username'] = $fields[ $form_settings['registration_username'] ]['value'];
		}

		// If an email was not set by field meta method then check for the mapped field.
		if ( isset( $form_settings['registration_email'] ) && ! empty( $fields[ $form_settings['registration_email'] ]['value'] ) ) {
			$required_fields['email'] = $fields[ $form_settings['registration_email'] ]['value'];
		}

		// If we _still_ don't have a username, then fallback to using email.
		if ( ! isset( $required_fields['username'] ) && isset( $required_fields['email'] ) ) {
			$required_fields['username'] = $required_fields['email'];

			add_filter( 'wpforms_user_registration_process_registration_process_username_invalid_error_message', [ $this, 'username_from_email_message' ], 1 );
		}

		return $required_fields;
	}

	/**
	 * Add custom user meta.
	 *
	 * @since 2.0.0
	 *
	 * @param int   $user_id   The user id.
	 * @param array $fields    The fields that have been submitted.
	 * @param array $form_data The information for the form.
	 */
	private function add_custom_meta( $user_id, $fields, $form_data ) {

		$form_settings = $form_data['settings'];

		if ( empty( $form_settings['registration_meta'] ) ) {
			return;
		}

		foreach ( $form_settings['registration_meta'] as $key => $id ) {

			if ( empty( $key ) || ( empty( $id ) && $id !== '0' ) || empty( $fields[ $id ]['value'] ) ) {
				continue;
			}

			// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
			$value = apply_filters_deprecated(
				'wpforms_user_registration_process_meta',
				[ $fields[ $id ]['value'], $key, $id, $fields, $form_data ],
				'2.0.0 of the WPForms User Registration plugin',
				'wpforms_user_registration_process_registration_custom_meta_value'
			);

			/**
			 * This filter allows overwriting custom meta while processing user registration.
			 *
			 * @since 2.0.0
			 *
			 * @param string $value     Meta value.
			 * @param string $key       Meta key.
			 * @param string $id        Meta id.
			 * @param array  $fields    The fields that have been submitted.
			 * @param array  $form_data The information for the form.
			 */
			$value = apply_filters( 'wpforms_user_registration_process_registration_custom_meta_value', $value, $key, $id, $fields, $form_data );

			update_user_meta( $user_id, $key, $value );
		}
	}

	/**
	 * Set author_id for uploaded media.
	 *
	 * @since 2.0.0
	 *
	 * @param int   $user_id User ID.
	 * @param array $fields  Fields.
	 */
	private function set_author_id_for_uploaded_media( $user_id, $fields ) {

		foreach ( $fields as $field ) {

			if (
				$field['type'] !== 'file-upload' ||
				( empty( $field['attachment_id'] ) && empty( $field['value_raw'] ) )
			) {
				continue;
			}

			if ( isset( $field['attachment_id'] ) ) {
				wp_update_post(
					[
						'ID'          => $field['attachment_id'],
						'post_author' => $user_id,
					]
				);

				continue;
			}

			foreach ( $field['value_raw'] as $file ) {
				if ( ! $file['attachment_id'] ) {
					continue;
				}

				wp_update_post(
					[
						'ID'          => $file['attachment_id'],
						'post_author' => $user_id,
					]
				);
			}
		}
	}
}
