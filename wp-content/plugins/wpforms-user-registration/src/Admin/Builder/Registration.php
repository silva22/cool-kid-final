<?php

namespace WPFormsUserRegistration\Admin\Builder;

use WPForms_Builder_Panel_Settings;
use WPFormsUserRegistration\EmailNotifications\Helper as NotificationsHelper;
use WPFormsUserRegistration\Process\Helpers\UserRegistration as RegistrationHelper;

/**
 * Registration form class for builder.
 *
 * @since 2.0.0
 */
class Registration extends Base {

	/**
	 * Template slug.
	 *
	 * @since 2.0.0
	 */
	const TEMPLATE_SLUG = 'user_registration';

	/**
	 * User Registration settings content.
	 *
	 * @since 2.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings instance.
	 */
	public function settings_content( $instance ) {

		$form_template = $this->get_form_template( $instance );

		if ( in_array( $form_template, [ Login::TEMPLATE_SLUG, Reset::TEMPLATE_SLUG ], true ) ) {
			return;
		}

		$this->open_content_html();

		$this->header_html( esc_html__( 'User Registration', 'wpforms-user-registration' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$hide                = '';
		$is_default_template = $form_template === self::TEMPLATE_SLUG;

		if ( ! $is_default_template ) {

			wpforms_panel_field(
				'toggle',
				'settings',
				'registration_enable',
				$instance->form_data,
				esc_html__( 'Enable User Registration', 'wpforms-user-registration' )
			);

			$hide = 'display:none;';
		}

		echo '<div id="wpforms-user-registration-forms-content-block" style="' . esc_attr( $hide ) . '">';

		wpforms_panel_fields_group(
			$this->basic_fields( $instance ),
			[
				'title'       => esc_html__( 'Field Mapping', 'wpforms-user-registration' ),
				'description' => esc_html__( 'Connect your form fields to information in the user’s account.', 'wpforms-user-registration' ),
			]
		);

		wpforms_panel_fields_group(
			$this->user_role( $instance ),
			[
				'title'   => esc_html__( 'User Roles', 'wpforms-user-registration' ),
				'borders' => [ 'top' ],
			]
		);

		wpforms_panel_fields_group(
			$this->user_activation( $instance ),
			[
				'title'   => esc_html__( 'User Activation & Login', 'wpforms-user-registration' ),
				'borders' => [ 'top' ],
			]
		);

		wpforms_panel_fields_group(
			$this->email_notifications( $instance ),
			[
				'title'   => esc_html__( 'Email Notifications', 'wpforms-user-registration' ),
				'borders' => [ 'top' ],
			]
		);

		wpforms_panel_fields_group(
			$this->custom_user_meta( $instance ),
			[
				'title'       => esc_html__( 'Custom User Meta', 'wpforms-user-registration' ),
				'description' => esc_html__( 'Connect additional form fields to custom fields which are associated with the user’s account.', 'wpforms-user-registration' ),
				'borders'     => [ 'top' ],
			]
		);

		if ( ! $is_default_template ) {
			wpforms_panel_fields_group(
				$this->conditional_register( $instance ),
				[
					'title'   => esc_html__( 'Conditional Logic', 'wpforms-user-registration' ),
					'borders' => [ 'top' ],
				]
			);
		}

		// close forms content block.
		echo '</div>';

		$this->close_content_html();
	}

	/**
	 * Get form template.
	 *
	 * @since 2.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings instance.
	 *
	 * @return string
	 */
	private function get_form_template( $instance ) {

		return ! empty( $instance->form_data['meta']['template'] ) ? $instance->form_data['meta']['template'] : '';
	}

	/**
	 * Basic fields page content.
	 *
	 * @since 2.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings instance.
	 *
	 * @return string
	 */
	private function basic_fields( $instance ) {

		$output = '';

		$username = wpforms_get_form_fields_by_meta( 'nickname', 'username', $instance->form_data );

		if ( empty( $username ) ) {
			$output .= wpforms_panel_field(
				'select',
				'settings',
				'registration_username',
				$instance->form_data,
				esc_html__( 'Username', 'wpforms-user-registration' ),
				[
					'field_map'   => [ 'name', 'text' ],
					'placeholder' => esc_html__( '--- Select Field ---', 'wpforms-user-registration' ),
					'tooltip'     => esc_html__( 'If a username is not set or provided, the user\'s email address will be used instead.', 'wpforms-user-registration' ),
				],
				false
			);
		}

		$email = wpforms_get_form_fields_by_meta( 'nickname', 'email', $instance->form_data );

		if ( empty( $email ) ) {

			// Email.
			$output .= wpforms_panel_field(
				'select',
				'settings',
				'registration_email',
				$instance->form_data,
				esc_html__( 'Email', 'wpforms-user-registration' ),
				[
					'field_map'     => [ 'email' ],
					'placeholder'   => esc_html__( '--- Select Field ---', 'wpforms-user-registration' ),
					'after_tooltip' => '&nbsp;<span class="required">*</span>',
					'input_class'   => 'wpforms-required',
				],
				false
			);
		}

		// Name.
		$output .= wpforms_panel_field(
			'select',
			'settings',
			'registration_name',
			$instance->form_data,
			esc_html__( 'Name', 'wpforms-user-registration' ),
			[
				'field_map'   => [ 'name' ],
				'placeholder' => esc_html__( '--- Select Field ---', 'wpforms-user-registration' ),
			],
			false
		);

		// Password.
		$output .= wpforms_panel_field(
			'select',
			'settings',
			'registration_password',
			$instance->form_data,
			esc_html__( 'Password', 'wpforms-user-registration' ),
			[
				'field_map'   => [ 'password' ],
				'placeholder' => esc_html__( 'Auto generate', 'wpforms-user-registration' ),
			],
			false
		);

		// Website.
		$output .= wpforms_panel_field(
			'select',
			'settings',
			'registration_website',
			$instance->form_data,
			esc_html__( 'Website', 'wpforms-user-registration' ),
			[
				'field_map'   => [ 'text', 'url' ],
				'placeholder' => esc_html__( '--- Select Field ---', 'wpforms-user-registration' ),
			],
			false
		);

		// Bio.
		$output .= wpforms_panel_field(
			'select',
			'settings',
			'registration_bio',
			$instance->form_data,
			esc_html__( 'Biographical Info', 'wpforms-user-registration' ),
			[
				'field_map'   => [ 'textarea' ],
				'placeholder' => esc_html__( '--- Select Field ---', 'wpforms-user-registration' ),
			],
			false
		);

		return $output;
	}

	/**
	 * User role page content.
	 *
	 * @since 2.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings instance.
	 *
	 * @return string
	 */
	private function user_role( $instance ) {

		// Role.
		$editable_roles = array_reverse( get_editable_roles() );

		$roles_options = [];

		foreach ( $editable_roles as $role => $details ) {
			$roles_options[ $role ] = translate_user_role( $details['name'] );
		}

		$wpforms_panel_field = wpforms_panel_field(
			'select',
			'settings',
			'registration_role',
			$instance->form_data,
			'',
			[
				'default' => get_option( 'default_role' ),
				'options' => $roles_options,
			],
			false
		);

		if ( ! current_user_can( 'create_users' ) ) {
			$wpforms_panel_field = str_replace( '<select ', '<select disabled ', $wpforms_panel_field );
		}

		return $wpforms_panel_field;
	}

	/**
	 * User activation page content.
	 *
	 * @since 2.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings instance.
	 *
	 * @return string
	 */
	private function user_activation( $instance ) {

		// User Activation.
		$output = wpforms_panel_field(
			'toggle',
			'settings',
			'registration_activation',
			$instance->form_data,
			esc_html__( 'Enable user activation', 'wpforms-user-registration' ),
			[],
			false
		);

		// User Activation Method.
		$output .= wpforms_panel_field(
			'select',
			'settings',
			'registration_activation_method',
			$instance->form_data,
			esc_html__( 'User Activation Method', 'wpforms-user-registration' ),
			[
				'default' => 'user',
				'options' => [
					'user'  => esc_html__( 'User Email', 'wpforms-user-registration' ),
					'admin' => esc_html__( 'Manual Approval', 'wpforms-user-registration' ),
				],
				'tooltip' => esc_html__( 'User Email method sends an email to the user with a link to activate their account. Manual Approval requires site admin to approve account.', 'wpforms-user-registration' ),
			],
			false
		);

		$output .= NotificationsHelper::settings_html( $instance->form_data, 'registration_email_user_activation', NotificationsHelper::default_user_activation_subject(), NotificationsHelper::default_user_activation_message(), RegistrationHelper::get_activation_type( $instance->form_data['settings'] ) !== 'admin' );

		$p     = [];
		$pages = get_pages();

		foreach ( $pages as $page ) {
			/* translators: %d - a page ID. */
			$title          = esc_html( wpforms_get_post_title( $page ) );
			$depth          = count( $page->ancestors );
			$p[ $page->ID ] = str_repeat( '&nbsp;', $depth * 3 ) . $title;
		}

		$output .= wpforms_panel_field(
			'select',
			'settings',
			'registration_activation_confirmation',
			$instance->form_data,
			esc_html__( 'User Activation Confirmation Page', 'wpforms-user-registration' ),
			[
				'placeholder' => esc_html__( '-- Select Page --', 'wpforms-user-registration' ),
				'options'     => $p,
				'tooltip'     => esc_html__( 'Select the page to show the user after they activate their account.', 'wpforms-user-registration' ),
			],
			false
		);

		$output .= wpforms_panel_field(
			'toggle',
			'settings',
			'registration_auto_log_in',
			$instance->form_data,
			esc_html__( 'Enable auto log in', 'wpforms-user-registration' ),
			[
				'tooltip' => esc_html__( 'Automatically log the user in after they\'ve registered.', 'wpforms-user-registration' ),
			],
			false
		);

		$output .= $this->hide_form_settings( $instance, 'registration_hide' );

		return $output;
	}

	/**
	 * User activation page content.
	 *
	 * @since 2.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings instance.
	 *
	 * @return string
	 */
	private function email_notifications( $instance ) {

		$toggle_email_template_link = wp_kses(
			__( '<a href="#" class="registration_email_template_toggle">Edit Template</a>', 'wpforms-user-registration' ),
			[
				'a' => [
					'href'  => [],
					'class' => [],
				],
			]
		);

		// Admin Email.
		$output = wpforms_panel_field(
			'toggle',
			'settings',
			'registration_email_admin',
			$instance->form_data,
			esc_html__( 'Email the admin when a new user is created.', 'wpforms-user-registration' ),
			[
				'after' => $toggle_email_template_link,
				'class' => 'wpforms-user-registration-email-notifications-option',
			],
			false
		);

		$output .= NotificationsHelper::settings_html( $instance->form_data, 'registration_email_admin', NotificationsHelper::default_admin_subject(), NotificationsHelper::default_admin_message(), false );

		// User Email.
		$output .= wpforms_panel_field(
			'toggle',
			'settings',
			'registration_email_user',
			$instance->form_data,
			esc_html__( 'Email the user with their account information.', 'wpforms-user-registration' ),
			[
				'after' => $toggle_email_template_link,
				'class' => 'wpforms-user-registration-email-notifications-option',
			],
			false
		);

		$output .= NotificationsHelper::settings_html( $instance->form_data, 'registration_email_user', NotificationsHelper::default_user_subject(), NotificationsHelper::default_user_message(), false );

		// User Email.
		$output .= wpforms_panel_field(
			'toggle',
			'settings',
			'registration_email_user_after_activation',
			$instance->form_data,
			esc_html__( 'Email the user once their account has been activated.', 'wpforms-user-registration' ),
			[
				'after' => $toggle_email_template_link,
				'class' => 'wpforms-user-registration-email-notifications-option',
			],
			false
		);

		$output .= NotificationsHelper::settings_html( $instance->form_data, 'registration_email_user_after_activation', NotificationsHelper::default_user_after_activation_subject(), NotificationsHelper::default_user_after_activation_message(), false );

		return $output;
	}

	/**
	 * Custom User Meta content.
	 *
	 * @since 2.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings instance.
	 *
	 * @return string
	 */
	private function custom_user_meta( $instance ) {

		$fields = wpforms_get_form_fields( $instance->form_data );
		$meta   = ! empty( $instance->form_data['settings']['registration_meta'] ) ? $instance->form_data['settings']['registration_meta'] : [ false ];

		return wpforms_render(
			WPFORMS_USER_REGISTRATION_PATH . 'templates/builder/custom-user-meta',
			[
				'meta'   => $meta,
				'fields' => $fields,
			],
			true
		);
	}

	/**
	 * Conditional logic for registering user.
	 *
	 * @since 2.0.0
	 *
	 * @param WPForms_Builder_Panel_Settings $instance Settings instance.
	 *
	 * @return string
	 */
	private function conditional_register( $instance ) {

		return wpforms_conditional_logic()->builder_block(
			[
				'form'        => $instance->form_data,
				'type'        => 'panel',
				'panel'       => 'user_registration',
				'parent'      => 'settings',
				'subsection'  => 'registration_conditional',
				'actions'     => [
					'create'     => esc_html__( 'Create', 'wpforms-user-registration' ),
					'not_create' => esc_html__( 'Do not create', 'wpforms-user-registration' ),
				],
				'action_desc' => esc_html__( ' a user account if...', 'wpforms-user-registration' ),
				'reference'   => esc_html__( 'User Registration setting', 'wpforms-user-registration' ),
			],
			false
		);
	}
}
