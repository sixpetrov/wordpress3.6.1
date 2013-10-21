<?php

/*
*  Gallery Field
*
*  @description: 
*  @created: 28/06/12
*/

class aki_Gallery extends aki_Field
{

	/*
	*  Constructer
	*
	*  @description: 
	*  @created: 5/07/12
	*/
	
	function __construct($parent)
	{
    	parent::__construct($parent);
    	
    	$this->name = 'gallery';
		$this->title = __("Gallery",'aki');
		
		
		// actions
		add_action('admin_head-media-upload-popup', array($this, 'popup_head'));
		add_action('aki_head-update_attachment-gallery', array($this, 'aki_head_update_attachment'));
		add_action('wp_ajax_aki/fields/gallery/get_image', array($this, 'get_image'));
   	}

	
	/*
	*  get_image
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 18/01/13
	*/
	
   	function get_image()
   	{
   		
   		// vars
		$options = array(
			'nonce' => '',
			'id' => '',
			'preview_size' => 'full'
		);
		$return = array();
		
		
		// load post options
		$options = array_merge($options, $_POST);

		
		// verify nonce
		if( ! wp_verify_nonce($options['nonce'], 'aki_nonce') )
		{
			die(0);
		}
		
		
		// get attachment object
		$attachment = get_post( $options['id'] );
		
		$src = wp_get_attachment_image_src( $attachment->ID, $options['preview_size'] );
		
		$return = array(
			'id' => $attachment->ID,
			'src' => $src[0],
			'title'=> $attachment->post_title,
			'caption'=> $attachment->post_excerpt,
			'alt'=> get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
			'description'=> $attachment->post_content,
		);
		
		
		echo json_encode($return);
		die;
   	}
   	
   	
	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*	- this function is called from core/field_meta_box.php to create extra options
	*	for your field
	*
	*	@params
	*	- $key (int) - the $_POST obejct key required to save the options to the field
	*	- $field (array) - the field object
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_options($key, $field)
	{
		// vars
		$defaults = array(
			'preview_size'	=>	'thumbnail',
		);
		
		$field = array_merge($defaults, $field);
		
?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Preview Size",'aki'); ?></label>
		<p class="description"><?php _e("Thumbnail is advised",'aki'); ?></p>
	</td>
	<td>
		<?php
		
		$image_sizes = $this->parent->get_all_image_sizes();
		
		do_action('aki/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][preview_size]',
			'value'		=>	$field['preview_size'],
			'layout'	=>	'horizontal',
			'choices'	=>	$image_sizes
		));
		
