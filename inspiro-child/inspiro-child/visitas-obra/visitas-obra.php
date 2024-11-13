<?php
/*
Plugin Name: VisitasObra
Plugin URI:
Description: Planificar visitas a obras
Version: 0.1
Author: Angiru
Author URI:
License:
License URI: GPL

Tablas MySql:

CREATE TABLE wp_angvisitas_obra (
    id INT NOT NULL AUTO_INCREMENT,
    day_visit date NOT NULL,
    hour_from time NOT NULL,
    hour_to time NOT NULL,
    name VARCHAR(128) NOT NULL,
    message TEXT,
    PRIMARY KEY (id)
);
*/
// Enable WP_DEBUG mode
// define( 'WP_DEBUG', true );
// Enable Debug logging to the /wp-content/debug.log file
// define( 'WP_DEBUG_LOG', true );

if ( !defined( 'ABSPATH' ) ) exit;

class VisitasObraWidget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'visitas_obra_widget',
            __('Visitas Obra', 'text_domain'),
            array('description' => __('Un widget para gestionar visitas a la obra', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        ?>
        <h2><?php echo apply_filters('widget_title', $instance['title'] ?? 'Tit del Wid'); ?></h2>

        <form id="visitaForm" method="post">
            <!-- Campo oculto para el ID -->
            <input type="hidden" name="record_id" id="record_id" value="">
            <input type="date" name="day_visit" placeholder="Fecha de Visita" required>
            <input type="time" name="hour_from" placeholder="Hora Desde" required>
            <input type="time" name="hour_to" placeholder="Hora Hasta" required>
            <input type="text" name="name" placeholder="Nombre" required>
            <textarea name="message" placeholder="Mensaje"></textarea>
            <button type="submit">Guardar</button>
        </form>

        <table id="visitasTable">
            <thead>
                <tr>
                    <!-- th>ID</th -->
                    <th>Fecha de Visita</th>
                    <th>Hora Desde</th>
                    <th>Hora Hasta</th>
                    <th>Nombre</th>
                    <th>Mensaje</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody><?php $this->display_records(); ?></tbody>
        </table>

        <script>
            function editRecord(id, day_visit, hour_from, hour_to, name, message) {
                document.getElementById('record_id').value = id; // Asigna el ID al campo oculto
                document.querySelector('input[name="day_visit"]').value = day_visit;
                document.querySelector('input[name="hour_from"]').value = hour_from;
                document.querySelector('input[name="hour_to"]').value = hour_to;
                document.querySelector('input[name="name"]').value = name;
                document.querySelector('textarea[name="message"]').value = message;
            }

            function deleteRecord(id) {
                if (confirm("¿Eliminar este registro?")) {
                    window.location.href = "<?php echo admin_url('admin-ajax.php?action=delete_record&id='); ?>" + id;
                }
            }
        </script>

        <?php
        echo $args['after_widget'];

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handle_form_submission();
        }
    }

    private function handle_form_submission() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'visitas_obra';

        // If there's an ID, it's an update
        if (isset($_POST['record_id']) && !empty($_POST['record_id'])) {
            $wpdb->update($table_name, array(
                'day_visit' => isset($_POST['day_visit']) ? sanitize_text_field($_POST['day_visit']) : '',
                'hour_from' => isset($_POST['hour_from']) ? sanitize_text_field($_POST['hour_from']) : '',
                'hour_to' => isset($_POST['hour_to']) ? sanitize_text_field($_POST['hour_to']) : '',
                'name' => isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '',
                'message' => isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '',
            ), array('id' => intval($_POST['record_id'])));
        } else {
            // Add new record
            $wpdb->insert($table_name, array(
                'day_visit' => isset($_POST['day_visit']) ? sanitize_text_field($_POST['day_visit']) : '',
                'hour_from' => isset($_POST['hour_from']) ? sanitize_text_field($_POST['hour_from']) : '',
                'hour_to' => isset($_POST['hour_to']) ? sanitize_text_field($_POST['hour_to']) : '',
                'name' => isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '',
                'message' => isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '',
            ));
        }
        // Recargar la página para ver los cambios
        echo "<script>location.reload();</script>";
        // Redirect to avoid resubmission
        wp_redirect(wp_get_referer());
        exit; // Ensure no further code is executed after redirect
    }

    public function display_records() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'visitas_obra';
        $records = $wpdb->get_results("SELECT * FROM $table_name");

        foreach ($records as $record) {
            echo "<tr>";
            // echo "<td>{$record->id}</td>";
            echo "<td>{$record->day_visit}</td>";
            echo "<td>{$record->hour_from}</td>";
            echo "<td>{$record->hour_to}</td>";
            echo "<td>{$record->name}</td>";
            echo "<td>{$record->message}</td>";
            echo "<td><button onclick=\"editRecord({$record->id}, '{$record->day_visit}', '{$record->hour_from}', '{$record->hour_to}', '{$record->name}', '{$record->message}')\">Editar</button> ";
            echo "<button onclick=\"deleteRecord({$record->id})\">Eliminar</button></td>";
            echo "</tr>";
        }
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Título del Widget', 'text_domain');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Título:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p><?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

// Register the widget
// function register_visitas_obra_widget() {
//    register_widget('VisitasObraWidget');
// }
// add_action('widgets_init', 'register_visitas_obra_widget');

// Handle record deletion via AJAX
add_action('wp_ajax_delete_record', 'delete_record');
function delete_record() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'visitas_obra';

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $wpdb->delete($table_name, array('id' => $id));
    }

    wp_redirect(wp_get_referer());
    exit; // Ensure no further code is executed after redirect
}