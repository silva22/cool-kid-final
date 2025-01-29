<?php

namespace WPFormsUserRegistration\Admin\Builder;

use WPForms_Builder_Panel_Settings;

/**
 * Base class for all template-related settings.
 *
 * @since 2.0.0
 */
abstract class Base {

	/**
	 * Settings content.
	 *
	 * @since 2.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings instance.
	 */
	abstract protected function settings_content( $instance );

	/**
	 * Get header HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $title Title.
	 */
	protected function header_html( $title ) {

		printf( '<div class="wpforms-panel-content-section-title">%s</div>', esc_html( $title ) );
	}

	/**
	 * Open content HTML.
	 *
	 * @since 2.0.0
	 */
	protected function open_content_html() {

		echo '<div class="wpforms-panel-content-section wpforms-panel-content-section-user_registration">';
	}

	/**
	 * Close content HTML.
	 *
	 * @since 2.0.0
	 */
	protected function close_content_html() {

		echo '</div>';
	}

	/**
	 * Hide form settings for logged-in users.
	 *
	 * @since 2.0.0
	 *
	 * @param object $instance Settings instance.
	 * @param string $field    Field name.
	 *
	 * @return string
	 */
	protected function hide_form_settings( $instance, $field ) {

		$output = '<div class="user-registration-hide-form-logged-user">';

		$output .= wpforms_panel_field(
			'toggle',
			'settings',
			$field,
			$instance->form_data,
			esc_html__( 'Hide form if user is logged in', 'wpforms-user-registration' ),
			[],
			false
		);

		$default_hide_message = wp_kses(
			sprintf( /* translators: %1$s - user's first name, %2$s - logout url. */
				__( 'Hi %1$s, you’re already logged in. <a href="%2$s">Log out</a>.', 'wpforms-user-registration' ),
				'{user_first_name}',
				'{url_logout}'
			),
			[
				'a' => [
					'href' => [],
				],
			]
		);

		$output .= wpforms_panel_field(
			'textarea',
			'settings',
			$field . '_message',
			$instance->form_data,
			esc_html__( 'Logged In Message', 'wpforms-user-registration' ),
			[
				'rows'        => 6,
				'default'     => $default_hide_message,
				'smarttags'   => [
					'type' => 'all',
				],
				'tooltip'     => esc_html__( 'Message to display instead of a form for logged in users.', 'wpforms-user-registration' ),
				'input_class' => 'wpforms-required',
				'class'       => empty( $instance->form_data['settings'][ $field ] ) ? 'wpforms-hidden' : '',
			],
			false
		);

		$output .= '</div>';

		return $output;
	}
}
