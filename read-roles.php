<?php
require_once('wp-load.php');
global $wp_roles;
if ( ! isset( $wp_roles ) ) {
    $wp_roles = new WP_Roles();
}
foreach ( $wp_roles->roles as $role => $details ) {
    echo "- " . $details['name'] . " (" . $role . ")\n";
}
