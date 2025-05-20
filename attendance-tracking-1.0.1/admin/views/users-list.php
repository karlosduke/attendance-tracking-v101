<?php
/**
 * Admin View: Users List
 *
 * @package AttendanceTracking
 * @since 1.0.1
 * @version 1.0.1
 * Last Modified: 2025-05-14 20:00:36
 * Modified by: karlosduke
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$current_date = current_time('mysql');

// Obtener parámetros de filtro
$centro = isset($_GET['centro']) ? intval($_GET['centro']) : 0;
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Obtener usuarios filtrados
$usuarios = DatabaseManager::get_instance()->get_filtered_users([
    'centro' => $centro,
    'search' => $search
]);

// Obtener centros para el filtro
$centros = DatabaseManager::get_instance()->get_centros();
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Usuarios', 'attendance-tracking'); ?></h1>
    
    <!-- Navigation -->
    <nav class="nav-tab-wrapper">
        <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-records')); ?>" 
           class="nav-tab">
            <span class="dashicons dashicons-list-view"></span>
            <?php _e('Registros', 'attendance-tracking'); ?>
        </a>
        
        <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-users')); ?>" 
           class="nav-tab nav-tab-active">
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

    <!-- Add New Button -->
    <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-new-user')); ?>" 
       class="page-title-action">
        <span class="dashicons dashicons-plus-alt2"></span>
        <?php _e('Añadir Nuevo', 'attendance-tracking'); ?>
    </a>

    <!-- Export Button -->
    <a href="<?php echo wp_nonce_url(
        add_query_arg(
            array(
                'action' => 'export_users_csv',
                'centro' => $centro,
                'search' => $search
            ),
            admin_url('admin-ajax.php')
        ),
        'export-users-csv'
    ); ?>" class="page-title-action">
        <span class="dashicons dashicons-download"></span>
        <?php _e('Exportar a CSV', 'attendance-tracking'); ?>
    </a>

    <hr class="wp-header-end">

    <!-- Search & Filters -->
    <div class="tablenav top">
        <form method="get" class="users-filters">
            <input type="hidden" name="page" value="attendance-users">
            
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

                <input type="search" 
                       name="s" 
                       id="user-search-input" 
                       value="<?php echo esc_attr($search); ?>"
                       placeholder="<?php esc_attr_e('Buscar usuarios...', 'attendance-tracking'); ?>">

                <input type="submit" 
                       class="button" 
                       value="<?php esc_attr_e('Filtrar', 'attendance-tracking'); ?>">
                       
                <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-users')); ?>" 
                   class="button">
                    <?php _e('Limpiar', 'attendance-tracking'); ?>
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-id">
                    <?php _e('ID', 'attendance-tracking'); ?>
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
                <th scope="col" class="manage-column column-email">
                    <?php _e('Email', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-phone">
                    <?php _e('Teléfono', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-center">
                    <?php _e('Centro', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-actions">
                    <?php _e('Acciones', 'attendance-tracking'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if ($usuarios): ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr data-user-id="<?php echo esc_attr($usuario->id); ?>">
                        <td data-colname="<?php esc_attr_e('ID', 'attendance-tracking'); ?>">
                            <?php echo esc_html($usuario->id); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Nombre', 'attendance-tracking'); ?>">
                            <?php echo esc_html($usuario->Nombre); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Apellidos', 'attendance-tracking'); ?>">
                            <?php echo esc_html($usuario->Apellidos); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('DNI', 'attendance-tracking'); ?>">
                            <?php echo esc_html($usuario->DNI); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Email', 'attendance-tracking'); ?>">
                            <?php if (!empty($usuario->email)): ?>
                                <a href="mailto:<?php echo esc_attr($usuario->email); ?>">
                                    <?php echo esc_html($usuario->email); ?>
                                </a>
                            <?php else: ?>
                                <em><?php _e('No disponible', 'attendance-tracking'); ?></em>
                            <?php endif; ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Teléfono', 'attendance-tracking'); ?>">
                            <?php if (!empty($usuario->telefono)): ?>
                                <a href="tel:<?php echo esc_attr($usuario->telefono); ?>">
                                    <?php echo esc_html($usuario->telefono); ?>
                                </a>
                            <?php else: ?>
                                <em><?php _e('No disponible', 'attendance-tracking'); ?></em>
                            <?php endif; ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Centro', 'attendance-tracking'); ?>">
                            <?php echo esc_html($usuario->centro); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Acciones', 'attendance-tracking'); ?>" class="actions">
                            <div class="row-actions">
                                <a href="<?php echo esc_url(add_query_arg(
                                    array(
                                        'page' => 'attendance-edit-user',
                                        'user_id' => $usuario->id
                                    ),
                                    admin_url('admin.php')
                                )); ?>" 
                                   class="button button-small"
                                   title="<?php esc_attr_e('Editar usuario', 'attendance-tracking'); ?>">
                                    <span class="dashicons dashicons-edit"></span>
                                    <span class="screen-reader-text">
                                        <?php _e('Editar usuario', 'attendance-tracking'); ?>
                                    </span>
                                </a>
                                
                                <?php if (current_user_can('manage_options')): ?>
                                    <button type="button" 
                                            class="button button-small delete-user" 
                                            data-user-id="<?php echo esc_attr($usuario->id); ?>"
                                            data-nonce="<?php echo esc_attr(wp_create_nonce('delete_user_' . $usuario->id)); ?>"
                                            title="<?php esc_attr_e('Eliminar usuario', 'attendance-tracking'); ?>">
                                        <span class="dashicons dashicons-trash"></span>
                                        <span class="screen-reader-text">
                                            <?php _e('Eliminar usuario', 'attendance-tracking'); ?>
                                        </span>
                                    </button>
                                <?php endif; ?>
                                
                                <a href="<?php echo esc_url(add_query_arg(
                                    array(
                                        'page' => 'attendance-records',
                                        'usuario' => $usuario->id
                                    ),
                                    admin_url('admin.php')
                                )); ?>" 
                                   class="button button-small"
                                   title="<?php esc_attr_e('Ver registros', 'attendance-tracking'); ?>">
                                    <span class="dashicons dashicons-list-view"></span>
                                    <span class="screen-reader-text">
                                        <?php _e('Ver registros', 'attendance-tracking'); ?>
                                    </span>
                                </a>
                                
                                <?php do_action('attendance_user_actions', $usuario); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="no-items">
                        <?php _e('No se encontraron usuarios.', 'attendance-tracking'); ?>
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
                            $total_items = count($usuarios);
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

<!-- Delete Confirmation Modal -->
<div id="delete-user-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2><?php _e('Confirmar Eliminación', 'attendance-tracking'); ?></h2>
        <p><?php _e('¿Estás seguro de que quieres eliminar este usuario? Esta acción no se puede deshacer.', 'attendance-tracking'); ?></p>
        <div class="modal-actions">
            <button type="button" class="button button-primary confirm-delete">
                <?php _e('Sí, eliminar', 'attendance-tracking'); ?>
            </button>
            <button type="button" class="button cancel-delete">
                <?php _e('Cancelar', 'attendance-tracking'); ?>
            </button>
        </div>
    </div>
</div>

<?php
// Incluir el JavaScript necesario
require_once ATTENDANCE_PLUGIN_DIR . 'assets/js/admin/pages/users-list.js.php';
?>