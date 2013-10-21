<?php
$posts_query = $el_class = $args = $my_query = $speed = $mode = $bxslider_options = '';
$content = $link = $layout = $thumb_size = $link_target = '';
$posts = array();
extract(shortcode_atts(array(
    'el_class' => '',
    'posts_query' => '',
    'mode' => 'horizontal',
    'speed' => '500',
    'easing' =>  '',
    'bxslider_options' => '',
    'content' => 'teaser',
    'link' => 'link_post', // link_post, link_image, link_image_post, link_no
    'layout' => 'title_thumbnail_text', // title_thumbnail_text, thumbnail_title_text, thumbnail_text, thumbnail_title, thumbnail, title_text
    'link_target' => '',
    'thumb_size' => 'thumbnail'
), $atts));
list($args, $my_query) = vc_build_loop_query($posts_query); //

while ( $my_query->have_posts() ) {
    $my_query->the_post(); // Get post from query
    $post = new stdClass(); // Creating post object.
    $post->id = get_the_ID();
    $post->title = the_title("", "", false);
    $post->post_type = get_post_type();
    $post->content = $this->getPostContent($content);
    $post->thumbnail_data = $this->getPostThumbnail($layout, $post->id, $thumb_size);
    $post->thumbnail = $post->thumbnail_data && isset($post->thumbnail_data['thumbnail']) ? $post->thumbnail_data['thumbnail'] : '';
    $post->link = $this->getPostLink($link, $post);
    $posts[] = $post;
}
wp_reset_query();

$options = vc_parse_options_string($bxslider_options, $this->shortcode, 'bxslider_options');

/**
 * Get teaser block template path
 */
$template_r = VcTeaserTemplates::getInstance();
$template_path =  $template_r->getTemplatePath($layout);
// }}
$this->setLinktarget($link_target);

$image_css = 'link_image'.($link==='link_post' ? '' : ' prettyphoto');
$title_css = 'link_title'.($link==='link_image' ? ' prettyphoto' : '');

wp_enqueue_script('vc_bxslider');
wp_enqueue_style('vc_bxslider_css');
if ( $link == 'link_image' || $link == 'link_image_post' ) {
    wp_enqueue_script( 'prettyphoto' );
    wp_enqueue_style( 'prettyphoto' );
}
?>
<ul class="bxslider vc_bxslider<?php echo $el_class ?>" data-settings="<?php echo htmlspecialchars(json_encode($options)) ?>">
    <?php foreach($posts as $post): ?>
    <li>
        <?php include $template_path; ?>
    </li>
    <?php endforeach; ?>
</ul>