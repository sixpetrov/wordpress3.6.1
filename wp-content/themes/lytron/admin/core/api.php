<?php

// vars
$GLOBALS['aki_field'] = array();
$GLOBALS['aki_register_field'] = array();
$GLOBALS['aki_register_field_group'] = array();
$GLOBALS['aki_options_pages'] = array();

class aki
{
    /**
     * @param bool $post_id
     * @return array|bool
     */
    public static function get_fields($post_id = false)
    {
        // vars
        global $post, $wpdb;

        if(!$post_id)
        {
            $post_id = $post->ID;
        }


        // allow for option == options
        if( $post_id == "option" )
        {
            $post_id = "options";
        }


        // vars
        $field_key = "";
        $value = array();


        // get field_names
        if( is_numeric($post_id) )
        {
            $keys = $wpdb->get_col($wpdb->prepare(
                "SELECT meta_key FROM $wpdb->postmeta WHERE post_id = %d and meta_key NOT LIKE %s",
                $post_id,
                '\_%'
            ));
        }
        elseif( strpos($post_id, 'user_') !== false )
        {
            $user_id = str_replace('user_', '', $post_id);

            $keys = $wpdb->get_col($wpdb->prepare(
                "SELECT meta_key FROM $wpdb->usermeta WHERE user_id = %d and meta_key NOT LIKE %s",
                $user_id,
                '\_%'
            ));
        }
        else
        {
            $keys = $wpdb->get_col($wpdb->prepare(
                "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
                $post_id . '\_%'
            ));
        }


        if($keys)
        {
            foreach($keys as $key)
            {
                $value[$key] = self::get_field($key, $post_id);
            }
        }


        // no value
        if(empty($value))
        {
            return false;
        }

        return $value;

    }

    /**
     * @param $field_key
     * @param bool $post_id
     * @param bool $format_value
     * @return bool|mixed
     */
    public static function get_field($field_key, $post_id = false, $format_value = true)
    {
        global $post, $aki_theme_options;

        if(!$post_id)
        {
            $post_id = $post->ID;
        }

        // allow for option == options
        if( $post_id == "option" )
        {
            $post_id = "options";
        }


        // return cache
        $cache = wp_cache_get('aki_get_field_' . $post_id . '_' . $field_key);
        if($cache)
        {
            return $cache;
        }

        // default
        $value = "";
        $field = array(
            'type'	=>	'text',
            'name'	=>	$field_key
        );


        if( $format_value )
        {
            // is $field_name a name? pre 3.4.0
            if( strpos($field_key, "field_") === false )
            {
                // get field key
                if( is_numeric($post_id) )
                {
                    $field_key = get_post_meta($post_id, '_' . $field_key, true);
                }
                elseif( strpos($post_id, 'user_') !== false )
                {
                    $temp_post_id = str_replace('user_', '', $post_id);
                    $field_key = get_user_meta($temp_post_id, '_' . $field_key, true);
                }
                else
                {
                    $field_key = get_option('_' . $post_id . '_' . $field_key);
                }
            }

            // get field
            if( strpos($field_key, "field_") !== false )
            {
                $field = $aki_theme_options->get_aki_field($field_key);
            }
        }
        else
        {
            $field = array(
                'type' => 'none',
                'name' => $field_key
            );
        }

        // load value
        $value = $aki_theme_options->get_value_for_api($post_id, $field);

        // no value?
        if( $value == "" )
        {
            $value = false;
        }

        // update cache
        wp_cache_set('aki_get_field_' . $post_id . '_' . $field_key, $value);

        return $value;
    }

