<?php

function my_crud_menu() {
    add_menu_page(
        'Secure CRUD',
        'Add Jobs',
        'manage_options',
        'secure-crud',
        'my_crud_admin_page',
        'dashicons-database'
    );
}
add_action('admin_menu', 'my_crud_menu');

function my_crud_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'all_jobs';

    // Handle Insert
    if (isset($_POST['add_entry']) && check_admin_referer('my_crud_nonce_action', 'my_crud_nonce')) {
        $data = [
            'jobtitle'              => sanitize_text_field($_POST['jobtitle']),
            'lastdatetoapply'       => date('Y-m-d', strtotime($_POST['lastdatetoapply'])),
            'noofvacancy'           => sanitize_text_field($_POST['noofvacancy']),
            'applylink'             => esc_url_raw($_POST['applylink']),
            'officialNotification'  => esc_url_raw($_POST['officialNotification']),
        ];
        
        // Using query function
        if (!empty($data['jobtitle']) && !empty($data['lastdatetoapply']) && !empty($data['noofvacancy']) && !empty($data['applylink']) && !empty($data['officialNotification'])) {
            insert_job($data);
            echo "<div class='notice notice-success'><p>Entry added successfully!</p></div>";
        } else {
            echo "<div class='notice notice-error'><p>Invalid input! Please check your entries.</p></div>";
        }
    }

    // Handle Delete
    if (isset($_GET['delete']) && check_admin_referer('delete_entry_' . $_GET['delete'])) {
        $id = intval($_GET['delete']);
        delete_job($id);
        echo "<div class='notice notice-success'><p>Entry deleted successfully!</p></div>";
    }

    // Handle Update
    if (isset($_POST['update_entry']) && check_admin_referer('update_entry_' . $_POST['entry_id'])) {
        $id = intval($_POST['entry_id']);
        $updated = $wpdb->update(
            $table_name,
            [
                'jobtitle'             => sanitize_text_field($_POST['jobtitle']),
                'lastdatetoapply'      => sanitize_text_field($_POST['lastdatetoapply']),
                'noofvacancy'          => sanitize_text_field($_POST['noofvacancy']),
                'applylink'            => esc_url_raw($_POST['applylink']),
                'officialNotification' => esc_url_raw($_POST['officialNotification'])
            ],
            ['id' => $id],
            ['%s', '%s', '%s', '%s', '%s'],
            ['%d']
        );

        if ($updated !== false) {
            echo "<div class='notice notice-success'><p>Entry updated successfully!</p></div>";
        } else {
            echo "<div class='notice notice-error'><p>Failed to update entry or no changes were made.</p></div>";
        }
    }



    $limit = 10; // Jobs per page
    $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($page - 1) * $limit;
    
    $total_jobs = get_total_jobs_count();
    $total_pages = ceil($total_jobs / $limit);
    
    // Fetch paginated jobs
    $entries = get_all_jobs($limit, $offset);
    
    ?>
    <div class="wrap">
        <h2>Secure CRUD Plugin</h2>

        <!-- Form for adding new entry in backend-->
        <form method="POST">
            <?php wp_nonce_field('my_crud_nonce_action', 'my_crud_nonce'); ?>
            <table>
                <tr>
                    <td><input type="text" name="jobtitle" placeholder="Job Title" required></td>
                    <td><input type="date" name="lastdatetoapply" required></td>
                    <td><input type="text" name="noofvacancy" placeholder="No. of Vacancy" required></td>
                    <td><input type="url" name="applylink" placeholder="Apply Link" required></td>
                    <td><input type="url" name="officialNotification" placeholder="Official Notification" required></td>
                    <td><input type="submit" name="add_entry" value="Add Entry"></td>
                </tr>
            </table>
        </form>

        <!-- Display existing entries in backend-->
        <h3>Entries:</h3>
        <table class="widefat fixed job-table" id="backendjobTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Job Title</th>
                    <th>Last Date to Apply</th>
                    <th>No of Vacancies</th>
                    <th>Official Notification</th>
                    <th>Apply Link</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $entry) : ?>
                    <tr>
                        <td><?php echo esc_html($entry->id); ?></td>
                        <td><?php echo esc_html($entry->jobtitle); ?></td>
                        <td><?php echo esc_html($entry->lastdatetoapply); ?></td>
                        <td><?php echo esc_html($entry->noofvacancy); ?></td>
                        <td><a href="<?php echo esc_url($entry->officialNotification); ?>" target="_blank">View</a></td>
                        <td><a href="<?php echo esc_url($entry->applylink); ?>" target="_blank">Apply</a></td>
                        <td>
                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=secure-crud&delete=' . $entry->id), 'delete_entry_' . $entry->id); ?>" onclick="return confirm('Are you sure?');">Delete</a>
                            |
                            <a href="javascript:void(0);" onclick="document.getElementById('editForm<?php echo $entry->id; ?>').style.display='block';">Edit</a>
                        </td>
                    </tr>
                    <tr id="editForm<?php echo $entry->id; ?>" style="display:none;">
                        <td colspan="7">
                            <form method="POST">
                                <?php wp_nonce_field('update_entry_' . $entry->id); ?>
                                <input type="hidden" name="entry_id" value="<?php echo $entry->id; ?>">
                                <input type="text" name="jobtitle" value="<?php echo esc_attr($entry->jobtitle); ?>" required>
                                <input type="text" name="noofvacancy" value="<?php echo esc_attr($entry->noofvacancy); ?>" required>
                                <input type="date" name="lastdatetoapply" value="<?php echo esc_attr($entry->lastdatetoapply); ?>" required>
                                <input type="url" name="applylink" value="<?php echo esc_attr($entry->applylink); ?>" required>
                                <input type="url" name="officialNotification" value="<?php echo esc_attr($entry->officialNotification); ?>" required>
                                <input type="submit" name="update_entry" value="Update">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <script>
            jQuery(document).ready(function($) {
                $('#backendjobTable').DataTable();
            });
        </script><?php if ($total_pages > 1): ?>
    <div class="tablenav">
        <div class="tablenav-pages">
            <span class="pagination-links">
                <?php if ($page > 1): ?>
                    <a class="prev-page" href="<?php echo esc_url(add_query_arg('paged', $page - 1)); ?>">&laquo;</a>
                <?php endif; ?>

                <span class="paging-input"><?php echo $page; ?> of <span class="total-pages"><?php echo $total_pages; ?></span></span>

                <?php if ($page < $total_pages): ?>
                    <a class="next-page" href="<?php echo esc_url(add_query_arg('paged', $page + 1)); ?>">&raquo;</a>
                <?php endif; ?>
            </span>
        </div>
    </div>
<?php endif; ?> 

    </div>
    <?php
}
