<?php

class aki_Flexible_content extends aki_Field
{

	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*	- $parent is passed buy reference so you can play with the aki functions
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function __construct($parent)
	{
    	parent::__construct($parent);
    	
    	$this->name = 'flexible_content';
		$this->title = __("Flexible Content",'aki');
		
		
		// filters
		add_filter('aki_save_field-' . $this->name, array($this, 'aki_save_field'));
		add_filter('aki_load_field-' . $this->name, array($this, 'aki_load_field'));
   	}
   	
   	
   	/*
	*  aki_load_field
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 21/02/13
	*/
	
	function aki_load_field( $field )
	{
		// apply_load field to all sub fields
		if( isset($field['layouts']) && is_array($field['layouts']) )
		{
			foreach( $field['layouts'] as $k => $layout )
			{
				if( isset($layout['sub_fields']) && is_array($layout['sub_fields']) )
				{
					foreach( $layout['sub_fields'] as $i => $sub_field )
					{
						// apply filters
						$sub_field = apply_filters('aki_load_field', $sub_field);
						
						
						$keys = array('type', 'name', 'key');
						$called = array(); // field[type] && field[name] may be the same! Don't run the same filter twice!
						foreach( $keys as $key )
						{
							// validate
							if( !isset($field[ $key ]) ){ continue; }
							if( in_array($field[ $key ], $called) ){ continue; }
							
							
							// add to $called
							$action = $field[ $key ] . '-' . $layout['name'] . '-' . $sub_field[ $key ];
							$called[] = $action;
							
							
							// run filters
							$sub_field = apply_filters('aki_load_field-' . $action, $sub_field); // old filter
							
						}
						
						
						// update sub field
						$field['layouts'][ $k ]['sub_fields'][ $i ] = $sub_field;
						
					}
					// foreach( $layout['sub_fields'] as $i => $sub_field )
				}
				// if( isset($layout['sub_fields']) && is_array($layout['sub_fields']) )
			}
			// foreach( $field['layouts'] as $k => $layout )
		}
		// if( isset($field['layouts']) && is_array($field['layouts']) )
		
		return $field;
		
		return $field;
	}
	

	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*	- called in lots of places to create the html version of the field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		$button_label = ( isset($field['button_label']) && $field['button_label'] != "" ) ? $field['button_label'] : __("+ Add Row",'aki');
		$layouts = array();
		foreach($field['layouts'] as $l)
		{
			$layouts[$l['name']] = $l;
		}
		
		?>
		<div class="aki_flexible_content">
			
			<div class="no_value_message" <?php if($field['value']){echo 'style="display:none;"';} ?>>
				<?php _e("Click the \"$button_label\" button below to start creating your layout",'aki'); ?>
			</div>
			
			<div class="clones">
			<?php $i = -1; ?>
			<?php foreach($layouts as $layout): $i++; ?>
			
				<div class="layout" data-layout="<?php echo $layout['name']; ?>">
					
					<input type="hidden" name="<?php echo $field['name']; ?>[akicloneindex][aki_fc_layout]" value="<?php echo $layout['name']; ?>" />
					
					<a class="ir fc-delete-layout" href="#"></a>
					<p class="menu-item-handle"><span class="fc-layout-order"><?php echo $i+1; ?></span>. <?php echo $layout['label']; ?></p>
					
					<table class="widefat aki-input-table <?php if( $layout['display'] == 'row' ): ?>row_layout<?php endif; ?>">
						<?php if( $layout['display'] == 'table' ): ?>
							<thead>
								<tr>
									<?php foreach( $layout['sub_fields'] as $sub_field_i => $sub_field): 
										
										// add width attr
										$attr = "";
										
										if( count($layout['sub_fields']) > 1 && isset($sub_field['column_width']) && $sub_field['column_width'] )
										{
											$attr = 'width="' . $sub_field['column_width'] . '%"';
										}
										
