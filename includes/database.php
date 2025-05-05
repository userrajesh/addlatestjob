<?php

function my_crud_install()
{
    global $wpdb;

    // First table: all_jobs
    $jobs_table = $wpdb->prefix . 'all_jobs';
    $charset_collate = $wpdb->get_charset_collate();

    $jobs_sql = "CREATE TABLE IF NOT EXISTS `$jobs_table` (
        id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
        jobtitle VARCHAR(150) NOT NULL,
        lastdatetoapply DATE NOT NULL,
        noofvacancy VARCHAR(10) NOT NULL,
        applylink VARCHAR(255) NOT NULL,
        officialNotification VARCHAR(255) NOT NULL,
        resultlink VARCHAR(255) DEFAULT '',
        admitcardlink VARCHAR(255) DEFAULT '',
        what_is_new int DEFAULT 0 NOT NULL ,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        isActive TINYINT(1) DEFAULT 0 NOT NULL,
        isDeleted TINYINT(1) DEFAULT 0 NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

  
    // Execute  table creations
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($jobs_sql);
}
