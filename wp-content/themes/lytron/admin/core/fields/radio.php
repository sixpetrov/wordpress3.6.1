<?php

class aki_Radio extends aki_Field
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
    	
    	$this->name = 'radio';
		$this->title = __('Radio Button','aki');
		
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
		// vars
		$defaults = array(
			'layout'		=>	'vertical',
			'choices'		=>	array(),
		);
		
		$field = array_merge($defaults, $field);
		
		
		// no choices
		if( empty($field['choices']) )
		{
			echo '<p>' . __("No choices to choose from",'aki') . '</p>';
			return false;
		}
		
				
		echo '<ul class="radio_list ' . $field['class'] . ' ' . $field['layout'] . '">';
		
		$i = 0;
		foreach($field['choices'] as $key => $value)
		{
			$i++;
			
			// if there is no value and this is the first of the choices and there is no "0" choice, select this on by default
			// the 0 choice would normally match a no value. This needs to remain possible for the create new field to work.
			if(!$field['value'] && $i == 1 && !isset($field['choices'][0]))
			{
				$field['value'] = $key;
			}
			
			$selected = '';
			
			if($key == $field['value'])
			{
				$selected = 'checked="checked" data-checked="checked"';
			}
			
			echo '<li><label><input id="' . $field['id'] . '-' . $key . '" type="radio" name="' . $field['name'] . '" value="' . $key . '" ' . $selected . ' />' . $value . '</label></li>';
		}
		
		echo '</ul>';

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
			'layout'		=>	'vertical',
			'default_value'	=>	'',
			'choices'		=>	'',
		);
		
		$field = array_merge($defaults, $field);
		
		
		// implode checkboxes so they work in a textarea
		if( is_array($field['choices']) )
		{		
			foreach( $field['choices'] as $k => $v )
			{
				$field['choices'][ $k ] = $k . ' : ' . $v;
			}
			$field['choices'] = implode("\n", $field['choices']);
		}
		
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label for=""><?php _e("Choices",'aki'); ?></label>
				<p class="description"><?php _e("Enter your choices one per line",'aki'); ?><br />
				<br />
				<?php _e("Red",'aki'); ?><br />
				<?php _e("Blue",'aki'); ?><br />
				<br />
				<?php _e("red : Red",'aki'); ?><br />
				<?php _e("blue : Blue",'aki'); ?><br />
				</p>
			</td>
			<td>
				<?php
				
				do_action('aki/create_field', array(
					'type'	=>	'textarea',
					'class' => 	'textarea field_option-choices',
					'name'	=>	'fields['.$key.'][choices]',
					'value'	=>	$field['choices'],
				));
				
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Default Value",'aki'); ?></label>
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
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label for=""><?php _e("Layout",'aki'); ?></label>
			</td>
			<td>
				<?php
				
				do_action('aki/create_field', array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][layout]',
					'value'	=>	$field['layout'],
					'layout' => 'horizontal', 
					'choices' => array(
						'vertical' => __("Vertical",'aki'),
						'horizontal' => __("Horizontal",'aki')
					)
				));
				
				?>
			</td>
		</tr>
		<?php
	}

	
	
}

?>