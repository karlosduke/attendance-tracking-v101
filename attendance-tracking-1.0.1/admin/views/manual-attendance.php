<?php
/**
 * Admin View: Manual Attendance Registration
 *
 * @package AttendanceTracking
 * @since 1.0.1
 * @version 1.0.1
 * Last Modified: 2025-05-14 19:57:36
 * Modified by: karlosduke
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$current_date = current_time('mysql');
$centros = DatabaseManager::get_instance()->get_centros();

// Procesar el formulario
if (isset($_POST['attendance_nonce']) && wp_verify_nonce($_POST['attendance_nonce'], 'register_manual_attendance')) {
    $attendance_data = array(
        'user_id' => intval($_POST['user_id']),
        'fecha' => sanitize_text_field($_POST['fecha']),
        'hora' => sanitize_text_field($_POST['hora']),
        'registered_by' => get_current_user_id(),
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']
    );

    $result = DatabaseManager::get_instance()->register_attendance($attendance_data);
    
    if ($result) {
        $message = __('Asistencia registrada correctamente.', 'attendance-tracking');
        $message_type = 'success';
    } else {
        $message = __('Error al registrar la asistencia.', 'attendance-tracking');
        $message_type = 'error';
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Registro Manual de Asistencia', 'attendance-tracking'); ?></h1>

    <!-- Navigation -->
    <nav class="nav-tab-wrapper">
        <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-records')); ?>" 
           class="nav-tab">
            <span class="dashicons dashicons-list-view"></span>
            <?php _e('Registros', 'attendance-tracking'); ?>
        </a>
        
        <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-users')); ?>" 
           class="nav-tab">
            <span class="dashicons dashicons-admin-users"></span>
            <?php _e('Usuarios', 'attendance-tracking'); ?>
        </a>
        
        <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-centers')); ?>" 
           class="nav-tab">
            <span class="dashicons dashicons-building"></span>
            <?php _e('Centros', 'attendance-tracking'); ?>
        </a>
        
        <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-manual')); ?>" 
           class="nav-tab nav-tab-active">
            <span class="dashicons dashicons-edit"></span>
            <?php _e('Registro Manual', 'attendance-tracking'); ?>
        </a>
        
        <?php if (current_user_can('manage_options')): ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-settings')); ?>" 
               class="nav-tab">
                <span class="dashicons dashicons-admin-settings"></span>
                <?php _e('Configuración', 'attendance-tracking'); ?>
            </a>
        <?php endif; ?>
    </nav>

    <hr class="wp-header-end">

    <?php if (isset($message)): ?>
        <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <div class="manual-attendance-form">
        <!-- Búsqueda por DNI -->
        <div class="search-user-section">
            <h2><?php _e('Buscar Usuario', 'attendance-tracking'); ?></h2>
            <div class="search-form">
                <label for="dni_search"><?php _e('DNI del usuario:', 'attendance-tracking'); ?></label>
                <input type="text" 
                       id="dni_search" 
                       name="dni_search" 
                       class="regular-text" 
                       pattern="[0-9]{8}[A-Za-z]{1}" 
                       maxlength="9"
                       placeholder="<?php esc_attr_e('Introduzca el DNI', 'attendance-tracking'); ?>">
                <button type="button" class="button search-user">
                    <span class="dashicons dashicons-search"></span>
                    <?php _e('Buscar Usuario', 'attendance-tracking'); ?>
                </button>
            </div>
            <p class="description">
                <?php _e('Introduzca el DNI del usuario (8 números y 1 letra)', 'attendance-tracking'); ?>
            </p>
        </div>

        <!-- User Info Box - Initially hidden -->
        <div id="user-info-section" class="user-info-wrapper" style="display: none;">
            <h3><?php _e('Información del Usuario', 'attendance-tracking'); ?></h3>
            <table class="form-table" aria-label="<?php esc_attr_e('Información del Usuario', 'attendance-tracking'); ?>">
                <tr>
                    <th scope="row"><?php _e('ID:', 'attendance-tracking'); ?></th>
                    <td class="user-id"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Nombre:', 'attendance-tracking'); ?></th>
                    <td class="user-nombre"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Apellidos:', 'attendance-tracking'); ?></th>
                    <td class="user-apellidos"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('DNI:', 'attendance-tracking'); ?></th>
                    <td class="user-dni"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Email:', 'attendance-tracking'); ?></th>
                    <td class="user-email"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Teléfono:', 'attendance-tracking'); ?></th>
                    <td class="user-telefono"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Centro:', 'attendance-tracking'); ?></th>
                    <td class="user-centro"></td>
                </tr>
            </table>
        </div>

        <!-- Manual Attendance Form - Initially hidden -->
        <form method="post" id="manual-attendance-form" style="display: none;">
            <?php wp_nonce_field('register_manual_attendance', 'attendance_nonce'); ?>
            <input type="hidden" name="user_id" id="selected_user_id" value="">

            <h3><?php _e('Registrar Asistencia', 'attendance-tracking'); ?></h3>
            
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="fecha">
                            <?php _e('Fecha', 'attendance-tracking'); ?>
                            <span class="required">*</span>
                        </label>
                    </th>
                    <td>
                        <input type="date" 
                               name="fecha" 
                               id="fecha" 
                               value="<?php echo esc_attr(wp_date('Y-m-d')); ?>"
                               required
                               aria-required="true"
                               max="<?php echo esc_attr(wp_date('Y-m-d')); ?>">
                        <p class="description">
                            <?php _e('La fecha no puede ser posterior a hoy', 'attendance-tracking'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="hora">
                            <?php _e('Hora', 'attendance-tracking'); ?>
                            <span class="required">*</span>
                        </label>
                    </th>
                    <td>
                        <input type="time" 
                               name="hora" 
                               id="hora" 
                               value="<?php echo esc_attr(wp_date('H:i')); ?>"
                               required
                               aria-required="true">
                        <p class="description">
                            <?php _e('Formato 24 horas (HH:MM)', 'attendance-tracking'); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <div class="submit-wrapper">
                <input type="submit" 
                       name="submit" 
                       id="submit" 
                       class="button button-primary" 
                       value="<?php esc_attr_e('Registrar Asistencia', 'attendance-tracking'); ?>">
                
                <button type="button" 
                        class="button button-secondary cancel-attendance">
                    <?php _e('Cancelar', 'attendance-tracking'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// Incluir el JavaScript necesario
require_once ATTENDANCE_PLUGIN_DIR . 'assets/js/admin/pages/manual-attendance.js.php';
?>