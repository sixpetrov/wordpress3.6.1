<?php

/*
*  Settings
*
*  @description: All the functionality for aki Settings
*  @since 3.2.6
*  @created: 23/06/12
*/


class aki_settings
{

    var $parent;


    /*
    *  __construct
    *
    *  @description:
    *  @since 3.1.8
    *  @created: 23/06/12
    */

    function __construct($parent)
    {

        // vars
        $this->parent = $parent;


        // actions
        add_action('admin_menu', array($this,'admin_menu'), 11);

    }


    /*
    *  admin_menu
    *
    *  @description:
    *  @since 3.1.8
    *  @created: 23/06/12
    */

    function admin_menu()
    {
        $page = add_submenu_page('edit.php?post_type=aki', __('Settings','aki'), __('Settings','aki'), 'manage_options','aki-settings',array($this,'html'));

        add_action('load-' . $page, array($this,'load'));

        add_action('admin_print_scripts-' . $page, array($this, 'admin_print_scripts'));
        add_action('admin_print_styles-' . $page, array($this, 'admin_print_styles'));

        add_action('admin_head-' . $page, array($this,'admin_head'));

    }


    /*
    *  load
    *
    *  @description:
    *  @since 3.5.2
    *  @created: 16/11/12
    *  @thanks: Kevin Biloski and Charlie Eriksen via Secunia SVCRP
    */

    function load()
    {
        // vars
        $defaults = array(
            'action' => ''
        );
        $options = array_merge($defaults, $_POST);


        if( $options['action'] == "export_xml" )
        {
            include_once($this->parent->path . 'core/actions/export.php');
            die;
        }
    }


    /*
    *  admin_print_scripts
    *
    *  @description:
    *  @since 3.1.8
    *  @created: 23/06/12
    */

    function admin_print_scripts()
    {
        wp_enqueue_script( 'wp-pointer' );
    }


    /*
    *  admin_print_styles
    *
    *  @description:
    *  @since 3.1.8
    *  @created: 23/06/12
    */

    function admin_print_styles()
    {
        wp_enqueue_style(array(
            'wp-pointer',
            'aki-global',
            'aki',
        ));
    }


    /*
    *  admin_head
    *
    *  @description:
    *  @since 3.1.8
    *  @created: 23/06/12
    */

    function admin_head()
    {

        // Activate / Deactivate Add-ons
        if( isset($_POST['aki_field_deactivate']) )
        {
            // vars
            $message = "";
            $field = $_POST['aki_field_deactivate'];

            // delete field
            delete_option('aki_'.$field.'_ac');

            //set message
            if($field == "repeater")
            {
                $message = '<p>' . __("Repeater field deactivated",'aki') . '</p>';
            }
            elseif($field == "options_page")
            {
                $message = '<p>' . __("Options page deactivated",'aki') . '</p>';
            }
            elseif($field == "flexible_content")
            {
                $message = '<p>' . __("Flexible Content field deactivated",'aki') . '</p>';
            }
            elseif($field == "gallery")
            {
                $message = '<p>' . __("Gallery field deactivated",'aki') . '</p>';
            }

            // show message on page
            $this->parent->admin_message($message);
        }


        if( isset($_POST['aki_field_activate']) && isset($_POST['key']) )
        {
            // vars
            $message = "";
            $field = $_POST['aki_field_activate'];
            $key = trim($_POST['key']);

            // update option
            update_option('aki_'.$field.'_ac', $key);

            // did it unlock?
            if($this->parent->is_field_unlocked($field))
            {
                //set message
                if($field == "repeater")
                {
                    $message = '<p>' . __("Repeater field activated",'aki') . '</p>';
                }
                elseif($field == "options_page")
                {
                    $message = '<p>' . __("Options page activated",'aki') . '</p>';
                }
                elseif($field == "flexible_content")
                {
                    $message = '<p>' . __("Flexible Content field activated",'aki') . '</p>';
                }
                elseif($field == "gallery")
                {
                    $message = '<p>' . __("Gallery field activated",'aki') . '</p>';
                }
            }
            else
            {
                $message = '<p>' . __("License key unrecognised",'aki') . '</p>';
            }

            $this->parent->admin_message($message);
        }
    }


    /*
    *  html_index
    *
    *  @description:
    *  @created: 9/08/12
    */

