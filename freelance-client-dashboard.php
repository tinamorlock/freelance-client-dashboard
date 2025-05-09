<?php
/**
 * Plugin Name: Freelance Client Dashboard
 * Description: A simple admin-only dashboard to manage clients, projects, and send updates via email.
 * Version: 1.0
 * Author: Tina Morlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include custom post types
require_once plugin_dir_path( __FILE__ ) . 'includes/post-types.php';

// Include email functions
require_once plugin_dir_path( __FILE__ ) . 'includes/email-functions.php';
