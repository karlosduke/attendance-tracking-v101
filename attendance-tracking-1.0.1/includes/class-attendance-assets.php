<?php
/**
 * Asset Manager Class
 *
 * Handles the registration and enqueuing of all CSS and JavaScript resources.
 *
 * @package AttendanceTracking
 * @since 1.0.1
 * @version 1.0.1
 * Last Modified: 2025-05-14 19:15:54
 * Modified by: karlosduke
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Attendance_Assets
 * 
 * Manages all assets (CSS and JavaScript) for the Attendance Tracking plugin.
 */
class Attendance_Assets {
    /**
     * Plugin version, used for cache-busting and script versioning
     *
     * @var string
     */
    private $version;

    /**
     * Instance of this class.
     *
     * @var Attendance_Assets
     */
    private static $instance = null;

    /**
     * Initialize the class and set its properties.
     */
    private function __construct() {
        $this->version = defined('ATTENDANCE_VERSION') ? ATTENDANCE_VERSION : '1.0.1';
        $this->init_hooks();
    }

    /**
     * Get instance of this class.
     *
     * @return Attendance_Assets
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Register and enqueue frontend styles
     */
    public function enqueue_frontend_styles() {
        // Solo cargar en páginas que usen el shortcode [attendance_form]
        global $post;
        if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'attendance_form')) {
            return;
        }

        // Registrar estilos principales
        wp_register_style(
            'attendance-frontend',
            ATTENDANCE_PLUGIN_URL . 'assets/css/frontend/frontend.css',
            array(),
            $this->version
        );

        // Registrar estilos de componentes
        $components = array(
            'forms',
            'signature-pad',
            'buttons',
            'messages',
            'user-info'
        );

        foreach ($components as $component) {
            wp_register_style(
                "attendance-component-{$component}",
                ATTENDANCE_PLUGIN_URL . "assets/css/frontend/components/_{$component}.css",
                array('attendance-frontend'),
                $this->version
            );
        }

        // Encolar estilos
        wp_enqueue_style('attendance-frontend');
        foreach ($components as $component) {
            wp_enqueue_style("attendance-component-{$component}");
        }

        // Encolar estilos de WordPress necesarios
        wp_enqueue_style('dashicons');
    }

    /**
     * Register and enqueue frontend scripts
     */
    public function enqueue_frontend_scripts() {
        global $post;
        if (!is_a($post, 'WP_Post') || !has_shortcode($post->post_content, 'attendance_form')) {
            return;
        }

        // Signature Pad library
        wp_register_script(
            'signature-pad',
            ATTENDANCE_PLUGIN_URL . 'assets/js/vendor/signature_pad.min.js',
            array(),
            '2.3.2',
            true
        );

        // Frontend scripts
        wp_register_script(
            'attendance-frontend',
            ATTENDANCE_PLUGIN_URL . 'assets/js/frontend/attendance.js',
            array('jquery', 'signature-pad'),
            $this->version,
            true
        );

        // Localize script
        wp_localize_script(
            'attendance-frontend',
            'attendanceParams',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('attendance_nonce'),
                'i18n' => array(
                    'confirmSubmit' => __('¿Estás seguro de registrar la asistencia?', 'attendance-tracking'),
                    'loading' => __('Procesando...', 'attendance-tracking'),
                    'error' => __('Ha ocurrido un error', 'attendance-tracking'),
                    'signatureRequired' => __('La firma es obligatoria', 'attendance-tracking'),
                    'invalidDNI' => __('DNI no válido', 'attendance-tracking')
                )
            )
        );

        wp_enqueue_script('attendance-frontend');
    }

    /**
     * Register and enqueue admin styles
     *
     * @param string $hook Current admin page hook
     */
    public function enqueue_admin_styles($hook) {
        // Solo cargar en páginas del plugin
        if (!$this->is_plugin_admin_page($hook)) {
            return;
        }

        // Registrar estilos del admin
        wp_register_style(
            'attendance-admin',
            ATTENDANCE_PLUGIN_URL . 'assets/css/admin/admin.css',
            array(),
            $this->version
        );

        // Registrar estilos de páginas específicas
        $admin_pages = array('centers', 'attendance-list', 'manual-attendance', 'users-list');
        
        foreach ($admin_pages as $page) {
            wp_register_style(
                "attendance-admin-{$page}",
                ATTENDANCE_PLUGIN_URL . "assets/css/admin/pages/_{$page}.css",
                array('attendance-admin'),
                $this->version
            );
        }

        // Encolar estilos
        wp_enqueue_style('attendance-admin');
        
        // Encolar estilos específicos de la página actual
        $current_page = $this->get_current_admin_page($hook);
        if ($current_page && in_array($current_page, $admin_pages)) {
            wp_enqueue_style("attendance-admin-{$current_page}");
        }
    }

    /**
     * Register and enqueue admin scripts
     *
     * @param string $hook Current admin page hook
     */
    public function enqueue_admin_scripts($hook) {
        if (!$this->is_plugin_admin_page($hook)) {
            return;
        }

        // Scripts comunes del admin
        wp_register_script(
            'attendance-admin',
            ATTENDANCE_PLUGIN_URL . 'assets/js/admin/admin.js',
            array('jquery'),
            $this->version,
            true
        );

        // Scripts específicos por página
        $admin_scripts = array(
            'centers' => array('jquery-ui-sortable'),
            'attendance-list' => array('jquery-ui-datepicker'),
            'manual-attendance' => array('jquery-ui-autocomplete'),
            'users-list' => array('jquery-ui-dialog')
        );

        $current_page = $this->get_current_admin_page($hook);
        
        wp_enqueue_script('attendance-admin');

        if ($current_page && isset($admin_scripts[$current_page])) {
            foreach ($admin_scripts[$current_page] as $dependency) {
                wp_enqueue_script($dependency);
            }

            wp_register_script(
                "attendance-admin-{$current_page}",
                ATTENDANCE_PLUGIN_URL . "assets/js/admin/pages/{$current_page}.js",
                array_merge(array('attendance-admin'), $admin_scripts[$current_page]),
                $this->version,
                true
            );

            wp_enqueue_script("attendance-admin-{$current_page}");
        }

        // Localizar scripts
        wp_localize_script(
            'attendance-admin',
            'attendanceAdminParams',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('attendance_admin_nonce'),
                'i18n' => $this->get_admin_translations()
            )
        );
    }

    /**
     * Check if current page is a plugin admin page
     *
     * @param string $hook Current admin page hook
     * @return boolean
     */
    private function is_plugin_admin_page($hook) {
        $plugin_pages = array(
            'attendance-tracking',
            'attendance-centers',
            'attendance-records',
            'attendance-users',
            'attendance-manual'
        );

        foreach ($plugin_pages as $page) {
            if (strpos($hook, $page) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get current admin page identifier
     *
     * @param string $hook Current admin page hook
     * @return string|null
     */
    private function get_current_admin_page($hook) {
        $page_map = array(
            'attendance-centers' => 'centers',
            'attendance-records' => 'attendance-list',
            'attendance-manual' => 'manual-attendance',
            'attendance-users' => 'users-list'
        );

        foreach ($page_map as $hook_part => $page) {
            if (strpos($hook, $hook_part) !== false) {
                return $page;
            }
        }

        return null;
    }

    /**
     * Get admin translations
     *
     * @return array
     */
    private function get_admin_translations() {
        return array(
            'confirmDelete' => __('¿Estás seguro de que quieres eliminar este elemento?', 'attendance-tracking'),
            'saving' => __('Guardando...', 'attendance-tracking'),
            'saved' => __('Guardado correctamente', 'attendance-tracking'),
            'error' => __('Ha ocurrido un error', 'attendance-tracking'),
            'required' => __('Este campo es obligatorio', 'attendance-tracking'),
            'loading' => __('Cargando...', 'attendance-tracking'),
            'noResults' => __('No se encontraron resultados', 'attendance-tracking')
        );
    }
}