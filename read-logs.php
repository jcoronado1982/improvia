<?php
require_once('wp-load.php');
global $wpdb;
$table_name = $wpdb->prefix . 'improvia_audit_log';
$results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 5;", ARRAY_A);
echo json_encode($results, JSON_PRETTY_PRINT);