    function html_index()
    {
        // vars
        $akis = get_posts(array(
            'numberposts' 	=> -1,
            'post_type' 	=> 'aki',
            'orderby' 		=> 'menu_order title',
            'order' 		=> 'asc',
        ));

        // blank array to hold akis
        $choices = array();

        if($akis)
        {
            foreach($akis as $aki)
            {
                // find title. Could use get_the_title, but that uses get_post(), so I think this uses less Memory
                $title = apply_filters( 'the_title', $aki->post_title, $aki->ID );

                $choices[$aki->ID] = $title;
            }
        }

        ?>
        <table class="form-table aki-form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <h3><?php _e("Export Field Groups to XML",'aki'); ?></h3>
                    <p><?php _e("aki will create a .xml export file which is compatible with the native WP import plugin.",'aki'); ?></p>
                    <p><a href="#" class="show-pointer" rel="xml-import-instructions-html"><?php _e("Instructions",'aki'); ?></a></p>
                    <div id="xml-import-instructions-html" style="display:none;">
                        <h3><?php _e("Import Field Groups",'aki'); ?></h3>
                        <p><?php _e("Imported field groups <b>will</b> appear in the list of editable field groups. This is useful for migrating fields groups between Wp websites.",'aki'); ?></p>
                        <ol>
                            <li><?php _e("Select field group(s) from the list and click \"Export XML\"",'aki'); ?></li>
                            <li><?php _e("Save the .xml file when prompted",'aki'); ?></li>
                            <li><?php _e("Navigate to Tools &raquo; Import and select WordPress",'aki'); ?></li>
                            <li><?php _e("Install WP import plugin if prompted",'aki'); ?></li>
                            <li><?php _e("Upload and import your exported .xml file",'aki'); ?></li>
                            <li><?php _e("Select your user and ignore Import Attachments",'aki'); ?></li>
                            <li><?php _e("That's it! Happy WordPressing",'aki'); ?></li>
                        </ol>
                    </div>
                </th>
                <td>
                    <form class="aki-export-form" method="post">
                        <input type="hidden" name="action" value="export_xml" />
                        <?php

                        do_action('aki/create_field', array(
                            'type'	=>	'select',
                            'name'	=>	'aki_posts',
                            'value'	=>	'',
                            'choices'	=>	$choices,
                            'multiple'	=>	1,
                        ));

                        ?>
                        <ul class="hl clearfix">
                            <li class="right"><input type="submit" class="aki-button" value="<?php _e("Export XML",'aki'); ?>" /></li>
                        </ul>
                    </form>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <h3><?php _e("Export Field Groups to PHP",'aki'); ?></h3>
                    <p><?php _e("aki will create the PHP code to include in your theme.",'aki'); ?></p>
                    <p><a href="#" class="show-pointer" rel="php-import-instructions-html"><?php _e("Instructions",'aki'); ?></a></p>
                    <div id="php-import-instructions-html" style="display:none;">
                        <h3><?php _e("Register Field Groups",'aki'); ?></h3>
                        <p><?php _e("Registered field groups <b>will not</b> appear in the list of editable field groups. This is useful for including fields in themes.",'aki'); ?></p>
                        <p><?php _e("Please note that if you export and register field groups within the same WP, you will see duplicate fields on your edit screens. To fix this, please move the origional field group to the trash or remove the code from your functions.php file.",'aki'); ?></p>
                        <ol>
                            <li><?php _e("Select field group(s) from the list and click \"Create PHP\"",'aki'); ?></li>
                            <li><?php _e("Copy the PHP code generated",'aki'); ?></li>
                            <li><?php _e("Paste into your functions.php file",'aki'); ?></li>
                            <li><?php _e("To activate any Add-ons, edit and use the code in the first few lines.",'aki'); ?></li>
                        </ol>
                    </div>
                </th>
                <td>
                    <form class="aki-export-form" method="post">
                        <input type="hidden" name="action" value="export_php" />
                        <?php

                        do_action('aki/create_field', array(
                            'type'	=>	'select',
                            'name'	=>	'aki_posts',
                            'value'	=>	'',
                            'choices'	=>	$choices,
                            'multiple'	=>	1,
                        ));

                        ?>
                        <ul class="hl clearfix">
                            <li class="right"><input type="submit" class="aki-button" value="<?php esc_attr_e("Create PHP",'aki'); ?>" /></li>
                        </ul>
                    </form>
                </td>
            </tr>
            </tbody>
        </table>
        <script type="text/javascript">
            (function($){

                $(document).ready(function(){

                    $('a.show-pointer').each(function(){

                        // vars
                        var a = $(this),
                            html = a.attr('rel');


                        // create pointer
                        a.pointer({
                            content: $('#' + html).html(),
                            position: {
                                my: 'left bottom',
                                at: 'left top',
                                edge: 'bottom'
                            },
                            close: function() {

                                a.removeClass('open');

                            }
                        });


                        // click
                        a.click(function(){

                            if( a.hasClass('open') )
                            {
                                a.removeClass('open');
                            }
                            else
                            {
                                a.addClass('open');
                            }

                        });


                        // show on hover
                        a.hover(function(){

                            $(this).pointer('open');

                        }, function(){

                            if( ! a.hasClass('open') )
                            {
                                $(this).pointer('close');
                            }

                        });

                    });

                });

            })(jQuery);
        </script>
    <?php

    }


