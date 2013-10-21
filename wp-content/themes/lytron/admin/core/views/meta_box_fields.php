<?php

/*
*  Meta Box: Fields
*
*  @description: This file creates the HTML for a list of fields within a Field Group
*  @created: 23/06/12
*/

 
// global
global $post;


// vars
$fields_names = array();


// get fields
$fields = $this->parent->get_aki_fields( $post->ID );


// add clone
$fields[] = array(
	'key' => 'field_clone',
	'label' => __("New Field",'aki'),
	'name' => __("new_field",'aki'),
	'type' => 'text',
	'order_no' =>	1,
	'instructions' =>	'',
	'required' => 0,
	'conditional_logic' => array(
		'status' => 0,
		'allorany' => 'all',
		'rules' => 0
	)
);


// get name of all fields for use in field type drop down
foreach($this->parent->fields as $f)
{
	if( $f->name )
	{
		$fields_names[$f->name] = $f->title;
	}
}


// conditional logic dummy data
$conditional_logic_rule = array(
	'field' => '',
	'operator' => '==',
	'value' => ''
);

?>

<!-- Hidden Fields -->
<div style="display:none;">
	<script type="text/javascript">
	aki.text.move_to_trash = "<?php _e("Move to trash. Are you sure?",'aki'); ?>";
	aki.text.checked = "<?php _e("checked",'aki'); ?>";
	aki.text.conditional_no_fields = "<?php _e('No toggle fields available','aki'); ?>";
	aki.text.flexible_content_no_fields = "<?php _e('Flexible Content requires at least 1 layout','aki'); ?>";
	</script>
	<input type="hidden" name="aki_field_group" value="<?php echo wp_create_nonce( 'aki_field_group' ); ?>" />
</div>
<!-- / Hidden Fields -->


<!-- Fields Header -->
<div class="fields_header">
	<table class="aki widefat">
		<thead>
			<tr>
				<th class="field_order"><?php _e('Field Order','aki'); ?></th>
				<th class="field_label"><?php _e('Field Label','aki'); ?></th>
				<th class="field_name"><?php _e('Field Name','aki'); ?></th>
				<th class="field_type"><?php _e('Field Type','aki'); ?></th>
				<th class="field_key"><?php _e('Field Key','aki'); ?></th>
			</tr>
		</thead>
	</table>
</div>
<!-- / Fields Header -->


