<?php
if (!defined('ABSPATH')) {
    exit;
}

// Shortcode to Display Jobs
add_shortcode('view_job_details', 'ajp_view_jobs');
function ajp_view_jobs() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'all_jobs';
    $entries = $wpdb->get_results("SELECT * FROM $table_name ORDER BY updated_at DESC");

    ob_start();
?>
    <table id="jobTable">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Last Date to Apply</th>
                <th>No. of Vacancies</th>
                <th>Official Notification</th>
                <th>Apply Link</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entries as $entry) : ?>
                <tr>
                    <td><?php echo esc_html($entry->jobtitle); ?></td>
                    <td><?php echo esc_html(date('d M, Y', strtotime($entry->lastdatetoapply))); ?></td>
                    <td><?php echo esc_html($entry->noofvacancy); ?></td>
                    <td><a href="<?php echo esc_url($entry->officialNotification); ?>" target="_blank">View</a></td>
                    <td><a href="<?php echo esc_url($entry->applylink); ?>" target="_blank">Apply</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php
    return ob_get_clean();
}
