<?php
/**
 * Admin View: Centers
 *
 * @package AttendanceTracking
 * @since 1.0.1
 * @version 1.0.1
 * Last Modified: 2025-05-14 20:02:09
 * Modified by: karlosduke
 */

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$current_date = current_time('mysql');

// Obtener parámetros de búsqueda
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Obtener centros filtrados
$centros = DatabaseManager::get_instance()->get_filtered_centers(['search' => $search]);

// Procesar el formulario de nuevo centro
if (isset($_POST['center_nonce']) && wp_verify_nonce($_POST['center_nonce'], 'add_center')) {
    $centro_data = array(
        'centro' => sanitize_text_field($_POST['centro']),
        'direccion' => sanitize_text_field($_POST['direccion']),
        'telefono' => sanitize_text_field($_POST['telefono']),
        'email' => sanitize_email($_POST['email'])
    );

    $result = DatabaseManager::get_instance()->add_center($centro_data);
    
    if ($result) {
        $message = __('Centro añadido correctamente.', 'attendance-tracking');
        $message_type = 'success';
        // Recargar la lista de centros
        $centros = DatabaseManager::get_instance()->get_filtered_centers(['search' => $search]);
    } else {
        $message = __('Error al añadir el centro.', 'attendance-tracking');
        $message_type = 'error';
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Centros', 'attendance-tracking'); ?></h1>
    
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
           class="nav-tab nav-tab-active">
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
                'action' => 'export_centers_csv',
                'search' => $search
            ),
            admin_url('admin-ajax.php')
        ),
        'export-centers-csv'
    ); ?>" class="page-title-action">
        <span class="dashicons dashicons-download"></span>
        <?php _e('Exportar a CSV', 'attendance-tracking'); ?>
    </a>

    <hr class="wp-header-end">

    <?php if (isset($message)): ?>
        <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <!-- Add New Center Form -->
    <?php if (current_user_can('manage_options')): ?>
        <div class="add-new-center-wrapper">
            <h2><?php _e('Añadir Nuevo Centro', 'attendance-tracking'); ?></h2>
            <form method="post" id="add-center-form" class="center-form" novalidate>
                <?php wp_nonce_field('add_center', 'center_nonce'); ?>
                
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="centro">
                                <?php _e('Nombre del Centro', 'attendance-tracking'); ?>
                                <span class="required">*</span>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                   name="centro"
                                   id="centro"
                                   class="regular-text"
                                   required
                                   aria-required="true"
                                   maxlength="100">
                            <p class="description">
                                <?php _e('Nombre del centro (máximo 100 caracteres)', 'attendance-tracking'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="direccion">
                                <?php _e('Dirección', 'attendance-tracking'); ?>
                                <span class="required">*</span>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                   name="direccion"
                                   id="direccion"
                                   class="regular-text"
                                   required
                                   aria-required="true"
                                   maxlength="200">
                            <p class="description">
                                <?php _e('Dirección completa del centro', 'attendance-tracking'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="telefono">
                                <?php _e('Teléfono', 'attendance-tracking'); ?>
                                <span class="required">*</span>
                            </label>
                        </th>
                        <td>
                            <input type="tel"
                                   name="telefono"
                                   id="telefono"
                                   class="regular-text"
                                   required
                                   aria-required="true"
                                   pattern="[0-9]{9}">
                            <p class="description">
                                <?php _e('Número de teléfono (9 dígitos)', 'attendance-tracking'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="email">
                                <?php _e('Email', 'attendance-tracking'); ?>
                                <span class="required">*</span>
                            </label>
                        </th>
                        <td>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="regular-text"
                                   required
                                   aria-required="true">
                            <p class="description">
                                <?php _e('Dirección de correo electrónico del centro', 'attendance-tracking'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <input type="submit" 
                           name="submit" 
                           id="submit" 
                           class="button button-primary" 
                           value="<?php esc_attr_e('Añadir Centro', 'attendance-tracking'); ?>">
                </p>
            </form>
        </div>
    <?php endif; ?>

    <!-- Search Form -->
    <div class="tablenav top">
        <form method="get" class="centers-search">
            <input type="hidden" name="page" value="attendance-centers">
            
            <div class="alignleft actions">
                <input type="search" 
                       name="s" 
                       id="center-search-input" 
                       value="<?php echo esc_attr($search); ?>"
                       placeholder="<?php esc_attr_e('Buscar centros...', 'attendance-tracking'); ?>">

                <input type="submit" 
                       class="button" 
                       value="<?php esc_attr_e('Buscar', 'attendance-tracking'); ?>">
                       
                <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-centers')); ?>" 
                   class="button">
                    <?php _e('Limpiar', 'attendance-tracking'); ?>
                </a>
            </div>
        </form>
    </div>

    <!-- Centers Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-id">
                    <?php _e('ID', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-name">
                    <?php _e('Centro', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-address">
                    <?php _e('Dirección', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-phone">
                    <?php _e('Teléfono', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-email">
                    <?php _e('Email', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-users">
                    <?php _e('Usuarios', 'attendance-tracking'); ?>
                </th>
                <th scope="col" class="manage-column column-actions">
                    <?php _e('Acciones', 'attendance-tracking'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if ($centros): ?>
                <?php foreach ($centros as $centro): ?>
                    <tr data-center-id="<?php echo esc_attr($centro->id); ?>">
                        <td data-colname="<?php esc_attr_e('ID', 'attendance-tracking'); ?>">
                            <?php echo esc_html($centro->id); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Centro', 'attendance-tracking'); ?>">
                            <strong><?php echo esc_html($centro->centro); ?></strong>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Dirección', 'attendance-tracking'); ?>">
                            <?php echo esc_html($centro->direccion); ?>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Teléfono', 'attendance-tracking'); ?>">
                            <a href="tel:<?php echo esc_attr($centro->telefono); ?>">
                                <?php echo esc_html($centro->telefono); ?>
                            </a>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Email', 'attendance-tracking'); ?>">
                            <a href="mailto:<?php echo esc_attr($centro->email); ?>">
                                <?php echo esc_html($centro->email); ?>
                            </a>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Usuarios', 'attendance-tracking'); ?>">
                            <a href="<?php echo esc_url(add_query_arg(
                                array(
                                    'page' => 'attendance-users',
                                    'centro' => $centro->id
                                ),
                                admin_url('admin.php')
                            )); ?>">
                                <?php echo esc_html($centro->users_count); ?>
                            </a>
                        </td>
                        
                        <td data-colname="<?php esc_attr_e('Acciones', 'attendance-tracking'); ?>" class="actions">
                            <div class="row-actions">
                                <?php if (current_user_can('manage_options')): ?>
                                    <button type="button" 
                                            class="button button-small edit-center" 
                                            data-center-id="<?php echo esc_attr($centro->id); ?>"
                                            title="<?php esc_attr_e('Editar centro', 'attendance-tracking'); ?>">
                                        <span class="dashicons dashicons-edit"></span>
                                        <span class="screen-reader-text">
                                            <?php _e('Editar centro', 'attendance-tracking'); ?>
                                        </span>
                                    </button>
                                    
                                    <button type="button" 
                                            class="button button-small delete-center" 
                                            data-center-id="<?php echo esc_attr($centro->id); ?>"
                                            data-nonce="<?php echo esc_attr(wp_create_nonce('delete_center_' . $centro->id)); ?>"
                                            title="<?php esc_attr_e('Eliminar centro', 'attendance-tracking'); ?>">
                                        <span class="dashicons dashicons-trash"></span>
                                        <span class="screen-reader-text">
                                            <?php _e('Eliminar centro', 'attendance-tracking'); ?>
                                        </span>
                                    </button>
                                <?php endif; ?>
                                
                                <a href="<?php echo esc_url(add_query_arg(
                                    array(
                                        'page' => 'attendance-records',
                                        'centro' => $centro->id
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
                                
                                <?php do_action('attendance_center_actions', $centro); ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="no-items">
                        <?php _e('No se encontraron centros.', 'attendance-tracking'); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7">
                    <div class="tablenav bottom">
                        <div class="tablenav-pages">
                            <?php
                            $total_items = count($centros);
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

<!-- Edit Center Modal -->
<div id="edit-center-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2><?php _e('Editar Centro', 'attendance-tracking'); ?></h2>
        <form method="post" id="edit-center-form" class="center-form" novalidate>
            <?php wp_nonce_field('edit_center', 'edit_center_nonce'); ?>
            <input type="hidden" name="center_id" id="edit-center-id" value="">
            
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="edit-centro">
                            <?php _e('Nombre del Centro', 'attendance-tracking'); ?>
                            <span class="required">*</span>
                        </label>
                    </th>
                    <td>
                        <input type="text"
                               name="centro"
                               id="edit-centro"
                               class="regular-text"
                               required
                               aria-required="true"
                               maxlength="100">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="edit-direccion">
                            <?php _e('Dirección', 'attendance-tracking'); ?>
                            <span class="required">*</span>
                        </label>
                    </th>
                    <td>
                        <input type="text"
                               name="direccion"
                               id="edit-direccion"
                               class="regular-text"
                               required
                               aria-required="true"
                               maxlength="200">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="edit-telefono">
                            <?php _e('Teléfono', 'attendance-tracking'); ?>
                            <span class="required">*</span>
                        </label>
                    </th>
                    <td>
                        <input type="tel"
                               name="telefono"
                               id="edit-telefono"
                               class="regular-text"
                               required
                               aria-required="true"
                               pattern="[0-9]{9}">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="edit-email">
                            <?php _e('Email', 'attendance-tracking'); ?>
                            <span class="required">*</span>
                        </label>
                    </th>
                    <td>
                        <input type="email"
                               name="email"
                               id="edit-email"
                               class="regular-text"
                               required
                               aria-required="true">
                    </td>
                </tr>
            </table>

            <div class="submit-wrapper">
                <input type="submit" 
                       name="submit" 
                       class="button button-primary" 
                       value="<?php esc_attr_e('Actualizar Centro', 'attendance-tracking'); ?>">
                       
                <button type="button" 
                        class="button cancel-edit">
                    <?php _e('Cancelar', 'attendance-tracking'); ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-center-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2><?php _e('Confirmar Eliminación', 'attendance-tracking'); ?></h2>
        <p><?php _e('¿Estás seguro de que quieres eliminar este centro? Esta acción eliminará también todos los registros asociados.', 'attendance-tracking'); ?></p>
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
require_once ATTENDANCE_PLUGIN_DIR . 'assets/js/admin/pages/centers.js.php';
?>