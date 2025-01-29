<?php

if (!defined('ABSPATH')) {
    exit; // Prevent direct file access.
}

// Register the REST API endpoint on REST API initialization.
add_action('rest_api_init', function () {
    register_rest_route('user-role-api/v1', '/update-role', [
        'methods'             => 'POST',
        'callback'            => 'update_user_role',
        'permission_callback' => 'is_user_admin_or_maintainer',
    ]);
});

// Permission callback to allow only administrators or maintainers.
function is_user_admin_or_maintainer() {
    return current_user_can('administrator') || current_user_can('maintainer');
}

// Callback function to handle user role updates.
function update_user_role(WP_REST_Request $request) {
    $email      = sanitize_email($request->get_param('email'));
    $first_name = sanitize_text_field($request->get_param('first_name'));
    $last_name  = sanitize_text_field($request->get_param('last_name'));
    $role       = sanitize_text_field($request->get_param('role'));

    // Predefined roles (already created via the Members plugin).
    $valid_roles = ['cool_kid', 'cooler_kid', 'coolest_kid'];
    if (!in_array($role, $valid_roles)) {
        return new WP_Error('invalid_role', 'Invalid role provided. Valid roles are Cool Kid, Cooler Kid, and Coolest Kid.', ['status' => 400]);
    }

    // Attempt to identify the user.
    $user = null;
    if ($email) {
        $user = get_user_by('email', $email);
    } elseif ($first_name && $last_name) {
        $user_query = new WP_User_Query([
            'meta_query' => [
                [
                    'key'     => 'first_name',
                    'value'   => $first_name,
                    'compare' => '=',
                ],
                [
                    'key'     => 'last_name',
                    'value'   => $last_name,
                    'compare' => '=',
                ],
            ],
        ]);
        $users = $user_query->get_results();
        $user = $users[0] ?? null;
    }

    // Check if the user exists.
    if (!$user) {
        return new WP_Error('user_not_found', 'User not found. Provide a valid email or first/last name.', ['status' => 404]);
    }

    // Update the user’s role.
    $user_id = $user->ID;
    $updated = wp_update_user(['ID' => $user_id, 'role' => $role]);

    if (is_wp_error($updated)) {
        return new WP_Error('update_failed', 'Failed to update the user role.', ['status' => 500]);
    }

    return new WP_REST_Response(['message' => 'User role updated successfully.'], 200);
}
