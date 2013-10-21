<?php

include('core/api.php');

$aki_theme_options = new AKI_Theme_Options();

class AKI_Theme_Options
{ 
	var $dir,
		$path,
		$version,
		$upgrade_version,
		$fields,
		$cache,
		$defaults,
		
		
		// controllers
		$upgrade,
		$settings,
		$field_groups,
		$field_group,
		$input,
		$options_page,
		$everything_fields,
		$third_party,
		$location;
	
	
	/*
	*  Constructor
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 23/06/12
	*/
	
	function __construct()
	{
		// vars
		$this->path = get_template_directory() . "/admin/";
		$this->dir = get_template_directory_uri() . "/admin/";
		$this->version = '3.5.8.1';
		$this->upgrade_version = '3.4.1'; // this is the latest version which requires an upgrade
		$this->cache = array(); // basic array cache to hold data throughout the page load
		$this->defaults = array(
			'options_page' => array(
				'capability' => 'edit_posts', // capability to view options page
				'title' => __('Theme Options','aki'), // title / menu name ('Site Options')
				'pages' => array(), // an array of sub pages ('Header, Footer, Home, etc')
			),
			'activation_codes' => array(
				'repeater'			=> '', // activation code for the repeater add-on (XXXX-XXXX-XXXX-XXXX)
				'options_page'		=> '', // activation code for the options page add-on (XXXX-XXXX-XXXX-XXXX)
				'flexible_content'	=> '', // activation code for the flexible content add-on (XXXX-XXXX-XXXX-XXXX)
				'gallery'			=> '', // activation code for the gallery add-on (XXXX-XXXX-XXXX-XXXX)
			),
		);
		
		
		// set text domain
		load_plugin_textdomain('aki', false, basename(dirname(__FILE__)).'/lang' );
		
		
		// controllers
		$this->setup_controllers();
		
		
		// actions
		add_action('init', array($this, 'init'));
		//add_action('admin_menu', array($this,'admin_menu')); #wpsp
		add_action('admin_head', array($this,'admin_head'));
		add_action('aki_save_post', array($this, 'aki_save_post'), 10); // save post, called from many places (api, input, everything, options)
		
		
		// action functions
		add_action('aki/create_field', array($this, 'create_field'), 1, 1);
		add_filter('aki/get_field_groups', array($this, 'get_field_groups'), 1, 1);
		//add_filter('aki/get_field', array($this, 'get_field'), 10, 1);
		
		
		// filters
		add_filter('aki_load_field', array($this, 'aki_load_field'), 1, 1);
		add_filter('post_updated_messages', array($this, 'post_updated_messages'));
		add_filter('aki_parse_value', array($this, 'aki_parse_value'));
		
		
		return true;
	}
	
	
	/*
	*  Init
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 23/06/12
	*/
	
	function init()
	{
		// setup defaults
		$this->defaults = apply_filters('aki_settings', $this->defaults);
		
		
		// allow for older filters
		$this->defaults['options_page']['title'] = apply_filters('aki_options_page_title', $this->defaults['options_page']['title']);
		
		
		// setup fields
		$this->setup_fields();
		

		// Create aki post type
		$labels = array(
		    'name' => __( 'Field&nbsp;Groups', 'aki' ),
			'singular_name' => __( 'Advanced Custom Fields', 'aki' ),
		    'add_new' => __( 'Add New' , 'aki' ),
		    'add_new_item' => __( 'Add New Field Group' , 'aki' ),
		    'edit_item' =>  __( 'Edit Field Group' , 'aki' ),
		    'new_item' => __( 'New Field Group' , 'aki' ),
		    'view_item' => __('View Field Group', 'aki'),
		    'search_items' => __('Search Field Groups', 'aki'),
		    'not_found' =>  __('No Field Groups found', 'aki'),
		    'not_found_in_trash' => __('No Field Groups found in Trash', 'aki'),
		);
		
		
		register_post_type('aki', array(
			'labels' => $labels,
			'public' => false,
			'show_ui' => true,
			'_builtin' =>  false,
			'capability_type' => 'page',
			'hierarchical' => true,
			'rewrite' => false,
			'query_var' => "aki",
			'supports' => array(
				'title',
			),
			'show_in_menu'	=> false,
		));
		
		
		// register aki scripts
		$scripts = array(
			'aki-field-group' => $this->dir . 'js/field-group.js',
			'aki-input' => $this->dir . 'js/input.php',
			'aki-input-ajax' => $this->dir . 'js/input/ajax.js',
			'aki-datepicker' => $this->dir . 'core/fields/date_picker/jquery.ui.datepicker.js',
		);
		
		foreach( $scripts as $k => $v )
		{
			wp_register_script( $k, $v, array('jquery'), $this->version );
		}
		
		
		// register aki styles
		$styles = array(
			'aki' => $this->dir . 'css/aki.css',
			'aki-field-group' => $this->dir . 'css/field-group.css',
			'aki-global' => $this->dir . 'css/global.css',
			'aki-input' => $this->dir . 'css/input.css',
			'aki-datepicker' => $this->dir . 'core/fields/date_picker/style.date_picker.css',
		);
		
		foreach( $styles as $k => $v )
		{
			wp_register_style( $k, $v, false, $this->version ); 
		}
		
		
	}
	
	
	/*
	*  get_cache
	*
	*  @description: Simple aki (once per page) cache
	*  @since 3.1.9
	*  @created: 23/06/12
	*/
	
