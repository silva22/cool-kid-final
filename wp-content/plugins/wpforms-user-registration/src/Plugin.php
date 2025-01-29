<?php

namespace WPFormsUserRegistration;

use stdClass;
use WPForms_Updater;
use WPFormsUserRegistration\Admin\Settings;
use WPFormsUserRegistration\EmailNotifications\Notifications;
use WPFormsUserRegistration\Frontend\Reset;
use WPFormsUserRegistration\Frontend\Frontend;
use WPFormsUserRegistration\Process\Process;

/**
 * Class Plugin that loads the whole plugin.
 *
 * @since 2.0.0
 */
class Plugin {

	/**
	 * Email Notifications class.
	 *
	 * @since 2.0.0
	 *
	 * @var EmailNotifications\Notifications
	 */
	private $email_notifications;

	/**
	 * Get a single instance of the addon.
	 *
	 * @since 2.0.0
	 *
	 * @return Plugin
	 */
	public static function get_instance() {

		static $instance;

		if ( ! $instance ) {
			$instance = new self();

			$instance->init();
		}

		return $instance;
	}

	/**
	 * Init class.
	 *
	 * @since 2.0.0
	 *
	 * @return Plugin
	 */
	private function init() {

		( new Migrations\Migrations() )->init();

		if ( is_admin() ) {
			( new Admin\Builder() )->hooks();
			( new Admin\UsersListing() )->hooks();
			( new Settings() )->hooks();
		} else {
			( new Activation() )->hooks();
			( new Frontend() )->hooks();
			( new Reset() )->hooks();
		}

		( new Process() )->init_processes();
		( new SmartTags() )->hooks();

		$this->email_notifications = new Notifications();

		$this->hooks();

		return $this;
	}

	/**
	 * Hooks.
	 *
	 * @since 2.0.0
	 */
	private function hooks() {

		add_action( 'init', [ $this, 'load_templates' ], 15 );
		add_action( 'init', [ $this, 'logout_unapproved_users' ] );

		add_action( 'wpforms_updater', [ $this, 'updater' ] );
	}

	/**
	 * Load the plugin updater.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key License key.
	 */
	public function updater( $key ) {

		new WPForms_Updater(
			[
				'plugin_name' => 'WPForms User Registration',
				'plugin_slug' => 'wpforms-user-registration',
				'plugin_path' => plugin_basename( WPFORMS_USER_REGISTRATION_FILE ),
				'plugin_url'  => trailingslashit( WPFORMS_USER_REGISTRATION_URL ),
				'remote_url'  => WPFORMS_UPDATER_API,
				'version'     => WPFORMS_USER_REGISTRATION_VERSION,
				'key'         => $key,
			]
		);
	}

	/**
	 * Get property.
	 *
	 * @since 2.0.0
	 *
	 * @param string $property_name Property name.
	 *
	 * @return mixed
	 */
	public function get( $property_name ) {

		return property_exists( $this, $property_name ) ? $this->{$property_name} : new stdClass();
	}

	/**
	 * Load the form templates.
	 *
	 * @since 2.0.0
	 */
	public function load_templates() {

		( new Templates\Login() )->init();
		( new Templates\Reset() )->init();
		( new Templates\Registration() )->init();
	}

	/**
	 * Logout user if it was marked as unapproved.
	 *
	 * @since 2.0.0
	 */
	public function logout_unapproved_users() {

		if ( ! is_user_logged_in() || empty( get_user_meta( get_current_user_id(), 'wpforms-pending', true ) ) ) {
			return;
		}

		wp_logout();
	}
}
