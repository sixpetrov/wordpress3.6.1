<?php
/* 
 * Filters 
 */
add_filter('widget_text', 'do_shortcode');

/* 
 * After theme setup 
 */
function neotheme_after_setup_theme()
{      
    //Theme support
    add_theme_support('post-thumbnails');
    add_post_type_support('page', 'excerpt');

    //Image sizes
    set_post_thumbnail_size(170, 145, true);
       
    //Menus
    register_nav_menus(array(
        'main_navigation' => __( 'Main Navigation', WPSP_DOMAIN )
    ));  
}

add_action('after_setup_theme', 'neotheme_after_setup_theme'); 

/* 
 * Sidebars 
 */
function neotheme_widgets_init()
{
    $sidebars = array(  
            'default_sidebar'    =>    array(
                    'name'          => 'Default Sidebar',
                    'id'            => 'sidebar-default',
                    'before_widget' => '<div class="widget widget-sidebar">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<span class="widget-title widget-title-sidebar">',
                    'after_title'   => '</span>'
            ), 
            'footer_sidebar'    =>    array(
                    'name'          => 'Sidebar Footer',
                    'id'            => 'sidebar-footer',
                    'before_widget' => '<div class="widget widget-footer">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<span class="widget-title widget-title-footer">',
                    'after_title'   => '</span>'
            ),
            'home_sidebar'    =>    array(
                'name'          => 'Home Sidebar',
                'id'            => 'sidebar-home',
                'before_widget' => '<div class="widget widget-home">',
                'after_widget'  => '</div>',
                'before_title'  => '<span class="widget-title widget-title-home">',
                'after_title'   => '</span>'
            )
    ); 
    
    foreach ($sidebars as $sidebar)
    {
        register_sidebar($sidebar);   
    }   
}

add_action('widgets_init', 'neotheme_widgets_init');


/*************************************************************
 *  Theme options
 *************************************************************/
if (WPSP_AKI_HIDE_FIELDS)
{
    function hide_aki_fields_cb()
    {
        global $pagenow;
        $screen = get_current_screen();

        if ( in_array($pagenow, array('edit.php', 'post.php')) )
        {
            if( $screen->post_type == 'aki' ):
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function(){
                        jQuery('.wrap').hide();
                    });
                </script>
            <?php
            endif;
        }
    }//end function

    add_action('admin_head', 'hide_aki_fields_cb');
}

require_once WPSP_DIR . "/admin/aki-theme-options.php";
aki::register_options_page("Global");
require_once(WPSP_DIR . "/admin/options.php");