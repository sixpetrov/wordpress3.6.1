<?php

class aki_Textarea extends aki_Field
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
    	
    	$this->name = 'textarea';
		$this->title = __("Text Area",'aki');
		
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
		// remove unwanted <br /> tags
		//$field['value'] = str_replace('<br />','',$field['value']);
		$field['value'] = esc_textarea($field['value']);
		
		//echo '<textarea id="' . $field['id'] . '" rows="4" class="' . $field['class'] . '" name="' . $field['name'] . '" >' . $field['value'] . '</textarea>';
		echo '<textarea id="' . $field['id'] . '" rows="4" class="' . $field['class'] . '" name="' . $field['name'] . '" maxlength="'. $field['maxlength'] .'" placeholder="'. $field['placeholder'] .'" >' . $field['value'] . '</textarea>';
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_options($key, $field)
	{
		// defaults
		$field['default_value'] = isset($field['default_value']) ? $field['default_value'] : '';
		$field['formatting'] = isset($field['formatting']) ? $field['formatting'] : 'br';

        //maxlength
        $field['maxlength'] = isset($field['maxlength']) ? $field['maxlength'] : '';

        //placeholder
        $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
		
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Default Value",'aki'); ?></label>
			</td>
			<td>
				<?php 
				do_action('aki/create_field', array(
					'type'	=>	'textarea',
					'name'	=>	'fields['.$key.'][default_value]',
					'value'	=>	$field['default_value'],
				));
				?>
			</td>
		</tr>

        <!-- Maxlength - Chars limit -->
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e("Character Limit",'aki'); ?></label>
                <p><?php _e("Leave blank for no limit",'aki') ?></p>
            </td>
            <td>
                <?php
                do_action('aki/create_field', array(
                    'type'	=>	'number',
                    'name'	=>	'fields[' .$key.'][maxlength]',
                    'value'	=>	$field['maxlength'],
                ));
                ?>
            </td>
        </tr>

        <!-- Placeholder -->
        <tr class="field_option field_option_<?php echo $this->name; ?>">
            <td class="label">
                <label><?php _e("Placeholder Text",'aki'); ?></label>
                <p><?php _e("Appears within the input",'aki') ?></p>
            </td>
            <td>
                <?php
                do_action('aki/create_field', array(
                    'type'	=>	'text',
                    'name'	=>	'fields[' .$key.'][placeholder]',
                    'value'	=>	$field['placeholder'],
                ));
                ?>
            </td>
        </tr>

		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Formatting",'aki'); ?></label>
				<p class="description"><?php _e("Define how to render html tags / new lines",'aki'); ?></p>
			</td>
			<td>
				<?php 
				do_action('aki/create_field', array(
					'type'	=>	'select',
					'name'	=>	'fields['.$key.'][formatting]',
					'value'	=>	$field['formatting'],
					'choices' => array(
						'none'	=>	__("None",'aki'),
						'br'	=>	__("auto &lt;br /&gt;",'aki'),
						'html'	=>	__("HTML",'aki'),
					)
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
		// vars
		$defaults = array(
			'formatting'	=>	'br',
		);
		
		$field = array_merge($defaults, $field);
		
		
		// load value
		$value = parent::get_value($post_id, $field);
		
		
		// validate type
		if( !is_string($value) )
		{
			return $value;
		}
		
		
		if($field['formatting'] == 'none')
		{
			$value = htmlspecialchars($value, ENT_QUOTES);
		}
		elseif($field['formatting'] == 'html')
		{
			//$value = html_entity_decode($value);
			//$value = nl2br($value);
		}
		elseif($field['formatting'] == 'br')
		{
			$value = htmlspecialchars($value, ENT_QUOTES);
			$value = nl2br($value);
		}

		return $value;
	}
}

?>