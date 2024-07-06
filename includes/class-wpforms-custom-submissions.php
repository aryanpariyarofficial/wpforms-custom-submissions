<?php

class WPForms_Custom_Submissions
{
    public function __construct()
    {
        // Plugin initialization and hooks
        add_action('wpforms_process_complete', [$this, 'save_wpforms_lite_submission'], 10, 4);
        add_action('admin_init', [$this, 'create_wpforms_submissions_table']);
        add_action('after_switch_theme', [$this, 'create_wpforms_submissions_table']);
        add_action('admin_menu', [$this, 'wpforms_submissions_menu']);
        add_action('wp_ajax_delete_wpforms_submission', [$this, 'delete_wpforms_submission']);
    }

    public function run()
    {
        // Any code to execute on plugin run
    }

    public function save_wpforms_lite_submission($fields, $entry, $form_data, $entry_id)
    {
        global $wpdb;
        $data = [
            'form_id' => $form_data['id'],
            'form_data' => json_encode($fields),
            'submission_date' => current_time('mysql'),
        ];
        $wpdb->insert("{$wpdb->prefix}wpforms_submissions", $data);
    }

    public function create_wpforms_submissions_table()
    {
        global $wpdb;
        $table_name = "{$wpdb->prefix}wpforms_submissions";
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            form_id mediumint(9) NOT NULL,
            form_data longtext NOT NULL,
            submission_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function wpforms_submissions_menu()
    {
        add_menu_page(
            'WPForms Submissions',
            'WPForms Submissions',
            'manage_options',
            'wpforms-submissions',
            [$this, 'wpforms_submissions_page'],
            'dashicons-feedback',
            6
        );
    }

    public function wpforms_submissions_page()
    {
        // Include the admin page template file
        include plugin_dir_path(__FILE__) . '../admin/class-wpforms-custom-submissions-admin.php';
    }

    public function delete_wpforms_submission()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }
        if (isset($_POST['submission_id']) && is_numeric($_POST['submission_id'])) {
            global $wpdb;
            $table_name = "{$wpdb->prefix}wpforms_submissions";
            $wpdb->delete($table_name, ['id' => intval($_POST['submission_id'])]);
            echo 'Success';
        } else {
            echo 'Failed';
        }
        wp_die();
    }
}
