<?php
/**
 * Plugin Name: Attendance Tracking
 * Plugin URI: https://example.com/attendance-tracking
 * Description: Sistema de registro y control de asistencia
 * Version: 1.0.1
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: karlosduke
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: attendance-tracking
 * Domain Path: /languages
 *
 * Last Modified: 2025-05-14 20:09:49
 * Modified by: karlosduke
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('ATTENDANCE_VERSION', '1.0.1');
define('ATTENDANCE_PLUGIN_FILE', __FILE__);
define('ATTENDANCE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ATTENDANCE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once ATTENDANCE_PLUGIN_DIR . 'includes/class-database-manager.php';
require_once ATTENDANCE_PLUGIN_DIR . 'includes/class-ajax-handler.php';
require_once ATTENDANCE_PLUGIN_DIR . 'includes/class-frontend-forms.php';
require_once ATTENDANCE_PLUGIN_DIR . 'includes/class-admin-menu.php';

// Initialize the plugin
function init_attendance_tracking() {
    // Initialize classes
    $database = new AttendanceTracking\DatabaseManager();
    $ajax = new AttendanceTracking\AjaxHandler();
    $frontend = new AttendanceTracking\FrontendForms();
    $admin = new AttendanceTracking\AdminMenu();

    // Initialize components
    add_action('init', [$frontend, 'init']);
    add_action('admin_init', [$admin, 'init']);
}

// Start the plugin
add_action('plugins_loaded', 'init_attendance_tracking');