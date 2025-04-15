<?php

function custom_enqueue_scripts() {
    // External CSS
    wp_enqueue_style('datatable-css', '//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css');
    wp_enqueue_style('datatables-responsive-css', '//cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css');

    // Custom CSS
    wp_enqueue_style('custom-job-styles', plugin_dir_url(__FILE__) . '../assets/css/styles.css');

    // External JS
    wp_enqueue_script('jquery');
    wp_enqueue_script('datatable-jquery', '//cdn.datatables.net/2.2.2/js/dataTables.min.js', ['jquery'], null, true);
    wp_enqueue_script('datatables-responsive-js', '//cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js', ['jquery'], null, true);
       // Enqueue custom jQuery file from include/js/script.js
       wp_enqueue_script('custom-script', plugin_dir_url(__FILE__) . '../assets/js/script.js', ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');
