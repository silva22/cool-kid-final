<?php

namespace WPFormsUserRegistration\Admin;

use WP_User;
use WP_User_Query;
use WPFormsUserRegistration\Helper;
use WPFormsUserRegistration\SmartTags\Helpers\Helper as SmartTagHelper;

/**
 * Actions for Users listing WP page.
 *
 * @since 2.0.0
 */
class UsersListing {

	/**
	 * Add hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_action( 'pre_user_query', [ $this, 'pre_user_query' ] );
		add_action( 'views_users', [ $this, 'users_list_view' ] );
		add_action( 'admin_print_scripts-users.php', [ $this, 'users_list_enqueue' ] );
		add_action( 'admin_print_scripts-site-users.php', [ $this, 'users_list_enqueue' ] );
		add_action( 'user_row_actions', [ $this, 'users_list_actions' ], 10, 2 );
		add_action( 'ms_user_row_actions', [ $this, 'users_list_actions' ], 10, 2 );
		add_action( 'admin_action_wpforms_approve', [ $this, 'approve' ] );
		add_action( 'admin_action_wpforms_bulk_approve', [ $this, 'bulk_approve' ] );
		add_action( 'admin_action_wpforms_unapprove', [ $this, 'unapprove' ] );
		add_action( 'admin_action_wpforms_bulk_unapprove', [ $this, 'bulk_unapprove' ] );
		add_action( 'admin_action_wpforms_update', [ $this, 'update_message' ] );
		add_action( 'admin_action_wpforms_resend_activation', [ $this, 'resend_activation' ] );
	}

	/**
	 * Reset the user query to handle request for unapproved users only.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_User_Query $query Default WordPress User Query.
	 */
	public function pre_user_query( $query ) {

		if ( $query->query_vars['role'] !== 'wpforms_unapproved' ) {
			return;
		}

		unset( $query->query_vars['meta_query'][0] );

		$query->query_vars['role']       = '';
		$query->query_vars['meta_key']   = 'wpforms-pending'; // phpcs:ignore WordPress.DB.SlowDBQuery
		$query->query_vars['meta_value'] = true; // phpcs:ignore WordPress.DB.SlowDBQuery

		$query->prepare_query();
	}

	/**
	 * User table view.
	 *
	 * @since  2.0.0
	 *
	 * @param array $views An array of available list table views.
	 *
	 * @return array
	 */
	public function users_list_view( $views ) {

		$unapproved_users = get_users(
			[
				'meta_key'   => 'wpforms-pending', // phpcs:disable WordPress.DB.SlowDBQuery
				'meta_value' => true, // phpcs:disable WordPress.DB.SlowDBQuery
			]
		);

		if ( empty( $unapproved_users ) ) {
			return $views;
		}

		$url = $this->get_listing_url();

		$views['unapproved'] = sprintf(
			wp_kses( /* translators: %1$s - link to unapproved users list admin page; %2$s - CSS class name; %3$s - count of unapproved users. */
				__( '<a href="%1$s" class="%2$s" rel="noopener noreferrer">Unapproved <span class="count">(%3$s)</span></a>', 'wpforms-user-registration' ),
				[
					'a'    => [
						'href'  => [],
						'rel'   => [],
						'class' => [],
					],
					'span' => [
						'class' => [],
					],
				]
			),
			esc_url( add_query_arg( [ 'role' => 'wpforms_unapproved' ], $url ) ),
			$this->get_role() === 'wpforms_unapproved' ? 'current' : '',
			count( $unapproved_users )
		);

		return $views;
	}

	/**
	 * Enqueue the script for users table.
	 *
	 * @since 2.0.0
	 */
	public function users_list_enqueue() {

		$min = wpforms_get_min_suffix();

		wp_enqueue_script(
			'wpforms-admin-users',
			WPFORMS_USER_REGISTRATION_URL . "assets/js/admin-users{$min}.js",
			[ 'jquery' ],
			WPFORMS_USER_REGISTRATION_VERSION,
			true
		);

		wp_localize_script(
			'wpforms-admin-users',
			'wpforms_admin_users',
			[
				'approve'   => esc_html__( 'Approve', 'wpforms-user-registration' ),
				'unapprove' => esc_html__( 'Unapprove', 'wpforms-user-registration' ),
			]
		);
	}

