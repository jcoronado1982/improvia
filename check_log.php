<?php
define('WP_USE_THEMES', false);
require_once('wp-load.php');
$logs = get_option('sop_mock_transactions_log');
echo json_encode($logs, JSON_PRETTY_PRINT);
?>
