<?php

/*
 *	Advanced Custom Fields - addon
 *
 *	Match aki post with WP page
 *
 *  @author: petrov
 */


class Metabox_Location_Field extends aki_Field
{

    /*--------------------------------------------------------------------------------------
    *
    *	Constructor
    *	- This function is called when the field class is initalized on each page.
    *	- Here you can add filters / actions and setup any other functionality for your field
    *
    *	@author Elliot Condon
    *	@since 2.2.0
    *
    *-------------------------------------------------------------------------------------*/

    function __construct($parent)
    {
        // do not delete!
        parent::__construct($parent);

        // set name / title
        $this->name = 'wpsp_metabox_location_field'; // variable name (no spaces / special characters / etc)
        $this->title = __("WPSP Metabox Location",'aki'); // field label (Displayed in edit screens)

    }


    /*--------------------------------------------------------------------------------------
    *
    *	create_options
    *	- this function is called from core/field_meta_box.php to create extra options
    *	for your field
    *
    *	@params
    *	- $key (int) - the $_POST obejct key required to save the options to the field
    *	- $field (array) - the field object
    *
    *	@author Elliot Condon
    *	@since 2.2.0
    *
    *-------------------------------------------------------------------------------------*/

    function create_options($key, $field)
    {
        // defaults
        $defaults = array(
            'post_type' 	=>	'',
            'multiple'		=>	0,
            'allow_null'	=>	0,
            'taxonomy' 		=>	array('all'),
        );

        $field = array_merge($defaults, $field);

        ?>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label for=""><?php _e("Post Type",'aki'); ?></label>
            </td>
            <td>
                <?php

                $choices = array(
                    ''	=>	__("All",'aki')
                );
                $choices = array_merge( $choices, $this->parent->get_post_types() );


                do_action('aki/create_field', array(
                    'type'	=>	'select',
                    'name'	=>	'fields['.$key.'][post_type]',
                    'value'	=>	$field['post_type'],
                    'choices'	=>	$choices,
                    'multiple'	=>	1,
                ));

                ?>
            </td>
        </tr>

        <!--List aki pages -->
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label for=""><?php _e("aki Pages",'aki'); ?></label>
            </td>
            <td>
                <?php

                $wp_query = new WP_Query(array('post_type'=>'aki'));
                $wpsp_aki_posts = array();

                while ($wp_query->have_posts()) {
                    $wp_query->the_post();
                    $wpsp_aki_posts[$wp_query->post->ID] = $wp_query->post->post_title;
                }

                do_action('aki/create_field', array(
                    'type'	=>	'select',
                    'name'	=>	'fields['.$key.'][wpsp_aki_page]',
                    'value'	=>	$field['wpsp_aki_page'],
                    'choices'	=>	$wpsp_aki_posts,
                    'multiple'	=>	1,
                ));

                ?>
            </td>
        </tr>
        <!--enc List aki pages -->

        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e("Filter from Taxonomy",'aki'); ?></label>
            </td>
            <td>
                <?php
                $choices = array(
                    '' => array(
                        'all' => __("All",'aki')
                    )
                );
                $choices = array_merge($choices, $this->parent->get_taxonomies_for_select());

                do_action('aki/create_field', array(
                    'type'	=>	'select',
                    'name'	=>	'fields['.$key.'][taxonomy]',
                    'value'	=>	$field['taxonomy'],
                    'choices' => $choices,
                    'optgroup' => true,
                    'multiple'	=>	1,
                ));

                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e("Allow Null?",'aki'); ?></label>
            </td>
            <td>
                <?php

                do_action('aki/create_field', array(
                    'type'	=>	'radio',
                    'name'	=>	'fields['.$key.'][allow_null]',
                    'value'	=>	$field['allow_null'],
                    'choices'	=>	array(
                        1	=>	__("Yes",'aki'),
                        0	=>	__("No",'aki'),
                    ),
                    'layout'	=>	'horizontal',
                ));

                ?>
            </td>
        </tr>
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e("Select multiple values?",'aki'); ?></label>
            </td>
            <td>
                <?php

                do_action('aki/create_field', array(
                    'type'	=>	'radio',
                    'name'	=>	'fields['.$key.'][multiple]',
                    'value'	=>	$field['multiple'],
                    'choices'	=>	array(
                        1	=>	__("Yes",'aki'),
                        0	=>	__("No",'aki'),
                    ),
                    'layout'	=>	'horizontal',
                ));

                ?>
            </td>
        </tr>
    <?php
    }