	/**
	 * User table row action.
	 *
	 * @since 2.0.0
	 *
	 * @param array   $actions     Actions.
	 * @param WP_User $user_object User.
	 *
	 * @return array
	 */
	public function users_list_actions( $actions, $user_object ) {

		if ( get_current_user_id() === $user_object->ID || ! wpforms_current_user_can() ) {
			return $actions;
		}

		$url = $this->get_listing_url();

		if ( ! get_user_meta( $user_object->ID, 'wpforms-pending', true ) ) {

			$url = wp_nonce_url(
				add_query_arg(
					[
						'action' => 'wpforms_unapprove',
						'user'   => $user_object->ID,
						'role'   => $this->get_role(),
					],
					$url
				),
				'wpforms-unapprove-users'
			);

			$actions['wpforms-unapprove'] = sprintf(
				wp_kses( /* translators: %s - Link to unapprove users action. */
					__( '<a href="%s" class="submitunapprove" rel="noopener noreferrer">Unapprove</a>', 'wpforms-user-registration' ),
					[
						'a' => [
							'href'  => [],
							'rel'   => [],
							'class' => [],
						],
					]
				),
				esc_url( $url )
			);

			return $actions;
		}

		$url = wp_nonce_url(
			add_query_arg(
				[
					'action' => 'wpforms_approve',
					'user'   => $user_object->ID,
					'role'   => $this->get_role(),
				],
				$url
			),
			'wpforms-approve-users'
		);

		$actions['wpforms-approve'] = sprintf(
			wp_kses( /* translators: %s - Link to approve users action. */
				__( '<a href="%s" class="submitapprove" rel="noopener noreferrer">Approve</a>', 'wpforms-user-registration' ),
				[
					'a' => [
						'href'  => [],
						'rel'   => [],
						'class' => [],
					],
				]
			),
			esc_url( $url )
		);

		if ( get_user_meta( $user_object->ID, 'wpforms-activate', true ) ) {

			$url = wp_nonce_url(
				add_query_arg(
					[
						'action' => 'wpforms_resend_activation',
						'user'   => $user_object->ID,
					],
					$url
				),
				'wpforms-resend-activation'
			);

			$actions['wpforms-resend'] = sprintf(
				wp_kses( /* translators: %s - Link to resend activation email. */
					__( '<a href="%s" rel="noopener noreferrer">Resend activation email</a>', 'wpforms-user-registration' ),
					[
						'a' => [
							'href' => [],
							'rel'  => [],
						],
					]
				),
				esc_url( $url )
			);
		}

		return $actions;
	}

	/**
	 * Update user_meta to approve user.
	 *
	 * @since 2.0.0
	 */
	public function approve() {

		check_admin_referer( 'wpforms-approve-users' );

		$this->do_approve();
	}

	/**
	 * Resend notification for users activation.
	 *
	 * @since 2.0.0
	 */
	public function resend_activation() {

		check_admin_referer( 'wpforms-resend-activation' );

		$userids = $this->get_ids();

		foreach ( $userids as $id ) {

			SmartTagHelper::set_user( $id );

			wpforms_user_registration()->get( 'email_notifications' )->resend_activation( $id );
		}

		$this->redirect_after_action( 'wpforms-resent-activation', $userids );
	}

	/**
	 * Update user_meta in bulk to approve user.
	 *
	 * @since 2.0.0
	 */
	public function bulk_approve() {

		check_admin_referer( 'bulk-users' );

		$this->set_up_role_context();
		$this->do_approve();
	}

	/**
	 * Update user_meta to unapprove user.
	 *
	 * @since 2.0.0
	 */
	public function unapprove() {

		check_admin_referer( 'wpforms-unapprove-users' );

		$this->do_unapprove();
	}

	/**
	 * Update user_meta in bulk to unapprove user.
	 *
	 * @since 2.0.0
	 */
	public function bulk_unapprove() {

		check_admin_referer( 'bulk-users' );

		$this->set_up_role_context();
		$this->do_unapprove();
	}

