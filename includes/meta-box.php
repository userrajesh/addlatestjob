<?php

function add_job_meta_box() {
    add_meta_box(
        'related_job',
        'Related Job Posting',
        'display_job_meta_box',
        'post',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'add_job_meta_box');

function display_job_meta_box($post) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'all_jobs';
    $jobs = $wpdb->get_results("SELECT id, jobtitle FROM $table_name");
    $selected_job = get_post_meta($post->ID, 'related_job_id', true);

    echo '<select name="related_job_id">';
    echo '<option value="">Select Job</option>';
    foreach ($jobs as $job) {
        echo '<option value="' . esc_attr($job->id) . '" ' . selected($selected_job, $job->id, false) . '>' . esc_html($job->jobtitle) . '</option>';
    }
    echo '</select>';
}

function save_related_job_meta($post_id) {
    if (isset($_POST['related_job_id'])) {
        update_post_meta($post_id, 'related_job_id', sanitize_text_field($_POST['related_job_id']));
    }
}
add_action('save_post', 'save_related_job_meta');
