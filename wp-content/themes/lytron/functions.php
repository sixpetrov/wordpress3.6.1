<?php

/* Theme Constants */
if (! defined('WPSP_DIR')) define('WPSP_DIR', get_template_directory());
if (! defined('WPSP_URI')) define('WPSP_URI', get_template_directory_uri());
if (! defined('WPSP_INC')) define('WPSP_INC', get_template_directory() . "/includes");
if (! defined('WPSP_JS')) define('WPSP_JS', get_template_directory_uri() . "/js");
if (! defined('WPSP_DOMAIN')) define('WPSP_DOMAIN', 'wpsp_text_domain');

if (! defined('WPSP_AKI_FROM_FILE')) define('WPSP_AKI_FROM_FILE', false);
if (! defined('WPSP_AKI_HIDE_FIELDS')) define('WPSP_AKI_HIDE_FIELDS', false);

/* load functions */
require_once(WPSP_DIR . "/functions/init.php");
require_once(WPSP_DIR . "/functions/functions-wp.php");
require_once(WPSP_DIR . "/functions/functions-custom.php");
//require_once(WPSP_DIR . "/functions/editor.php");

//slider
require_once(WPSP_DIR . "/functions/class.slider.php");
$wpspSlider = Wpsp_Slider::getInstance("camera");

//carousel
//include_once(WPSP_DIR . "/functions/class.jcarousel.php");
//$wpspCrs = WPSP_Jcarousel::getInstance();

//include_once (WPSP_INC . '/vc_extend/vc_carousel.php');

/*=====================================================================================================================*/

/*
 * Enqueue scripts and styles
 */
function wpsp_scripts_styles()
{
    global $wp_styles;

    /* Loads main stylesheet. */
    wp_enqueue_style('main-style', WPSP_URI . '/css/screen.css', array(), '0.1', 'screen');

    /* Loads the Internet Explorer specific stylesheet. */
    wp_enqueue_style( 'wpsp-ie', get_template_directory_uri() . '/css/ie.css', array( 'main-style' ), '0.1' );
    $wp_styles->add_data( 'wpsp-ie', 'conditional', 'lt IE 9' );

    /* standard scripts */
    wp_enqueue_script('jquery');
    wp_enqueue_script('hoverIntent', WPSP_JS . '/hover-intent.js', array(), '1.0', true);
    wp_enqueue_script('wpspDropdown', WPSP_JS . '/dropdown.js', array(), '1.0', true);

    //comments reply
    if (is_single() && get_option('thread_comments')){
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'wpsp_scripts_styles' );

//---------------------------------------- HEADER -----------------------------------------------
function headerSearch_cb()
{
?>
    <div id="headerSearch">
        <form method="get" action="<?php echo home_url('/'); ?>" >
            <input class="headerSearch_input" type="text" name="s" placeholder="" />
            <input class="submit-button sprites search-dark" type="submit" value="" />
        </form>
    </div>
<?php
}

function headerIcons_cb()
{
?>
    <div id="headerIcons">
        <ul>
            <li><a class="sprites icon-twitter-header" href="<?php echo aki::get_option('twitter_link') ?>">Twitter</a></li>
            <li><a class="sprites icon-facebook-header" href="<?php echo aki::get_option('facebook_link') ?>">Facebook</a></li>
            <li><span><?php echo aki::get_option('phone_number') ?></span></li>
        </ul>
    </div>
<?php
}

function phone_cb()
{
    ?>
    <ul id="headerPhone">
        <li><span><?php echo aki::get_option('phone_number') ?></span></li>
    </ul>
<?php
}

// header_right
// navbar
add_action('header_right', 'headerSearch_cb');
add_action('header_right', 'headerIcons_cb');
//add_action('navbar', 'phone_cb');

//---------------------------------------- FOOTER -----------------------------------------------
function footer_copyrights_cb($standalone = FALSE)
{
    if ($standalone) {
        echo '<div class="wrapper" id="WrapperFooterCopyright">';
        echo '<div class="container">';
    }
?>
    <footer id="footer-main">
        <!-- copyrights -->
        <p id="footer-copyright">
            Copyright &copy; 2012 &middot; <span title="<?php bloginfo('name') ?>"><?php echo wpsp_strip_title(100, get_bloginfo('name')) ?></span> &middot;
            Developed by <a href="http://www.lytrondesign.com/web-agency-digital/" target="_blank">Lytron Marketing Agency</a>
        </p>

        <!-- search -->
        <div id="footer-search">
            <form method="get" action="<?php echo home_url('/'); ?>" >
                <input id="footer-search-inputbox" type="text" name="s" placeholder="" />
                <input id="footer-search-button" type="submit" class="sprites search-dark submit-button" />
            </form>
        </div>
    </footer>
<?php
    if ($standalone) {
        echo '</div></div>';
    }
}

//footer_main
add_action('footer_sidebar', 'footer_copyrights_cb');