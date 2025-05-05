<?php

function get_all_jobs($limit = 10, $offset = 0)
{
    global $wpdb;
    $table = $wpdb->prefix . 'all_jobs';
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table WHERE isDeleted = 0 ORDER BY id DESC LIMIT %d OFFSET %d",
        $limit,
        $offset
    ));
}
function get_total_jobs_count()
{
    global $wpdb;
    $table = $wpdb->prefix . 'all_jobs';
    return $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE isDeleted = 0");
}


function get_recent_jobs($limit = 5)
{
    global $wpdb;
    $table = $wpdb->prefix . 'all_jobs';
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT %d", $limit));
}


function insert_job($data)
{
    global $wpdb;

    // Insert into all_jobs table
    $jobs_table = $wpdb->prefix . 'all_jobs';
    $job_data = [
        'jobtitle'             => sanitize_text_field($data['jobtitle']),
        'lastdatetoapply'      => sanitize_text_field($data['lastdatetoapply']),
        'noofvacancy'          => sanitize_text_field($data['noofvacancy']),
        'applylink'            => esc_url_raw($data['applylink']),
        'officialNotification' => esc_url_raw($data['officialNotification']),
        'resultlink' => esc_url_raw($data['resultlink']),
        'admitcardlink' => esc_url_raw($data['admitcardlink'])
    ];

    $inserted = $wpdb->insert($jobs_table, $job_data);

    if ($inserted === false) {
        return false; // Return false if job insertion fails
    }

    // // Get the ID of the newly inserted job
    // $job_id = $wpdb->insert_id;

    // // Insert into result table with empty result_check_link
    // $result_table = $wpdb->prefix . 'result';
    // $result_data = [
    //     'job_id'                  => $job_id,
    //     'result_declaration_status' => 0, // Default value
    //     'result_check_link'       => '', // Empty link, to be updated later
    //     'isActive'                => 0,
    //     'isDeleted'               => 0,
    // ];

    // $wpdb->insert($result_table, $result_data);

    // // Insert into admitcard table with empty admitcard_check_link
    // $admitcard_table = $wpdb->prefix . 'admitcard';
    // $admitcard_data = [
    //     'job_id'            => $job_id,
    //     'admitcard_status'  => 0, // Default value
    //     'admitcard_check_link' => '', // Empty link, to be updated later
    //     'isActive'          => 0,
    //     'isDeleted'         => 0,
    // ];

    // $wpdb->insert($admitcard_table, $admitcard_data);

    // return $job_id; // Return the job ID for further use
}

function delete_job($id)
{
    global $wpdb;
    $table = $wpdb->prefix . 'all_jobs';

    return $wpdb->update(
        $table,
        ['isDeleted' => 1],
        ['id' => absint($id)]
    );
}
