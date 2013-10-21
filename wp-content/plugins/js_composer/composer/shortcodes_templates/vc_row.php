<?php
$output = $el_class = $pre_css = $row_layout = '';
extract(shortcode_atts(array(
    'el_class' => '',
    'pre_css' => '',
    'row_layout' => ''
), $atts));

wp_enqueue_style( 'js_composer_front' );
wp_enqueue_script( 'wpb_composer_front_js' );
wp_enqueue_style('js_composer_custom_css');

$el_class = $this->getExtraClass($el_class);
$css_class =  apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_row '.get_row_css_class().$el_class, $this->settings['base']);
$pre_css = str_replace(",", " ", $pre_css);

$output .= "\n\t".'<div class="' .$css_class . ' ' . $pre_css . ' ' . $row_layout . '">';
$output .= wpb_js_remove_wpautop($content);
$output .= '</div>'.$this->endBlockComment('row');

echo $output;