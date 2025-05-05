<?php

function my_crud_menu()
{
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

function my_crud_admin_page()
{
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
            'resultlink' => esc_url_raw($_POST['resultlink']),
            'admitcardlink' => esc_url_raw($_POST['admitcardlink'])
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
        echo "<div class='notice notice-warning'><p>Entry deleted successfully!</p></div>";
    }


    // Handle Update
    if (isset($_POST['update_entry']) && wp_verify_nonce($_POST['update_entry_nonce'], 'update_entry_' . $_POST['entry_id'])) {
        $id = intval($_POST['entry_id']);
        $updated = $wpdb->update(
            $table_name,
            [
                'jobtitle'             => sanitize_text_field($_POST['jobtitle']),
                'lastdatetoapply'      => sanitize_text_field($_POST['lastdatetoapply']),
                'noofvacancy'          => sanitize_text_field($_POST['noofvacancy']),
                'applylink'            => esc_url_raw($_POST['applylink']),
                'officialNotification' => esc_url_raw($_POST['officialNotification']),
                'resultlink'            => esc_url_raw($_POST['resultlink']),
                'admitcardlink' => esc_url_raw($_POST['admitcardlink'])
            ],
            ['id' => $id],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s'],
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
            <table style="width: 100%;">
                <tr>
                    <td><input type="text" name="jobtitle" placeholder="Job Title" required></td>
                    <td><input type="date" name="lastdatetoapply" placeholder="last date"></td>
                    <td><input type="text" name="noofvacancy" placeholder="No. of Vacancy" required></td>
                    <td><input type="url" name="applylink" placeholder="Apply Link" required></td>
                
                </tr>
                <tr>
                    <td><input type="url" name="officialNotification" placeholder="Official Notification" required></td>
                    <td><input type="url" name="resultlink" placeholder="Result Link"></td>
                    <td><input type="url" name="admitcardlink" placeholder="Admit Card Link"></td>
                    <td>
                        <select>
                            <option value="1">New Job Posting</option>
                            <option value="2">New Result </option>
                            <option value="3">New Admit Card</option>
                        </select>
                    </td>
                </tr>
                <tr> <td colspan="4"><input type="submit" name="add_entry" value="Add Entry" style="width: 100%;"></td></tr>
            </table>
        </form>

        <!-- Display existing entries in backend-->
        <h3>Entries:</h3>
        <style>
            /* Modal Styling (unchanged) */
            .modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1000;
                overflow: auto;
            }

            .modal-content {
                background-color: #fff;
                margin: 5% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                max-width: 600px;
                border-radius: 5px;
                position: relative;
            }

            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }

            .close:hover,
            .close:focus {
                color: #000;
                text-decoration: none;
            }

            .modal-content form {
                display: flex;
                flex-direction: column;
                gap: 15px;
            }

            .modal-content input[type="text"],
            .modal-content input[type="date"],
            .modal-content input[type="url"] {
                padding: 8px;
                font-size: 16px;
                width: 100%;
                box-sizing: border-box;
            }

            .modal-content input[type="submit"] {
                padding: 10px;
                background-color: #0073aa;
                color: #fff;
                border: none;
                cursor: pointer;
                border-radius: 3px;
            }

            .modal-content input[type="submit"]:hover {
                background-color: #005177;
            }

            .widefat {
                width: 100%;
                border-collapse: collapse;
            }

            .widefat th,
            .widefat td {
                padding: 8px;
                text-align: left;
                border: 1px solid #ddd;
            }
        </style>

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
                            <a href="javascript:void(0);" class="edit-entry"
                                data-id="<?php echo esc_attr($entry->id); ?>"
                                data-jobtitle="<?php echo esc_attr($entry->jobtitle); ?>"
                                data-noofvacancy="<?php echo esc_attr($entry->noofvacancy); ?>"
                                data-lastdatetoapply="<?php echo esc_attr($entry->lastdatetoapply); ?>"
                                data-applylink="<?php echo esc_attr($entry->applylink); ?>"
                                data-officialnotification="<?php echo esc_attr($entry->officialNotification); ?>"
                                data-resultlink="<?php echo esc_attr($entry->resultlink); ?>"
                                data-admitcardlink="<?php echo esc_attr($entry->admitcardlink); ?>"
                                data-nonce="<?php echo esc_attr(wp_create_nonce('update_entry_' . $entry->id)); ?>">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal for Edit Form -->
        <div id="editModal" class="modal" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
            <div class="modal-content">
                <span class="close" aria-label="Close">×</span>
                <h2 id="modalTitle">Edit Job Entry</h2>
                <form method="POST" id="editJobForm">
                    <input type="hidden" name="entry_id" id="entry_id">
                    <input type="hidden" name="update_entry_nonce" id="update_entry_nonce">
                    <label for="jobtitle">Job Title:</label>
                    <input type="text" name="jobtitle" id="jobtitle" required>
                    <label for="noofvacancy">No of Vacancies:</label>
                    <input type="text" name="noofvacancy" id="noofvacancy" required>
                    <label for="lastdatetoapply">Last Date to Apply:</label>
                    <input type="date" name="lastdatetoapply" id="lastdatetoapply" required>
                    <label for="applylink">Apply Link:</label>
                    <input type="url" name="applylink" id="applylink" required>
                    <label for="officialNotification">Official Notification:</label>
                    <input type="url" name="officialNotification" id="officialNotification" required>
                    <label for="resultlink">Result Link (Optional):</label>
                    <input type="url" name="resultlink" id="resultlink">
                    <label for="admitcardlink">Admit Card Link (Optional):</label>
                    <input type="url" name="admitcardlink" id="admitcardlink">
                    <input type="submit" name="update_entry" value="Update">
                </form>
            </div>
        </div>

        <script>
            // JavaScript for Modal Handling
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('editModal');
                const closeBtn = document.querySelector('.close');
                const editLinks = document.querySelectorAll('.edit-entry');
                const form = document.getElementById('editJobForm');

                // Open modal and populate form
                editLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        const data = this.dataset;
                        document.getElementById('entry_id').value = data.id;
                        document.getElementById('update_entry_nonce').value = data.nonce;
                        document.getElementById('jobtitle').value = data.jobtitle;
                        document.getElementById('noofvacancy').value = data.noofvacancy;
                        document.getElementById('lastdatetoapply').value = data.lastdatetoapply;
                        document.getElementById('applylink').value = data.applylink;
                        document.getElementById('officialNotification').value = data.officialnotification;
                        document.getElementById('resultlink').value = data.resultlink || '';
                        document.getElementById('admitcardlink').value = data.admitcardlink || '';
                        modal.style.display = 'block';
                        modal.setAttribute('aria-hidden', 'false');
                        document.getElementById('jobtitle').focus();
                    });
                });

                // Close modal
                closeBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                    modal.setAttribute('aria-hidden', 'true');
                    form.reset();
                });

                // Close modal on outside click
                window.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                        modal.setAttribute('aria-hidden', 'true');
                        form.reset();
                    }
                });

                // Close modal with Escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape' && modal.style.display === 'block') {
                        modal.style.display = 'none';
                        modal.setAttribute('aria-hidden', 'true');
                        form.reset();
                    }
                });
            });
        </script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const notices = document.querySelectorAll('.notice');
                notices.forEach(function(notice) {
                    setTimeout(function() {
                        notice.style.transition = 'opacity 1s ease';
                        notice.style.opacity = '0';
                        setTimeout(() => {
                            notice.remove();
                        }, 1000); // Remove it completely after fade-out
                    }, 3000); // Wait 3 seconds before fading
                });
            });
        </script>
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