    /*--------------------------------------------------------------------------------------
    *
    *	pre_save_field
    *	- this function is called when saving your aki object. Here you can manipulate the
    *	field object and it's options before it gets saved to the database.
    *
    *	@author Elliot Condon
    *	@since 2.2.0
    *
    *-------------------------------------------------------------------------------------*/

    function pre_save_field($field)
    {
        // do stuff with field (mostly format options data)

        return parent::pre_save_field($field);
    }


    /*--------------------------------------------------------------------------------------
    *
    *	create_field
    *	- this function is called on edit screens to produce the html for this field
    *
    *	@author Elliot Condon
    *	@since 2.2.0
    *
    *-------------------------------------------------------------------------------------*/

    function create_field($field)
    {
        // vars
        $args = array(
            'numberposts' => -1,
            'post_type' => null,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_status' => array('publish', 'private', 'draft', 'inherit', 'future'),
            'suppress_filters' => false,
        );

        $defaults = array(
            'multiple'		=>	0,
            'post_type' 	=>	false,
            'taxonomy' 		=>	array('all'),
            'allow_null'	=>	0,
        );


        $field = array_merge($defaults, $field);


        // validate taxonomy
        if( !is_array($field['taxonomy']) )
        {
            $field['taxonomy'] = array('all');
        }

        // load all post types by default
        if( !$field['post_type'] || !is_array($field['post_type']) || $field['post_type'][0] == "" )
        {
            $field['post_type'] = $this->parent->get_post_types();
        }


        // create tax queries
        if( ! in_array('all', $field['taxonomy']) )
        {
            // vars
            $taxonomies = array();
            $args['tax_query'] = array();

            foreach( $field['taxonomy'] as $v )
            {

                // find term (find taxonomy!)
                // $term = array( 0 => $taxonomy, 1 => $term_id )
                $term = explode(':', $v);


                // validate
                if( !is_array($term) || !isset($term[1]) )
                {
                    continue;
                }


                // add to tax array
                $taxonomies[ $term[0] ][] = $term[1];

            }


            // now create the tax queries
            foreach( $taxonomies as $k => $v )
            {
                $args['tax_query'][] = array(
                    'taxonomy' => $k,
                    'field' => 'id',
                    'terms' => $v,
                );
            }
        }


        // Change Field into a select
        $field['type'] = 'select';
        $field['choices'] = array();
        $field['optgroup'] = false;


        foreach( $field['post_type'] as $post_type )
        {
            // set post_type
            $args['post_type'] = $post_type;


            // set order
            if( is_post_type_hierarchical($post_type) && !isset($args['tax_query']) )
            {
                $args['sort_column'] = 'menu_order, post_title';
                $args['sort_order'] = 'ASC';

                $posts = get_pages( $args );
            }
            else
            {
                $posts = get_posts( $args );
            }


            if($posts)
            {
                foreach( $posts as $post )
                {
                    // find title. Could use get_the_title, but that uses get_post(), so I think this uses less Memory
                    $title = '';
                    $ancestors = get_ancestors( $post->ID, $post->post_type );
                    if($ancestors)
                    {
                        foreach($ancestors as $a)
                        {
                            $title .= '–';
                        }
                    }
                    $title .= ' ' . apply_filters( 'the_title', $post->post_title, $post->ID );


                    // status
                    if($post->post_status != "publish")
                    {
                        $title .= " ($post->post_status)";
                    }

                    // WPML
                    if( defined('ICL_LANGUAGE_CODE') )
                    {
                        $title .= ' (' . ICL_LANGUAGE_CODE . ')';
                    }

                    // add to choices
                    if( count($field['post_type']) == 1 )
                    {
                        $field['choices'][ $post->ID ] = $title;
                    }
                    else
                    {
                        // group by post type
                        $post_type_object = get_post_type_object( $post->post_type );
                        $post_type_name = $post_type_object->labels->name;

                        $field['choices'][ $post_type_name ][ $post->ID ] = $title;
                        $field['optgroup'] = true;
                    }


                }
                // foreach( $posts as $post )
            }
            // if($posts)
        }
        // foreach( $field['post_type'] as $post_type )

        // create field
        do_action('aki/create_field', $field );
    }