    /**
     * Get theme option from database
     *
     * @param $field_key
     * @param bool $format_value
     * @return bool|mixed
     */
    public static function get_option($field_key, $format_value = true)
    {
        global $aki_theme_options;

        $post_id = "options";

        // return cache
        $cache = wp_cache_get('aki_get_field_' . $post_id . '_' . $field_key);
        if($cache)
        {
            return $cache;
        }

        // default
        $value = "";
        $field = array(
            'type'	=>	'text',
            'name'	=>	$field_key
        );


        if( $format_value )
        {
            // is $field_name a name? pre 3.4.0
            if( strpos($field_key, "field_") === false )
            {
                // get field key
                if( is_numeric($post_id) )
                {
                    $field_key = get_post_meta($post_id, '_' . $field_key, true);
                }
                elseif( strpos($post_id, 'user_') !== false )
                {
                    $temp_post_id = str_replace('user_', '', $post_id);
                    $field_key = get_user_meta($temp_post_id, '_' . $field_key, true);
                }
                else
                {
                    $field_key = get_option('_' . $post_id . '_' . $field_key);
                }
            }

            // get field
            if( strpos($field_key, "field_") !== false )
            {
                $field = $aki_theme_options->get_aki_field($field_key);
            }
        }
        else
        {
            $field = array(
                'type' => 'none',
                'name' => $field_key
            );
        }

        // load value
        $value = $aki_theme_options->get_value_for_api($post_id, $field);

        // no value?
        if( $value == "" )
        {
            $value = false;
        }

        // update cache
        wp_cache_set('aki_get_field_' . $post_id . '_' . $field_key, $value);

        return $value;
    }

    /**
     * @param $field_name
     * @param bool $post_id
     */
    public static function the_field($field_name, $post_id = false)
    {
        $value = self::get_field($field_name, $post_id);

        if(is_array($value))
        {
            $value = @implode(', ',$value);
        }

        echo $value;
    }

    /**
     * @param $field_name
     * @param bool $post_id
     */
    public static function the_option($field_name)
    {
        $value = self::get_option($field_name);

        if(is_array($value))
        {
            $value = @implode(', ',$value);
        }

        echo $value;
    }

    /**
     * @param $field_name
     * @param bool $post_id
     * @return bool
     */
    public static function has_sub_field($field_name, $post_id = false)
    {
        // needs a post_id
        global $post;

        if( !$post_id )
        {
            $post_id = $post->ID;
        }

        // empty?
        if( empty($GLOBALS['aki_field']) )
        {
            $GLOBALS['aki_field'][] = array(
                'name'	=>	$field_name,
                'value'	=>	self::get_field($field_name, $post_id),
                'row'	=>	-1,
                'post_id' => $post_id,
            );
        }


        // vars
        $depth = count( $GLOBALS['aki_field'] ) - 1;
        $name = $GLOBALS['aki_field'][$depth]['name'];
        $value = $GLOBALS['aki_field'][$depth]['value'];
        $row = $GLOBALS['aki_field'][$depth]['row'];
        $id = $GLOBALS['aki_field'][$depth]['post_id'];


        // if ID has changed, this is a new repeater / flexible field!
        if( $post_id != $id )
        {
            // reset
            $GLOBALS['aki_field'] = array();
            return self::has_sub_field($field_name, $post_id);
        }


        // does the given $field_name match the current field?
        if( $field_name != $name )
        {
            // is this a "new" while loop refering to a sub field?
            if( isset($value[$row][$field_name]) )
            {
                $GLOBALS['aki_field'][] = array(
                    'name'	=>	$field_name,
                    'value'	=>	$value[$row][$field_name],
                    'row'	=>	-1,
                    'post_id' => $post_id,
                );
            }
            elseif( isset($GLOBALS['aki_field'][$depth-1]) && $GLOBALS['aki_field'][$depth-1]['name'] == $field_name )
            {
                // if someone used break; We should see if the parent value has this field_name as a value.
                unset( $GLOBALS['aki_field'][$depth] );
                $GLOBALS['aki_field'] = array_values($GLOBALS['aki_field']);
            }
            else
            {
                // this was a break; (probably to get the first row only). Clear the repeater
                $GLOBALS['aki_field'] = array();
                return self::has_sub_field($field_name, $post_id);
            }

        }


        // update vars
        $depth = count( $GLOBALS['aki_field'] ) - 1;
        $value = $GLOBALS['aki_field'][$depth]['value'];
        $row = $GLOBALS['aki_field'][$depth]['row'];


        // increase row number
        $GLOBALS['aki_field'][$depth]['row']++;
        $row++;


        if( isset($value[$row]) )
        {
            // next row exists
            return true;
        }


        // no next row! Unset this array and return false to stop while loop
        unset( $GLOBALS['aki_field'][$depth] );
        $GLOBALS['aki_field'] = array_values($GLOBALS['aki_field']);

        return false;
    }

