<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>" />

    <?php
        $iPad_initial_scale = trim(aki::get_option('ipad_initial_scale'));
        $iPhone_initial_scale = trim(aki::get_option('iphone_initial_scale'));
        $is_iPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'],'iPad');
    ?>

    <?php if ($is_iPad): ?>
    <meta name="viewport" content="width=device-width, initial-scale=<?php echo $iPad_initial_scale ?>">
    <?php else: ?>
    <meta name="viewport" content="width=device-width, initial-scale=<?php echo $iPhone_initial_scale ?>">
    <?php endif; ?>

	<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' -'; } ?> <?php bloginfo('name'); ?></title>

	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php
		$favicon_id = aki::get_option('favicon');
		$favicon = wpsp_aki_img($favicon_id, 'full');
		if ( ! empty($favicon)){
			echo "<link rel='shortcut icon' href='". $favicon ."' />";
		}
	?>
	
	<!--[if lt IE 9]>
		<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->

    <link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	
	<?php wp_head(); ?>
</head>

<body class="<?php echo (wpsp_is_home()) ? 'body_home' : 'body_page'; ?> <?php echo 'site_' . $post->post_type ?> <?php echo ($tpl_name) ? $tpl_name : '' ?>">
<?php get_header('main') ?>