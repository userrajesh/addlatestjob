<?php

function get_all_jobs($limit = 10, $offset = 0) {
    global $wpdb;
    $table = $wpdb->prefix . 'all_jobs';
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table WHERE isDeleted = 0 ORDER BY id DESC LIMIT %d OFFSET %d",
        $limit,
        $offset
    ));
}
function get_total_jobs_count() {
    global $wpdb;
    $table = $wpdb->prefix . 'all_jobs';
    return $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE isDeleted = 0");
}


function get_recent_jobs($limit = 5) {
    global $wpdb;
    $table = $wpdb->prefix . 'all_jobs';
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT %d", $limit));
}

function insert_job($data) {
    global $wpdb;
    $table = $wpdb->prefix . 'all_jobs';
    return $wpdb->insert($table, [
        'jobtitle'             => sanitize_text_field($data['jobtitle']),
        'lastdatetoapply'      => sanitize_text_field($data['lastdatetoapply']),
        'noofvacancy'          => sanitize_text_field($data['noofvacancy']),
        'applylink'            => esc_url_raw($data['applylink']),
        'officialNotification' => esc_url_raw($data['officialNotification']),
    ]);
}

function delete_job($id) {
    global $wpdb;
    $table = $wpdb->prefix . 'all_jobs';

    return $wpdb->update(
        $table,
        ['isDeleted' => 1],
        ['id' => absint($id)]
    );
}

