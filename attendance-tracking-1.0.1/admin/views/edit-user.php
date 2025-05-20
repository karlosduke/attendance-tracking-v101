<?php
/**
 * Admin View: Edit User
 *
 * @package AttendanceTracking
 * @since 1.0.1
 * @version 1.0.1
 * Last Modified: 2025-05-14 19:56:01
 * Modified by: karlosduke
 */

if (!defined('ABSPATH')) {
    exit;
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$user = $user_id ? DatabaseManager::get_instance()->get_user($user_id) : null;

if (!$user) {
    wp_die(__('Usuario no encontrado.', 'attendance-tracking'));
}

// Obtener los centros para el selector
$centros = DatabaseManager::get_instance()->get_centros();

// Procesar el formulario
if (isset($_POST['user_nonce']) && wp_verify_nonce($_POST['user_nonce'], 'edit_user')) {
    $user_data = array(
        'id' => $user_id,
        'nombre' => sanitize_text_field($_POST['nombre']),
        'apellidos' => sanitize_text_field($_POST['apellidos']),
        'dni' => sanitize_text_field($_POST['dni']),
        'email' => sanitize_email($_POST['email']),
        'telefono' => sanitize_text_field($_POST['telefono']),
        'centro_id' => intval($_POST['centro_id'])
    );

    $result = DatabaseManager::get_instance()->update_user($user_data);
    
    if ($result) {
        $message = __('Usuario actualizado correctamente.', 'attendance-tracking');
        $message_type = 'success';
        // Recargar datos del usuario
        $user = DatabaseManager::get_instance()->get_user($user_id);
    } else {
        $message = __('Error al actualizar el usuario.', 'attendance-tracking');
        $message_type = 'error';
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Editar Usuario', 'attendance-tracking'); ?></h1>
    
    <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-users')); ?>" 
       class="page-title-action">
        <?php _e('Volver al listado', 'attendance-tracking'); ?>
    </a>

    <hr class="wp-header-end">

    <?php if (isset($message)): ?>
        <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <!-- User Info Box -->
    <div class="user-info-wrapper">
        <h3><?php _e('Información Actual del Usuario', 'attendance-tracking'); ?></h3>
        <table class="form-table" aria-label="<?php esc_attr_e('Información del Usuario', 'attendance-tracking'); ?>">
            <tr>
                <th scope="row"><?php _e('ID:', 'attendance-tracking'); ?></th>
                <td><?php echo esc_html($user->id); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('DNI:', 'attendance-tracking'); ?></th>
                <td><?php echo esc_html($user->DNI); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Email:', 'attendance-tracking'); ?></th>
                <td>
                    <?php if (!empty($user->email)): ?>
                        <a href="mailto:<?php echo esc_attr($user->email); ?>">
                            <?php echo esc_html($user->email); ?>
                        </a>
                    <?php else: ?>
                        <em><?php _e('No disponible', 'attendance-tracking'); ?></em>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Teléfono:', 'attendance-tracking'); ?></th>
                <td>
                    <?php if (!empty($user->telefono)): ?>
                        <a href="tel:<?php echo esc_attr($user->telefono); ?>">
                            <?php echo esc_html($user->telefono); ?>
                        </a>
                    <?php else: ?>
                        <em><?php _e('No disponible', 'attendance-tracking'); ?></em>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Centro actual:', 'attendance-tracking'); ?></th>
                <td><?php echo esc_html($user->centro); ?></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Fecha de registro:', 'attendance-tracking'); ?></th>
                <td>
                    <?php echo esc_html(
                        wp_date(
                            get_option('date_format') . ' ' . get_option('time_format'),
                            strtotime($user->created_at)
                        )
                    ); ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Última modificación:', 'attendance-tracking'); ?></th>
                <td>
                    <?php echo esc_html(
                        wp_date(
                            get_option('date_format') . ' ' . get_option('time_format'),
                            strtotime($user->updated_at)
                        )
                    ); ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- Edit Form -->
    <form method="post" id="edit-user-form" class="attendance-form" novalidate>
        <?php wp_nonce_field('edit_user', 'user_nonce'); ?>
        <input type="hidden" name="user_id" value="<?php echo esc_attr($user_id); ?>">

        <table class="form-table" role="presentation">
            <tr>
                <th scope="row">
                    <label for="nombre">
                        <?php _e('Nombre', 'attendance-tracking'); ?>
                        <span class="required">*</span>
                    </label>
                </th>
                <td>
                    <input type="text"
                           name="nombre"
                           id="nombre"
                           value="<?php echo esc_attr($user->Nombre); ?>"
                           class="regular-text"
                           required
                           aria-required="true"
                           minlength="2"
                           maxlength="50">
                    <p class="description">
                        <?php _e('Nombre del usuario (2-50 caracteres)', 'attendance-tracking'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="apellidos">
                        <?php _e('Apellidos', 'attendance-tracking'); ?>
                        <span class="required">*</span>
                    </label>
                </th>
                <td>
                    <input type="text"
                           name="apellidos"
                           id="apellidos"
                           value="<?php echo esc_attr($user->Apellidos); ?>"
                           class="regular-text"
                           required
                           aria-required="true"
                           minlength="2"
                           maxlength="100">
                    <p class="description">
                        <?php _e('Apellidos del usuario (2-100 caracteres)', 'attendance-tracking'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="dni">
                        <?php _e('DNI', 'attendance-tracking'); ?>
                    </label>
                </th>
                <td>
                    <input type="text"
                           name="dni"
                           id="dni"
                           value="<?php echo esc_attr($user->DNI); ?>"
                           class="regular-text"
                           readonly
                           disabled>
                    <p class="description">
                        <?php _e('El DNI no se puede modificar', 'attendance-tracking'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="email">
                        <?php _e('Email', 'attendance-tracking'); ?>
                    </label>
                </th>
                <td>
                    <input type="email"
                           name="email"
                           id="email"
                           value="<?php echo esc_attr($user->email); ?>"
                           class="regular-text">
                    <p class="description">
                        <?php _e('Dirección de correo electrónico (opcional)', 'attendance-tracking'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="telefono">
                        <?php _e('Teléfono', 'attendance-tracking'); ?>
                    </label>
                </th>
                <td>
                    <input type="tel"
                           name="telefono"
                           id="telefono"
                           value="<?php echo esc_attr($user->telefono); ?>"
                           class="regular-text"
                           pattern="[0-9]{9}">
                    <p class="description">
                        <?php _e('Número de teléfono (opcional)', 'attendance-tracking'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="centro_id">
                        <?php _e('Centro', 'attendance-tracking'); ?>
                        <span class="required">*</span>
                    </label>
                </th>
                <td>
                    <select name="centro_id" 
                            id="centro_id" 
                            required 
                            aria-required="true">
                        <option value="">
                            <?php _e('Selecciona un centro', 'attendance-tracking'); ?>
                        </option>
                        <?php foreach ($centros as $centro): ?>
                            <option value="<?php echo esc_attr($centro->id); ?>"
                                    <?php selected($user->centro_id, $centro->id); ?>>
                                <?php echo esc_html($centro->centro); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">
                        <?php _e('Centro al que pertenece el usuario', 'attendance-tracking'); ?>
                    </p>
                </td>
            </tr>

            <?php do_action('attendance_user_form_fields', $user); ?>
        </table>

        <p class="submit">
            <input type="submit" 
                   name="submit" 
                   id="submit" 
                   class="button button-primary" 
                   value="<?php esc_attr_e('Actualizar Usuario', 'attendance-tracking'); ?>">
                   
            <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-users')); ?>" 
               class="button button-secondary">
                <?php _e('Cancelar', 'attendance-tracking'); ?>
            </a>
        </p>
    </form>
</div>

<?php
// Incluir el JavaScript necesario para la validación del formulario
require_once ATTENDANCE_PLUGIN_DIR . 'assets/js/admin/pages/user-form-validation.js.php';
?>