	/**
	 * Add the update message to the admin notices queue.
	 *
	 * @since 2.0.0
	 */
	public function update_message() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		if ( ! isset( $_REQUEST['update'] ) || empty( $_REQUEST['count'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$count = absint( $_REQUEST['count'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		switch ( sanitize_text_field( wp_unslash( $_REQUEST['update'] ) ) ) {  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			case 'wpforms-approved':
				/**
				 * This filter allows overwriting a message which displays after admin activated account on the users.php admin page.
				 *
				 * @since 2.0.0
				 *
				 * @param string $message Message text.
				 */
				$message = apply_filters(
					'wpforms_user_registration_admin_users_listing_update_message_approved',
					sprintf(
						/* translators: %s - number of users. */
						_n( '%s user approved.', '%s users approved.', $count, 'wpforms-user-registration' ),
						number_format_i18n( $count )
					)
				);
				break;

			case 'wpforms-unapproved':
				/**
				 * This filter allows overwriting a message which displays after admin unapproved account on the users.php admin page.
				 *
				 * @since 2.0.0
				 *
				 * @param string $message Message text.
				 */
				$message = apply_filters(
					'wpforms_user_registration_admin_users_listing_update_message_unapproved',
					sprintf(
						/* translators: %s - number of users. */
						_n( '%s user unapproved.', '%s users unapproved.', $count, 'wpforms-user-registration' ),
						number_format_i18n( $count )
					)
				);
				break;

			case 'wpforms-resent-activation':
				/**
				 * This filter allows overwriting a message which displays after admin resent activation email on the users.php admin page.
				 *
				 * @since 2.0.0
				 *
				 * @param string $message Message text.
				 */
				$message = apply_filters(
					'wpforms_user_registration_admin_users_listing_update_message_resent_activation',
					sprintf(
						/* translators: %s - number of users. */
						_n( '%s user was notified.', '%s users were notified.', $count, 'wpforms-user-registration' ),
						number_format_i18n( $count )
					)
				);
				break;

			default:
				// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
				$message = apply_filters_deprecated(
					'wpforms_update_message_handler',
					[ '', sanitize_text_field( wp_unslash( $_REQUEST['update'] ) ) ], // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					'2.0.0 of the WPForms User Registration plugin',
					'wpforms_user_registration_admin_users_listing_update_message_default'
				);

				/**
				 * This filter allows overwriting a default message which displays on the users.php admin page after admin action.
				 *
				 * @since 2.0.0
				 *
				 * @param string $message Message text.
				 * @param string $update  Update type.
				 */
				$message = apply_filters( 'wpforms_user_registration_admin_users_listing_update_message_default', $message, sanitize_text_field( wp_unslash( $_REQUEST['update'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		add_settings_error(
			'wpforms_user_registration',
			esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['update'] ) ) ),  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			esc_html( $message ),
			'updated'
		);

		add_action( 'all_admin_notices', [ $this, 'display_settings_errors' ] );

		// Prevent other admin action handlers from trying to handle our action.
		$_REQUEST['action'] = - 1;
	}

	/**
	 * Display all addon settings errors.
	 *
	 * @since 2.0.0
	 */
	public function display_settings_errors() {

		settings_errors( 'wpforms_user_registration' );
	}

	/**
	 * Activate a user after Admin approve.
	 *
	 * @since 2.0.0
	 */
	private function do_approve() {

		$userids = $this->get_ids();

		foreach ( $userids as $id ) {

			$this->can_edit( $id );

			delete_user_meta( $id, 'wpforms-pending' );
			delete_user_meta( $id, 'wpforms-confirmation' );

			Helper::set_user_role( absint( $id ) );

			wpforms_user_registration()->get( 'email_notifications' )->after_activation( $id );

			// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
			do_action_deprecated(
				'wpforms_user_approve',
				[ $id ],
				'2.0.0 of the WPForms User Registration plugin',
				'wpforms_user_registration_admin_users_listing_do_approve_after'
			);

			/**
			 * This action fires after admin activated user account.
			 *
			 * @since 2.0.0
			 *
			 * @param int $id User id.
			 */
			do_action( 'wpforms_user_registration_admin_users_listing_do_approve_after', $id );
		}

		$this->redirect_after_action( 'wpforms-approved', $userids );
	}

	/**
	 * Update user_meta to unapprove user.
	 *
	 * @since 2.0.0
	 */
	private function do_unapprove() {

		$userids = $this->get_ids();

		foreach ( $userids as $id ) {

			$this->can_edit( $id );

			update_user_meta( $id, 'wpforms-pending', true );

			// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
			do_action_deprecated(
				'wpforms_user_unapprove',
				[ $id ],
				'2.0.0 of the WPForms User Registration plugin',
				'wpforms_user_registration_admin_users_listing_do_unapprove_after'
			);

			/**
			 * This action fires after admin unapproved user account.
			 *
			 * @since 2.0.0
			 *
			 * @param int $id User id.
			 */
			do_action( 'wpforms_user_registration_admin_users_listing_do_unapprove_after', $id );
		}

		$this->redirect_after_action( 'wpforms-unapproved', $userids );
	}

	/**
	 * Check permissions and assembles User IDs.
	 *
	 * @since 2.0.0
	 *
	 * @return array User IDs.
	 * @noinspection ForgottenDebugOutputInspection
	 */
	private function get_ids() {

		if ( empty( $_REQUEST['users'] ) && empty( $_REQUEST['user'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			wp_safe_redirect( $this->get_listing_url() );
			exit();
		}

		if ( ! current_user_can( 'promote_users' ) ) {
			wp_die(
				esc_html__( 'You can&#8217;t unapprove users.', 'wpforms-user-registration' ),
				'',
				[
					'back_link' => true,
				]
			);
		}

		return empty( $_REQUEST['users'] ) ? [ absint( $_REQUEST['user'] ) ] : array_map( 'absint', (array) $_REQUEST['users'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Set the role context on bulk actions.
	 *
	 * On bulk actions the role parameter is not passed, since we're using a form
	 * to submit information. The information is only available through the
	 * `_wp_http_referer` parameter, so we get it from there and make it available
	 * for the request.
	 *
	 * @since 2.0.0
	 */
	private function set_up_role_context() {

		if ( ! empty( $_REQUEST['role'] ) || empty( $_REQUEST['_wp_http_referer'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		$referrer = wp_parse_url( sanitize_text_field( wp_unslash( $_REQUEST['_wp_http_referer'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( empty( $referrer['query'] ) ) {
			return;
		}

		$args = wp_parse_args( $referrer['query'] );

		if ( empty( $args['role'] ) ) {
			return;
		}

		$_REQUEST['role'] = $args['role'];
	}

	/**
	 * Return the current role.
	 *
	 * If the user list is in the context of a specific role, this function makes
	 * sure that the requested role is valid. By returning `false` otherwise, we
	 * make sure that parameter gets removed from the activation link.
	 *
	 * @since 2.0.0
	 *
	 * @return string|bool The role key if set, false otherwise.
	 */
	private function get_role() {

		if ( ! isset( $_REQUEST['role'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}

		$roles   = array_keys( get_editable_roles() );
		$roles[] = 'wpforms_unapproved';

		if ( ! in_array( $_REQUEST['role'], $roles, true ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}

		return sanitize_key( wp_unslash( $_REQUEST['role'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Get listing URL.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function get_listing_url() {

		$site_id = isset( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return get_current_screen()->id === 'site-users-network' ? add_query_arg( [ 'id' => $site_id ], 'site-users.php' ) : 'users.php';
	}

	/**
	 * Check if a user has the edit_user capability.
	 *
	 * @since 2.0.0
	 *
	 * @param int $id User ID.
	 *
	 * @noinspection ForgottenDebugOutputInspection
	 */
	private function can_edit( $id ) {

		if ( ! current_user_can( 'edit_user', $id ) ) {
			wp_die(
				esc_html__( 'You can&#8217;t edit that user.', 'wpforms-user-registration' ),
				'',
				[
					'back_link' => true,
				]
			);
		}
	}

	/**
	 * Redirect after action.
	 *
	 * @since 2.0.0
	 *
	 * @param string $update  Update action.
	 * @param array  $userids User IDs.
	 */
	private function redirect_after_action( $update, $userids ) {

		wp_safe_redirect(
			add_query_arg(
				[
					'action' => 'wpforms_update',
					'update' => $update,
					'count'  => count( $userids ),
					'role'   => $this->get_role(),
				],
				$this->get_listing_url()
			)
		);
		exit();
	}
}
