<?php

class aki_Color_picker extends aki_Field
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
    	
    	$this->name = 'color_picker';
		$this->title = __("Color Picker",'aki');
		
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
		// html
		echo '<input type="text" value="' . $field['value'] . '" class="aki_color_picker" name="' . $field['name'] . '" id="' . $field['id'] . '" />';

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
		// vars
		$defaults = array(
			'default_value'	=>	'',
		);
		
		$field = array_merge($defaults, $field);

		
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Default Value",'aki'); ?></label>
				<p class="description"><?php _e("eg: #ffffff",'aki'); ?></p>
			</td>
			<td>
				<?php 
				do_action('aki/create_field', array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][default_value]',
					'value'	=>	$field['default_value'],
				));
				?>
			</td>
		</tr>
		<?php
	}
	
	
}

?>