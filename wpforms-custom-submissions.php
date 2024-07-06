<?php
/**
 * Plugin Name:       WPForms Custom Submissions
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       A custom plugin to handle WPForms submissions and display them in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Aryan Pariyar
 * Author URI:        https://aryanpariyar.com.np
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpforms-custom-submissions
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Include the main plugin class file
require plugin_dir_path(__FILE__) . 'includes/class-wpforms-custom-submissions.php';

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function run_wpforms_custom_submissions()
{
    $plugin = new WPForms_Custom_Submissions();
    $plugin->run();
}
run_wpforms_custom_submissions();