    /**
     * @param $field_name
     * @return bool
     */
    public static function get_sub_field($field_name)
    {
        // no field?
        if( empty($GLOBALS['aki_field']) )
        {
            return false;
        }

        // vars
        $depth = count( $GLOBALS['aki_field'] ) - 1;
        $value = $GLOBALS['aki_field'][$depth]['value'];
        $row = $GLOBALS['aki_field'][$depth]['row'];

        // no value at i
        if( !isset($GLOBALS['aki_field'][$depth]['value'][$row][$field_name]) )
        {
            return false;
        }

        return $GLOBALS['aki_field'][$depth]['value'][$row][$field_name];
    }

    /**
     * @param $field_name
     */
    public static function the_sub_field($field_name)
    {
        $value = self::get_sub_field($field_name);

        if(is_array($value))
        {
            $value = implode(', ',$value);
        }

        echo $value;
    }

    /**
     * @param string $class
     * @param string $url
     */
    public static function register_field($class = "", $url = "")
    {
        $GLOBALS['aki_register_field'][] =  array(
            'url'	=> $url,
            'class'	=>	$class,
        );
    }

    /**
     * @param $array
     */
    public static function register_field_group($array)
    {
        // add id
        if(!isset($array['id']))
        {
            $array['id'] = uniqid();
        }


        // 3.2.5 - changed show_on_page option
        if( !isset($array['options']['hide_on_screen']) && isset($array['options']['show_on_page']) )
        {
            $show_all = array('the_content', 'discussion', 'custom_fields', 'comments', 'slug', 'author');
            $array['options']['hide_on_screen'] = array_diff($show_all, $array['options']['show_on_page']);
            unset( $array['options']['show_on_page'] );
        }


        $GLOBALS['aki_register_field_group'][] = $array;
    }

    /**
     * @param string $title
     */
    public static function register_options_page( $title = "" )
    {
        $GLOBALS['aki_options_pages'][] = $title;
    }

    /**
     * @return mixed
     */
    public static function get_row_layout()
    {
        // vars
        $value = self::get_sub_field('aki_fc_layout');
        return $value;
    }

    /**
     * @param $field_key
     * @param $value
     * @param bool $post_id
     * @return bool
     */
    public static function update_field($field_key, $value, $post_id = false)
    {
        global $post, $aki_theme_options;

        if(!$post_id)
        {
            $post_id = $post->ID;
        }


        // allow for option == options
        if( $post_id == "option" )
        {
            $post_id = "options";
        }

        // is $field_name a name? pre 3.4.0
        if( strpos($field_key, "field_") === false )
        {
            // get field key
            if( is_numeric($post_id) )
            {
                $field_key = get_post_meta($post_id, '_' . $field_key, true);
            }
            elseif( strpos($post_id, 'user_') !== false )
            {
                $temp_post_id = str_replace('user_', '', $post_id);
                $field_key = get_user_meta($temp_post_id, '_' . $field_key, true);
            }
            else
            {
                $field_key = get_option('_' . $post_id . '_' . $field_key);
            }
        }

        // get field
        $field = $aki_theme_options->get_aki_field($field_key);

        // backup if no field was found, save as a text field
        if( !$field )
        {
            $field = array(
                'type' => 'none',
                'name' => $field_key
            );
        }

        // sub fields? They need formatted data
        if( $field['type'] == 'repeater' )
        {
            $value = self::aki_convert_field_names_to_keys( $value, $field );
        }
        elseif( $field['type'] == 'flexible_content' )
        {
            if( $field['layouts'] )
            {
                foreach( $field['layouts'] as $layout )
                {
                    $value = self::aki_convert_field_names_to_keys( $value, $layout );
                }
            }
        }

        // save
        $aki_theme_options->update_value($post_id, $field, $value);

        return true;
    }

