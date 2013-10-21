<?php

class WPSP_Jcarousel
{
    private static $instance;

    /**
     * Singleton
     *
     * @return mixed
     */
    static public function getInstance()
    {
        if (self::$instance == NULL) {
            $c = __CLASS__;
            self::$instance = new $c();
        }

        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        add_action( 'wp_enqueue_scripts', array($this, 'scripts') );
    }

    /**
     * Load scripts
     */
    public function scripts()
    {
        if (wpsp_is_home()) {
            wp_enqueue_script('jCarousel', WPSP_URI . '/includes/jcarousel/jcarousel.js', array(), false, true);
        }
    }

    /**
     * Include crs file
     *
     * @param $template (string), name of php template file located in /includes/jcarousel/...
     */
    public function display($template)
    {
        include_once(WPSP_DIR . "/includes/jcarousel/$template.php");
    }
}