<?php
/**
 * Admin View: Attendance List
 *
 * @package AttendanceTracking
 * @since 1.0.1
 * @version 1.0.1
 * Last Modified: 2025-05-14 19:58:47
 * Modified by: karlosduke
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$current_date = current_time('mysql');

// Obtener parámetros de filtro
$centro = isset($_GET['centro']) ? intval($_GET['centro']) : 0;
$usuario = isset($_GET['usuario']) ? intval($_GET['usuario']) : 0;
$fecha_inicio = isset($_GET['fecha_inicio']) ? sanitize_text_field($_GET['fecha_inicio']) : '';
$fecha_fin = isset($_GET['fecha_fin']) ? sanitize_text_field($_GET['fecha_fin']) : '';

// Obtener registros filtrados
$registros = DatabaseManager::get_instance()->get_filtered_attendance([
    'centro' => $centro,
    'usuario' => $usuario,
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin
]);

// Obtener listas para filtros
$centros = DatabaseManager::get_instance()->get_centros();
$usuarios = DatabaseManager::get_instance()->get_all_users();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Registros de Asistencia', 'attendance-tracking'); ?></h1>
    
    <!-- Navigation -->
    <nav class="nav-tab-wrapper">
        <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-records')); ?>" 
           class="nav-tab nav-tab-active">
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
           class="nav-tab">
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
    
    <!-- Export Button -->
    <a href="<?php echo wp_nonce_url(
        add_query_arg(
            array(
                'action' => 'export_attendance_csv',
                'centro' => $centro,
                'usuario' => $usuario,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin
            ),
            admin_url('admin-ajax.php')
        ),
        'export-attendance-csv'
    ); ?>" class="page-title-action">
        <span class="dashicons dashicons-download"></span>
        <?php _e('Exportar a CSV', 'attendance-tracking'); ?>
    </a>

    <hr class="wp-header-end">

    <!-- Filtros -->
    <div class="tablenav top">
        <form method="get" class="attendance-filters">
            <input type="hidden" name="page" value="attendance-records">
            
            <div class="alignleft actions">
                <select name="centro" id="filter-centro">
                    <option value=""><?php _e('Todos los centros', 'attendance-tracking'); ?></option>
                    <?php foreach ($centros as $c): ?>
                        <option value="<?php echo esc_attr($c->id); ?>" 
                                <?php selected($centro, $c->id); ?>>
                            <?php echo esc_html($c->centro); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="usuario" id="filter-usuario">
                    <option value=""><?php _e('Todos los usuarios', 'attendance-tracking'); ?></option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?php echo esc_attr($u->id); ?>" 
                                <?php selected($usuario, $u->id); ?>>
                            <?php echo esc_html($u->Nombre . ' ' . $u->Apellidos); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="date" 
                       name="fecha_inicio" 
                       id="filter-fecha-inicio" 
                       value="<?php echo esc_attr($fecha_inicio); ?>" 
                       aria-label="<?php esc_attr_e('Fecha inicio', 'attendance-tracking'); ?>">

                <input type="date" 
                       name="fecha_fin" 
                       id="filter-fecha-fin" 
                       value="<?php echo esc_attr($fecha_fin); ?>" 
                       aria-label="<?php esc_attr_e('Fecha fin', 'attendance-tracking'); ?>">

                <input type="submit" 
                       class="button" 
                       value="<?php esc_attr_e('Filtrar', 'attendance-tracking'); ?>">
                       
                <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-records')); ?>" 
                   class="button">
                    <?php _e('Limpiar', 'attendance-tracking'); ?>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de registros -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-date">
                    <?php _e('Fecha', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-time">
                    <?php _e('Hora', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-name">
                    <?php _e('Nombre', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-surname">
                    <?php _e('Apellidos', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-dni">
                    <?php _e('DNI', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-center">
                    <?php _e('Centro', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-signature">
                    <?php _e('Firma', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-actions">
                    <?php _e('Acciones', 'attendance-tracking'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if ($registros): ?>
                <?php foreach ($registros as $registro): ?>
                    <tr data-record-id="<?php echo esc_attr($registro->id); ?>">
                        <td data-colname="<?php esc_attr_e('Fecha', 'attendance-tracking'); ?>">
                            <?php echo esc_html(wp_date('d/m/Y', strtotime($registro->Fecha))); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Hora', 'attendance-tracking'); ?>">
                            <?php echo esc_html(wp_date('H:i', strtotime($registro->Hora))); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Nombre', 'attendance-tracking'); ?>">
                            <?php echo esc_html($registro->Nombre); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Apellidos', 'attendance-tracking'); ?>">
                            <?php echo esc_html($registro->Apellidos); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('DNI', 'attendance-tracking'); ?>">
                            <?php echo esc_html($registro->DNI); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Centro', 'attendance-tracking'); ?>">
                            <?php echo esc_html($registro->centro); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Firma', 'attendance-tracking'); ?>">
                            <?php if ($registro->firma_url): ?>
                                <a href="<?php echo esc_url(wp_upload_dir()['baseurl'] . '/' . $registro->firma_url); ?>" 
                                   class="signature-preview-link"
                                   target="_blank"
                                   title="<?php esc_attr_e('Ver firma', 'attendance-tracking'); ?>">
                                    <img src="<?php echo esc_url(wp_upload_dir()['baseurl'] . '/' . $registro->firma_url); ?>" 
                                         alt="<?php esc_attr_e('Firma del usuario', 'attendance-tracking'); ?>"
                                         class="signature-thumbnail"
                                         loading="lazy"
                                         width="50"
                                         height="30">
                                </a>
                            <?php else: ?>
                                <span class="no-signature">
                                    <?php _e('Sin firma', 'attendance-tracking'); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Acciones', 'attendance-tracking'); ?>" class="actions">
                            <div class="row-actions">
                                <button type="button" 
                                        class="button button-small view-details" 
                                        data-record-id="<?php echo esc_attr($registro->id); ?>"
                                        title="<?php esc_attr_e('Ver detalles', 'attendance-tracking'); ?>">
                                    <span class="dashicons dashicons-visibility"></span>
                                    <span class="screen-reader-text">
                                        <?php _e('Ver detalles', 'attendance-tracking'); ?>
                                    </span>
                                </button>
                                
                                <?php if (current_user_can('manage_options')): ?>
                                    <button type="button" 
                                            class="button button-small delete-record" 
                                            data-record-id="<?php echo esc_attr($registro->id); ?>"
                                            data-nonce="<?php echo esc_attr(wp_create_nonce('delete_attendance_' . $registro->id)); ?>"
                                            title="<?php esc_attr_e('Eliminar registro', 'attendance-tracking'); ?>">
                                        <span class="dashicons dashicons-trash"></span>
                                        <span class="screen-reader-text">
                                            <?php _e('Eliminar registro', 'attendance-tracking'); ?>
                                        </span>
                                    </button>
                                <?php endif; ?>
                                
                                <?php do_action('attendance_record_actions', $registro); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="no-items">
                        <?php _e('No se encontraron registros.', 'attendance-tracking'); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8">
                    <div class="tablenav bottom">
                        <div class="tablenav-pages">
                            <?php
                            $total_items = count($registros);
                            $items_per_page = 20;
                            $total_pages = ceil($total_items / $items_per_page);
                            
                            if ($total_pages > 1): ?>
                                <span class="displaying-num">
                                    <?php printf(
                                        _n(
                                            '%s elemento', 
                                            '%s elementos', 
                                            $total_items, 
                                            'attendance-tracking'
                                        ),
                                        number_format_i18n($total_items)
                                    ); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Modal para ver detalles -->
<div id="attendance-details-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2><?php _e('Detalles del Registro', 'attendance-tracking'); ?></h2>
        
        <!-- User Info Section -->
        <div class="user-info-wrapper">
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
                    <th scope="row"><?php _e('Centro:', 'attendance-tracking'); ?></th>
                    <td class="user-centro"></td>
                </tr>
            </table>
        </div>

        <!-- Attendance Details Section -->
        <div class="attendance-details">
            <h3><?php _e('Detalles de la Asistencia', 'attendance-tracking'); ?></h3>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Fecha:', 'attendance-tracking'); ?></th>
                    <td class="record-fecha"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Hora:', 'attendance-tracking'); ?></th>
                    <td class="record-hora"></td>
                </tr>
                <tr class="record-signature-row">
                    <th scope="row"><?php _e('Firma:', 'attendance-tracking'); ?></th>
                    <td class="record-signature">
                        <div class="signature-preview">
                            <img src="" 
                                 alt="<?php esc_attr_e('Firma del usuario', 'attendance-tracking'); ?>" 
                                 loading="lazy">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('IP:', 'attendance-tracking'); ?></th>
                    <td class="record-ip"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Dispositivo:', 'attendance-tracking'); ?></th>
                    <td class="record-device"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Registrado por:', 'attendance-tracking'); ?></th>
                    <td class="record-registered-by"></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Fecha de registro:', 'attendance-tracking'); ?></th>
                    <td class="record-created-at"></td>
                </tr>
            </table>

            <?php if (current_user_can('manage_options')): ?>
                <div class="attendance-record-actions">
                    <button type="button" 
                            class="button button-link-delete delete-record-modal" 
                            data-record-id=""
                            data-nonce="">
                        <span class="dashicons dashicons-trash"></span>
                        <?php _e('Eliminar registro', 'attendance-tracking'); ?>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Incluir el JavaScript necesario
require_once ATTENDANCE_PLUGIN_DIR . 'assets/js/admin/pages/attendance-list.js.php';
?>