										?>
										<th class="aki-th-<?php echo $sub_field['name']; ?>" <?php echo $attr; ?>>
											<span><?php echo $sub_field['label']; ?></span>
											<?php if( isset($sub_field['instructions']) ): ?>
												<span class="sub-field-instructions"><?php echo $sub_field['instructions']; ?></span>
											<?php endif; ?>
										</th><?php
									endforeach; ?>
								</tr>
							</thead>
						<?php endif; ?>
						<tbody>
							<tr>
							<?php
		
							// layout: Row
							
							if( $layout['display'] == 'row' ): ?>
								<td class="aki_input-wrap">
									<table class="widefat aki_input">
							<?php endif; ?>
							
							
							<?php
		
							// loop though sub fields
							
							foreach( $layout['sub_fields'] as $j => $sub_field ): ?>
							
								<?php
							
								// layout: Row
								
								if( $layout['display'] == 'row' ): ?>
									<tr>
										<td class="label">
											<label><?php echo $sub_field['label']; ?></label>
											<?php if( isset($sub_field['instructions']) ): ?>
												<span class="sub-field-instructions"><?php echo $sub_field['instructions']; ?></span>
											<?php endif; ?>
										</td>
								<?php endif; ?>
								
								<td>
									<?php
									
									// add value
									$sub_field['value'] = isset($sub_field['default_value']) ? $sub_field['default_value'] : false;
									
									// add name
									$sub_field['name'] = $field['name'] . '[akicloneindex][' . $sub_field['key'] . ']';
									
									// create field
									do_action('aki/create_field', $sub_field);
									
									?>
								</td>
								
								<?php
							
								// layout: Row
								
								if( $layout['display'] == 'row' ): ?>
									</tr>
								<?php endif; ?>
								
							
							<?php endforeach; ?>
							
							<?php
	
							// layout: Row
							
							if( $layout['display'] == 'row' ): ?>
									</table>
								</td>
							<?php endif; ?>
															
							</tr>
						</tbody>
						
					</table>
				</div>
			<?php endforeach; ?>
			</div>
			<div class="values">
				<?php 
				
				if($field['value']):
					
					foreach($field['value'] as $i => $value):
						
						// validate layout
						if( !isset($layouts[$value['aki_fc_layout']]) )
						{
							continue;
						}
						
						
						// vars
						$layout = $layouts[$value['aki_fc_layout']];
						
						
						?>
						<div class="layout" data-layout="<?php echo $layout['name']; ?>">
							
							<input type="hidden" name="<?php echo $field['name'] ?>[<?php echo $i ?>][aki_fc_layout]" value="<?php echo $layout['name']; ?>" />
							
							<a class="ir fc-delete-layout" href="#"></a>
							<p class="menu-item-handle"><span class="fc-layout-order"><?php echo $i+1; ?></span>. <?php echo $layout['label']; ?></p>
							
							
							<table class="widefat aki-input-table <?php if( $layout['display'] == 'row' ): ?>row_layout<?php endif; ?>">
							<?php if( $layout['display'] == 'table' ): ?>
								<thead>
									<tr>
										<?php foreach( $layout['sub_fields'] as $sub_field_i => $sub_field): 
											
											// add width attr
											$attr = "";
											
											if( count($layout['sub_fields']) > 1 && isset($sub_field['column_width']) && $sub_field['column_width'] )
											{
												$attr = 'width="' . $sub_field['column_width'] . '%"';
											}
											
											?>
											<th class="aki-th-<?php echo $sub_field['name']; ?>" <?php echo $attr; ?>>
												<span><?php echo $sub_field['label']; ?></span>
												<?php if( isset($sub_field['instructions']) ): ?>
													<span class="sub-field-instructions"><?php echo $sub_field['instructions']; ?></span>
												<?php endif; ?>
											</th><?php
										endforeach; ?>
									</tr>
								</thead>
							<?php endif; ?>
							<tbody>
								<tr>
								<?php
			
								// layout: Row
								
								if( $layout['display'] == 'row' ): ?>
									<td class="aki_input-wrap">
										<table class="widefat aki_input">
								<?php endif; ?>
								
								
								<?php
			
