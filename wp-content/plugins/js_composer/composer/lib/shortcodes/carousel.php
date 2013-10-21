<?php

class WPBakeryShortCode_Vc_Carousel extends WPBakeryShortCode_VC_Posts_Grid {
    public function __construct($settings) {
        parent::__construct($settings);
        $this->addAction('wp_enqueue_scripts', 'jsCssScripts');
    }

    public function jsCssScripts() {
        wp_register_script('vc_bxslider', WPBakeryVisualComposer::getInstance()->assetURL('lib/bxslider-4/jquery.bxslider.min.js'));
        wp_register_style('vc_bxslider_css', WPBakeryVisualComposer::getInstance()->assetURL('lib/bxslider-4/jquery.bxslider.css'));
    }

}