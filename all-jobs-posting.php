<?php
/*
Plugin Name: ALL JOB POSTING
Description: A secure WordPress plugin for Job Posting.
Version: 1.0
Author: Rajesh
*/

if (!defined('ABSPATH')) {
    exit;
}

//Register Activation Hooks 
register_activation_hook(__FILE__, 'my_crud_install');

// Include files
require_once plugin_dir_path(__FILE__) . 'includes/database.php';
require_once plugin_dir_path(__FILE__) . 'includes/enqueue-scripts.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/meta-box.php';
require_once plugin_dir_path(__FILE__) . 'includes/queries.php';
