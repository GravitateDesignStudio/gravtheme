<?php 
/****************** THEME ACTIVATION SETUP ***********************/
function do_tracking_setup($oldname, $oldtheme=false) {
//Check if the table has already been created, if not CREATE IT!

global $wpdb;

$check_for_table = "SHOW TABLES LIKE 'wp_grav_tracking'";
$table_exists = $wpdb->query($check_for_table);

if($table_exists == 0):
	$create_tracking_table = "CREATE TABLE wp_grav_tracking(id INT NOT NULL AUTO_INCREMENT,PRIMARY KEY(id),name VARHCAR(32),query VARCHAR(64),selector VARCHAR(64),type VARCHAR(32),page INT)";
	$wpdb->query($create_tracking_table); 
endif;
}

add_action("after_switch_theme", "do_tracking_setup", 10 ,  2);


/****************** ADMIN UI ***********************/
function grav_make_tracking_page(){	
  	include_once 'menu-page.php';
}
function grav_make_tracking_sub_page(){	
  	include_once 'menu-sub-page.php';
}
function grav_add_tracking_pages(){
    add_menu_page( 'Add New Event to Track', 'Gravitate Event Tracking', 'manage_options', 'grav-tracking-page','grav_make_tracking_page');
    add_submenu_page( 'grav-tracking-page', 'Google Analytics Settings', 'Settings', 'manage_options', 'tracking-options', 'grav_make_tracking_sub_page');
}
add_action('admin_menu', 'grav_add_tracking_pages');
?>