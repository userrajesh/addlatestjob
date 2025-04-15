<?php
if (!defined('ABSPATH')) {
    exit;
}

function ajp_plugin_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'all_jobs';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
        id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
        jobtitle VARCHAR(150) NOT NULL,
        lastdatetoapply DATE NOT NULL,
        noofvacancy VARCHAR(10) NOT NULL,
        applylink VARCHAR(255) NOT NULL,
        officialNotification VARCHAR(500) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
