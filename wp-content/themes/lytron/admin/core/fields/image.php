<?php

class aki_Image extends aki_Field
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
    	
    	$this->name = 'image';
		$this->title = __('Image','aki');
		
		add_action('admin_head-media-upload-popup', array($this, 'popup_head'));
		add_filter('get_media_item_args', array($this, 'allow_img_insertion'));
		add_action('aki_head-update_attachment-image', array($this, 'aki_head_update_attachment'));
		
		add_action('wp_ajax_aki/fields/image/get_images', array($this, 'ajax_get_images'));
		//add_filter('image_size_names_choose', array($this, 'image_size_names_choose'));
		add_action('wp_prepare_attachment_for_js', array($this, 'wp_prepare_attachment_for_js'), 10, 3);
		
   	}
   	
   	
   	/*
   	*  aki_head_update_attachment
   	*
   	*  @description: 
   	*  @since: 3.2.7
   	*  @created: 4/07/12
   	*/
   	
   	function aki_head_update_attachment()
	{
		?>
<script type="text/javascript">
(function($){
	
	// vars
	var div = self.parent.aki.media.div;
	
	
	// add message
	self.parent.aki.helpers.add_message("<?php _e("Image Updated.",'aki'); ?>", div);
	

})(jQuery);
</script>
		<?php
	}
	
	
	/*
   	*  ajax_get_images
   	*
   	*  @description: 
   	*  @since: 3.5.7
   	*  @created: 13/01/13
   	*/
	
   	function ajax_get_images()
   	{
   		// vars
		$options = array(
			'nonce' => '',
			'images' => array(),
			'preview_size' => 'thumbnail'
		);
		$return = array();
		
		
		// load post options
		$options = array_merge($options, $_POST);
		
		
		// verify nonce
		if( ! wp_verify_nonce($options['nonce'], 'aki_nonce') )
		{
			die(0);
		}
		
		
		if( $options['images'] )
		{
			foreach( $options['images'] as $id )
			{
				$src = wp_get_attachment_image_src( $id, $options['preview_size'] );
				
				
				$return[] = array(
					'id' => $id,
					'src' => $src[0],
				);
			}
		}
		
		
		// return json
		echo json_encode( $return );
		die;
		
   	}
   	
   	
   	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts / admin_print_styles
	*
	*	@author Elliot Condon
	*	@since 3.0.1
	* 
	*-------------------------------------------------------------------------------------*/
	
	function allow_img_insertion($vars)
	{
	    $vars['send'] = true;
	    return($vars);
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
		$class = "";
		$file_src = "";
		$preview_size = isset($field['preview_size']) ? $field['preview_size'] : 'thumbnail';
		
		// get image url
		if($field['value'] != '' && is_numeric($field['value']))
		{
			$file_src = wp_get_attachment_image_src($field['value'], $preview_size);
			$file_src = $file_src[0];
			
			if($file_src)
			{
				$class = "active";
			}
		}
		
		?>
<div class="aki-image-uploader clearfix <?php echo $class; ?>" data-preview_size="<?php echo $preview_size; ?>">
	<input class="aki-image-value" type="hidden" name="<?php echo $field['name']; ?>" value="<?php echo $field['value']; ?>" />
	<div class="has-image">
		<div class="hover">
			<ul class="bl">
				<li><a class="aki-button-delete ir" href="#"><?php _e("Remove",'aki'); ?></a></li>
				<li><a class="aki-button-edit ir" href="#"><?php _e("Edit",'aki'); ?></a></li>
			</ul>
		</div>
		<img src="<?php echo $file_src; ?>" alt=""/>
	</div>
	<div class="no-image">
		<p><?php _e('No image selected','aki'); ?> <input type="button" class="button add-image" value="<?php _e('Add Image','aki'); ?>" />
	</div>
</div>
		<?php
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
			'save_format'	=>	'id',
			'preview_size'	=>	'thumbnail',
		);
		
		$field = array_merge($defaults, $field);
		
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Return Value",'aki'); ?></label>
			</td>
			<td>
				<?php
				do_action('aki/create_field', array(
					'type'		=>	'radio',
					'name'		=>	'fields['.$key.'][save_format]',
					'value'		=>	$field['save_format'],
					'layout'	=>	'horizontal',
					'choices'	=> array(
						'object'	=>	__("Image Object",'aki'),
						'url'		=>	__("Image URL",'aki'),
						'id'		=>	__("Image ID",'aki')
					)
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Preview Size",'aki'); ?></label>
			</td>
			<td>
				<?php
				
				$image_sizes = $this->parent->get_all_image_sizes();
				
				do_action('aki/create_field', array(
					'type'		=>	'radio',
					'name'		=>	'fields['.$key.'][preview_size]',
					'value'		=>	$field['preview_size'],
					'layout'	=>	'horizontal',
					'choices' 	=>	$image_sizes
				));

				?>
			</td>
		</tr>
		<?php
	}

	
	/*
	*  popup_head
	*
	*  @description: css + js for thickbox
	*  @since: 1.1.4
	*  @created: 7/12/12
	*/
	
	function popup_head()
	{
		// options
		$defaults = array(
			'aki_type' => '',
			'aki_preview_size' => 'thumbnail',
			'tab'	=>	'type',	
		);
		
		$options = array_merge($defaults, $_GET);
		
		
		// validate
		if( $options['aki_type'] != 'image' )
		{
			return;
		}
		
		
		// update attachment
		if( isset($_POST["attachments"]) )
		{
			echo '<div class="updated"><p>' . __("Media attachment updated.") . '</p></div>';
		}
		
		
?><style type="text/css">
	#media-upload-header #sidemenu li#tab-type_url,
	#media-items .media-item a.toggle,
	#media-items .media-item tr.image-size,
	#media-items .media-item tr.align,
	#media-items .media-item tr.url,
	#media-items .media-item .slidetoggle {
		display: none !important;
	}
	
	#media-items .media-item {
		position: relative;
		overflow: hidden;
	}
	
	#media-items .media-item .aki-checkbox {
		float: left;
		margin: 28px 10px 0;
	}
	
	#media-items .media-item .pinkynail {
		max-width: 64px;
		max-height: 64px;
		display: block !important;
		margin: 2px;
	}
	
	#media-items .media-item .filename.new {
		min-height: 0;
		padding: 20px 10px 10px 10px;
		line-height: 15px;
	}
	
	#media-items .media-item .title {
		line-height: 14px;
	}
	
	#media-items .media-item .aki-select {
		float: right;
		margin: 22px 12px 0 10px;
	}
	
	#media-upload .ml-submit {
		display: none !important;
	}

	#media-upload .aki-submit {
		margin: 1em 0;
		padding: 1em 0;
		position: relative;
		overflow: hidden;
		display: none; /* default is hidden */
		clear: both;
	}
	
	#media-upload .aki-submit a {
		float: left;
		margin: 0 10px 0 0;
	}
	
