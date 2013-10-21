/*
*  File
*
*  @description: 
*  @since: 3.5.8
*  @created: 17/01/13
*/

(function($){
	
	var _file = aki.fields.file,
		_media = aki.media;
	
	
	/*
	*  Add
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	_file.add = function( file )
	{

		// vars
		var div = _media.div;
		
		// set atts
		div.find('.aki-file-value').val( file.id ).trigger('change');
	 	div.find('.aki-file-icon').attr( 'src', file.icon );
	 	div.find('.aki-file-name').text( file.name );
	 	
	 	
	 	// set div class
	 	div.addClass('active');
	 	
	 	
	 	// validation
		div.closest('.field').removeClass('error');

	};
		
	
	/*
	*  Edit
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	_file.edit = function(){
		
		// vars
		var div = _media.div,
			id = div.find('.aki-file-value').val();
		
		
		// show edit attachment
		tb_show( _file.text.title_edit , aki.admin_url + 'media.php?attachment_id=' + id + '&action=edit&aki_action=edit_attachment&aki_field=file&TB_iframe=1');
		
	};
	
	
	/*
	*  Remove
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	_file.remove = function()
	{
		// vars
		var div = _media.div;
		
		
		// remove atts
		div.find('.aki-file-value').val( '' ).trigger('change');
	 	div.find('.aki-file-icon').attr( 'src', '' );
	 	div.find('.aki-file-name').text( '' );
		
		
		// remove class
		div.removeClass('active');
		
	};
	
	
	/*
	*  Add Button
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$('.aki-file-uploader .add-file').live('click', function(){
				
		// vars
		var div = _media.div = $(this).closest('.aki-file-uploader'),
			multiple = div.closest('.repeater').exists() ? true : false;
			
			
		// show the thickbox
		if( _media.type() == 'backbone' )
		{
			// clear the frame
			_media.clear_frame();
			
			
		    // Create the media frame. Leave options blank for defaults
			_media.frame = wp.media({
				title : _file.text.title_add,
				multiple : multiple
			});
			
			
			/*
			_media.frame.on('all', function( e ){
				console.log( e );
			});
			*/
			
			
			// add filter by overriding the option when the title is being created. This is an evet fired before the rendering / creating of the library content so it works but is a bit of a hack. In the future, this should be changed to an init / options event
			_media.frame.on('title:create', function(){
				var state = _media.frame.state();
				state.set('filterable', 'uploaded');
			});

			
			// When an image is selected, run a callback.
			_media.frame.on( 'select', function() {
				
				// get selected images
				selection = _media.frame.state().get('selection');
				
				if( selection )
				{
					var i = 0;
					
					selection.each(function(attachment){
	
				    	// counter
				    	i++;
				    	
				    	
				    	// select / add another file field?
				    	if( i > 1 )
						{
							var tr = _media.div.closest('tr'),
								repeater = tr.closest('.repeater');
							
							
							if( tr.next('.row').exists() )
							{
								_media.div = tr.next('.row').find('.aki-file-uploader');
							}
							else
							{
								// add row 
				 				repeater.find('.add-row-end').trigger('click'); 
				 			 
				 				// set aki_div to new row file
				 				_media.div = repeater.find('> table > tbody > tr.row:last .aki-file-uploader');
							}
						}
						
						
				    	// vars
				    	var file = {
					    	id : attachment.id,
					    	name : attachment.attributes.filename,
					    	icon : attachment.attributes.icon
				    	};
				    	
				    	
				    	// add file to field
				        _file.add( file );
				        
						
				    });
				    // selection.each(function(attachment){
				}
				// if( selection )
			});
			// _media.frame.on( 'select', function() {
					 
				
			// Finally, open the modal
			_media.frame.open();
			
			var state = _media.frame.state();
			
		}
		else
		{	
			tb_show( _file.text.title_add , aki.admin_url + 'media-upload.php?post_id=' + aki.post_id + '&post_ID=' + aki.post_id + '&type=file&aki_type=file&TB_iframe=1');
		}
		
		return false;
		
	});
		
	
	/*
	*  Edit Button
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$('.aki-file-uploader .edit-file').live('click', function(){
		
		// vars
		_media.div = $(this).closest('.aki-file-uploader');
		
		_file.edit();
		
		return false;
			
	});
	
	
	/*
	*  Remove Button
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$('.aki-file-uploader .remove-file').live('click', function(){
		
		// vars
		_media.div = $(this).closest('.aki-file-uploader');
				
		_file.remove();
		
		return false;
			
	});
		

})(jQuery);