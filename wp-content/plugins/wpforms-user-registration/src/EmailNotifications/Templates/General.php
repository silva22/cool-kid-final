<?php

namespace WPFormsUserRegistration\EmailNotifications\Templates;

/**
 * General Registration email template.
 *
 * This class is no longer used and is only here for backward compatibility.
 *  It will be removed in the future, so please do not use it.
 *
 * @since 2.0.0
 * @deprecated 2.4.0
 */
class General extends \WPForms\Emails\Templates\General {

	/**
	 * Template slug.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	const TEMPLATE_SLUG = 'user-registration';

	/**
	 * Initialize class.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {

		// Add a deprecated notice to the constructor to alert developers about the change.
		_deprecated_function( __CLASS__, '2.4.0 of the WPForms User Registration Add-on' );

		// Call the parent constructor.
		parent::__construct();
	}
}
