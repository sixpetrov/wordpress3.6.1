<?php

/*
*  Meta Box: Options
*
*  @description: 
*  @created: 23/06/12
*/
	

// global
global $post;

	
// vars
$options = $this->parent->get_aki_options($post->ID);
	

?>
<table class="aki_input widefat" id="aki_options">
	<tr>
		<td class="label">
			<label for=""><?php _e("Order No.",'aki'); ?></label>
			<p class="description"><?php _e("Field groups are created in order <br />from lowest to highest",'aki'); ?></p>
		</td>
		<td>
			<?php 
			
			do_action('aki/create_field', array(
				'type'	=>	'text',
				'name'	=>	'menu_order',
				'value'	=>	$post->menu_order,
			));
			
			?>
		</td>
	</tr>
	<tr>
		<td class="label">
			<label for=""><?php _e("Position",'aki'); ?></label>
		</td>
		<td>
			<?php 
			
			do_action('aki/create_field', array(
				'type'	=>	'radio',
				'name'	=>	'options[position]',
				'value'	=>	$options['position'],
				'choices' => array(
                    'aki_after_title' =>	__("High (after title)",'aki'),
					'normal'	=>	__("Normal",'aki'),
					'side'		=>	__("Side",'aki'),
				)
			));

			?>
		</td>
	</tr>
	<tr>
		<td class="label">
			<label for="post_type"><?php _e("Style",'aki'); ?></label>
		</td>
		<td>
			<?php 
			
			do_action('aki/create_field', array(
				'type'	=>	'radio',
				'name'	=>	'options[layout]',
				'value'	=>	$options['layout'],
				'choices' => array(
					'no_box'	=>	__("No Metabox",'aki'),
					'default'	=>	__("Standard Metabox",'aki'),
				)
			));
			
			?>
		</td>
	</tr>
	<tr>
		<td class="label">
			<label for="post_type"><?php _e("Hide on screen",'aki'); ?></label>
			<p class="description"><?php _e("<b>Select</b> items to <b>hide</b> them from the edit screen",'aki'); ?></p>
			<p class="description"><?php _e("If multiple field groups appear on an edit screen, the first field group's options will be used. (the one with the lowest order number)",'aki'); ?></p>
		</td>
		<td>
			<?php 
			
			do_action('aki/create_field', array(
				'type'	=>	'checkbox',
				'name'	=>	'options[hide_on_screen]',
				'value'	=>	$options['hide_on_screen'],
				'choices' => array(
					'the_content'		=>	__("Content Editor",'aki'),
					'excerpt'			=>	__("Excerpt"),
					'custom_fields'		=>	__("Custom Fields"),
					'discussion'		=>	__("Discussion"),
					'comments'			=>	__("Comments"),
					'revisions'			=>	__("Revisions"),
					'slug'				=>	__("Slug"),
					'author'			=>	__("Author"),
					'format'			=>	__("Format"),
					'featured_image'	=>	__("Featured Image"),
					'categories'		=>	__("Categories"),
					'tags'				=>	__("Tags"),
					'send-trackbacks'	=>	__("Send Trackbacks"),
				)
			));
			
			?>
		</td>
	</tr>
</table>