<?php
/**
 * Attendance form template
 *
 * @package AttendanceTracking
 * @since 1.0.1
 * @version 1.0.1
 * Last Modified: 2025-05-14 19:14:29
 * Modified by: karlosduke
 */

if (!defined('ABSPATH')) {
    exit;
}

// Verificar si el usuario ya registró asistencia hoy
$already_registered = false;
if (isset($_COOKIE['attendance_registered'])) {
    $last_attendance = json_decode(stripslashes($_COOKIE['attendance_registered']), true);
    if ($last_attendance['date'] === date('Y-m-d')) {
        $already_registered = true;
    }
}
?>

<div class="attendance-form-container">
    <?php if ($already_registered): ?>
        <div class="status-message info">
            <div class="message-content">
                <span class="dashicons dashicons-info"></span>
                <div class="message-text">
                    <?php _e('Ya has registrado tu asistencia hoy.', 'attendance-tracking'); ?>
                    <br>
                    <span class="attendance-time">
                        <?php printf(
                            __('Último registro: %s', 'attendance-tracking'),
                            date('H:i', strtotime($last_attendance['time']))
                        ); ?>
                    </span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <form id="attendance-form" class="attendance-form">
            <?php wp_nonce_field('process_attendance', 'attendance_nonce'); ?>
            
            <div class="form-field">
                <label for="dni">
                    <?php _e('DNI', 'attendance-tracking'); ?>
                    <span class="required">*</span>
                </label>
                <div class="input-group">
                    <input type="text" 
                           id="dni" 
                           name="dni" 
                           required 
                           pattern="[0-9]{8}[A-Za-z]{1}"
                           maxlength="9"
                           placeholder="12345678A"
                           autocomplete="off"
                           aria-required="true">
                    <span class="dni-status" aria-live="polite"></span>
                </div>
                <p class="description">
                    <?php _e('Introduce tu DNI (8 números y 1 letra)', 'attendance-tracking'); ?>
                </p>
            </div>

            <div class="user-info" style="display: none;">
                <div class="info-box">
                    <h4><?php _e('Datos del usuario:', 'attendance-tracking'); ?></h4>
                    <p>
                        <strong><?php _e('Nombre:', 'attendance-tracking'); ?></strong> 
                        <span id="user-nombre"></span>
                    </p>
                    <p>
                        <strong><?php _e('Apellidos:', 'attendance-tracking'); ?></strong> 
                        <span id="user-apellidos"></span>
                    </p>
                    <p>
                        <strong><?php _e('Centro:', 'attendance-tracking'); ?></strong> 
                        <span id="user-centro"></span>
                    </p>
                </div>
            </div>

            <div class="form-field signature-field">
                <label for="signature-pad">
                    <?php _e('Firma', 'attendance-tracking'); ?>
                    <span class="required">*</span>
                </label>
                <div class="signature-pad-container">
                    <canvas id="signature-pad"></canvas>
                </div>
                <p class="description">
                    <?php _e('Firma con el dedo o el ratón dentro del recuadro', 'attendance-tracking'); ?>
                </p>
                <div class="signature-controls">
                    <button type="button" id="clear-signature" class="button button-secondary">
                        <span class="dashicons dashicons-dismiss"></span>
                        <?php _e('Borrar firma', 'attendance-tracking'); ?>
                    </button>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" id="submit-attendance" class="button button-primary" disabled>
                    <span class="button-text">
                        <?php _e('Registrar Asistencia', 'attendance-tracking'); ?>
                    </span>
                    <span class="spinner"></span>
                </button>
            </div>

            <div id="attendance-response" class="response-message" aria-live="polite"></div>
        </form>
    <?php endif; ?>
</div>

<template id="attendance-success-template">
    <div class="success-content">
        <div class="success-icon">
            <span class="dashicons dashicons-yes-alt"></span>
        </div>
        <h3><?php _e('¡Asistencia Registrada!', 'attendance-tracking'); ?></h3>
        <div class="success-details">
            <p>
                <strong><?php _e('Fecha:', 'attendance-tracking'); ?></strong> 
                <span class="attendance-date"></span>
            </p>
            <p>
                <strong><?php _e('Hora:', 'attendance-tracking'); ?></strong> 
                <span class="attendance-time"></span>
            </p>
            <p class="success-message">
                <?php _e('Gracias por registrar tu asistencia.', 'attendance-tracking'); ?>
            </p>
        </div>
    </div>
</template>

<?php
/**
 * Hook para permitir a otros plugins o temas añadir contenido después del formulario
 * 
 * @since 1.0.1
 */
do_action('attendance_after_form');
?>