								// loop though sub fields
								
								foreach( $layout['sub_fields'] as $j => $sub_field ): ?>
								
									<?php
								
									// layout: Row
									
									if( $layout['display'] == 'row' ): ?>
										<tr>
											<td class="label">
												<label><?php echo $sub_field['label']; ?></label>
												<?php if( isset($sub_field['instructions']) ): ?>
													<span class="sub-field-instructions"><?php echo $sub_field['instructions']; ?></span>
												<?php endif; ?>
											</td>
									<?php endif; ?>
									
									<td>
										<?php
										
										// add value
										$sub_field['value'] = isset($value[$sub_field['key']]) ? $value[$sub_field['key']] : false;
										
										// add name
										$sub_field['name'] = $field['name'] . '[' . $i . '][' . $sub_field['key'] . ']';
										
										// create field
										do_action('aki/create_field', $sub_field);
										
										?>
									</td>
									
									<?php
								
									// layout: Row
									
									if( $layout['display'] == 'row' ): ?>
										</tr>
									<?php endif; ?>
									
								
								<?php endforeach; ?>
								
								<?php
		
								// layout: Row
								
								if( $layout['display'] == 'row' ): ?>
										</table>
									</td>
								<?php endif; ?>
																
								</tr>
							</tbody>
							
						</table>
						</div>
					<?php
					
					endforeach; 
					// foreach($field['value'] as $i => $value)
					
				endif; 
				// if($field['value']): 
				
				?>
			</div>

			<ul class="hl clearfix flexible-footer">
				<li class="right">
					<a href="javascript:;" class="add-row-end aki-button"><?php echo $button_label; ?></a>
					<div class="aki-popup">
						<ul>
							<?php foreach($field['layouts'] as $layout): $i++; ?>
							<li><a href="javascript:;" data-layout="<?php echo $layout['name']; ?>"><?php echo $layout['label']; ?></a></li>
							<?php endforeach; ?>
						</ul>
						<div class="bit"></div>
					</div>
				</li>
			</ul>

