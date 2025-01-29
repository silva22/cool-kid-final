<?php

namespace WPFormsUserRegistration\Admin\Builder;

use WPForms_Builder_Panel_Settings;
use WPFormsUserRegistration\EmailNotifications\Helper as NotificationsHelper;

/**
 * Password Reset form class for builder.
 *
 * @since 2.0.0
 */
class Reset extends Base {

	/**
	 * Template slug.
	 *
	 * @since 2.0.0
	 */
	const TEMPLATE_SLUG = 'user_reset';

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

		$this->header_html( esc_html__( 'User Registration - Reset Password', 'wpforms-user-registration' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$output = NotificationsHelper::settings_html( $instance->form_data, 'user_reset_email_user', NotificationsHelper::default_user_reset_subject(), NotificationsHelper::default_user_reset_message(), true );

		$output .= $this->hide_form_settings( $instance, 'user_reset_hide' );

		wpforms_panel_fields_group(
			$output,
			[
				'title'       => esc_html__( 'Reset Password Email', 'wpforms-user-registration' ),
				'description' => esc_html__( 'You may customize the email that’s sent when a user requests a password reset.', 'wpforms-user-registration' ),
			]
		);

		$this->close_content_html();
	}
}