		?>
	</td>
</tr>
<?php
		
	}

	
	function aki_head_update_attachment()
	{
		?>
<script type="text/javascript">
(function($){

	// vars
	var div = self.parent.aki.media.div;


	self.parent.aki.fields.gallery.update_image();


	// add message
	self.parent.aki.helpers.add_message('<?php _e("Image Updated",'aki'); ?>.', div);


})(jQuery);
</script>
		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*	- this function is called on edit screens to produce the html for this field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		// vars
		$defaults = array(
			'value'	=>	false,
			'preview_size' => 'thumbnail',
		);
		
		$field = array_merge($defaults, $field);
		
		?>
<div class="aki-gallery" data-preview_size="<?php echo $field['preview_size']; ?>">
	
	<input type="hidden" name="<?php echo $field['name']; ?>" value="" />
	
	<div class="thumbnails">
		<div class="inner clearfix">
		<?php if( $field['value'] ): foreach( $field['value'] as $attachment ): 
			
			$src = wp_get_attachment_image_src( $attachment['id'], $field['preview_size'] );
			$src = $src[0];
			
			?>
			<div class="thumbnail" data-id="<?php echo $attachment['id']; ?>">
				<input class="aki-image-value" type="hidden" name="<?php echo $field['name']; ?>[]" value="<?php echo $attachment['id']; ?>" />
				<div class="inner clearfix">
					<img src="<?php echo $src; ?>" alt="" />
					<div class="list-data">
						<table>
							<tbody>
							<tr>
								<th><label><?php _e("Title",'aki'); ?>:</label></th>
								<td><?php echo $attachment['title']; ?></td>
							</tr>
							<tr>
								<th><label><?php _e("Alternate Text",'aki'); ?>:</label></th>
								<td><?php echo $attachment['alt']; ?></td>
							</tr>
							<tr>
								<th><label><?php _e("Caption",'aki'); ?>:</label></th>
								<td><?php echo $attachment['caption']; ?></td>
							</tr>
							<tr>
								<th><label><?php _e("Description",'aki'); ?>:</label></th>
								<td><?php echo $attachment['description']; ?></td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="hover">
					<ul class="bl">
						<li><a href="#" class="aki-button-delete ir"><?php _e("Remove",'aki'); ?></a></li>
						<li><a href="#" class="aki-button-edit ir"><?php _e("Edit",'aki'); ?></a></li>
					</ul>
				</div>
				
			</div>
		<?php endforeach; endif; ?>
		</div>
	</div>

	<div class="toolbar">
		<ul class="hl clearfix">
			<li class="add-image-li"><a class="aki-button add-image" href="#"><?php _e("Add Image",'aki'); ?></a></li>
			<li class="gallery-li view-grid-li active"><div class="divider divider-left"></div><a class="ir view-grid" href="#"><?php _e("Grid",'aki'); ?></a><div class="divider"></div></li>
			<li class="gallery-li view-list-li"><a class="ir view-list" href="#"><?php _e("List",'aki'); ?></a><div class="divider"></div></li>
			<li class="gallery-li count-li right">
				<span class="count" data-0="<?php _e("No images selected",'aki'); ?>" data-1="<?php _e("1 image selected",'aki'); ?>" data-2="<?php _e("{count} images selected",'aki'); ?>"></span>
			</li>
		</ul>
	</div>
	
	<script type="text/html" class="tmpl-thumbnail">
	<div class="thumbnail" data-id="{id}">
		<input type="hidden" class="aki-image-value" name="<?php echo $field['name']; ?>[]" value="{id}" />
		<div class="inner clearfix">
			<img src="{src}" alt="{alt}" />
			<div class="list-data">
				<table>
				<tbody>
					<tr>
						<th><label><?php _e("Title",'aki'); ?>:</label></th>
						<td class="td-title">{title}</td>
					</tr>
					<tr>
						<th><label><?php _e("Alternate Text",'aki'); ?>:</label></th>
						<td class="td-alt">{alt}</td>
					</tr>
					<tr>
						<th><label><?php _e("Caption",'aki'); ?>:</label></th>
						<td class="td-caption">{caption}</td>
					</tr>
					<tr>
						<th><label><?php _e("Description",'aki'); ?>:</label></th>
						<td class="td-description">{description}</td>
					</tr>
				</tbody>
			</table>
			</div>
		</div>
		<div class="hover">
			<ul class="bl">
				<li><a href="#" class="aki-button-delete ir"><?php _e("Remove",'aki'); ?></a></li>
				<li><a href="#" class="aki-button-edit ir"><?php _e("Edit",'aki'); ?></a></li>
			</ul>
		</div>
		
	</div>
	</script>
	
</div>
		<?php
	}

	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value
	*	- called from the edit page to get the value of your field. This function is useful
	*	if your field needs to collect extra data for your create_field() function.
	*
	*	@params
	*	- $post_id (int) - the post ID which your value is attached to
	*	- $field (array) - the field object.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value($post_id, $field)
	{
		// get value
		$value = parent::get_value($post_id, $field);
		$new_value = array();
		
		
		// empty?
		if( empty($value) )
		{
			return $value;
		}
		
		
		// find attachments (DISTINCT POSTS)
		$attachments = get_posts(array(
			'post_type' => 'attachment',
			'numberposts' => -1,
			'post_status' => null,
			'post__in' => $value,
		));
		
		$ordered_attachments = array();
		foreach( $attachments as $attachment)
		{
			// create array to hold value data
			$ordered_attachments[ $attachment->ID ] = array(
				'id' => $attachment->ID,
				'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
				'title' => $attachment->post_title,
				'caption' => $attachment->post_excerpt,
				'description' => $attachment->post_content,
			);
		}
		
		
		// override value array with attachments
		foreach( $value as $v)
		{
			if( isset($ordered_attachments[ $v ]) )
			{
				$new_value[] = $ordered_attachments[ $v ];
			}
		}
		
		
		/*
		// format attachments
		$ordered_attachments = array();
		foreach( $attachments as $attachment )
		{
			$ordered_attachments[ $attachment->ID ] = $attachment;
		}
		
		
		// update value with corisponding attachments
		foreach( $value as $k => $v)
		{
			// get the attachment onject for this value (attachment id)
			$attachment = $ordered_attachments[ $v ];
			
			// create array to hold value data
			$value[ $k ] = array(
				'id' => $attachment->ID,
				'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
				'title' => $attachment->post_title,
				'caption' => $attachment->post_excerpt,
				'description' => $attachment->post_content,
			);
		}
		
		*/
		
		// return value
		return $new_value;	
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*	- called from your template file when using the API functions (get_field, etc). 
	*	This function is useful if your field needs to format the returned value
	*
	*	@params
	*	- $post_id (int) - the post ID which your value is attached to
	*	- $field (array) - the field object.
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		// get value
		$value = $this->get_value($post_id, $field);
		
		
		// find all image sizes
		$image_sizes = get_intermediate_image_sizes();


		if( $value )
		{
			foreach( $value as $k => $v )
			{
				// full url
				$src = wp_get_attachment_image_src( $v['id'], 'full' );
				$value[$k]['url'] = $src[0];
				$value[$k]['width'] = $src[1];
				$value[$k]['height'] = $src[2];
				
				// sizes
				if( $image_sizes )
				{
					$value[$k]['sizes'] = array();
					
					foreach( $image_sizes as $image_size )
					{
						// find src
						$src = wp_get_attachment_image_src( $v['id'], $image_size );
						
						// add src
						$value[$k]['sizes'][ $image_size ] = $src[0];
						$value[$k]['sizes'][ $image_size . '-width' ] = $src[1];
						$value[$k]['sizes'][ $image_size . '-height' ] = $src[2];

					}
					// foreach( $image_sizes as $image_size )
				}
				// if( $image_sizes )	
			}
			// foreach( $value as $k => $v )
		}
		// if( $value )
		
		
		// return value
		return $value;

	}
	
	
	/*
	*  popup_head
	*
	*  @description: 
	*  @since 3.2.8
	*  @created: 6/07/12
	*/
	
	function popup_head()
	{	
		// options
		$defaults = array(
			'aki_type' => '',
			'aki_gallery_id' => '',
			'aki_preview_size' => 'thumbnail',
			'tab'	=>	'type',	
		);
		
		$options = array_merge($defaults, $_GET);
		
		
		// validate
		if( $options['aki_type'] != 'gallery' )
		{
			return;
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
	
	.aki-message-wrapper .message {
		margin: 2px 2px 0;
	}
	
	#media-items .media-item.media-item-added {
		background: #F9F9F9;
	}
	
	#wpcontent {
   		margin-left: 0 !important;
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
	*  Exists
	*
	*  @description: returns true / false		
	*  @created: 1/03/2011
	*/
	
	$.fn.exists = function()
	{
		return $(this).length>0;
	};
	
	
	/*
	*  global vars
	*
	*  @description: 
	*  @created: 16/08/12
	*/
	var gallery,
		tmpl_thumbnail;
	
	
	/*
	*  Disable / Enable image
	*
	*  @description: 
	*  @created: 16/08/12
	*/
	
	function disable_image( div )
	{
		// add class
		div.addClass('media-item-added');
		
		
		// change button text
		div.find('.aki-select').addClass('disabled').text("<?php _e("Added",'aki'); ?>");
		
	}
	
	
	/*
	*  Select Image
	*
	*  @created : 28/03/2012
	*/
	
	$('#media-items .media-item a.aki-select').live('click', function(){
		
		var attachment_id = $(this).attr('href'),
			div = $(this).closest('.media-item');
		
		
		// does this image already exist in the gallery?
		if( gallery.find('.thumbnail[data-id="' + attachment_id + '"]').length > 0 )
		{
			alert("<?php _e("Image already exists in gallery",'aki'); ?>");
			return false;
		}
		
		
		// show added message
		disable_image( div );
		
		
		// add image
		add_image( attachment_id );
		
		
		return false;
		
	});
	
	
	// edit toggle
	$('#media-items .media-item a.aki-toggle-edit').live('click', function(){
		
		if( $(this).hasClass('active') )
		{
			$(this).removeClass('active');
			$(this).closest('.media-item').find('.slidetoggle').attr('style', 'display: none !important');
			return false;
		}
		else
		{
			$(this).addClass('active');
			$(this).closest('.media-item').find('.slidetoggle').attr('style', 'display: table !important');
			return false;
		}
		
	});
	
	
	// set a interval function to add buttons to media items
	function aki_add_buttons()
	{

		// add buttons to media items
		$('#media-items .media-item:not(.aki-active)').each(function(){
			
			// needs attachment ID
			if($(this).children('input[id*="type-of-"]').length == 0){ return false; }
			
			// only once!
			$(this).addClass('aki-active');
			
			// find id
			var id = $(this).children('input[id*="type-of-"]').attr('id').replace('type-of-', '');
			
			// Add edit button
			$(this).find('.filename.new').append('<br /><a href="#" class="aki-toggle-edit">Edit</a>');
			
			// Add select button
			$(this).find('.filename.new').before('<a href="' + id + '" class="button aki-select"><?php _e("Add Image",'aki'); ?></a>');
			
			// add save changes button
			$(this).find('tr.submit input.button').hide().before('<input type="submit" value="<?php _e("Update Image",'aki'); ?>" class="button savebutton" />');

			
		});
		
		
		// disable images
		gallery.find('.thumbnails .thumbnail input[type="hidden"]').each(function(){
			
			var div = $('#media-item-' + $(this).val());
			
			if( div.exists() )
			{
				disable_image( div );
			}
			
		});
	}
	<?php
	
	// run the aki_add_buttons ever 500ms when on the image upload tab
	if($options['tab'] == 'type'): ?>
	var aki_t = setInterval(function(){
	
		aki_add_buttons();
		
		// auto add images
		$('#media-items .media-item:not(.media-item-added)').each(function(){
			$(this).find('a.aki-select').trigger('click');
		});
		
		
	}, 500);
	<?php endif; ?>
	
	
	// add aki input filters to allow for tab navigation
	$(document).ready(function(){
		
		// vars
		gallery = self.parent.aki.media.div;
		tmpl_thumbnail = gallery.find('.tmpl-thumbnail').html();
		
		
		// add buttins
		setTimeout(function(){
		
			aki_add_buttons();
			
		}, 1);
		
		
		$('form#filter').each(function(){
			
			$(this).append('<input type="hidden" name="aki_preview_size" value="<?php echo $options['aki_preview_size']; ?>" />');
			$(this).append('<input type="hidden" name="aki_type" value="gallery" />');
						
		});
		
		$('form#image-form, form#library-form').each(function(){
			
			var action = $(this).attr('action');
			action += "&aki_type=gallery&aki_preview_size=<?php $options['aki_preview_size']; ?>";
			$(this).attr('action', action);
			
		});
		
		
	});
	
	
	/*
	*  add_image
	*
	*  @description: 
	*  @created: 2/07/12
	*/
	
	function add_image( id )
	{
		var ajax_data = {
			action : 'aki/fields/gallery/get_image',
			nonce : self.parent.aki.nonce,
			id : id,
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
				self.parent.aki.fields.gallery.add( json );
				
			}
		});
		
	}

				
})(jQuery);
</script><?php

	}
	
}

?>