		</div>
		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*	- called from core/field_meta_box.php to create special options
	*
	*	@params : 	$key (int) - neccessary to group field data together for saving
	*				$field (array) - the field data from the database
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_options($key, $field)
	{
		// vars
		$fields_names = array();
		$defaults = array(
			'layouts' 		=> array(),
			'button_label'	=>	__("Add Row",'aki'),
		);
		
		$field = array_merge($defaults, $field);
		
				
		// load default layout
		if(empty($field['layouts']))
		{
			$field['layouts'][] = array(
				'name' => '',
				'label' => '',
				'display' => 'row',
				'sub_fields' => array(),
			);
		}
		
		
		// get name of all fields for use in field type
		foreach($this->parent->fields as $f)
		{
			if( $f->name )
			{
				$fields_names[$f->name] = $f->title;
			}
		}
		unset( $fields_names['flexible_content'], $fields_names['tab'] );
		
		
		// loop through layouts and create the options for them
		if($field['layouts']):
		foreach($field['layouts'] as $layout_key => $layout):
		
			$layout['sub_fields'][] = array(
				'key' => 'field_clone',
				'label' => __("New Field",'aki'),
				'name' => __("new_field",'aki'),
				'type' => 'text',
				'order_no' =>	1,
				'instructions' =>	'',
			);
			
?>
<tr class="field_option field_option_<?php echo $this->name; ?>" data-id="<?php echo $layout_key; ?>">
	<td class="label">
		<label><?php _e("Layout",'aki'); ?></label>
		<p class="desription">
			<span><a class="aki_fc_reorder" title="<?php _e("Reorder Layout",'aki'); ?>" href="javascript:;"><?php _e("Reorder",'aki'); ?></a> | </span>
			<span><a class="aki_fc_delete" title="<?php _e("Delete Layout",'aki'); ?>" href="javascript:;"><?php _e("Delete",'aki'); ?></a>
			
			<br />
			
			<span><a class="aki_fc_add" title="<?php _e("Add New Layout",'aki'); ?>" href="javascript:;"><?php _e("Add New",'aki'); ?></a> | </span>
			<span><a class="aki_fc_duplicate" title="<?php _e("Duplicate Layout",'aki'); ?>" href="javascript:;"><?php _e("Duplicate",'aki'); ?></a></span>
		</p>
	</td>
	<td>
	<div class="repeater">
		
		<table class="aki_cf_meta">
			<tbody>
				<tr>
					<td class="aki_fc_label" style="padding-left:0;">
						<label><?php _e('Label','aki'); ?></label>
						<?php 
						do_action('aki/create_field', array(
							'type'	=>	'text',
							'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][label]',
							'value'	=>	$layout['label'],
						));
						?>
					</td>
					<td class="aki_fc_name">
						<label><?php _e('Name','aki'); ?></label>
						<?php 
						do_action('aki/create_field', array(
							'type'	=>	'text',
							'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][name]',
							'value'	=>	$layout['name'],
						));
						?>
					</td>
					<td class="aki_fc_display" style="padding-right:0;">
						<label><?php _e('Display','aki'); ?></label>
						<?php 
						do_action('aki/create_field', array(
							'type'	=>	'select',
							'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][display]',
							'value'	=>	$layout['display'],
							'choices'	=>	array(
								'row' => __("Row",'aki'),
								'table' => __("Table",'aki'),
							)
						));
						?>
					</td>
				</tr>
			</tbody>
		</table>
					
		<div class="fields_header">
			<table class="aki widefat">
				<thead>
					<tr>
						<th class="field_order"><?php _e('Field Order','aki'); ?></th>
						<th class="field_label"><?php _e('Field Label','aki'); ?></th>
						<th class="field_name"><?php _e('Field Name','aki'); ?></th>
						<th class="field_type"><?php _e('Field Type','aki'); ?></th>
					</tr>
				</thead>
			</table>
		</div>
		<div class="fields">
			
			<div class="no_fields_message" <?php if(count($layout['sub_fields']) > 1){ echo 'style="display:none;"'; } ?>>
				<?php _e("No fields. Click the \"+ Add Sub Field button\" to create your first field.",'aki'); ?>
			</div>
	
			<?php foreach($layout['sub_fields'] as $sub_field): ?>
				<div class="field field-<?php echo $sub_field['key']; ?> sub_field" data-id="<?php echo $sub_field['key']; ?>">
					<div class="field_meta">
					<table class="aki widefat">
						<tr>
							<td class="field_order"><span class="circle"><?php echo (int)$sub_field['order_no'] + 1; ?></span></td>
							<td class="field_label">
								<strong>
									<a class="aki_edit_field" title="<?php _e("Edit this Field",'aki'); ?>" href="javascript:;"><?php echo $sub_field['label']; ?></a>
								</strong>
								<div class="row_options">
									<span><a class="aki_edit_field" title="<?php _e("Edit this Field",'aki'); ?>" href="javascript:;"><?php _e("Edit",'aki'); ?></a> | </span>
									<span><a title="<?php _e("Read documentation for this field",'aki'); ?>" href="http://www.advancedcustomfields.com/docs/field-types/" target="_blank"><?php _e("Docs",'aki'); ?></a> | </span>
									<span><a class="aki_duplicate_field" title="<?php _e("Duplicate this Field",'aki'); ?>" href="javascript:;"><?php _e("Duplicate",'aki'); ?></a> | </span>
									<span><a class="aki_delete_field" title="<?php _e("Delete this Field",'aki'); ?>" href="javascript:;"><?php _e("Delete",'aki'); ?></a>
								</div>
							</td>
							<td class="field_name"><?php echo $sub_field['name']; ?></td>
							<td class="field_type"><?php echo $sub_field['type']; ?></td>
						</tr>
					</table>
					</div>
					
					<div class="field_form_mask">
					<div class="field_form">
						<table class="aki_input widefat">
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
											'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][sub_fields]['.$sub_field['key'].'][label]',
											'value'	=>	$sub_field['label'],
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
											'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][sub_fields]['.$sub_field['key'].'][name]',
											'value'	=>	$sub_field['name'],
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
											'type'	=>	'select',
											'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][sub_fields]['.$sub_field['key'].'][type]',
											'value'	=>	$sub_field['type'],
											'class'	=>	'type',
											'choices'	=>	$fields_names
										));
										?>
									</td>
								</tr>
								<tr class="field_instructions">
									<td class="label"><label><?php _e("Field Instructions",'aki'); ?></label></td>
									<td>
										<?php
										
										if( !isset($sub_field['instructions']) )
										{
											$sub_field['instructions'] = "";
										}
										
										do_action('aki/create_field', array(
											'type'	=>	'text',
											'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][sub_fields]['.$sub_field['key'].'][instructions]',
											'value'	=>	$sub_field['instructions'],
											'class'	=>	'instructions',
										));
										?>
									</td>
								</tr>
								<tr class="field_column_width">
									<td class="label">
										<label><?php _e("Column Width",'aki'); ?></label>
										<p class="description"><?php _e("Leave blank for auto",'aki'); ?></p>
									</td>
									<td>
										<?php 
										
										if( !isset($sub_field['column_width']) )
										{
											$sub_field['column_width'] = "";
										}
										
										do_action('aki/create_field', array(
											'type'	=>	'number',
											'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][sub_fields]['.$sub_field['key'].'][column_width]',
											'value'	=>	$sub_field['column_width'],
											'class'	=>	'column_width',
										));
										?> %
									</td>
								</tr>
								<?php 
								
								if( isset($this->parent->fields[ $sub_field['type'] ]) )
								{
									$this->parent->fields[$sub_field['type']]->create_options($key.'][layouts][' . $layout_key . '][sub_fields]['.$sub_field['key'], $sub_field);
								}
								
								?>
								<tr class="field_save">
									<td class="label">
										<!-- <label><?php _e("Save Field",'aki'); ?></label> -->
									</td>
									<td>
										<ul class="hl clearfix">
											<li>
												<a class="aki_edit_field aki-button grey" title="<?php _e("Close Field",'aki'); ?>" href="javascript:;"><?php _e("Close Sub Field",'aki'); ?></a>
											</li>
										</ul>
									</td>
								</tr>								
							</tbody>
						</table>
					</div><!-- End Form -->
					</div><!-- End Form Mask -->
				
				</div>
			<?php endforeach; ?>
		</div>
		<div class="table_footer">
			<div class="order_message"><?php _e('Drag and drop to reorder','aki'); ?></div>
			<a href="javascript:;" id="add_field" class="aki-button"><?php _e('+ Add Sub Field','aki'); ?></a>
		</div>
	</div>
	</td>
