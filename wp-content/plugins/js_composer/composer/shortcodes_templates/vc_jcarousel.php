<?php
$output = $onclick = $custom_links = $img_size = $custom_links_target = $images = $el_class = $right_arrow = $left_arrow = $images_visible = '';
$crs_timeout = "";

extract( shortcode_atts( array(
    'images' => '',
    'onclick' => 'link_image',
    'custom_links' => '',
    'custom_links_target' => '',
    'img_size' => 'thumbnail',
    'el_class' => '',
    'left_arrow' => '',
    'right_arrow' => '',
    'images_visible' => '5',
    'crs_timeout' => 3000
), $atts ) );

if (empty($images)) {
    return;
}

//jcarousel script
wp_enqueue_script( 'jcarousellite' );

//elements
$el_class = $this->getExtraClass($el_class);
$el_start = "<li>";
$el_end = "</li>";
$crs_timeout = trim($crs_timeout);

if ( $onclick == 'custom_link' ) {
    $custom_links = explode( ',', $custom_links);
}

$images = explode( ',', $images);
$pretty_rel_random = ' rel="prettyPhoto"';

//images
$i = -1;
foreach ($images as $attach_id)
{
    $i++;
    $post_thumbnail = wpb_getImageBySize(array( 'attach_id' => $attach_id, 'thumb_size' => $img_size ));
    $thumbnail = $post_thumbnail['thumbnail'];
    $p_img_large = $post_thumbnail['p_img_large'];

    if ( $onclick == 'link_image' ) {
        $link_start = '<a class="prettyphoto" href="'.$p_img_large[0].'"'.$pretty_rel_random.'>';
        $link_end = '</a>';
    }
    else if ( $onclick == 'custom_link' && isset( $custom_links[$i] ) && $custom_links[$i] != '' ) {
        $link_start = '<a href="'.$custom_links[$i].'"' . (!empty($custom_links_target) ? ' target="'.$custom_links_target.'"' : '') . '>';
        $link_end = '</a>';
    }
    $gal_images .= $el_start . $link_start . $thumbnail . $link_end . $el_end;
}

//arrows
$left_arrow_img = wp_get_attachment_image_src($left_arrow, 'full');
$right_arrow_img = wp_get_attachment_image_src($right_arrow, 'full');

//output
$div_css_id = "crs_" . rand();
$left_arrow_id = "arrowLeft_" . rand();
$right_arrow_id = "arrowRight_" . rand();

$css_class =  apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'vc_jcarousel wpb_content_element'.$el_class, $this->settings['base']);
$output .= '<div class="'.$css_class.'">';
$output .= '<div class="wpb_wrapper">';
$output .= "<a href='#' id='". $left_arrow_id ."' class='crsArrow crsArrowLeft'><img src='". $left_arrow_img[0] . "' /></a>";
$output .= "<div id='". $div_css_id ."' class='vc_jcarousel_content'>";
$output .= "<ul>". $gal_images ."</ul>";
$output .= "</div>";
$output .= "<a href='#' id='". $right_arrow_id ."' class='crsArrow crsArrowRight'><img src='". $right_arrow_img[0] . "' /></a>";
$output .= "</div>";
$output .= <<<EOD
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#$div_css_id').jCarouselLite({
        auto: $crs_timeout,
        speed: 800,
        visible: $images_visible,
        btnPrev: '#$left_arrow_id',
        btnNext: '#$right_arrow_id'
    });
});
</script>
EOD;
$output .= "</div>"; //end vc_jcarousel

echo $output;