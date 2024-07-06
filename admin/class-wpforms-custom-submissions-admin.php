<?php

class WPForms_Custom_Submissions_Admin
{
    public function display_submissions_page()
    {
        // Pagination settings
        $page = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
        $per_page = 10;

        // Search and sorting parameters
        $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $order_by = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'submission_date';
        $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC';

        $submissions = $this->get_wpforms_submissions($page, $per_page, $search_query, $order_by, $order);

        global $wpdb;
        $table_name = "{$wpdb->prefix}wpforms_submissions";
        $total_submissions = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE form_data LIKE %s",
            '%' . $wpdb->esc_like($search_query) . '%'
        ));
        $total_pages = ceil($total_submissions / $per_page);

        // Display the submissions
        include plugin_dir_path(__FILE__) . 'partials/wpforms-submissions-display.php';
    }

    public function get_wpforms_submissions($page, $per_page, $search_query, $order_by, $order)
    {
        global $wpdb;
        $table_name = "{$wpdb->prefix}wpforms_submissions";
        $offset = ($page - 1) * $per_page;
        $like_query = '%' . $wpdb->esc_like($search_query) . '%';
        $submissions = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE form_data LIKE %s ORDER BY $order_by $order LIMIT %d OFFSET %d",
            $like_query, $per_page, $offset
        ));
        return $submissions;
    }
}

$admin = new WPForms_Custom_Submissions_Admin();
$admin->display_submissions_page();
