<?php

namespace WPFormsUserRegistration\Admin\Builder;

use WPForms_Builder_Panel_Settings;

/**
 * Login form class for builder.
 *
 * @since 2.0.0
 */
class Login extends Base {

	/**
	 * Template slug.
	 *
	 * @since 2.0.0
	 */
	const TEMPLATE_SLUG = 'user_login';

	/**
	 * Load the custom template settings for the form template.
	 *
	 * @since 2.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings instance.
	 */
	public function settings_content( $instance ) {

		if ( empty( $instance->form_data['meta']['template'] ) || $instance->form_data['meta']['template'] !== self::TEMPLATE_SLUG ) {
			return;
		}

		$this->open_content_html();

		$this->header_html( esc_html__( 'User Registration - Login', 'wpforms-user-registration' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo $this->hide_form_settings( $instance, 'user_login_hide' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$this->close_content_html();
	}
}
