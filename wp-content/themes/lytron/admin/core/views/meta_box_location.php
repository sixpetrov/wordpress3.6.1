<?php

/*
*  Meta Box: Location
*
*  @description: 
*  @created: 23/06/12
*/

// global
global $post;
		
		
// vars
$location = $this->parent->get_aki_location($post->ID);


// at lease 1 location rule
if( empty($location['rules']) )
{
	$location['rules'] = array(
		array(
			'param'		=>	'post_type',
			'operator'	=>	'',
			'value'		=>	'',
		)
	);
}

?>
<table class="aki_input widefat" id="aki_location">
	<tbody>
	<tr>
		<td class="label">
			<label for="post_type"><?php _e("Rules",'aki'); ?></label>
			<p class="description"><?php _e("Create a set of rules to determine which edit screens will use these advanced custom fields",'aki'); ?></p>
		</td>
		<td>
			<div class="location_rules">
				<table class="aki_input widefat aki-rules <?php if( count($location['rules']) == 1) echo 'remove-disabled'; ?>" id="location_rules">
					<tbody>
						<?php foreach($location['rules'] as $k => $rule): ?>
						<tr data-i="<?php echo $k; ?>">
						<td class="param"><?php 
							
							$choices = array(
								__("Basic",'aki') => array(
									'post_type'		=>	__("Post Type",'aki'),
									'user_type'		=>	__("Logged in User Type",'aki'),
								),
								__("Page",'aki') => array(
									'page'			=>	__("Page",'aki'),
									'page_type'		=>	__("Type",'aki'),
									'page_parent'	=>	__("Parent",'aki'),
									'page_template'	=>	__("Template",'aki'),
								),
								__("Post",'aki') => array(
									'post'			=>	__("Post",'aki'),
									'post_category'	=>	__("Category",'aki'),
									'post_format'	=>	__("Format",'aki'),
									'taxonomy'		=>	__("Taxonomy",'aki'),
								),
								__("Other",'aki') => array(
									'ef_taxonomy'	=>	__("Taxonomy (Add / Edit)",'aki'),
									'ef_user'		=>	__("User (Add / Edit)",'aki'),
									'ef_media'		=>	__("Media (Edit)",'aki')
								)
							);
							

							// validate
							if($this->parent->is_field_unlocked('options_page'))
							{
								$choices[__("Options Page",'aki')]['options_page'] = __("Options Page",'aki');
							}
							
							
							// allow custom location rules
							$choices = apply_filters( 'aki/location/rule_types', $choices );
							
							
							// create field
							$args = array(
								'type'	=>	'select',
								'name'	=>	'location[rules]['.$k.'][param]',
								'value'	=>	$rule['param'],
								'choices' => $choices,
								'optgroup' => true,
							);
							
							do_action('aki/create_field', $args);
							
						?></td>
						<td class="operator"><?php 	
							
							$choices = array(
								'=='	=>	__("is equal to",'aki'),
								'!='	=>	__("is not equal to",'aki'),
							);
							
							
							// allow custom location rules
							$choices = apply_filters( 'aki/location/rule_operators', $choices );
							
							
							// create field
							do_action('aki/create_field', array(
								'type'	=>	'select',
								'name'	=>	'location[rules]['.$k.'][operator]',
								'value'	=>	$rule['operator'],
								'choices' => $choices
							)); 	
							
						?></td>
						<td class="value"><?php 
							
							$this->ajax_aki_location(array(
								'key' => $k,
								'value' => $rule['value'],
								'param' => $rule['param'],
							)); 
							
						?></td>
						<td class="buttons">
							<ul class="hl clearfix">
								<li><a href="javascript:;" class="aki-button-remove"></a></li>
								<li><a href="javascript:;" class="aki-button-add"></a></li>
							</ul>
						</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
					
				</table>
				<ul class="hl clearfix">
					<li style="padding:4px 4px 0 0;"><?php _e("match",'aki'); ?></li>
					<li><?php do_action('aki/create_field', array(
									'type'	=>	'select',
									'name'	=>	'location[allorany]',
									'value'	=>	$location['allorany'],
									'choices' => array(
										'all'	=>	__("all",'aki'),
										'any'	=>	__("any",'aki'),
									),
					)); ?></li>
					<li style="padding:4px 0 0 4px;"><?php _e("of the above",'aki'); ?></li>
				</ul>
			</div>
			
			
		</td>
		
	</tr>

	</tbody>
</table>
<script type="text/html" id="aki_location_options_deactivated">
	<optgroup label="<?php _e("Options",'aki'); ?>" disabled="true">
		<option value="" disabled="true"><?php _e("Unlock options add-on with an activation code",'aki'); ?></option>
	</optgroup>
</script>