    /**
     * @description: Helper for the update_field function
     * @param $value
     * @param $field
     * @return mixed
     */
    private static function aki_convert_field_names_to_keys( $value, $field )
    {
        // only if $field has sub fields
        if( !isset($field['sub_fields']) )
        {
            return $value;
        }

        // define sub field keys
        $sub_fields = array();
        if( $field['sub_fields'] )
        {
            foreach( $field['sub_fields'] as $sub_field )
            {
                $sub_fields[ $sub_field['name'] ] = $sub_field;
            }
        }

        // loop through the values and format the array to use sub field keys
        if( $value )
        {
            foreach( $value as $row_i => $row)
            {
                if( $row )
                {
                    foreach( $row as $sub_field_name => $sub_field_value )
                    {
                        // sub field must exist!
                        if( !isset($sub_fields[ $sub_field_name ]) )
                        {
                            continue;
                        }

                        // vars
                        $sub_field = $sub_fields[ $sub_field_name ];
                        $sub_field_value = self::aki_convert_field_names_to_keys( $sub_field_value, $sub_field );

                        // set new value
                        $value[$row_i][ $sub_field['key'] ] = $sub_field_value;


                        // unset old value
                        unset( $value[$row_i][$sub_field_name] );


                    }
                    // foreach( $row as $sub_field_name => $sub_field_value )
                }
                // if( $row )
            }
            // foreach( $value as $row_i => $row)
        }
        // if( $value )

        return $value;
    }

    /*--------------------------------------------------------------------------------------
    *
    *	get_field_object
    *
    *	@description: returns an array containing all the field data for a given field_name.
    *	@created: 3/09/12
    *	@author Elliot Condon
    *	@since 3.4.0
    *
    *	@return: Array in this format

    Array
    (
        [key] => field_5043fe0e3c58f
        [label] => Select
        [name] => select
        [type] => select
        [instructions] =>
        [required] => 1
        [choices] => Array
            (
                [yes] => Yes
                [no] => No
                [maybe] => Maybe
            )

        [default_value] =>
        [allow_null] => 1
        [multiple] => 1
        [order_no] => 4
        [value] => Array
            (
                [0] => Yes
                [1] => Maybe
            )

    )

    *-------------------------------------------------------------------------------------*/
    public static function get_field_object($field_key, $post_id = false, $options = array())
    {
        // defaults for options
        $defaults = array(
            'load_value'	=>	true,
        );

        $options = array_merge($defaults, $options);


        // vars
        global $post, $aki_theme_options;

        if(!$post_id)
        {
            $post_id = $post->ID;
        }


        // allow for option == options
        if( $post_id == "option" )
        {
            $post_id = "options";
        }


        // is $field_name a name? pre 3.4.0
        if( strpos($field_key, "field_") === false )
        {
            // get field key
            if( is_numeric($post_id) )
            {
                $field_key = get_post_meta($post_id, '_' . $field_key, true);
            }
            elseif( strpos($post_id, 'user_') !== false )
            {
                $temp_post_id = str_replace('user_', '', $post_id);
                $field_key = get_user_meta($temp_post_id, '_' . $field_key, true);
            }
            else
            {
                $field_key = get_option('_' . $post_id . '_' . $field_key);
            }
        }


        // get field
        $field = $aki_theme_options->get_aki_field($field_key);


        // backup if no field was found, save as a text field
        if( !$field )
        {
            return false;
        }


        if( $options['load_value'] )
        {
            $field['value'] = $aki_theme_options->get_value_for_api($post_id, $field);
        }

        return $field;

    }

    /**
     * Return Page id, this is the page where metabox will be placed
     *
     * @param $page (string), this is the theme option, user will choose the page
     * @return int page ID
     */
    public static function get_page_id($page)
    {
        $page_object = self::get_option($page);
        return $page_object->ID;
    }

} //end class aki


/*--------------------------------------------------------------------------------------
*
*	register_field
*
*	@author Elliot Condon
*	@since 3.0.0
*
*-------------------------------------------------------------------------------------*/
function aki_register_field_cb($array)
{
	$array = array_merge($array, $GLOBALS['aki_register_field']);

	return $array;
}
add_filter('aki_register_field', 'aki_register_field_cb');


