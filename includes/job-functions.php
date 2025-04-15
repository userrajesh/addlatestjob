<?php
if (!defined('ABSPATH')) {
    exit;
}

function ajp_add_job($jobtitle, $lastdatetoapply, $noofvacancy, $applylink, $officialNotification) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'all_jobs';

    return $wpdb->insert(
        $table_name,
        [
            'jobtitle' => sanitize_text_field($jobtitle),
            'lastdatetoapply' => date('Y-m-d', strtotime($lastdatetoapply)),
            'noofvacancy' => sanitize_text_field($noofvacancy),
            'applylink' => esc_url_raw($applylink),
            'officialNotification' => esc_url_raw($officialNotification),
        ],
        ['%s', '%s', '%s', '%s', '%s']
    );
}

function ajp_get_jobs() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'all_jobs';
    return $wpdb->get_results("SELECT * FROM $table_name ORDER BY updated_at DESC");
}

function ajp_delete_job($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'all_jobs';
    return $wpdb->delete($table_name, ['id' => intval($id)]);
}

function ajp_update_job($id, $jobtitle, $lastdatetoapply, $noofvacancy, $applylink, $officialNotification) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'all_jobs';

    return $wpdb->update(
        $table_name,
        [
            'jobtitle' => sanitize_text_field($jobtitle),
            'lastdatetoapply' => sanitize_text_field($lastdatetoapply),
            'noofvacancy' => sanitize_text_field($noofvacancy),
            'applylink' => esc_url_raw($applylink),
            'officialNotification' => esc_url_raw($officialNotification),
        ],
        ['id' => intval($id)],
        ['%s', '%s', '%s', '%s', '%s'],
        ['%d']
    );
}
