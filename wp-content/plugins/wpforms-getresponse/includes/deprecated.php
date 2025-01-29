<?php
/**
 * Deprecated functions.
 * This file is used to keep backward compatibility with older versions of the plugin.
 * The functions and classes listed below will be removed in December 2023.
 *
 * @since 1.6.0
 */

/**
 * Check addon requirements.
 *
 * @since 1.3.0
 * @deprecated 1.6.0
 */
function wpforms_getresponse_required() {

	_deprecated_function( __FUNCTION__, '1.6.0 of the WPForms GetResponse addon' );
}

/**
 * Deactivate the plugin.
 *
 * @since 1.3.0
 * @deprecated 1.6.0
 */
function wpforms_getresponse_deactivation() {

	_deprecated_function( __FUNCTION__, '1.6.0 of the WPForms GetResponse addon' );
}

/**
 * Admin notice for a minimum PHP version.
 *
 * @since 1.3.0
 * @deprecated 1.6.0
 */
function wpforms_getresponse_fail_php_version() {

	_deprecated_function( __FUNCTION__, '1.6.0 of the WPForms GetResponse addon' );
}

/**
 * Admin notice for minimum WPForms version.
 *
 * @since 1.3.0
 * @deprecated 1.6.0
 */
function wpforms_getresponse_fail_wpforms_version() {

	_deprecated_function( __FUNCTION__, '1.6.0 of the WPForms GetResponse addon' );
}

/**
 * Get the instance of the `\WPFormsGetResponse\Plugin` class.
 * This function is useful for quickly grabbing data used throughout the plugin.
 *
 * @since 1.3.0
 * @deprecated 1.6.0
 */
function wpforms_getresponse_plugin() {

	_deprecated_function( __FUNCTION__, '1.6.0 of the WPForms GetResponse addon' );
}

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 * @deprecated 1.6.0
 *
 * @param string $key License key.
 */
function wpforms_getresponse_updater( $key ) {

	_deprecated_function( __FUNCTION__, '1.6.0 of the WPForms GetResponse addon' );
}
