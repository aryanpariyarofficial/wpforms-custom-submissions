<div class="wrap">
    <h1>WPForms Submissions</h1>

    <!-- Search form -->
    <form method="get">
        <input type="hidden" name="page" value="wpforms-submissions">
        <input type="text" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="Search submissions">
        <input type="submit" class="button" value="Search">
    </form>

    <?php if ($submissions) : ?>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th class="manage-column column-columnname" scope="col"><a href="<?php echo add_query_arg(['orderby' => 'submission_date', 'order' => $order === 'ASC' ? 'DESC' : 'ASC']); ?>">Submission Date</a></th>
                    <th class="manage-column column-columnname" scope="col"><a href="<?php echo add_query_arg(['orderby' => 'name', 'order' => $order === 'ASC' ? 'DESC' : 'ASC']); ?>">Name</a></th>
                    <th class="manage-column column-columnname" scope="col"><a href="<?php echo add_query_arg(['orderby' => 'email', 'order' => $order === 'ASC' ? 'DESC' : 'ASC']); ?>">Email</a></th>
                    <th class="manage-column column-columnname" scope="col">Message</th>
                    <th class="manage-column column-columnname" scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $submission) : ?>
                    <?php
                    $form_data = json_decode($submission->form_data, true);
                    $name = isset($form_data[0]['value']) ? esc_html($form_data[0]['value']) : 'N/A';
                    $email = isset($form_data[1]['value']) ? sanitize_email($form_data[1]['value']) : 'N/A';
                    $message = isset($form_data[2]['value']) ? esc_html($form_data[2]['value']) : 'N/A';
                    ?>
                    <tr>
                        <td><?php echo esc_html($submission->submission_date); ?></td>
                        <td><?php echo esc_html($name); ?></td>
                        <td><?php echo esc_html($email); ?></td>
                        <td><?php echo esc_html($message); ?></td>
                        <td>
                            <button class="view-submission button-primary" data-id="<?php echo esc_attr($submission->id); ?>" data-formdata="<?php echo esc_attr($submission->form_data); ?>">View</button>
                            <button class="delete-submission button-delete" data-id="<?php echo esc_attr($submission->id); ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination links -->
        <div class="tablenav">
            <div class="tablenav-pages">
                <?php if ($page > 1) : ?>
                    <a class="prev-page button" href="<?php echo add_query_arg('paged', $page - 1); ?>">&laquo;</a>
                <?php endif; ?>
                <?php if ($page < $total_pages) : ?>
                    <a class="next-page button" href="<?php echo add_query_arg('paged', $page + 1); ?>">&raquo;</a>
                <?php endif; ?>
            </div>
        </div>

    <?php else : ?>
        <p>No submissions found.</p>
    <?php endif; ?>

    <!-- Add the popup container -->
    <div id="submission-popup" style="display:none;">
        <div id="submission-popup-content">
            <button id="close-popup">Close</button>
            <div id="submission-data"></div>
        </div>
    </div>

    <!-- Add the CSS for the popup -->
    <style>
        #submission-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #submission-popup-content {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            max-width: 500px;
            width: 100%;
            position: relative;
        }
        #submission-popup-content button {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .button-delete, #close-popup {
            background-color: #dc3545;
            color: #fff;
            padding: 8px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button-primary {
            background-color: #0073aa;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
        }
        form {
            display: flex;
            justify-content: end;
            margin-bottom: 20px;
        }
    </style>

    <!-- Add the JavaScript for handling the popup and delete actions -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".view-submission").forEach(function(button) {
                button.addEventListener("click", function() {
                    var formData = JSON.parse(button.getAttribute("data-formdata"));
                    var popup = document.getElementById("submission-popup");
                    var submissionData = document.getElementById("submission-data");

                    var dataHtml = "";
                    for (var key in formData) {
                        if (formData.hasOwnProperty(key)) {
                            dataHtml += "<p><strong>" + formData[key]["name"] + ":</strong> " + formData[key]["value"] + "</p>";
                        }
                    }

                    submissionData.innerHTML = dataHtml;
                    popup.style.display = "flex";
                });
            });

            document.getElementById("close-popup").addEventListener("click", function() {
                document.getElementById("submission-popup").style.display = "none";
            });

            document.querySelectorAll(".delete-submission").forEach(function(button) {
                button.addEventListener("click", function() {
                    var submissionId = button.getAttribute("data-id");
                    if (confirm("Are you sure you want to delete this submission?")) {
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", ajaxurl, true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function () {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                location.reload();
                            }
                        };
                        xhr.send("action=delete_wpforms_submission&submission_id=" + submissionId);
                    }
                });
            });
        });
    </script>
</div>
