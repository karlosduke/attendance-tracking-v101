<?php
/**
 * Admin View: New User
 *
 * @package AttendanceTracking
 * @since 1.0.1
 * @version 1.0.1
 * Last Modified: 2025-05-14 19:45:02
 * Modified by: karlosduke
 */

if (!defined('ABSPATH')) {
    exit;
}

// Obtener los centros para el selector
$centros = DatabaseManager::get_instance()->get_centros();

// Procesar el formulario
if (isset($_POST['user_nonce']) && wp_verify_nonce($_POST['user_nonce'], 'new_user')) {
    $user_data = array(
        'nombre' => sanitize_text_field($_POST['nombre']),
        'apellidos' => sanitize_text_field($_POST['apellidos']),
        'dni' => sanitize_text_field($_POST['dni']),
        'email' => sanitize_email($_POST['email']),
        'telefono' => sanitize_text_field($_POST['telefono']),
        'centro_id' => intval($_POST['centro_id'])
    );

    $result = DatabaseManager::get_instance()->add_user($user_data);
    
    if ($result) {
        $message = __('Usuario añadido correctamente.', 'attendance-tracking');
        $message_type = 'success';
        // Limpiar el formulario
        $user_data = array_fill_keys(array_keys($user_data), '');
    } else {
        $message = __('Error al añadir el usuario.', 'attendance-tracking');
        $message_type = 'error';
    }
}

// Valores por defecto para el formulario
$values = isset($user_data) ? $user_data : array(
    'nombre' => '',
    'apellidos' => '',
    'dni' => '',
    'email' => '',
    'telefono' => '',
    'centro_id' => ''
);
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Nuevo Usuario', 'attendance-tracking'); ?></h1>
    
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

    <form method="post" id="new-user-form" class="attendance-form" novalidate>
        <?php wp_nonce_field('new_user', 'user_nonce'); ?>

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
                           value="<?php echo esc_attr($values['nombre']); ?>"
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
                           value="<?php echo esc_attr($values['apellidos']); ?>"
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
                        <span class="required">*</span>
                    </label>
                </th>
                <td>
                    <input type="text"
                           name="dni"
                           id="dni"
                           value="<?php echo esc_attr($values['dni']); ?>"
                           class="regular-text"
                           required
                           aria-required="true"
                           pattern="[0-9]{8}[A-Za-z]{1}"
                           maxlength="9">
                    <p class="description">
                        <?php _e('8 números seguidos de 1 letra', 'attendance-tracking'); ?>
                    </p>
                    <div id="dni-validation-message" 
                         class="validation-message" 
                         aria-live="polite"></div>
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
                           value="<?php echo esc_attr($values['email']); ?>"
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
                           value="<?php echo esc_attr($values['telefono']); ?>"
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
                                    <?php selected($values['centro_id'], $centro->id); ?>>
                                <?php echo esc_html($centro->centro); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">
                        <?php _e('Centro al que pertenece el usuario', 'attendance-tracking'); ?>
                    </p>
                </td>
            </tr>

            <?php do_action('attendance_user_form_fields', $values); ?>
        </table>

        <p class="submit">
            <input type="submit" 
                   name="submit" 
                   id="submit" 
                   class="button button-primary" 
                   value="<?php esc_attr_e('Crear Usuario', 'attendance-tracking'); ?>">
                   
            <a href="<?php echo esc_url(admin_url('admin.php?page=attendance-users')); ?>" 
               class="button button-secondary">
                <?php _e('Cancelar', 'attendance-tracking'); ?>
            </a>
        </p>
    </form>
</div>

<?php
// Incluir el JavaScript necesario
require_once ATTENDANCE_PLUGIN_DIR . 'assets/js/admin/pages/user-form-validation.js.php';
?>