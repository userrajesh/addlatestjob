<?php

function view_all_jobs() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'all_jobs';

    // Fetch job entries using our query function
    $entries = get_all_jobs();

    ob_start();
    ?>
    <div class="job-listings-container">
        <?php if (!empty($entries)) : ?>
            <table class="job-table" id="jobTable">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Last Date to Apply</th>
                        <th>No of Vacancies</th>
                        <th>Official Notification</th>
                        <th>Apply Link</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entries as $entry) : ?>
                        <tr>
                            <td>
                                <?php echo esc_html($entry->jobtitle); ?>
                                <?php
                                if (!empty($entry->created_at)) {
                                    $createdDate = new DateTime($entry->created_at);
                                    $currentDate = new DateTime();
                                    $interval = $createdDate->diff($currentDate);
                                    if ($interval->days < 3) {
                                        echo ' <span class="new-badge">New!</span>';
                                    }
                                }
                                ?>
                            </td>
                            <td><?php echo esc_html(date('d M, Y', strtotime($entry->lastdatetoapply))); ?></td>
                            <td><?php echo esc_html($entry->noofvacancy); ?></td>
                            <td>
                                <?php if (!empty($entry->officialNotification)) : ?>
                                    <a href="<?php echo esc_url($entry->officialNotification); ?>" target="_blank">View</a>
                                <?php else : ?>
                                    <span>N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($entry->applylink)) : ?>
                                    <a href="<?php echo esc_url($entry->applylink); ?>" target="_blank">Apply Now</a>
                                <?php else : ?>
                                    <span>Not Available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
        <?php else : ?>
            <p>No job listings available.</p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('view_job_details', 'view_all_jobs');

// For recent jobs with linked posts (if applicable)
function display_recent_jobs_latest() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'all_jobs';
    $entries = get_all_jobs();

    if (empty($entries)) {
        return '<p>No job postings available.</p>';
    }

    $output = "<ul class='recent-jobs-list'>";
    foreach ($entries as $entry) {
        // Check for related post
        $related_posts = get_posts([
            'meta_key'   => 'related_job_id',
            'meta_value' => $entry->id,
            'post_type'  => 'post',
            'numberposts' => 1,
        ]);

        if (!empty($related_posts)) {
            $post_link = get_permalink($related_posts[0]->ID);
            $output .= "<li><a href='" . esc_url($post_link) . "'>" . esc_html($entry->jobtitle) . "</a>";
        } else {
            $output .= "<li>" . esc_html($entry->jobtitle);
        }

        if (!empty($entry->created_at)) {
            $createdDate = new DateTime($entry->created_at);
            $currentDate = new DateTime();
            $interval = $createdDate->diff($currentDate);
            if ($interval->days < 3) {
                $output .= ' <span class="new-badge">New!</span>';
            }
        }
        $output .= "</li>";
    }
    $output .= "</ul>";

    return $output;
}
add_shortcode('recent_jobs_on_homepage', 'display_recent_jobs_latest');
