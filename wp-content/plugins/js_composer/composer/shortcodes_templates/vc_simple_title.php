<?php
$output = $title = $title_align = $el_class = $title_html_tag = '';
extract(shortcode_atts(array(
    'title' => __("Title", "js_composer"),
    'title_align' => 'separator_align_center',
    'el_class' => '',
    'title_html_tag' => "p"
), $atts));

$el_class = $this->getExtraClass($el_class);

$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'vc_simple_title wpb_content_element '.$title_align.$el_class, $this->settings['base']);
$output .= '<div class="' . $css_class . '">';
$output .= "<" . $title_html_tag . ">" . $title . "</" . $title_html_tag .">";
$output .= "</div>";

echo $output;