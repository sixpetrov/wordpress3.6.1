<?php

/**
 * Strip the post's title, if title is not provided
 * it'll use get_the_title()
 *
 * @param $amount
 * @param string $title
 * @return string
 */
function wpsp_strip_title($amount, $title="")
{
	$text = empty($title) ? get_the_title() : $title;

	$stripped_title = substr($text, 0, $amount);

	if (strlen($text) > $amount) 
		$stripped_title .= '...';

	return $stripped_title;
}

/**
 * Strip the text
 *
 * @param $content
 * @param $amount
 * @return string
 */
function wpsp_trim_text($content, $amount)
{	
	$content = preg_replace('@\[caption[^\]]*?\].*?\[\/caption]@si', '', $content);
	$content = preg_replace('@<script[^>]*?>.*?</script>@si', '', $content);
	$content = preg_replace('@<style[^>]*?>.*?</style>@si', '', $content);
	$content = strip_tags($content);
	
	$excerpt = substr($content, 0, $amount);
	
	if (strlen($content) > $amount){
		$excerpt .= "...";
	}
	
	return $excerpt;
}

/**
 * Check if post has excerpt, if not post_content is used.
 * Return trimmed excerpt or post content
 *
 * @param $amount
 * @param int $id
 * @return excerpt
 */
function wpsp_trim_content($amount, $id = 0)
{
	$post = &get_post($id);
	$content_to_trim = (!empty($post->post_excerpt)) ? $post->post_excerpt : $post->post_content;

    //strip shortcodes
    $text = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $content_to_trim);

    return wpsp_trim_text($text , $amount);
}

/**
 * Image src, returns image src located in /images
 *
 * @param string $image - image name with extension
 * @return string image url
 */
function wpsp_get_image($image)
{
	return WPSP_URI . "/images/$image";
}

/**
 * Check if post has featured image
 * 
 * @param (int) $post_id
 * @return image_src or FALSE
 */
function wpsp_thumb_exists($post_id)
{
	$post_thumbnail_id = get_post_thumbnail_id($post_id);
	$image = wp_get_attachment_image_src($post_thumbnail_id, 'full');
	$image_src = $image[0];

	if ($image_src)
		return $image_src;
	else
		return FALSE;
}

/**
 * Tim thumb script
 * 
 * @param int $width
 * @param int $height
 * @param string $image_src - false if $post_id is used
 * @return thumbnail src
 */
function wpsp_timthumb($width, $height, $image_src)
{	
	return WPSP_URI . "/timthumb.php?src=$image_src&amp;w=$width&amp;h=$height&amp;zc=1";
}

/**
 * Generate thumbnail with timthumb if post has one
 *
 * @param $post_id
 * @param $width
 * @param $height
 * @param bool $img_css
 * @return bool|string
 */
function wpsp_thumb($post_id, $width, $height, $img_css=false)
{
	$img_src = wpsp_thumb_exists($post_id);
	
	if ($img_src)
	{
		$img_output  = "<img ";
		$img_output .= "src='". wpsp_timthumb($width, $height, $img_src). "' ";
		$img_output .= "alt=''";
		if ($img_css) {
			$img_output .= " class='{$img_css}'";
		}
		$img_output .= " />";
		
		return $img_output;
	}

	return false;
}


/**
 * Get featured image outside of the loop
 * 
 * @param (string) $post_id
 */
function wpsp_get_featured($post_id)
{
	$post_thumbnail_id = get_post_thumbnail_id($post_id);
	$image = wp_get_attachment_image_src($post_thumbnail_id, 'full');
	$src = $image[0];

	return $src;
}

/**
 * Return TRUE if page has template-home.php as template
 */
function wpsp_is_home()
{
    if (! is_page()) {
        return;
    }

    global $post;
    $template = get_post_meta( $post->ID, '_wp_page_template', true );

    if ( ! $template || 'default' == $template ) {
        return;
    }
    elseif ($template == 'page-templates/template-home.php') {
        return TRUE;
    }

    return FALSE;
}

/**
 * get image src from template options
 *
 * @param $id
 * @param string $size
 * @param bool $icon
 * @return mixed
 */
function wpsp_aki_img($id, $size="thumbnail", $icon=FALSE)
{
	if (empty($id)) {
		return;
	}
	
	$img_id = wp_get_attachment_image_src($id, $size, $icon);
	
	return $img_id[0];
}

/**
 * Check whether the plugin is active by checking the active_plugins list.
 *
 * @param string $plugin Base plugin path from plugins directory.
 * @return bool True, if in the active plugins list. False, not in the list.
 */
function wpsp_is_active_plugin($plugin) {
    return in_array($plugin, apply_filters('active_plugins', get_option('active_plugins')));
}

/**
 * Show featured image on the page
 *
 * @param string $css, default: pageFeaturedImage
 */
function wpspPageFeaturedImage($css="pageFeaturedImage")
{
    if(has_post_thumbnail()):
        ?>
        <div class="<?php echo $css ?>">
            <?php the_post_thumbnail('full', array('alt' => get_the_title(), 'title'=>'')); ?>
        </div>
    <?php
    endif;
}