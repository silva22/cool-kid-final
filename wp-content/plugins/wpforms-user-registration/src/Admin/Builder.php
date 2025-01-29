<?php

namespace WPFormsUserRegistration\Admin;

/**
 * Main class to initialize builders.
 *
 * @since 2.0.0
 */
class Builder {

	/**
	 * Get instances of builder template settings.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_builders() {

		return [
			'login'        => new Builder\Login(),
			'registration' => new Builder\Registration(),
			'reset'        => new Builder\Reset(),
		];
	}

	/**
	 * Hooks.
	 *
	 * @since 2.0.0
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function hooks() {

		add_action( 'wpforms_builder_enqueues', [ $this, 'admin_enqueues' ] );
		add_filter( 'wpforms_builder_strings', [ $this, 'builder_strings' ] );
		add_filter( 'wpforms_builder_settings_sections', [ $this, 'settings_sections' ], 20, 2 );
		add_filter( 'wpforms_helpers_templates_include_html_located', [ $this, 'templates' ], 10, 2 );
		add_filter( 'wpforms_save_form_args', [ $this, 'save_form' ], 10, 3 );

		foreach ( $this->get_builders() as $builder ) {

			add_action( 'wpforms_form_settings_panel_content', [ $builder, 'settings_content' ], 20 );
		}
	}

	/**
	 * Save form arguments filter.
	 *
	 * @since 2.3.0
	 *
	 * @param array $form Form to be saved.
	 * @param array $data Initial form settings data.
	 * @param array $args Form updating args.
	 *
	 * @return array
	 * @noinspection PhpMissingParamTypeInspection
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function save_form( $form, $data, $args ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		$form_handler = wpforms()->get( 'form' );

		if ( $form_handler === null || current_user_can( 'create_users' ) ) {
			return $form;
		}

		$form_data       = json_decode( stripslashes( $form['post_content'] ), true );
		$saved_form_data = ! empty( $data['id'] ) ? $form_handler->get( $data['id'], [ 'content_only' => true ] ) : [];

		if ( ! empty( $saved_form_data['settings']['registration_role'] ) ) {
			$role = $saved_form_data['settings']['registration_role'];
		} else {
			$role = get_option( 'default_role' );
		}

		$form_data['settings']['registration_role'] = $role;
		$form['post_content']                       = wpforms_encode( $form_data );

		return $form;
	}

	/**
	 * Enqueue assets for the builder.
	 *
	 * @since 2.0.0
	 */
	public function admin_enqueues() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_style(
			'wpforms-builder-user-registration',
			WPFORMS_USER_REGISTRATION_URL . "assets/css/admin-builder{$min}.css",
			[],
			WPFORMS_USER_REGISTRATION_VERSION
		);

		wp_enqueue_script(
			'wpforms-builder-user-registration',
			WPFORMS_USER_REGISTRATION_URL . "assets/js/admin-builder-user-registration{$min}.js",
			[ 'jquery' ],
			WPFORMS_USER_REGISTRATION_VERSION,
			false
		);
	}

	/**
	 * Add our localized strings to be available in the form builder.
	 *
	 * @since 2.0.0
	 *
	 * @param array $strings Form builder strings.
	 *
	 * @return array
	 */
	public function builder_strings( $strings ) {

		$strings['user_registration_edit_template']  = esc_html__( 'Edit Template', 'wpforms-user-registration' );
		$strings['user_registration_hide_template']  = esc_html__( 'Hide Template', 'wpforms-user-registration' );
		$strings['user_registration_required_email'] = esc_html__( 'You started to configure the User Registration addon. Please fill out all required fields or disable the addon.', 'wpforms-user-registration' );

		return $strings;
	}

	/**
	 * Settings register section.
	 *
	 * @since 2.0.0
	 *
	 * @param array $sections  Settings sections.
	 * @param array $form_data Form data.
	 *
	 * @return array
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function settings_sections( $sections, $form_data ) {

		$sections['user_registration'] = esc_html__( 'User Registration', 'wpforms-user-registration' );

		return $sections;
	}

	/**
	 * Change a template location.
	 *
	 * @since 2.0.0
	 *
	 * @param string $located  Template location.
	 * @param string $template Template.
	 *
	 * @return string
	 */
	public function templates( $located, $template ) {

		// Checking if `$template` is an absolute path and passed from this plugin.
		if (
			( strpos( $template, WPFORMS_USER_REGISTRATION_PATH ) === 0 ) &&
			is_readable( $template )
		) {
			return $template;
		}

		return $located;
	}
}