<div class="fields">
	
	<!-- No Fields Message -->
	<div class="no_fields_message" <?php if(count($fields) > 1){ echo 'style="display:none;"'; } ?>>
		<?php _e("No fields. Click the <strong>+ Add Field</strong> button to create your first field.",'aki'); ?>
	</div>
	<!-- / No Fields Message -->
	
	<?php foreach($fields as $field): ?>
	<div class="field field-<?php echo $field['type']; ?> field-<?php echo $field['key']; ?>" data-type="<?php echo $field['type']; ?>" data-id="<?php echo $field['key']; ?>">
		<div class="field_meta">
			<table class="aki widefat">
				<tr>
					<td class="field_order"><span class="circle"><?php echo (int)$field['order_no'] + 1; ?></span></td>
					<td class="field_label">
						<strong>
							<a class="aki_edit_field row-title" title="<?php _e("Edit this Field",'aki'); ?>" href="javascript:;"><?php echo $field['label']; ?></a>
						</strong>
						<div class="row_options">
							<span><a class="aki_edit_field" title="<?php _e("Edit this Field",'aki'); ?>" href="javascript:;"><?php _e("Edit",'aki'); ?></a> | </span>
							<span><a title="<?php _e("Read documentation for this field",'aki'); ?>" href="http://www.advancedcustomfields.com/docs/field-types/" target="_blank"><?php _e("Docs",'aki'); ?></a> | </span>
							<span><a class="aki_duplicate_field" title="<?php _e("Duplicate this Field",'aki'); ?>" href="javascript:;"><?php _e("Duplicate",'aki'); ?></a> | </span>
							<span><a class="aki_delete_field" title="<?php _e("Delete this Field",'aki'); ?>" href="javascript:;"><?php _e("Delete",'aki'); ?></a></span>
						</div>
					</td>
					<td class="field_name"><?php echo $field['name']; ?></td>
					<td class="field_type"><?php echo $fields_names[$field['type']]; ?></td>
					<td class="field_key"><?php echo $field['key']; ?></td>
				</tr>
			</table>
		</div>
		<div class="field_form_mask">
			<div class="field_form">
				
				<table class="aki_input widefat aki_field_form_table">
					<tbody>
						<tr class="field_label">
							<td class="label">
								<label><span class="required">*</span><?php _e("Field Label",'aki'); ?></label>
								<p class="description"><?php _e("This is the name which will appear on the EDIT page",'aki'); ?></p>
							</td>
							<td>
								<?php 
								do_action('aki/create_field', array(
									'type'	=>	'text',
									'name'	=>	'fields['.$field['key'].'][label]',
									'value'	=>	$field['label'],
									'class'	=>	'label',
								));
								?>
							</td>
						</tr>
						<tr class="field_name">
							<td class="label">
								<label><span class="required">*</span><?php _e("Field Name",'aki'); ?></label>
								<p class="description"><?php _e("Single word, no spaces. Underscores and dashes allowed",'aki'); ?></p>
							</td>
							<td>
								<?php 
								do_action('aki/create_field', array(
									'type'	=>	'text',
									'name'	=>	'fields['.$field['key'].'][name]',
									'value'	=>	$field['name'],
									'class'	=>	'name',
								));
								?>
							</td>
						</tr>
						<tr class="field_type">
							<td class="label"><label><span class="required">*</span><?php _e("Field Type",'aki'); ?></label></td>
							<td>
								<?php 
								do_action('aki/create_field', array(
									'type'		=>	'select',
									'name'		=>	'fields['.$field['key'].'][type]',
									'value'		=>	$field['type'],
									'choices' 	=>	$fields_names,
								));
								?>
							</td>
						</tr>
						<tr class="field_instructions">
							<td class="label"><label><?php _e("Field Instructions",'aki'); ?></label>
							<p class="description"><?php _e("Instructions for authors. Shown when submitting data",'aki'); ?></p></td>
							<td>
								<?php 
								do_action('aki/create_field', array(
									'type'	=>	'textarea',
									'name'	=>	'fields['.$field['key'].'][instructions]',
									'value'	=>	$field['instructions'],
								));
								?>
							</td>
						</tr>
						<tr class="required">
							<td class="label"><label><?php _e("Required?",'aki'); ?></label></td>
							<td>
								<?php 
								do_action('aki/create_field', array(
									'type'	=>	'radio',
									'name'	=>	'fields['.$field['key'].'][required]',
									'value'	=>	$field['required'],
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
						
						if( isset($this->parent->fields[ $field['type'] ]) )
						{
							$this->parent->fields[$field['type']]->create_options($field['key'], $field);
						}
						
						?>
						<tr class="conditional-logic" data-field_name="<?php echo $field['key']; ?>">
							<td class="label"><label><?php _e("Conditional Logic",'aki'); ?></label></td>
							<td>
								<?php 
								do_action('aki/create_field', array(
									'type'	=>	'radio',
									'name'	=>	'fields['.$field['key'].'][conditional_logic][status]',
									'value'	=>	$field['conditional_logic']['status'],
									'choices'	=>	array(
										1	=>	__("Yes",'aki'),
										0	=>	__("No",'aki'),
									),
									'layout'	=>	'horizontal',
								));
								
								
								// no rules?
								if( ! $field['conditional_logic']['rules'] )
								{
									$field['conditional_logic']['rules'] = array(
										array() // this will get merged with $conditional_logic_rule
									);
								}
								
								?>
								<div class="contional-logic-rules-wrapper" <?php if( ! $field['conditional_logic']['status'] ) echo 'style="display:none"'; ?>>
									<table class="conditional-logic-rules widefat aki-rules <?php if( count($field['conditional_logic']['rules']) == 1) echo 'remove-disabled'; ?>">
										<tbody>
										<?php foreach( $field['conditional_logic']['rules'] as $rule_i => $rule ): 
											
											// validate
											$rule = array_merge($conditional_logic_rule, $rule);
											
											
											// fix PHP error in 3.5.4.1
											if( strpos($rule['value'],'Undefined index: value in') !== false  )
											{
												$rule['value'] = '';
											}
											
											?>
											<tr data-i="<?php echo $rule_i; ?>">
												<td>
													<input class="conditional-logic-field" type="hidden" name="fields[<?php echo $field['key']; ?>][conditional_logic][rules][<?php echo $rule_i; ?>][field]" value="<?php echo $rule['field']; ?>" />
												</td>
												<td width="25%">
													<?php 
													do_action('aki/create_field', array(
														'type'	=>	'select',
														'name'	=>	'fields['.$field['key'].'][conditional_logic][rules][' . $rule_i . '][operator]',
														'value'	=>	$rule['operator'],
														'choices'	=>	array(
															'=='	=>	__("is equal to",'aki'),
															'!='	=>	__("is not equal to",'aki'),
														),
													));
													?>
												</td>
												<td><input class="conditional-logic-value" type="hidden" name="fields[<?php echo $field['key']; ?>][conditional_logic][rules][<?php echo $rule_i; ?>][value]" value="<?php echo $rule['value']; ?>" /></td>
												<td class="buttons">
													<ul class="hl clearfix">
														<li><a class="aki-button-remove" href="javascript:;"></a></li>
														<li><a class="aki-button-add" href="javascript:;"></a></li>
													</ul>
												</td>
											</tr>	
										<?php endforeach; ?>
										</tbody>
									</table>
									
									<ul class="hl clearfix">
										<li style="padding:4px 4px 0 0;"><?php _e("Show this field when",'aki'); ?></li>
										<li><?php do_action('aki/create_field', array(
												'type'	=>	'select',
												'name'	=>	'fields['.$field['key'].'][conditional_logic][allorany]',
												'value'	=>	$field['conditional_logic']['allorany'],
												'choices' => array(
													'all'	=>	__("all",'aki'),
													'any'	=>	__("any",'aki'),
												),
										)); ?></li>
										<li style="padding:4px 0 0 4px;"><?php _e("these rules are met",'aki'); ?></li>
									</ul>
									
								</div>
								

								
							</td>
						</tr>
						<tr class="field_save">
							<td class="label"></td>
							<td>
								<ul class="hl clearfix">
									<li>
										<a class="aki_edit_field aki-button grey" title="<?php _e("Close Field",'aki'); ?>" href="javascript:;"><?php _e("Close Field",'aki'); ?></a>
									</li>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>	
	</div>
	<?php endforeach; ?>
</div>
<div class="table_footer">
	<div class="order_message"><?php _e('Drag and drop to reorder','aki'); ?></div>
	<a href="javascript:;" id="add_field" class="aki-button"><?php _e('+ Add Field','aki'); ?></a>
</div>