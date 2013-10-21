<?php
/*
Plugin Name: PrettyPhoto
Plugin URI: http://en.ibabar.com
Description: The WordPress port of the jQuery library named PrettyPhoto.
Version: 1.0
Author: Babar
Author URI: http://www.iBabar.com
Requires at least: 3.0
Tested Up to: 3.5.1
Stable Tag: 1.0
License: GPL v2
*/

add_action ('wp_footer', 'prettyphoto');

function prettyphoto() {

	
echo '<link rel="stylesheet" href="' . plugin_dir_url("prettyphoto.php") .'prettyphoto/css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
	';
echo '<script src="' . plugin_dir_url("prettyphoto.php") .'prettyphoto/js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
	';
echo "
<script type=\"text/javascript\" charset=\"utf-8\">
    jQuery(document).ready(function() {
    jQuery(\"a[rel^='prettyPhoto']\").prettyPhoto({
	    deeplinking: false,
	    });
    });
</script>
";
	}
