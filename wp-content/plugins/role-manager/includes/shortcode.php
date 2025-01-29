<?php

if (!defined('ABSPATH')) {
    exit; // Prevent direct file access.
}

// Add the shortcode to display the user role management form.
add_shortcode('user_role_manager', 'render_user_role_manager_form');

// Render the user role management form.
function render_user_role_manager_form() {
    // Only allow administrators or maintainers to view this page.
    if (!current_user_can('administrator') && !current_user_can('maintainer')) {
        return '<p>You do not have permission to access this page.</p>';
    }

    // Check if a form submission occurred.
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_role_manager_nonce']) && wp_verify_nonce($_POST['user_role_manager_nonce'], 'user_role_manager_action')) {
        $email      = sanitize_email($_POST['email'] ?? '');
        $first_name = sanitize_text_field($_POST['first_name'] ?? '');
        $last_name  = sanitize_text_field($_POST['last_name'] ?? '');
        $role       = sanitize_text_field($_POST['role'] ?? '');
        
        // Validate the role.
        $valid_roles = ['cool_kid', 'cooler_kid', 'coolest_kid'];
        if (!in_array($role, $valid_roles)) {
            echo '<p style="color: red;">Invalid role selected.</p>';
        } else {
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
                            'compare' => '='
                        ],
                        [
                            'key'     => 'last_name',
                            'value'   => $last_name,
                            'compare' => '='
                        ],
                    ],
                ]);
                $users = $user_query->get_results();
                $user = $users[0] ?? null;
            }

            // Update the user's role if found.
            if ($user) {
                $updated = wp_update_user(['ID' => $user->ID, 'role' => $role]);

                if (is_wp_error($updated)) {
                    echo '<p style="color: red;">Failed to update the user role.</p>';
                } else {
                    echo '<p style="color: green;">User role updated successfully.</p>';
                }
            } else {
                echo '<p style="color: red;">User not found.</p>';
            }
        }
    }

    // Render the HTML form.
    ob_start();
    ?>
    <form method="POST" action="">
        <?php wp_nonce_field('user_role_manager_action', 'user_role_manager_nonce'); ?>
        <p>
            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" value="" style="width: 100%;" placeholder="Enter user email">
        </p>
        <p>
            <label for="first_name">First Name:</label><br>
            <input type="text" name="first_name" id="first_name" value="" style="width: 100%;" placeholder="Enter first name">
        </p>
        <p>
            <label for="last_name">Last Name:</label><br>
            <input type="text" name="last_name" id="last_name" value="" style="width: 100%;" placeholder="Enter last name">
        </p>
        <p>
            <label for="role">Select Role:</label><br>
            <select name="role" id="role" style="width: 100%;">
                <option value="cool_kid">Cool Kid</option>
                <option value="cooler_kid">Cooler Kid</option>
                <option value="coolest_kid">Coolest Kid</option>
            </select>
        </p>
        <p>
            <button type="submit" style="padding: 10px 20px; background-color: #0073aa; color: #fff; border: none; cursor: pointer;">Update Role</button>
        </p>
    </form>
    <?php
    return ob_get_clean();
}
