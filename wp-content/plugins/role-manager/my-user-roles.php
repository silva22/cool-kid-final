<?php

/**
 * Plugin Name: Role Manager
 * Plugin URI:  #
 * Author:      Falcon_coder
 * Author URI:  #
 * Description: This an API plugin that leverages wordpress for user role management
 * Version:     0.1.0
 * License:     GPL-2.0+
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: prefix-plugin-name
*/


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the file containing REST API endpoints.
require_once plugin_dir_path(__FILE__) . 'includes/api-endpoints.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';



register_activation_hook(__FILE__, 'ensure_custom_roles_have_capabilities');

function ensure_custom_roles_have_capabilities() {
    $roles = ['cool_kid', 'cooler_kid', 'coolest_kid'];
    $capabilities = ['read'];

    foreach ($roles as $role) {
        $wp_role = get_role($role);
        if (!$wp_role) {
            continue;
        }

        // Add necessary capabilities.
        foreach ($capabilities as $cap) {
            if (!$wp_role->has_cap($cap)) {
                $wp_role->add_cap($cap);
            }
        }
    }
}