	function get_cache($key = false)
	{
		// key is required
		if( !$key )
			return false;
		
		
		// does cache at key exist?
		if( !isset($this->cache[$key]) )
			return false;
		
		
		// return cahced item
		return $this->cache[$key];
	}
	
	
	/*
	*  set_cache
	*
	*  @description: Simple aki (once per page) cache
	*  @since 3.1.9
	*  @created: 23/06/12
	*/
	
	function set_cache($key = false, $value = null)
	{
		// key is required
		if( !$key )
			return false;
		
		
		// update the cache array
		$this->cache[$key] = $value;
		
		
		// return true. Probably not needed
		return true;
	}
	
	
	/*
	*  setup_fields
	*
	*  @description: Create an array of field objects, including custom registered field types
	*  @since 1.0.0
	*  @created: 23/06/12
	*/
	
	function setup_fields()
	{
		// include parent field
		include_once('core/fields/aki_field.php');
		
		
		// include child fields
		include_once('core/fields/aki_field.php');
		include_once('core/fields/tab.php');
		include_once('core/fields/text.php');
		include_once('core/fields/textarea.php');
		include_once('core/fields/wysiwyg.php');
		include_once('core/fields/image.php');
		include_once('core/fields/file.php');
		include_once('core/fields/number.php');
		include_once('core/fields/select.php');
		include_once('core/fields/checkbox.php');
		include_once('core/fields/radio.php');
		include_once('core/fields/true_false.php');
		include_once('core/fields/page_link.php');
		include_once('core/fields/post_object.php');
		include_once('core/fields/relationship.php');
		include_once('core/fields/date_picker/date_picker.php');
		include_once('core/fields/color_picker.php');
		
		
		// add child fields
		$this->fields['none'] = new aki_Field($this); 
		$this->fields['tab'] = new aki_Tab($this); 
		$this->fields['text'] = new aki_Text($this); 
		$this->fields['textarea'] = new aki_Textarea($this); 
		$this->fields['wysiwyg'] = new aki_Wysiwyg($this); 
		$this->fields['image'] = new aki_Image($this); 
		$this->fields['file'] = new aki_File($this); 
		$this->fields['number'] = new aki_Number($this); 
		$this->fields['select'] = new aki_Select($this); 
		$this->fields['checkbox'] = new aki_Checkbox($this);
		$this->fields['radio'] = new aki_Radio($this);
		$this->fields['true_false'] = new aki_True_false($this);
		$this->fields['page_link'] = new aki_Page_link($this);
		$this->fields['post_object'] = new aki_Post_object($this);
		$this->fields['relationship'] = new aki_Relationship($this);
		$this->fields['date_picker'] = new aki_Date_picker($this);
		$this->fields['color_picker'] = new aki_Color_picker($this);
		
		
		// add repeater
        include_once('core/fields/repeater.php');
        $this->fields['repeater'] = new aki_Repeater($this);
		
		
		// add flexible content
        include_once('core/fields/flexible_content.php');
        $this->fields['flexible_content'] = new aki_Flexible_content($this);
		
		
		// add gallery
        include_once('core/fields/gallery.php');
        $this->fields['gallery'] = new aki_Gallery($this);
		
		
		// hook to load in third party fields
		$custom = apply_filters('aki_register_field',array());
		if(!empty($custom))
		{
			foreach($custom as $v)
			{
				//var_dump($v['url']);
				include($v['url']);
				$name = $v['class'];
				$custom_field = new $name($this);
				$this->fields[$custom_field->name] = $custom_field;
			}
		}
		
	}
	
	
	/*
	*  setup_fields
	*
	*  @description: 
	*  @since 3.2.6
	*  @created: 23/06/12
	*/