    /*--------------------------------------------------------------------------------------
    *
    *	admin_head
    *	- this function is called in the admin_head of the edit screen where your field
    *	is created. Use this function to create css and javascript to assist your
    *	create_field() function.
    *
    *	@author Elliot Condon
    *	@since 2.2.0
    *
    *-------------------------------------------------------------------------------------*/

//    function admin_head()
//    {
//
//    }


    /*--------------------------------------------------------------------------------------
    *
    *	admin_print_scripts / admin_print_styles
    *	- this function is called in the admin_print_scripts / admin_print_styles where
    *	your field is created. Use this function to register css and javascript to assist
    *	your create_field() function.
    *
    *	@author Elliot Condon
    *	@since 3.0.0
    *
    *-------------------------------------------------------------------------------------*/

//    function admin_print_scripts()
//    {
//
//    }
//
//    function admin_print_styles()
//    {
//
//    }


    /*--------------------------------------------------------------------------------------
    *
    *	update_value
    *	- this function is called when saving a post object that your field is assigned to.
    *	the function will pass through the 3 parameters for you to use.
    *
    *	@params
    *	- $post_id (int) - usefull if you need to save extra data or manipulate the current
    *	post object
    *	- $field (array) - usefull if you need to manipulate the $value based on a field option
    *	- $value (mixed) - the new value of your field.
    *
    *	@author Elliot Condon
    *	@since 2.2.0
    *
    *-------------------------------------------------------------------------------------*/

    function update_value($post_id, $field, $value)
    {
        // do stuff with value
        //$TODO add other matching rules
        $aki_page_id = $field['wpsp_aki_page'][0]; //aki page that holds metaboxes
        $meta = array(
            'param'     => 'page',
            'operator'  => '==',
            'value'     => $value,  //wp page where to put those metaboxes
            'order_no'  => 0
        );

        update_post_meta($aki_page_id, 'rule', $meta);

        // save value
        parent::update_value($post_id, $field, $value);
    }





    /*--------------------------------------------------------------------------------------
    *
    *	get_value
    *	- called from the edit page to get the value of your field. This function is useful
    *	if your field needs to collect extra data for your create_field() function.
    *
    *	@params
    *	- $post_id (int) - the post ID which your value is attached to
    *	- $field (array) - the field object.
    *
    *	@author Elliot Condon
    *	@since 2.2.0
    *
    *-------------------------------------------------------------------------------------*/

    function get_value($post_id, $field)
    {
        // get value
        $value = parent::get_value($post_id, $field);

        // format value

        // return value
        return $value;
    }


    /*--------------------------------------------------------------------------------------
    *
    *	get_value_for_api
    *	- called from your template file when using the API functions (get_field, etc).
    *	This function is useful if your field needs to format the returned value
    *
    *	@params
    *	- $post_id (int) - the post ID which your value is attached to
    *	- $field (array) - the field object.
    *
    *	@author Elliot Condon
    *	@since 3.0.0
    *
    *-------------------------------------------------------------------------------------*/

    function get_value_for_api($post_id, $field)
    {
        // get value
        $value = parent::get_value($post_id, $field);


        // no value?
        if( !$value )
        {
            return false;
        }


        // null?
        if( $value == 'null' )
        {
            return false;
        }


        // multiple / single
        if( is_array($value) )
        {
            // find posts (DISTINCT POSTS)
            $posts = get_posts(array(
                'numberposts' => -1,
                'post__in' => $value,
                'post_type'	=>	$this->parent->get_post_types(),
                'post_status' => array('publish', 'private', 'draft', 'inherit', 'future'),
            ));


            $ordered_posts = array();
            foreach( $posts as $post )
            {
                // create array to hold value data
                $ordered_posts[ $post->ID ] = $post;
            }


            // override value array with attachments
            foreach( $value as $k => $v)
            {
                // check that post exists (my have been trashed)
                if( isset($ordered_posts[ $v ]) )
                {
                    $value[ $k ] = $ordered_posts[ $v ];
                }
            }

        }
        else
        {
            $value = get_post($value);
        }


        // return the value
        return $value;

    }

}


