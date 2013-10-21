/*
*  WYSIWYG
*
*  @description: 
*  @since: 3.5.8
*  @created: 17/01/13
*/

(function($){
	
	var _wysiwyg = aki.fields.wysiwyg;
	
	
	/*
	*  has_tinymce
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	_wysiwyg.has_tinymce = function(){
		
		var r = false;
		
		if( typeof(tinyMCE) == "object" )
		{
			r = true;
		}
		
		return r;
		
	}
	
	/*
	*  aki/wysiwyg_activate
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$(document).live('aki/wysiwyg_activate', function(e, div){
		
		// validate tinymce
		if( ! _wysiwyg.has_tinymce() )
		{
			return;
		}
		

		// activate
		$(div).find('.aki_wysiwyg textarea').each(function(){

			// vars
			var textarea = $(this),
				id = textarea.attr('id'),
				toolbar = textarea.closest('.aki_wysiwyg').attr('data-toolbar');
			
			
			// is clone field?
			if( aki.helpers.is_clone_field(textarea) )
			{
				return;
			}
			
			
			// reset tinyMCE settings
			tinyMCE.settings.theme_advanced_buttons1 = '';
			tinyMCE.settings.theme_advanced_buttons2 = '';
			tinyMCE.settings.theme_advanced_buttons3 = '';
			tinyMCE.settings.theme_advanced_buttons4 = '';
			
			if( _wysiwyg.toolbars[ toolbar ] )
			{
				$.each( _wysiwyg.toolbars[ toolbar ], function( k, v ){
					tinyMCE.settings[ k ] = v;
				})
			}
			
			
			// add functionality back in
			tinyMCE.execCommand("mceAddControl", false, id);
			
			
			// events - load
			$(document).trigger('aki/wysiwyg/load', id);
			
			
			// add events (click, focus, blur) for inserting image into correct editor
			_wysiwyg.add_events( id );
			
		});
		
		
		wpActiveEditor = null;

	});
	
	
	/*
	*  add_wysiwyg_events
	*
	*  @description: 
	*  @since: 2.0.4
	*  @created: 16/12/12
	*/
	
	_wysiwyg.add_events = function( id ){
		
		// validate tinymce
		if( ! _wysiwyg.has_tinymce() )
		{
			return;
		}
		
		
		var editor = tinyMCE.get( id );
		
		if( !editor )
		{
			return;
		}
		
		
		var	container = $('#wp-' + id + '-wrap'),
			body = $( editor.getBody() );


		container.click(function(){
			$(document).trigger('aki/wysiwyg/click', id);
		});
		
		body.focus(function(){
			$(document).trigger('aki/wysiwyg/focus', id);
		}).blur(function(){
			$(document).trigger('aki/wysiwyg/blur', id);
		});
		
	};
	
	
	/*
	*  aki/wysiwyg_deactivate
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$(document).live('aki/wysiwyg_deactivate', function(e, div){
		
		// validate tinymce
		if( ! _wysiwyg.has_tinymce() )
		{
			return;
		}
		
		
		$(div).find('.aki_wysiwyg textarea').each(function(){
			
			// vars
			var textarea = $(this),
				id = textarea.attr('id'),
				wysiwyg = tinyMCE.get( id );
			
			
			// if wysiwyg was found (should be always...), remove its functionality and set the value (to keep line breaks)
			if( wysiwyg )
			{
				var val = wysiwyg.getContent();
				
				tinyMCE.execCommand("mceRemoveControl", false, id);
			
				textarea.val( val );
			}
			
		});
		
		
		wpActiveEditor = null;

	});
	
	
	/*
	*  aki/wysiwyg/click
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$(document).live('aki/wysiwyg/click', function(e, id){
		
		wpActiveEditor = id;
		
		container = $('#wp-' + id + '-wrap').closest('.field').removeClass('error');
		
	});
	
	
	/*
	*  aki/wysiwyg/focus
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$(document).live('aki/wysiwyg/focus', function(e, id){
		
		wpActiveEditor = id;
		
		container = $('#wp-' + id + '-wrap').closest('.field').removeClass('error');
		
	});
	
	/*
	*  aki/wysiwyg/blur
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$(document).live('aki/wysiwyg/blur', function(e, id){
		
		wpActiveEditor = null;
		
		// update the hidden textarea
		// - This fixes a but when adding a taxonomy term as the form is not posted and the hidden tetarea is never populated!
		tinyMCE.get( id ).save();
		
	});
	
	
	/*
	*  aki/setup_fields
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$(document).live('aki/setup_fields', function(e, div){
		
		$(document).trigger('aki/wysiwyg_activate', div);

	});

	
	/*
	*  aki/sortable_start
	*
	*  @description:
	*  @since 3.5.1
	*  @created: 10/11/12
	*/
	
	$(document).live('aki/sortable_start', function(e, div) {
		
		$(document).trigger('aki/wysiwyg_deactivate', div);
		
	});
	
	
	/*
	*  aki/sortable_stop
	*
	*  @description:
	*  @since 3.5.1
	*  @created: 10/11/12
	*/
	
	$(document).live('aki/sortable_stop', function(e, div) {
		
		$(document).trigger('aki/wysiwyg_activate', div);
		
	});
	
	
	/*
	*  window load
	*
	*  @description: 
	*  @since: 3.5.5
	*  @created: 22/12/12
	*/
	
	$(window).load(function(){
		
		// vars
		var wp_content = $('#wp-content-wrap').exists(),
			wp_aki_settings = $('#wp-aki_settings-wrap').exists()
			mode = 'tmce';
		
		
		// has_editor
		if( wp_aki_settings )
		{
			// html_mode
			if( $('#wp-aki_settings-wrap').hasClass('html-active') )
			{
				mode = 'html';
			}
		}
		
		
		setTimeout(function(){
			
			// trigger click on hidden wysiwyg (to get in HTML mode)
			if( wp_aki_settings && mode == 'html' )
			{
				$('#aki_settings-tmce').trigger('click');
			}
			
		}, 1);
		
		
		setTimeout(function(){
			
			// trigger html mode for people who want to stay in HTML mode
			if( wp_aki_settings && mode == 'html' )
			{
				$('#aki_settings-html').trigger('click');
			}
			
			// Add events to content editor
			if( wp_content )
			{
				_wysiwyg.add_events( 'content' );
			}
			
			
		}, 11);
		
	});
	
	
	/*
	*  Full screen
	*
	*  @description: this hack will hide the 'image upload' button in the wysiwyg full screen mode if the field has disabled image uploads!
	*  @since: 3.6
	*  @created: 26/02/13
	*/
	
	$('.aki_wysiwyg a.mce_fullscreen').live('click', function(){
		
		// vars
		var wysiwyg = $(this).closest('.aki_wysiwyg'),
			upload = wysiwyg.attr('data-upload');
		
		if( upload == 'no' )
		{
			$('#mce_fullscreen_container td.mceToolbar .mce_add_media').hide();
		}
		
	});
	

})(jQuery);