<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

$page_check = get_page_by_path('solicitudes');
if ( ! isset($page_check->ID) ) {
    $page_id = wp_insert_post(array(
        'post_title'    => 'Solicitudes',
        'post_name'     => 'solicitudes',
        'post_content'  => '[sop_layout][sop_solicitudes][/sop_layout]',
        'post_status'   => 'publish',
        'post_type'     => 'page',
    ));
    if ( is_wp_error($page_id) ) {
        echo "Error: " . $page_id->get_error_message();
    } else {
        echo "Created Page ID: " . $page_id;
    }
} else {
    echo "Page already exists! ID: " . $page_check->ID;
}
?>