/*--------------------------------------------------------------------------------------
*
*	register_field_group
*
*	@author Elliot Condon
*	@since 3.0.6
*
*-------------------------------------------------------------------------------------*/
add_filter('aki/get_field_groups', 'aki_register_field_group_cb', 10, 1);
function aki_register_field_group_cb( $return )
{

	// validate
	if( empty($GLOBALS['aki_register_field_group']) )
	{
		return $return;
	}

	// ensure $return is an array
	if( ! is_array( $return ) )
	{
	    $return = array();
    }

	// merge in custom
	$return = array_merge($return, $GLOBALS['aki_register_field_group']);

	// order field groups based on menu_order, title
	// Obtain a list of columns
	foreach ($return as $key => $row)
	{
	    $menu_order[ $key ] = $row['menu_order'];
	    $title[ $key ] = $row['title'];
	}

	// Sort the array with menu_order ascending
	// Add $array as the last parameter, to sort by the common key
	if(isset($menu_order))
	{
		array_multisort($menu_order, SORT_ASC, $title, SORT_ASC, $return);
	}

	return $return;
}



/*--------------------------------------------------------------------------------------
*
*	register_options_page
*
*	@author Elliot Condon
*	@since 3.0.0
*
*-------------------------------------------------------------------------------------*/
function aki_settings_options_pages_cb( $options )
{
	// merge in options pages
	$options['options_page']['pages'] = array_merge( $options['options_page']['pages'], $GLOBALS['aki_options_pages'] );


	return $options;
}
add_filter('aki_settings', 'aki_settings_options_pages_cb');


/*--------------------------------------------------------------------------------------
*
*	shorcode support
*
*	@author Elliot Condon
*	@since 1.1.1
*
*-------------------------------------------------------------------------------------*/

function aki_shortcode( $atts )
{
	// extract attributs
	extract( shortcode_atts( array(
		'field' => "",
		'post_id' => false,
	), $atts ) );


	// $field is requird
	if( !$field || $field == "" )
	{
		return "";
	}


	// get value and return it
	$value = self::get_field( $field, $post_id );


	if(is_array($value))
	{
		$value = @implode( ', ',$value );
	}

	return $value;
}
add_shortcode( 'aki', 'aki_shortcode' );


/*--------------------------------------------------------------------------------------
*
*	Front end form Head
*
*	@author Elliot Condon
*	@since 1.1.4
*
*-------------------------------------------------------------------------------------*/

function aki_form_head()
{
	// global vars
	global $aki_theme_options, $post_id;



	// run database save first
	if( isset($_POST['aki_save']) )
	{
		// $post_id to save against
		$post_id = $_POST['post_id'];


		// allow for custom save
		$post_id = apply_filters('aki_form_pre_save_post', $post_id);


		// save the data
		do_action('aki_save_post', $post_id);


		// redirect
		if(isset($_POST['return']))
		{
			wp_redirect($_POST['return']);
			exit;
		}

	}


	// register css / javascript
	do_action('aki_print_scripts-input');
	do_action('aki_print_styles-input');


	// need wp styling
	wp_enqueue_style(array(
		'colors-fresh'
	));


	// form was not posted, load js head stuff
	add_action('wp_head', 'aki_form_wp_head');

}

function aki_form_wp_head()
{
	// add user js + css
	do_action('aki_head-input');
}


/*--------------------------------------------------------------------------------------
*
*	Front end form
*
*	@author Elliot Condon
*	@since 1.1.4
*
*-------------------------------------------------------------------------------------*/

