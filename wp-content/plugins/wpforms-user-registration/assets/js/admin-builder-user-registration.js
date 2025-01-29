/* global wpforms_builder */

/**
 * WPForms User Registration builder form functions.
 *
 * @since 2.0.0
 */

'use strict';

var WPFormsUserRegistration = window.WPFormsUserRegistration || ( function( $ ) {

	/**
	 * Builder element.
	 *
	 * @since 2.0.0
	 */
	var $builder;

	/**
	 * Public functions and properties.
	 *
	 * @since 2.0.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function() {

			$( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 1.0.0
		 */
		ready: function() {

			$builder = $( '#wpforms-builder' );

			app.bindUIActions();

			// User Registration settings.
			app.registrationToggle();

			// User Activation settings.
			app.activationToggle();

			app.hideMessageToggle();
		},

		/**
		 * Element bindings.
		 *
		 * @since 1.0.0
		 */
		bindUIActions: function() {

			$builder
				.on( 'change', '#wpforms-panel-field-settings-registration_enable', app.registrationToggle )
				.on( 'change', '#wpforms-panel-field-settings-registration_activation, #wpforms-panel-field-settings-registration_activation_method', app.activationToggle )
				.on( 'click', '.registration_email_template_toggle', app.toggleEmailTemplate )
				.on( 'change', '.user-registration-hide-form-logged-user input[type="checkbox"]', app.hideMessageToggle )
				.on( 'wpformsBeforeSave', app.showRequiredFieldsPopup );
		},

		/**
		 * Check if addon is enabled.
		 *
		 * @since 2.3.0
		 *
		 * @return {boolean} True if addon is enabled, false otherwise.
		 */
		isUserRegistrationEnabled() {
			return $( '#wpforms-panel-field-settings-registration_enable' ).is( ':checked' );
		},

		/**
		 * Show warning popup with message.
		 *
		 * @since 2.3.0
		 *
		 * @param {Event} event Event.
		 */
		showRequiredFieldsPopup( event ) {
			if ( ! app.isUserRegistrationEnabled() ) {
				return;
			}

			let hasErrors = false;

			if ( $( '#wpforms-panel-field-settings-registration_email' ).val().trim().length === 0 ) {
				hasErrors = true;
			}

			if ( hasErrors ) {
				$.alert( {
					title: wpforms_builder.heads_up,
					content: wpforms_builder.user_registration_required_email,
					icon: 'fa fa-exclamation-circle',
					type: 'orange',
					buttons: {
						confirm: {
							text: wpforms_builder.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
						},
					},
				} );

				event.preventDefault();
			}
		},

		/**
		 * Toggle the displaying settings depending on if user enabled registration.
		 *
		 * @since 2.0.0
		 */
		registrationToggle: function() {

			var $enable   = $( '#wpforms-panel-field-settings-registration_enable' ),
				$settings = $( '#wpforms-user-registration-forms-content-block' );

			if ( ! $enable.length ) {
				return;
			}

			if ( app.isUserRegistrationEnabled() ) {
				$settings.show();
			} else {
				$settings.hide();
			}
		},

		/**
		 * Toggle the displaying activation method settings.
		 *
		 * @since 1.0.0
		 */
		activationToggle: function() {

			var $activation             = $( '#wpforms-panel-field-settings-registration_activation' ),
				$method                 = $( '#wpforms-panel-field-settings-registration_activation_method-wrap' ),
				$confirmation           = $( '#wpforms-panel-field-settings-registration_activation_confirmation-wrap' ),
				$notifications          = $( '#wpforms-notifications-block-registration_email_user_activation' ),
				$autoLogIn              = $( '#wpforms-panel-field-settings-registration_auto_log_in-wrap' ),
				$afterEmail             = $( '#wpforms-panel-field-settings-registration_email_user_after_activation-wrap' ),
				$afterEmailNotification = $( '#wpforms-notifications-block-registration_email_user_after_activation' );

			if ( $activation.is( ':checked' ) ) {
				$autoLogIn.hide();
				$method.show();
				$afterEmail.show();

				if ( $method.find( 'option:selected' ).val() === 'user' ) {
					$confirmation.show();
					$notifications.show();
				} else {
					$confirmation.hide();
					$notifications.hide();
				}
			} else {
				$autoLogIn.show();
				$method.hide();
				$confirmation.hide();
				$notifications.hide();
				$afterEmail.hide();
				$afterEmailNotification.hide();
				$afterEmail.find( '.registration_email_template_toggle' ).html( wpforms_builder.user_registration_edit_template );
			}
		},

		/**
		 * Toggle the email template.
		 *
		 * @since 2.0.0
		 *
		 * @param {Event} e Event.
		 */
		toggleEmailTemplate: function( e ) {

			e.preventDefault();

			var $el = $( this ),
				$templateId = $el.parent().prop( 'id' ).replace( 'wpforms-panel-field-settings-', '' ).replace( '-wrap', '' ),
				$templateBlock = $( '#wpforms-notifications-block-' + $templateId );

			if ( $templateBlock.is( ':visible' ) ) {
				$templateBlock.hide();
				$el.html( wpforms_builder.user_registration_edit_template );
			} else {
				$templateBlock.show();
				$el.html( wpforms_builder.user_registration_hide_template );
			}
		},

		/**
		 * Toggle the hide message depending on if user hiding a from.
		 *
		 * @since 2.0.0
		 */
		hideMessageToggle: function() {

			var $hide = $( '.user-registration-hide-form-logged-user input[type="checkbox"]' );

			if ( ! $hide.length ) {
				return;
			}

			var $message = $( '.user-registration-hide-form-logged-user .wpforms-panel-field-textarea' );

			$message.toggle( $hide.is( ':checked' ) );
		},
	};

	return app;

}( jQuery ) );

// Initialize.
WPFormsUserRegistration.init();