    /*
    *  html_php
    *
    *  @description:
    *  @created: 9/08/12
    */

    function html_php()
    {

        ?>
        <p><a href="">&laquo; <?php _e("Back to settings",'aki'); ?></a></p>
        <table class="form-table aki-form-table">
            <tbody>
            <tr>
                <th scope="row">
                    <h3><?php _e("Register Field Groups",'aki'); ?></h3>
                    <p><?php _e("Registered field groups <b>will not</b> appear in the list of editable field groups. This is useful for including fields in themes.",'aki'); ?></p>
                    <p><?php _e("Please note that if you export and register field groups within the same WP, you will see duplicate fields on your edit screens. To fix this, please move the origional field group to the trash or remove the code from your functions.php file.",'aki'); ?></p>
                    <ol>
                        <li><?php _e("Copy the PHP code generated",'aki'); ?></li>
                        <li><?php _e("Paste into your functions.php file",'aki'); ?></li>
                        <li><?php _e("To activate any Add-ons, edit and use the code in the first few lines.",'aki'); ?></li>
                    </ol>
                </th>
                <td valign="top">
                    <div class="wp-box">
                        <div class="inner">
                            <textarea class="pre" readonly="true"><?php

                                $akis = array();

                                if(isset($_POST['aki_posts']))
                                {
                                    $akis = get_posts(array(
                                        'numberposts' 	=> -1,
                                        'post_type' 	=> 'aki',
                                        'orderby' 		=> 'menu_order title',
                                        'order' 		=> 'asc',
                                        'include'		=>	$_POST['aki_posts'],
                                    ));
                                }
                                if($akis)
                                {
                                    ?>

                                    <?php
                                    foreach($akis as $aki)
                                    {
                                        $var = array(
                                            'source' => 'file',
                                            'id' => uniqid(),
                                            'title' => get_the_title($aki->ID),
                                            'fields' => $this->parent->get_aki_fields($aki->ID),
                                            'location' => $this->parent->get_aki_location($aki->ID),
                                            'options' => $this->parent->get_aki_options($aki->ID),
                                            'menu_order' => $aki->menu_order,
                                        );

                                        $html = var_export($var, true);

                                        // change double spaces to tabs
                                        $html = str_replace("  ", "\t", $html);

                                        // add extra tab at start of each line
                                        $html = str_replace("\n", "\n\t", $html);

                                        ?>
                                        aki::register_field_group(<?php echo $html ?>);
                                    <?php
                                    }
                                    ?>

                                <?php
                                }
                                else
                                {
                                    _e("No field groups were selected",'aki');
                                }
                                ?></textarea>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <script type="text/javascript">
            (function($){

                var i = 0;

                $('textarea.pre').live( 'mousedown', function (){

                    if( i == 0 )
                    {
                        i++;

                        $(this).focus().select();

                        return false;
                    }

                });


                $('textarea.pre').live( 'keyup', function (){
                    $(this).height( 0 );
                    $(this).height( this.scrollHeight );
                });


                $(document).ready(function(){

                    $('textarea.pre').trigger('keyup');

                });

            })(jQuery);
        </script>
    <?php
    }


    /*
    *  html
    *
    *  @description:
    *  @since 3.1.8
    *  @created: 23/06/12
    */

    function html()
    {
        // vars
        $defaults = array(
            'action' => ''
        );
        $options = array_merge($defaults, $_POST);

        ?>
        <div class="wrap">

            <div class="icon32" id="icon-aki"><br></div>
            <h2 style="margin: 4px 0 25px;"><?php _e("AKI Theme Options Settings",'aki'); ?></h2>
            <?php

            if( $options['action'] == "export_php" )
            {
                $this->html_php();
            }
            else
            {
                $this->html_index();
            }

            ?>
        </div>
        <?php

        return;

    }
}

?>