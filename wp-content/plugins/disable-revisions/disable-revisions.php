<?php
/*
Plugin Name: Disable Revisions
Plugin URI:  http://wordpress.org/extend/plugins/disable-revisions/
Description: Disable revision functions in WordPress and delete all entries of revisions in database. <strong>It is extremely important to back up your database before install this plugin</strong>.
Author: Slaver
Author URI: http://slaver.info/
Version: 1.1
*/

register_activation_hook(__FILE__, 'wp_disable_revisions_install');

/**
 * Delete all entries of revisions in database
 * 
 * @global $wpdb WordPress database connection
 * @return $wpdb->query
 */
function wp_disable_revisions_install() {
	global $wpdb;

	$sql = "
		DELETE
			`a`, `b`, `c`  
		FROM
			`" . $wpdb->prefix . "posts` `a`  
		LEFT JOIN
			`" . $wpdb->prefix . "term_relationships` `b` ON (`a`.`ID` = `b`.`object_id`)  
		LEFT JOIN
			`" . $wpdb->prefix . "postmeta` `c` ON (`a`.`ID` = `c`.`post_id`)  
		WHERE
			`a`.`post_type` = 'revision'
	";
	// Clean tables
	$wpdb->query($sql);
	// Optimize tables
	$wpdb->query("OPTIMIZE TABLE `" . $wpdb->prefix . "postmeta`, `" . $wpdb->prefix . "posts`, `" . $wpdb->prefix . "term_relationships`");
}

/**
 * Set define WP_POST_REVISIONS to zero
 */
define ('WP_POST_REVISIONS', 0);

/**
 * Remove revisions actions in post.php
 */
remove_action('pre_post_update', 'wp_save_post_revision');
?>