	function setup_controllers()
	{
		// Settings
		include_once('core/controllers/settings.php');
		$this->settings = new aki_settings($this);
		
		
		// upgrade
		include_once('core/controllers/upgrade.php');
		$this->upgrade = new aki_upgrade($this);
		
		
		// field_groups
		include_once('core/controllers/field_groups.php');
		$this->field_groups =  new aki_field_groups($this);
		
		
		// field_group
		include_once('core/controllers/field_group.php');
		$this->field_group = new aki_field_group($this);
		
		
		// input
		include_once('core/controllers/input.php');
		$this->input = new aki_input($this);
		
		
		// options page
		include_once('core/controllers/options_page.php');
		$this->options_page = new aki_options_page($this);
		
		
		// everthing fields
		include_once('core/controllers/everything_fields.php');
		$this->everything_fields = new aki_everything_fields($this);
		
		
		// Third Party Compatibility
		include_once('core/controllers/third_party.php');
		$this->third_party = new aki_third_party($this);
		
		
		// Location
		include_once('core/controllers/location.php');
		$this->location = new aki_location($this);
	}
	
	
	/*
	*  admin_menu
	*
	*  @description: 
	*  @since 1.0.0
	*  @created: 23/06/12
	*/
	
	function admin_menu() {
	
		// add aki page to options menu
		add_utility_page(__("Custom Fields",'aki'), __("Custom Fields",'aki'), 'manage_options', 'edit.php?post_type=aki');
		
	}
	
	
	/*
	*  post_updated_messages
	*
	*  @description: messages for saving a field group
	*  @since 1.0.0
	*  @created: 23/06/12
	*/

