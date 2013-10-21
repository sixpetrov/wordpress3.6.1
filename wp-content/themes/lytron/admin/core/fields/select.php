<?php

class aki_Select extends aki_Field
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
    	
    	$this->name = 'select';
		$this->title = __("Select",'aki');
		
		
		// filters (for all fields with choices)
		add_filter('aki_save_field-select', array($this, 'aki_save_field'));
		add_filter('aki_save_field-checkbox', array($this, 'aki_save_field'));
		add_filter('aki_save_field-radio', array($this, 'aki_save_field'));
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
			'value'			=>	array(),
			'multiple' 		=>	0,
			'allow_null' 	=>	0,
			'choices'		=>	array(),
			'optgroup'		=>	0,
		);
		
		$field = array_merge($defaults, $field);

		
		// no choices
		if(empty($field['choices']))
		{
			echo '<p>' . __("No choices to choose from",'aki') . '</p>';
			return false;
		}
		
		
		// multiple select
		$multiple = '';
		if( $field['multiple'] )
		{
			// create a hidden field to allow for no selections
			echo '<input type="hidden" name="' . $field['name'] . '" />';
			
			$multiple = ' multiple="multiple" size="5" ';
			$field['name'] .= '[]';
		} 
		
		
		// html
		echo '<select id="' . $field['id'] . '" class="' . $field['class'] . '" name="' . $field['name'] . '" ' . $multiple . ' >';	
		
		
		// null
		if( $field['allow_null'] )
		{
			echo '<option value="null"> - Select - </option>';
		}
		
		// loop through values and add them as options
		foreach($field['choices'] as $key => $value)
		{
			if($field['optgroup'])
			{
				// this select is grouped with optgroup
				if($key != '') echo '<optgroup label="'.$key.'">';
				
				if($value)
				{
					foreach($value as $id => $label)
					{
						$selected = '';
						if(is_array($field['value']) && in_array($id, $field['value']))
						{
							// 2. If the value is an array (multiple select), loop through values and check if it is selected
							$selected = 'selected="selected"';
						}
						else
						{
							// 3. this is not a multiple select, just check normaly
							if($id == $field['value'])
							{
								$selected = 'selected="selected"';
							}
						}	
						echo '<option value="'.$id.'" '.$selected.'>'.$label.'</option>';
					}
				}
				
				if($key != '') echo '</optgroup>';
			}
			else
			{
				$selected = '';
				if(is_array($field['value']) && in_array($key, $field['value']))
				{
					// 2. If the value is an array (multiple select), loop through values and check if it is selected
					$selected = 'selected="selected"';
				}
				else
				{
					// 3. this is not a multiple select, just check normaly
					if($key == $field['value'])
					{
						$selected = 'selected="selected"';
					}
				}	
				echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			}


			
		}

		echo '</select>';
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
			'multiple'		=>	0,
			'allow_null'	=>	0,
			'default_value' => '',
			'choices'		=>	'',
		);
		
		$field = array_merge($defaults, $field);
		
				
		// implode selects so they work in a textarea
		if(isset($field['choices']) && is_array($field['choices']))
		{		
			foreach($field['choices'] as $choice_key => $choice_val)
			{
				$field['choices'][$choice_key] = $choice_key.' : '.$choice_val;
			}
			$field['choices'] = implode("\n", $field['choices']);
		}
		else
		{
			$field['choices'] = "";
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
	*	- called just before saving the field to the database.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function aki_save_field( $field )
	{
		// vars
		$defaults = array(
			'choices'	=>	'',
		);
		
		$field = array_merge($defaults, $field);
		
		
		// check if is array. Normal back end edit posts a textarea, but a user might use update_field from the front end
		if( is_array( $field['choices'] ))
		{
		    return $field;
		}

		
		// vars
		$new_choices = array();
		
		
		// explode choices from each line
		if( $field['choices'] )
		{
			// stripslashes ("")
			$field['choices'] = stripslashes_deep($field['choices']);
		
			if(strpos($field['choices'], "\n") !== false)
			{
				// found multiple lines, explode it
				$field['choices'] = explode("\n", $field['choices']);
			}
			else
			{
				// no multiple lines! 
				$field['choices'] = array($field['choices']);
			}
			
			
			// key => value
			foreach($field['choices'] as $choice)
			{
				if(strpos($choice, ' : ') !== false)
				{
					$choice = explode(' : ', $choice);
					$new_choices[trim($choice[0])] = trim($choice[1]);
				}
				else
				{
					$new_choices[trim($choice)] = trim($choice);
				}
			}
		}
		
		
		
		// update choices
		$field['choices'] = $new_choices;
		
		
		// return updated field
		return $field;

	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*
	*	@author Elliot Condon
	*	@since 3.1.2
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		$value = parent::get_value($post_id, $field);
		
		if($value == 'null')
		{
			$value = false;
		}
		
		return $value;
	}
	
}

?>