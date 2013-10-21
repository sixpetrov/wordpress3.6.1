<?php

class aki_Page_link extends aki_Field
{
	
	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function __construct($parent)
	{
    	parent::__construct($parent);
    	
    	$this->name = 'page_link';
		$this->title = __('Page Link','aki');
		
   	}
   	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*
	*	@author Elliot Condon
	*	@since 2.0.5
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		// let post_object create the field
		$field['type'] = 'post_object';
		
		do_action('aki/create_field', $field );

	}
	

	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_options($key, $field)
	{	
		// defaults
		$defaults = array(
			'post_type' 	=>	'',
			'multiple'		=>	0,
			'allow_null'	=>	0,
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
	*	get_value_for_api
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		// get value
		$value = parent::get_value($post_id, $field);
		
		if(!$value)
		{
			return false;
		}
		
		if($value == 'null')
		{
			return false;
		}
		
		if(is_array($value))
		{
			foreach($value as $k => $v)
			{
				$value[$k] = get_permalink($v);
			}
		}
		else
		{
			$value = get_permalink($value);
		}
		
		return $value;
	}
	

	
}

?>