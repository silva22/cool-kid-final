<?php

namespace WPFormsUserRegistration;

/**
 * User activation.
 *
 * @since 2.0.0
 */
class Activation {

	/**
	 * Initialize.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_action( 'init', [ $this, 'user_activation' ] );
		add_filter( 'the_content', [ $this, 'activated_user_message' ], 999 );
	}

	/**
	 * Listen for user activation key.
	 *
	 * @since 2.0.0
	 */
	public function user_activation() {

		if ( empty( $_GET['wpforms_activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$user_id = $this->get_user_id_from_activation_link();

		if ( $user_id === null ) {
			$user_id = $this->get_user_id_from_legacy_activation_link();
		}

		if ( $user_id === null ) {
			return;
		}

		delete_user_meta( $user_id, 'wpforms-pending' );
		delete_user_meta( $user_id, 'wpforms-activate' );

		Helper::set_user_role( absint( $user_id ) );

		// Redirect user to confirmation page.
		$redirect_page_url    = home_url();
		$confirmation_page_id = absint( get_user_meta( $user_id, 'wpforms-confirmation', true ) );

		// Make sure that page exists and is published.
		if ( $confirmation_page_id && get_post_status( $confirmation_page_id ) === 'publish' ) {
			$redirect_page_url = get_permalink( $confirmation_page_id );
		}

		delete_user_meta( $user_id, 'wpforms-confirmation' );

		wpforms_user_registration()->get( 'email_notifications' )->after_activation( $user_id );

		// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
		do_action_deprecated(
			'wpforms_user_approve',
			[ $user_id ],
			'2.0.0 of the WPForms User Registration plugin',
			'wpforms_user_registration_activation_user_activation_after'
		);

		/**
		 * This action fires after user confirmed his account.
		 *
		 * @since 2.0.0
		 *
		 * @param int $user_id User id.
		 */
		do_action( 'wpforms_user_registration_activation_user_activation_after', $user_id );

		// As we allow only internal redirects (to custom WP Page), it's safe to use wp_safe_redirect().
		wp_safe_redirect( $redirect_page_url );
		exit;
	}

	/**
	 * Display a message if account was already activated.
	 *
	 * @since 2.0.0
	 *
	 * @param string $content The content.
	 *
	 * @return string
	 */
	public function activated_user_message( $content ) {

		if ( ! isset( $_GET['wpforms_activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $content;
		}

		$message  = '<p>';
		$message .= esc_html__( 'The link is expired please contact the site administrator for more details.', 'wpforms-user-registration' );
		$message .= '</p>';

		/**
		 * This filter allows overwriting an already activated message.
		 *
		 * This message displayed on the front page if the user tried to activate an already activated account.
		 *
		 * @since 2.0.0
		 *
		 * @param string $message Message text.
		 */
		return apply_filters( 'wpforms_user_registration_activation_activated_user_message', $message );
	}

	/**
	 * Get User ID from activation link.
	 *
	 * @since 2.0.0
	 *
	 * @return null|int
	 */
	private function get_user_id_from_activation_link() {

		if ( ! isset( $_GET['wpforms_activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return null;
		}

		$hash = sanitize_text_field( wp_unslash( $_GET['wpforms_activate'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( empty( $hash ) ) {
			return null;
		}

		$user = get_users(
			[
				'number'     => 1,
				'meta_query' => [ // phpcs:ignore WordPress.DB.SlowDBQuery
					[
						'key'     => 'wpforms-activate',
						'value'   => $hash,
						'compare' => '==',
					],
				],
			]
		);

		if ( empty( $user ) ) {
			return null;
		}

		return reset( $user )->ID;
	}

	/**
	 * Get User ID from activation link prior 2.0.0.
	 *
	 * @since 2.0.0
	 *
	 * @return null|string
	 */
	private function get_user_id_from_legacy_activation_link() {

		if ( ! isset( $_GET['wpforms_activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return null;
		}

		$activation_data = sanitize_text_field( wp_unslash( $_GET['wpforms_activate'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$query_args      = base64_decode( $activation_data ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		parse_str( $query_args, $output );

		if ( empty( $output['hash'] ) || empty( $output['user_id'] ) || empty( $output['user_email'] ) ) {
			return null;
		}

		/*
		 * All values returned into array are already urldecode()'d.
		 * Thus, we need to manually fix "+" (plus) character in emails (user+test@gmail.com) - it appears to be a space now.
		 */
		if ( strpos( $output['user_email'], ' ' ) !== false ) {
			$output['user_email'] = str_replace( ' ', '+', $output['user_email'] );
		}

		// Verify hash matches.
		if ( wp_hash( $output['user_id'] . ',' . $output['user_email'] ) !== $output['hash'] ) {
			return null;
		}

		return $output['user_id'];
	}
}
