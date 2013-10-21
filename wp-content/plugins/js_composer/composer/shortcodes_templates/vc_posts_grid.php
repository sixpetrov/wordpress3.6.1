<?php
$grid_link = $grid_layout_mode = $title = $filter= '';
$posts = array();
extract(shortcode_atts(array(
    'title' => '',
    'grid_columns_count' => 4,
    'grid_teasers_count' => 8,
    'grid_layout' => 'title_thumbnail_text', // title_thumbnail_text, thumbnail_title_text, thumbnail_text, thumbnail_title, thumbnail, title_text
    'grid_link' => 'link_post', // link_post, link_image, link_image_post, link_no
    'grid_link_target' => '_self',
    'filter' => '', //grid,
    'grid_thumb_size' => 'thumbnail',
    'grid_layout_mode' => 'fitRows',
    'grid_content' => 'teaser', // teaser, content
    'el_class' => '',
    'teaser_width' => '12',
    'orderby' => NULL,
    'order' => 'DESC',
    'loop' => '',
), $atts));

if(empty($loop)) return;

$this->getLoop($loop);
$query = $this->query;
$args = $this->loop_args;

while ( $query->have_posts() ) {
    $query->the_post(); // Get post from query

    $post = new stdClass(); // Creating post object.
    $post->id = get_the_ID();
    $post->title = the_title("", "", false);
    $post->categories_css = $this->getCategoriesCss($post->id);
    $post->post_type = get_post_type();
    $post->content = $this->getPostContent($grid_content);
    $post->thumbnail_data = $this->getPostThumbnail($grid_layout, $post->id, $grid_thumb_size);
    $post->thumbnail = $post->thumbnail_data && isset($post->thumbnail_data['thumbnail']) ? $post->thumbnail_data['thumbnail'] : '';
    $post->link = $this->getPostLink($grid_link, $post);
    // $teaser_post_type = 'posts_grid_teaser_'.$query->post->post_type . ' '; // MOVE
    $posts[] = $post;
}
wp_reset_query();
/**
 * Css classes for grid and teasers.
 * {{
 */
$post_types_teasers = '';
if ( !empty($args['post_type']) && is_array($args['post_type']) ) {
    foreach ( $args['post_type'] as $post_type ) {
        $post_types_teasers .= 'wpb_teaser_grid_'.$post_type . ' ';
    }
}
$el_class = $this->getExtraClass( $el_class );
$li_span_class = $this->spanClass( $grid_columns_count );

$css_class = 'wpb_teaser_grid wpb_content_element '.
             $this->getMainCssClass($filter) . // Css class as selector for isotope plugin
             ' columns_count_'.$grid_columns_count . // Custom margin/padding for different count of columns in grid
             ' grid_layout-'.$grid_layout . // Teaser block template
             ' columns_count_'.$grid_columns_count.'_'.$grid_layout . // Combination of layout and column count
             ' '  . $grid_layout.'_'.$li_span_class . ' ' .
             ' ' . $post_types_teasers . // Css classes by selected post types
             $el_class; // Custom css class from shortcode attributes
$image_css = 'link_image'.($grid_link==='link_post' ? '' : ' prettyphoto');
$title_css = 'link_title'.($grid_link==='link_image' ? ' prettyphoto' : '');
// }}
/**
 * Get teaser block template path
 */
$template_r = VcTeaserTemplates::getInstance();
$template_path =  $template_r->getTemplatePath($grid_layout);
// }}
$this->setLinktarget($grid_link_target);
?>
<div class="<?php echo apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_class, $this->settings['base']) ?>">
    <div class="wpb_wrapper">
        <?php echo wpb_widget_title(array('title' => $title, 'extraclass' => 'wpb_teaser_grid_heading')) ?>
        <div class="teaser_grid_container">
            <?php include $this->getPartial('_filter'); ?>
            <ul class="wpb_thumbnails wpb_thumbnails-fluid clearfix" data-layout-mode="<?php echo $grid_layout_mode ?>'">
                <?php if(count($posts) > 0): ?>
                <?php
                /**
                 * Enqueue js/css
                 * {{
                 */
                wp_enqueue_style('isotope-css');
                wp_enqueue_script( 'isotope' );
                if ( $grid_link == 'link_image' || $grid_link == 'link_image_post' ) {
                    wp_enqueue_script( 'prettyphoto' );
                    wp_enqueue_style( 'prettyphoto' );
                }
                ?>
                <?php foreach($posts as $post): ?>
                <li class="isotope-item <?php echo apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $li_span_class, 'vc_teaser_grid_li').$post->categories_css ?>">
                    <?php include $template_path; ?>
                </li> <?php echo $this->endBlockComment('single teaser'); ?>
                <?php endforeach; ?>
                <?php else: ?>
                <li><?php _e("Nothing found." , "js_composer") ?></li>
                <?php endif; ?>
            </ul>
        </div>
    </div> <?php echo $this->endBlockComment('.wpb_wrapper') ?>
</div> <?php echo $this->endBlockComment('.wpb_teaser_grid') ?>