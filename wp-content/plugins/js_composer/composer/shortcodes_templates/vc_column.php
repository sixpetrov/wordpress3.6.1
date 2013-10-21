<?php
$output = $el_class = $width = $pre_css = '';
extract(shortcode_atts(array(
    'el_class' => '',
    'width' => '1/1',
    'pre_css' => ''
), $atts));

$el_class = $this->getExtraClass($el_class);
$width = wpb_translateColumnWidthToSpan($width);
$el_class .= ' wpb_column column_container';
$pre_css = str_replace(",", " ", $pre_css);

$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $width.$el_class, $this->settings['base']);
$output .= "\n\t".'<div class="' .$css_class . ' ' . $pre_css . '">';
$output .= "\n\t\t".'<div class="wpb_wrapper">';
$output .= "\n\t\t\t".wpb_js_remove_wpautop($content);
$output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
$output .= "\n\t".'</div> '.$this->endBlockComment($el_class) . "\n";

echo $output;