function aki_form($options = null)
{
	global $post, $aki_theme_options;


	// defaults
	$defaults = array(
		'post_id' => $post->ID, // post id to get field groups from and save data to
		'field_groups' => array(), // this will find the field groups for this post
		'form_attributes' => array( // attributes will be added to the form element
			'class' => ''
		),
		'return' => add_query_arg( 'updated', 'true', get_permalink() ), // return url
		'html_before_fields' => '', // html inside form before fields
		'html_after_fields' => '', // html inside form after fields
		'submit_value' => 'Update', // vale for submit field
		'updated_message' => 'Post updated.', // default updated message. Can be false
	);


	// merge defaults with options
	if($options && is_array($options))
	{
		$options = array_merge($defaults, $options);
	}
	else
	{
		$options = $defaults;
	}


	// register post box
	if( !$options['field_groups'] )
	{
		// get field groups
		$filter = array(
			'post_id' => $options['post_id']
		);

		if( strpos($options['post_id'], 'user_') !== false )
		{
			$user_id = str_replace('user_', '', $options['post_id']);
			$filter['ef_user'] = $user_id;
		}
		elseif( strpos($options['post_id'], 'taxonomy_') !== false )
		{
			$taxonomy_id = str_replace('taxonomy_', '', $options['post_id']);
			$filter['ef_taxonomy'] = $taxonomy_id;
		}


		$options['field_groups'] = array();
		$options['field_groups'] = apply_filters( 'aki/location/match_field_groups', $options['field_groups'], $filter );
	}


	// updated message
	if(isset($_GET['updated']) && $_GET['updated'] == 'true' && $options['updated_message'])
	{
		echo '<div id="message" class="updated"><p>' . $options['updated_message'] . '</p></div>';
	}


	// Javascript
	$script_post_id = is_numeric($options['post_id']) ? $options['post_id'] : 0;
	echo '<script type="text/javascript">aki.post_id = ' . $script_post_id . '; </script>';


	// display form
	?>
	<form action="" id="post" method="post" <?php if($options['form_attributes']){foreach($options['form_attributes'] as $k => $v){echo $k . '="' . $v .'" '; }} ?>>
	<div style="display:none">
		<input type="hidden" name="aki_save" value="true" />
		<input type="hidden" name="post_id" value="<?php echo $options['post_id']; ?>" />
		<input type="hidden" name="return" value="<?php echo $options['return']; ?>" />
		<?php wp_editor('', 'aki_settings'); ?>
	</div>

	<div id="poststuff">
	<?php

	// html before fields
	echo $options['html_before_fields'];

	$field_groups = apply_filters('aki/get_field_groups', false);

	if($field_groups):
		foreach($field_groups as $field_group):

			if(!in_array($field_group['id'], $options['field_groups'])) continue;


			// defaults
			if(!$field_group['options'])
			{
				$field_group['options'] = array(
					'layout'	=>	'default'
				);
			}


			if($field_group['fields'])
			{
				echo '<div id="aki_' . $field_group['id'] . '" class="postbox aki_postbox">';
				echo '<h3 class="hndle"><span>' . $field_group['title'] . '</span></h3>';
				echo '<div class="inside">';
					echo '<div class="options" data-layout="' . $field_group['options']['layout'] . '" data-show="1"></div>';
					$aki_theme_options->render_fields_for_input($field_group['fields'], $options['post_id']);
				echo '</div></div>';
			}

		endforeach;
	endif;

	// html after fields
	echo $options['html_after_fields'];

	?>
	<!-- Submit -->
	<div class="field">
		<input type="submit" value="<?php echo $options['submit_value']; ?>" />
	</div>
	<!-- / Submit -->

	</div><!-- <div id="poststuff"> -->
	</form>
	<?php
}


/*
*  Depreceated Functions
*
*  @description:
*  @created: 23/07/12
*/


/*--------------------------------------------------------------------------------------
*
*	reset_the_repeater_field
*
*	@author Elliot Condon
*	@depreciated: 3.3.4 - now use has_sub_field
*	@since 1.0.3
*
*-------------------------------------------------------------------------------------*/

//function reset_the_repeater_field()
//{
//
//}


/*--------------------------------------------------------------------------------------
*
*	the_repeater_field
*
*	@author Elliot Condon
*	@depreciated: 3.3.4 - now use has_sub_field
*	@since 1.0.3
*
*-------------------------------------------------------------------------------------*/

//function the_repeater_field($field_name, $post_id = false)
//{
//	return has_sub_field($field_name, $post_id);
//}


/*--------------------------------------------------------------------------------------
*
*	the_flexible_field
*
*	@author Elliot Condon
*	@depreciated: 3.3.4 - now use has_sub_field
*	@since 3.?.?
*
*-------------------------------------------------------------------------------------*/

//function the_flexible_field($field_name, $post_id = false)
//{
//	return has_sub_field($field_name, $post_id);
//}