</tr><?php endforeach; endif; ?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Button Label",'aki'); ?></label>
	</td>
	<td>
		<?php 
		do_action('aki/create_field', array(
			'type'	=>	'text',
			'name'	=>	'fields['.$key.'][button_label]',
			'value'	=>	$field['button_label'],
		));
		?>
	</td>
</tr><?php
  	}
	

	/*--------------------------------------------------------------------------------------
	*
	*	update_value
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function update_value($post_id, $field, $value)
	{
		$sub_fields = array();
		
		foreach($field['layouts'] as $layout)
		{
			foreach($layout['sub_fields'] as $sub_field)
			{
				$sub_fields[$sub_field['key']] = $sub_field;
			}
		}

		$total = array();
		
		if($value)
		{
			// remove dummy field
			unset($value['akicloneindex']);
			
			$i = -1;
			
			// loop through rows
			foreach($value as $row)
			{	
				$i++;
				
				// increase total
				$total[] = $row['aki_fc_layout'];
				unset($row['aki_fc_layout']);
					
				// loop through sub fields
				foreach($row as $field_key => $v)
				{
					$sub_field = $sub_fields[$field_key];

					// update full name
					$sub_field['name'] = $field['name'] . '_' . $i . '_' . $sub_field['name'];
					
					// save sub field value
					$this->parent->update_value($post_id, $sub_field, $v);
				}
			}
		}
		
		
		/*
		*  Remove Old Data
		*
		*  @credit: http://support.advancedcustomfields.com/discussion/1994/deleting-single-repeater-fields-does-not-remove-entry-from-database
		*/
		
		$old_total = parent::get_value($post_id, $field);
		$old_total = count( $old_total );
		$new_total = count( $total );

		if( $old_total > $new_total )
		{
			foreach( $sub_fields as $sub_field )
			{
				for ( $j = $new_total; $j < $old_total; $j++ )
				{ 
					parent::delete_value( $post_id, $field['name'] . '_' . $j . '_' . $sub_field['name'] );
				}
			}
		}
		
		parent::update_value($post_id, $field, $total);
		
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

		// format sub_fields
		if($field['layouts'])
		{
			// loop through and save fields
			foreach($field['layouts'] as $layout_key => $layout)
			{				
			
				if( $layout['sub_fields'] )
				{
					// remove dummy field
					unset( $layout['sub_fields']['field_clone'] );
				
				
					// loop through and save fields
					$i = -1;
					$sub_fields = array();
					
					
					foreach( $layout['sub_fields'] as $key => $f )
					{
						$i++;
						
						
						// order
						$f['order_no'] = $i;
						$f['key'] = $key;
						
						
						// apply filters
						$f = apply_filters('aki_save_field', $f );
						$f = apply_filters('aki_save_field-' . $f['type'], $f );
						
						
						$sub_fields[ $f['key'] ] = $f;
						
					}
					
					$layout['sub_fields'] = $sub_fields;
				}
				
				// update $layout
				$field['layouts'][ $layout_key ] = $layout;
				
			}
		}
		
		// return updated repeater field
		return $field;

	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value
	*	- called from the input edit page to get the value.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value($post_id, $field)
	{
		$layouts = array();
		foreach($field['layouts'] as $l)
		{
			$layouts[$l['name']] = $l;
		}

		// vars
		$values = array();
		$layout_order = false;
		
		
		// get total rows
		$layout_order = parent::get_value($post_id, $field);
		

		if( !empty( $layout_order) )
		{
			$i = -1;
			// loop through rows
			foreach($layout_order as $layout)
			{
				$i++;
				$values[$i]['aki_fc_layout'] = $layout;
				
				// check if layout still exists
				if(isset($layouts[$layout]))
				{
					// loop through sub fields
					foreach($layouts[$layout]['sub_fields'] as $sub_field)
					{
						// update full name
						$sub_field['name'] = $field['name'] . '_' . $i . '_' . $sub_field['name'];
						
						$values[$i][ $sub_field['key'] ] = $this->parent->get_value($post_id, $sub_field);
					}
				}
			}
		}
		else
		{
			$values = false;
		}

		return $values;	
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
		$layouts = array();
		foreach($field['layouts'] as $l)
		{
			$layouts[$l['name']] = $l;
		}

		// vars
		$values = array();
		$layout_order = false;
		
		
		// get total rows
		$layout_order = parent::get_value($post_id, $field);
		

		if($layout_order)
		{
			$i = -1;
			// loop through rows
			foreach($layout_order as $layout)
			{
				$i++;
				$values[$i]['aki_fc_layout'] = $layout;
				
				// loop through sub fields
				foreach($layouts[$layout]['sub_fields'] as $sub_field)
				{
					// store name
					$field_name = $sub_field['name'];
					
					// update full name
					$sub_field['name'] = $field['name'] . '_' . $i . '_' . $field_name;
					
					$values[$i][$field_name] = $this->parent->get_value_for_api($post_id, $sub_field);
				}
			}
			
			return $values;
		}
		
		return array();	
	}
	
}

?>