	function post_updated_messages( $messages )
	{
		global $post, $post_ID;
	
		$messages['aki'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __('Field group updated.', 'aki'),
			2 => __('Custom field updated.', 'aki'),
			3 => __('Custom field deleted.', 'aki'),
			4 => __('Field group updated.', 'aki'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Field group restored to revision from %s', 'aki'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __('Field group published.', 'aki'),
			7 => __('Field group saved.', 'aki'),
			8 => __('Field group submitted.', 'aki'),
			9 => __('Field group scheduled for.', 'aki'),
			10 => __('Field group draft updated.', 'aki'),
		);
	
		return $messages;
	}	
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_head()
	{
		// hide upgrade page from nav
		echo '<style type="text/css"> 
			#adminmenu #toplevel_page_edit-post_type-aki a[href="edit.php?post_type=aki&page=aki-upgrade"]{ display:none; }
			#adminmenu #toplevel_page_edit-post_type-aki .wp-menu-image { background-position: 1px -33px; }
			#adminmenu #toplevel_page_edit-post_type-aki:hover .wp-menu-image,
			#adminmenu #toplevel_page_edit-post_type-aki.wp-menu-open .wp-menu-image { background-position: 1px -1px; }
		</style>';
	}
	
	
	/*
	*  get_field_groups
	*
	*  @description: 
	*  @since: 3.5.7
	*  @created: 12/01/13
	*/
	
	function get_field_groups( $return )
	{
		// return must be an array
		if( !is_array($return) )
		{
			$return = array();
		}
		
		
		// get aki's
		$result = get_posts(array(
			'numberposts' 	=> -1,
			'post_type' 	=> 'aki',
			'orderby' 		=> 'menu_order title',
			'order' 		=> 'asc',
			'suppress_filters' => false,
		));

		
		// populate akis
		if($result)
		{
			foreach($result as $aki)
			{
				$return[] = array(
					'id' => $aki->ID,
					'title' => get_the_title($aki->ID),
					'fields' => $this->get_aki_fields($aki->ID),
					'location' => $this->get_aki_location($aki->ID),
					'options' => $this->get_aki_options($aki->ID),
					'menu_order' => $aki->menu_order,
				);
			}
		}
		
		// hook to load in registered field groups
		//$return = apply_filters('aki_register_field_group', $return);
		
		return $return;
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_aki_fields
	*	- returns an array of fields for a aki object
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/

	function get_aki_fields($post_id)
	{
		// vars
		global $wpdb;
		
		$return = array();
		
		
		// get field from postmeta
		$rows = $wpdb->get_results( $wpdb->prepare("SELECT meta_key FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s", $post_id, 'field\_%'), ARRAY_A);
		
		if( $rows )
		{
			foreach( $rows as $row )
			{
				$field = $this->get_aki_field( $row['meta_key'], $post_id );
	
			 	$return[ $field['order_no'] ] = $field;
			}
		 	
		 	ksort($return);
	 	}
	 	
	 	
	 	// return
		return $return;
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_aki_field
	*	- returns a field
	*	- $post_id can be passed to make sure the correct field is loaded. Eg: a duplicated
	*	field group may have the same field_key, but a different post_id
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/

	function get_aki_field( $field_key, $post_id = false )
	{
		
		
		// return cache
		$cache = $this->get_cache('aki_field_' . $field_key);
		if($cache != false)
		{
			return $cache;
		}
		
		
		// vars
		global $wpdb;
		
		
		// get field from postmeta
		$sql = $wpdb->prepare("SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = %s", $field_key);
		
		if( $post_id )
		{
			$sql .= $wpdb->prepare("AND post_id = %d", $post_id);
		}

		$row = $wpdb->get_results( $sql, ARRAY_A );
		
		
		
		if( $row )
		{
			$row = $row[0];
			
			
			// return field if it is not in a trashed field group
			if( get_post_status( $row['post_id'] ) != "trash" )
			{
				$row['meta_value'] = maybe_unserialize( $row['meta_value'] );
				$row['meta_value'] = maybe_unserialize( $row['meta_value'] ); // run again for WPML
				
				
				// store field
				$field = $row['meta_value'];
				
				
				// apply filters
				$field = apply_filters('aki_load_field', $field);
				
				$keys = array('type', 'name', 'key');
				foreach( $keys as $key )
				{
					if( isset($field[ $key ]) )
					{
						$field = apply_filters('aki_load_field-' . $field[ $key ], $field);
					}
				}
				
			
				// set cache
				$this->set_cache('aki_field_' . $field_key, $field);
				
				return $field;
			}
		}
		


		// hook to load in registered field groups
		$akis = apply_filters('aki/get_field_groups', false);
		
		if($akis)
		{
			// loop through akis
			foreach($akis as $aki)
			{
				// loop through fields
				if($aki['fields'])
				{
					foreach($aki['fields'] as $field)
					{
						if($field['key'] == $field_key)
						{
							// apply filters
							$field = apply_filters('aki_load_field', $field);
							
							$keys = array('type', 'name', 'key');
							foreach( $keys as $key )
							{
								if( isset($field[ $key ]) )
								{
									$field = apply_filters('aki_load_field-' . $field[ $key ], $field);
								}
							}
							
							
							// set cache
							$this->set_cache('aki_field_' . $field_key, $field);
							
							return $field;
						}
					}
				}
				// if($aki['fields'])
			}
			// foreach($akis as $aki)
		}
		// if($akis)

 		
 		return null;
	}
	
	
	/*
	*  aki_load_field
	*
	*  @description: 
	*  @since 3.5.1
	*  @created: 14/10/12
	*/
	
	function aki_load_field( $field )
	{
		if( !is_array($field) )
		{
			return $field;	
		}
		
		$defaults = array(
			'key' => '',
			'label' => '',
			'name' => '',
			'type' => 'text',
			'order_no' =>	1,
			'instructions' =>	'',
			'required' => 0,
			'conditional_logic' => array(
				'status' => 0,
				'allorany' => 'all',
				'rules' => 0
			),
		);
		
		$field = array_merge($defaults, $field);
		
		
		// Parse Values
		$field = apply_filters( 'aki_parse_value', $field );
		
		
		// trim name
		$field['name'] = trim( $field['name'] );
		
		
		return $field;
	}
	
	
	/*
	*  aki_parse_value
	*
	*  @description: 
	*  @since: 2.0.4
	*  @created: 9/12/12
	*/
	
	function aki_parse_value( $value )
	{
		
		// is value another array?
		if( is_array($value) )
		{
			foreach( $value as $k => $v )
			{
				$value[ $k ] = apply_filters( 'aki_parse_value', $v );
			}	
		}
		else
		{
			// numbers
			if( is_numeric($value) )
			{
				// float / int
				if( strpos($value,'.') !== false )
				{
					$value = floatval( $value );
				}
				else
				{
					$value = intval( $value );
				}
			}
		}
		
		
		// return
		return $value;
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		
		if(!isset($this->fields[$field['type']]) || !is_object($this->fields[$field['type']]))
		{
			_e('Error: Field Type does not exist!','aki');
			return false;
		}
		
		
		// defaults - class
		if( ! isset($field['class']) )
		{
			$field['class'] = $field['type'];
		}
		
		
		// defaults - id
		if( ! isset($field['id']) )
		{
			$id = $field['name'];
			$id = str_replace('][', '_', $id);
			$id = str_replace('fields[', '', $id);
			$id = str_replace('[', '-', $id); // location rules (select) does'nt have "fields[" in it
			$id = str_replace(']', '', $id);
			
			
			$field['id'] = 'aki-' . $id;
		}
		
		
		$this->fields[ $field['type'] ]->create_field($field);
		

		// conditional logic
		// - isset is needed for the edit field group page where fields are created without many parameters
		if( isset($field['conditional_logic']['status']) && $field['conditional_logic']['status'] ):
			
			$join = ' && ';
			if( $field['conditional_logic']['allorany'] == "any" )
			{
				$join = ' || ';
			}
			
			?>
<script type="text/javascript">
(function($){
	
	// create the conditional function
	$(document).live('aki/conditional_logic/<?php echo $field['key']; ?>', function(){
		
		var field = $('.field-<?php echo $field['key']; ?>');		
<?php

		$if = array();
		foreach( $field['conditional_logic']['rules'] as $rule )
		{
			$if[] = 'aki.conditional_logic.calculate({ field : "'. $field['key'] .'", toggle : "' . $rule['field'] . '", operator : "' . $rule['operator'] .'", value : "' . $rule['value'] . '"})' ;
		}
		
?>
		if(<?php echo implode( $join, $if ); ?>)
		{
			field.removeClass('aki-conditional_logic-hide').addClass('aki-conditional_logic-show');
		}
		else
		{
			field.removeClass('aki-conditional_logic-show').addClass('aki-conditional_logic-hide');
		}
		
	});
	
	
	// add change events to all fields
<?php 

$already_added = array();

foreach( $field['conditional_logic']['rules'] as $rule ): 

	if( in_array( $rule['field'], $already_added) )
	{
		continue;
	}
	else
	{
		$already_added[] = $rule['field'];
	}
	
	?>
	$('.field-<?php echo $rule['field']; ?> *[name]').live('change', function(){
		$(document).trigger('aki/conditional_logic/<?php echo $field['key']; ?>');
	});
<?php endforeach; ?>
	
	$(document).live('aki/setup_fields', function(e, postbox){
		$(document).trigger('aki/conditional_logic/<?php echo $field['key']; ?>');
	});
		
})(jQuery);
</script>
			<?php
		endif;
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_aki_location
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_aki_location($post_id)
	{
		// defaults
		$return = array(
	 		'rules'		=>	array(),
	 		'allorany'	=>	'all', 
	 	);
		
		
		// vars
		$allorany = get_post_meta($post_id, 'allorany', true);
		if( $allorany )
		{
			$return['allorany'] = $allorany;
		}
		
		
		// get all fields
	 	$rules = get_post_meta($post_id, 'rule', false);
	 	

	 	if($rules)
	 	{
	 		
		 	foreach($rules as $rule)
		 	{
		 		// if field group was duplicated, it may now be a serialized string!
		 		$rule = maybe_unserialize($rule);
		
		
		 		$return['rules'][$rule['order_no']] = $rule;
		 	}
	 	}
	 	
	 	
	 	// sort
	 	ksort($return['rules']);
	 	
	 	
	 	// return fields
		return $return;
	 	
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_aki_options
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_aki_options($post_id)
	{
		
		// defaults
	 	$options = array(
	 		'position'			=>	'normal',
	 		'layout'			=>	'no_box',
	 		'hide_on_screen'	=>	array(),
	 	);
	 	
	 	
	 	// vars
	 	$position = get_post_meta($post_id, 'position', true);
	 	if( $position )
		{
			$options['position'] = $position;
		}
		
		$layout = get_post_meta($post_id, 'layout', true);
	 	if( $layout )
		{
			$options['layout'] = $layout;
		}
		
		$hide_on_screen = get_post_meta($post_id, 'hide_on_screen', true);
	 	if( $hide_on_screen )
		{
			$hide_on_screen = maybe_unserialize($hide_on_screen);
			$options['hide_on_screen'] = $hide_on_screen;
		}
		
	 	
	 	// return
	 	return $options;
	}
	
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value($post_id, $field)
	{
		if( empty($this->fields) )
		{
			$this->setup_fields();
		}
		
		if( !isset($field['type'], $this->fields[ $field['type'] ]) )
		{
			return false;
		}
				
		return $this->fields[$field['type']]->get_value($post_id, $field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		if( empty($this->fields) )
		{
			$this->setup_fields();
		}
		
		if( !isset($field['type'], $this->fields[ $field['type'] ]) )
		{
			return false;
		}
		
		return $this->fields[$field['type']]->get_value_for_api($post_id, $field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	update_value
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function update_value($post_id, $field, $value)
	{
		if( isset($field['type'], $this->fields[ $field['type'] ]) )
		{
			$this->fields[$field['type']]->update_value($post_id, $field, $value);
		}
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	update_field
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function update_field($post_id, $field)
	{
		// apply filters
		$field = apply_filters('aki_save_field', $field );
		$field = apply_filters('aki_save_field-' . $field['type'], $field );
		
		
		// save
		update_post_meta($post_id, $field['key'], $field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	format_value_for_api
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function format_value_for_api($value, $field)
	{
		return $this->fields[$field['type']]->format_value_for_api($value, $field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_format_data
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_format_data($field)
	{
		return $this->fields[$field['type']]->create_format_data($field);
	}
	
	
	/*
	*  render_fields_for_input
	*
	*  @description: 
	*  @since 3.1.6
	*  @created: 23/06/12
	*/
	
	function render_fields_for_input($fields, $post_id)
	{
			
		// create fields
		if($fields)
		{
			foreach($fields as $field)
			{
				// if they didn't select a type, skip this field
				if(!$field['type'] || $field['type'] == 'null') continue;
				
				
				// set value
				if( ! isset($field['value']) )
				{	
					$field['value'] = $this->get_value($post_id, $field);
				}
				
				
				$required_class = "";
				$required_label = "";
				
				if( $field['required'] )
				{
					$required_class = ' required';
					$required_label = ' <span class="required">*</span>';
				}
				
				echo '<div id="aki-' . $field['name'] . '" class="field field-' . $field['type'] . ' field-' . $field['key'] . $required_class . '" data-field_name="' . $field['name'] . '" data-field_key="' . $field['key'] . '">';

					echo '<p class="label">';
						echo '<label for="fields[' . $field['key'] . ']">' . $field['label'] . $required_label . '</label>';
						echo $field['instructions'];
					echo '</p>';
					
					$field['name'] = 'fields[' . $field['key'] . ']';
					$this->create_field($field);
				
				echo '</div>';
				
			}
			// foreach($fields as $field)
		}
		// if($fields)
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_input_metabox_ids
	*	- called by function.fields to hide / show metaboxes
	*	
	*	@author Elliot Condon
	*	@since 2.0.5
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_input_metabox_ids( $options = array() )
	{
		// vars
		$defaults = array(
			'post_id' => 0,
			'post_type' => 0,
			'page_template' => 0,
			'page_parent' => 0,
			'page_type' => 0,
			'page' => 0,
			'post' => 0,
			'post_category' => 0,
			'post_format' => 0,
			'taxonomy' => 0,
			'lang' => 0,
			'return' => 'php'
		);
		
		
		// merge in $options
		$options = array_merge($defaults, $options);
		
		
		// merge in $_POST
		if( isset($_POST) )
		{
			$options = array_merge($options, $_POST);
		}
		
		
		// Parse values
		$options = apply_filters( 'aki_parse_value', $options );
		

		// WPML
		if( $options['lang'] )
		{
			global $sitepress;
			$sitepress->switch_lang( $options['lang'] );
		}
		
		
		// find all aki objects
		$akis = apply_filters('aki/get_field_groups', false);
		
		
		// blank array to hold akis
		$return = array();
		
		
		if( $akis )
		{
			foreach( $akis as $aki )
			{
				// vars
				$add_box = false;
				
				
				// if all of the rules are required to match, start at true and let any !$match set $add_box to false
				if( $aki['location']['allorany'] == 'all' )
				{
					$add_box = true;
				}
						
				
				if( $aki['location']['rules'] )
				{
					// defaults
					$rule_defaults = array(
						'param' => '',
						'operator' => '==',
						'value' => ''
					);
					
					foreach($aki['location']['rules'] as $rule)
					{
						// make sure rule has all 3 keys
						$rule = array_merge( $rule_defaults, $rule );
						
						
						// $match = true / false
						$match = false;
						$match = apply_filters( 'aki/location_rules/match/' . $rule['param'] , $match, $rule, $options );
						
						
						if( $aki['location']['allorany'] == 'all' && !$match )
						{
							// if all of the rules are required to match and this rule did not, don't add this box!
							$add_box = false;
						}
						elseif($aki['location']['allorany'] == 'any' && $match )
						{
							// if any of the rules are required to match and this rule did, add this box!
							$add_box = true;
						}
						
						
					}
				}
					
				
				// add ID to array	
				if( $add_box )
				{
					$return[] = $aki['id'];
				}
				
			}
		}
		
		
		// if json
		if( $options['return'] == 'json' )
		{
			echo json_encode($return);
			die;
		}
		
		
		// not json, normal return
		return $return;
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	is_field_unlocked
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	*	@wpsp
	*-------------------------------------------------------------------------------------*/
	
	function is_field_unlocked($field_name)
	{
		/*$hashes = array(
			'repeater'			=> 'bbefed143f1ec106ff3a11437bd73432',
			'options_page'		=> '1fc8b993548891dc2b9a63ac057935d8',
			'flexible_content'	=> 'd067e06c2b4b32b1c1f5b6f00e0d61d6',
			'gallery'			=> '69f4adc9883195bd206a868ffa954b49',
		);
			
		$hash = md5( $this->get_license_key($field_name) );
		
		if( $hashes[$field_name] == $hash )
		{
			return true;
		}*/
		
		return true;
		
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	is_field_unlocked
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_license_key($field_name)
	{
		$value = '';
		
		if( isset( $this->defaults['activation_codes'][ $field_name ] ) )
		{
			$value = $this->defaults['activation_codes'][ $field_name ];
		}
		
		if( !$value )
		{
			$value = get_option('aki_' . $field_name . '_ac');
		}

		return $value;
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_message
	*
	*	@author Elliot Condon
	*	@since 2.0.5
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_message($message = "", $type = 'updated')
	{
		$GLOBALS['aki_mesage'] = $message;
		$GLOBALS['aki_mesage_type'] = $type;
		
		add_action('admin_notices', array($this, 'aki_admin_notice'));
	}
	
	function aki_admin_notice()
	{
	    echo '<div class="' . $GLOBALS['aki_mesage_type'] . '" id="message">'.$GLOBALS['aki_mesage'].'</div>';
	}
		
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_taxonomies_for_select
	*
	*---------------------------------------------------------------------------------------
	*
	*	returns a multidimentional array of taxonomies grouped by the post type / taxonomy
	*
	*	@author Elliot Condon
	*	@since 3.0.2
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_taxonomies_for_select( $args = array() )
	{	
		// vars
		$post_types = get_post_types();
		$choices = array();
		$defaults = array(
			'simple_value'	=>	false,
		);
		
		$options = array_merge($defaults, $args);
		
		
		if($post_types)
		{
			foreach($post_types as $post_type)
			{
				$post_type_object = get_post_type_object($post_type);
				$taxonomies = get_object_taxonomies($post_type);
				if($taxonomies)
				{
					foreach($taxonomies as $taxonomy)
					{
						if(!is_taxonomy_hierarchical($taxonomy)) continue;
						$terms = get_terms($taxonomy, array('hide_empty' => false));
						if($terms)
						{
							foreach($terms as $term)
							{
								$value = $taxonomy . ':' . $term->term_id;
								
								if( $options['simple_value'] )
								{
									$value = $term->term_id;
								}
								
								$choices[$post_type_object->label . ': ' . $taxonomy][$value] = $term->name; 
							}
						}
					}
				}
			}
		}
		
		return $choices;
	}
	
	

	/*
	*  get_all_image_sizes
	*
	*  @description: returns an array holding all the image sizes
	*  @since 3.2.8
	*  @created: 6/07/12
	*/
	
	function get_all_image_sizes()
	{
		// find all sizes
		$all_sizes = get_intermediate_image_sizes();
		
		
		// define default sizes
		$image_sizes = array(
			'thumbnail'	=>	__("Thumbnail",'aki'),
			'medium'	=>	__("Medium",'aki'),
			'large'		=>	__("Large",'aki'),
			'full'		=>	__("Full",'aki')
		);
		
		
		// add extra registered sizes
		foreach($all_sizes as $size)
		{
			if (!isset($image_sizes[$size]))
			{
				$image_sizes[$size] = ucwords( str_replace('-', ' ', $size) );
			}
		}
		
		
		// return array
		return $image_sizes;
	}
	
	
	/*
	*  aki_save_post
	*
	*  @description: 
	*  @created: 4/09/12
	*/
	
	function aki_save_post( $post_id )
	{

		// load from post
		if( !isset($_POST['fields']) )
		{
			return false;
		}
		
		
		// loop through and save
		if( $_POST['fields'] )
		{
			foreach( $_POST['fields'] as $key => $value )
			{
				// get field
				$field = $this->get_aki_field($key);
				
				$this->update_value($post_id, $field, $value);
			}
			// foreach($fields as $key => $value)
		}
		// if($fields)
		
		
		return true;
	}
	
	
	
	/*
	*  get_post_language
	*
	*  @description: finds the translation code for a post
	*  @since 3.3.9
	*  @created: 17/08/12
	*/
	
	/*function get_post_language( $post )
	{
		// global
		global $wpdb;


		// vars
		$table = $wpdb->prefix.'icl_translations';
		$element_type = 'post_' . $post->post_type;
		$element_id = $post->ID;
		
		$lang = $wpdb->get_var("SELECT language_code FROM $table WHERE element_type = '$element_type' AND element_id = '$element_id'");
		
		return ' (' . $lang . ')';
	}*/
	
	
	/*
	*  get_post_types
	*
	*  @description: 
	*  @since: 3.5.5
	*  @created: 16/12/12
	*/
	
	function get_post_types( $exclude = array(), $include = array() )
	{
		// get all custom post types
		$post_types = get_post_types();
		
		
		// core include / exclude
		$aki_includes = array_merge( array(), $include );
		$aki_excludes = array_merge( array( 'aki', 'revision', 'nav_menu_item' ), $exclude );
	 
		
		// include
		foreach( $aki_includes as $p )
		{					
			if( post_type_exists($p) )
			{							
				$post_types[ $p ] = $p;
			}
		}
		
		
		// exclude
		foreach( $aki_excludes as $p )
		{
			unset( $post_types[ $p ] );
		}
	 
		return $post_types;
		
	}
	
	
	/*
	*  get_next_field_id
	*
	*  @description: 
	*  @since: 3.5.5
	*  @created: 31/12/12
	*/
	
	function get_next_field_id()
	{
		// vars
		global $wpdb;
		$exists = true;
		
		
		// get next id
		$next_id = intval( get_option('aki_next_field_id', 1) );
		
			
		// while doesnt exist
		while( $exists == true )
		{
			// get field from postmeta
			$row = $wpdb->get_row($wpdb->prepare(
				"
				SELECT meta_id 
				FROM $wpdb->postmeta 
				WHERE meta_key = %s
				", 
				'field_' . $next_id
			), ARRAY_A );
			
			
			// loop again or break through?
			if( ! $row )
			{
				$exists = false;
			}
			else
			{
				$next_id++;
			}
		}
		
		
		// update the aki_next_field_id
		update_option('aki_next_field_id', ($next_id + 1) );
		
		
		// return
		return $next_id;
	}
	
}
?>
