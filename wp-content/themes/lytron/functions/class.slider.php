<?php

class Wpsp_Slider
{
    private $type;
    static private $instance;

    static public function getInstance($type)
    {
        if (self::$instance == NULL) {
            $c = __CLASS__;
            self::$instance = new $c($type);
        }

        return self::$instance;
    }

    private function  __construct($type)
    {
        $this->type = $type;
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Include slider based on type
     */
    public function includes()
    {
        include_once (WPSP_INC . "/$this->type/slider.php" );
    }

    public function enqueue_scripts()
    {
        if (wpsp_is_home()) {
            wp_enqueue_script('slider', get_template_directory_uri() . "/includes/$this->type/js/$this->type.js", array(), '1.0', true);
            wp_enqueue_script('slider-settings', get_template_directory_uri() . "/includes/$this->type/js/slider-settings.js", array(), '1.0', true);

            wp_localize_script('slider-settings', 'sliderSettings', array(
                'timeout' => aki::get_option('slider_timeout'),
                'speed' => aki::get_option('slider_speed'),
                'effect' => aki::get_option('slider_effect'),
                'autoplay' => aki::get_option('slider_autoplay')
            ));
        }
    }
}