<?php if( $options['tab'] == 'gallery' ): ?>
	#sort-buttons,
	#gallery-form > .widefat,
	#media-items .menu_order,
	#gallery-settings {
		display: none !important;
	}
<?php endif; ?>

</style>
<script type="text/javascript">
(function($){	
	
	/*
	*  Select Image
	*
	*  @description: 
	*  @since: 2.0.4
	*  @created: 11/12/12
	*/
	
	$('#media-items .media-item a.aki-select').live('click', function(){
		
		var id = $(this).attr('href');
		
		
		// IE7 Fix
		if( id.indexOf("/") != -1 )
		{
			var split = id.split("/");
			id = split[split.length-1];
		}
		
		
		var ajax_data = {
			action : 'aki/fields/image/get_images',
			nonce : self.parent.aki.nonce,
			images : [ id ],
			preview_size : "<?php echo $options['aki_preview_size']; ?>"
		};
	
		
		// ajax
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data : ajax_data,
			cache: false,
			dataType: "json",
			success: function( json ) {	    	

				// validate
				if( !json )
				{
					return false;
				}
				
				
				// add file
				self.parent.aki.fields.image.add( json[0] );
				
	 			self.parent.tb_remove();
	 	
	 	
			}
		});
		
 						
		return false;
	});
	
	
	
	/*
	*  Select Images
	*
	*  @description: 
	*  @since: 2.0.4
	*  @created: 11/12/12
	*/
	
	$('#aki-add-selected').live('click', function(){
		 
		// check total 
		var total = $('#media-items .media-item .aki-checkbox:checked').length;
		if( total == 0 ) 
		{ 
			alert("<?php _e("No images selected",'aki'); ?>");
			return false; 
		} 
		
		 
		var ajax_data = {
			action : 'aki/fields/image/get_images',
			nonce : self.parent.aki.nonce,
			images : [],
			preview_size : "<?php echo $options['aki_preview_size']; ?>"
		};
		
		
		// add to id array
		$('#media-items .media-item .aki-checkbox:checked').each(function(){
			
			ajax_data.images.push( $(this).val() );
			
		});
		
		
		// ajax
		$.ajax({
			url: ajaxurl,
			type: 'post',
			data : ajax_data,
			cache: false,
			dataType: "json",
			success: function( json ) {
			
				// validate
				if( !json )
				{
					return false;
				}
				
				
				
				var selection = json,
		    		i = 0;
		    		
		    	
		    	$.each( json, function( k, image ){

			    	// counter
			    	i++;
			    	
			    	
			    	// vars
			    	var div = self.parent.aki.media.div;
			    	
			    	
			    	// add image to field
			        self.parent.aki.fields.image.add( image );
			        
			        
			        // select / add another file field?
			        if( i < selection.length )
					{
						var tr = div.closest('tr'),
							repeater = tr.closest('.repeater');
						
						
						if( tr.next('.row').exists() )
						{
							self.parent.aki.media.div = tr.next('.row').find('.aki-image-uploader');
						}
						else
						{
							// add row 
			 				repeater.find('.add-row-end').trigger('click'); 
			 			 
			 				// set div to new row file 
			 				self.parent.aki.media.div = repeater.find('> table > tbody > tr.row:last .aki-image-uploader');
						}
					}
					
			    });
				
				
	 			self.parent.tb_remove();
	 	
			}
		});
 		
 		
		return false; 
		 
	}); 
		
	
	/*
	*  Edit Attachment Toggle
	*
	*  @description: 
	*  @since: 2.0.4
	*  @created: 11/12/12
	*/
	
	$('#media-items .media-item a.aki-toggle-edit').live('click', function(){
		
		// vars
		var a = $(this),
			item = a.closest('.media-item');
		
		
		// toggle
		if( a.hasClass('active') )
		{
			a.removeClass('active');
			item.find('.slidetoggle').attr('style', 'display: none !important');
		}
		else
		{
			a.addClass('active');
			item.find('.slidetoggle').attr('style', 'display: table !important');
		}
		
		
		// return
		return false;
		
	});
	
	
	/*
	*  add_buttons
	*
	*  @description: 
	*  @since: 2.0.4
	*  @created: 11/12/12
	*/

	function add_buttons()
	{
		// vars
		var is_sub_field = (self.parent.aki.media.div.closest('.repeater').length > 0) ? true : false;
		
		
		// add submit after media items (on for sub fields)
		if($('.aki-submit').length == 0 && is_sub_field)
		{
			$('#media-items').after('<div class="aki-submit"><a id="aki-add-selected" class="button"><?php _e("Add Selected Images",'aki'); ?></a></div>');
		}
		
		
		// add buttons to media items
		$('#media-items .media-item:not(.aki-active)').each(function(){
			
			// show the add all button
			$('.aki-submit').show();
			
			// needs attachment ID
			if($(this).children('input[id*="type-of-"]').length == 0){ return false; }
			
			// only once!
			$(this).addClass('aki-active');
			
			// find id
			var id = $(this).children('input[id*="type-of-"]').attr('id').replace('type-of-', '');
			
			// if inside repeater, add checkbox
			if(is_sub_field)
			{
				$(this).prepend('<input type="checkbox" class="aki-checkbox" value="' + id + '" <?php if( $options['tab'] == "type" ){echo 'checked="checked"';} ?> />');
			}
			
			// Add edit button
			$(this).find('.filename.new').append('<br /><a href="#" class="aki-toggle-edit">Edit</a>');
			
			// Add select button
			$(this).find('.filename.new').before('<a href="' + id + '" class="button aki-select"><?php _e("Select Image",'aki'); ?></a>');
			
			// add save changes button
			$(this).find('tr.submit input.button').hide().before('<input type="submit" value="<?php _e("Update Image",'aki'); ?>" class="button savebutton" />');

			
		});
	}
	<?php
	
	// run the aki_add_buttons ever 500ms when on the image upload tab
	if( $options['tab'] == "type" ): ?>
	var aki_t = setInterval(function(){
		add_buttons();
	}, 500);
	<?php endif; ?>
	
	
	// add aki input filters to allow for tab navigation
	$(document).ready(function(){
		
		setTimeout(function(){
			add_buttons();
		}, 1);
		
		
		$('form#filter').each(function(){
			
			$(this).append('<input type="hidden" name="aki_preview_size" value="<?php echo $options['aki_preview_size']; ?>" />');
			$(this).append('<input type="hidden" name="aki_type" value="image" />');
						
		});
		
		$('form#image-form, form#library-form').each(function(){
			
			var action = $(this).attr('action');
			action += "&aki_type=image&aki_preview_size=<?php echo $options['aki_preview_size']; ?>";
			$(this).attr('action', action);
			
		});
		
		
		<?php
	
		// add support for media tags
		
		if( $options['tab'] == 'mediatags' ): ?>
		$('#media-items .mediatag-item-count a').each(function(){
			
			var href = $(this).attr('href');
			href += "&aki_type=image&aki_preview_size=<?php echo $options['aki_preview_size']; ?>";
			$(this).attr('href', href);
			
		});
		<?php endif; ?>
	});
				
})(jQuery);
</script><?php

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
		$format = isset($field['save_format']) ? $field['save_format'] : 'url';
		$value = parent::get_value($post_id, $field);
		
		
		// validate
		if( !$value )
		{
			return false;
		}
		
		
		// format
		if($format == 'url')
		{
			$value = wp_get_attachment_url($value);
		}
		elseif($format == 'object')
		{
			$attachment = get_post( $value );
			
			
			// validate
			if( !$attachment )
			{
				return false;	
			}
			
			
			// create array to hold value data
			$src = wp_get_attachment_image_src( $attachment->ID, 'full' );
			
			$value = array(
				'id' => $attachment->ID,
				'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
				'title' => $attachment->post_title,
				'caption' => $attachment->post_excerpt,
				'description' => $attachment->post_content,
				'url' => $src[0],
				'width' => $src[1],
				'height' => $src[2],
				'sizes' => array(),
			);

			
			// find all image sizes
			$image_sizes = get_intermediate_image_sizes();
			
			if( $image_sizes )
			{
				foreach( $image_sizes as $image_size )
				{
					// find src
					$src = wp_get_attachment_image_src( $attachment->ID, $image_size );
					
					// add src
					$value[ 'sizes' ][ $image_size ] = $src[0];
					$value[ 'sizes' ][ $image_size . '-width' ] = $src[1];
					$value[ 'sizes' ][ $image_size . '-height' ] = $src[2];
				}
				// foreach( $image_sizes as $image_size )
			}
			// if( $image_sizes )
			
		}
		
		return $value;
	}
	
	
	/*
	*  image_size_names_choose
	*
	*  @description: 
	*  @since: 3.5.7
	*  @created: 13/01/13
	*/
	
	function image_size_names_choose( $sizes )
	{
		global $_wp_additional_image_sizes;
			
		if( $_wp_additional_image_sizes )
		{
			foreach( $_wp_additional_image_sizes as $k => $v )
			{
				$title = $k;
				$title = str_replace('-', ' ', $title);
				$title = str_replace('_', ' ', $title);
				$title = ucwords( $title );
				
				$sizes[ $k ] = $title;
			}
			// foreach( $image_sizes as $image_size )
		}
		
        return $sizes;
	}
	
	
	/*
	*  wp_prepare_attachment_for_js
	*
	*  @description: This sneaky hook adds the missing sizes to each attachment in the 3.5 uploader. It would be a lot easier to add all the sizes to the 'image_size_names_choose' filter but then it will show up on the normal the_content editor
	*  @since: 3.5.7
	*  @created: 13/01/13
	*/
	
	function wp_prepare_attachment_for_js( $response, $attachment, $meta )
	{
		// only for image
		if( $response['type'] != 'image' )
		{
			return $response;
		}
		
		
		// make sure sizes exist. Perhaps they dont?
		if( !isset($meta['sizes']) )
		{
			return $response;
		}
		
		
		$attachment_url = $response['url'];
		$base_url = str_replace( wp_basename( $attachment_url ), '', $attachment_url );
		
		if( isset($meta['sizes']) && is_array($meta['sizes']) )
		{
			foreach( $meta['sizes'] as $k => $v )
			{
				if( !isset($response['sizes'][ $k ]) )
				{
					$response['sizes'][ $k ] = array(
						'height'      =>  $v['height'],
						'width'       =>  $v['width'],
						'url'         => $base_url .  $v['file'],
						'orientation' => $v['height'] > $v['width'] ? 'portrait' : 'landscape',
					);
				}
			}
		}

		return $response;
	}
	
		
}

?>
