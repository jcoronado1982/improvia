<?php
require_once('wp-load.php');
global $wpdb;
$table_name = $wpdb->prefix . 'improvia_audit_log';
var_dump($wpdb->get_var("SHOW TABLES LIKE '$table_name'"));
