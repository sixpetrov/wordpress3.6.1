/*
*  Input Ajax
*
*  @description: show / hide metaboxes from changing category / tempalte / etc		
*  @author: Elliot Condon
*  @since: 3.1.4
*/

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
	*  Vars
	*
	*  @description: 
	*  @created: 3/09/12
	*/
	
	aki.data = {
		'action' 			:	'aki/location/match_field_groups_ajax',
		'post_id'			:	0,
		'page_template'		:	0,
		'page_parent'		:	0,
		'page_type'			:	0,
		'post_category'		:	0,
		'post_format'		:	0,
		'taxonomy'			:	0,
		'lang'				:	0,
		'nonce'				:	0,
		'return'			:	'json'
	};
	
		
	/*
	*  Document Ready
	*
	*  @description: adds ajax data		
	*  @created: 1/03/2011
	*/
	
	$(document).ready(function(){
		
		
		// update post_id
		aki.data.post_id = aki.post_id;
		aki.data.nonce = aki.nonce;
		
		
		// MPML
		if( $('#icl-als-first').exists() )
		{
			var href = $('#icl-als-first').children('a').attr('href'),
				regex = new RegExp( "lang=([^&#]*)" ),
				results = regex.exec( href );
			
			// lang
			aki.data.lang = results[1];
			
		}
		
	});
	
	
	/*
	*  update_field_groups
	*
	*  @description: finds the new id's for metaboxes and show's hides metaboxes
	*  @created: 1/03/2011
	*/
	
	$(document).live('aki/update_field_groups', function(){
		
		$.ajax({
			url: ajaxurl,
			data: aki.data,
			type: 'post',
			dataType: 'json',
			success: function(result){
				
				// validate
				if( !result )
				{
					return false;
				}
				
				
				// hide all metaboxes
				$('#poststuff .aki_postbox').addClass('aki-hidden');
				$('#adv-settings .aki_hide_label').hide();
				
				
				// dont bother loading style or html for inputs
				if( result.length == 0 )
				{
					return false;
				}
				
				
				// show the new postboxes
				$.each(result, function(k, v) {
					
					
					var postbox = $('#poststuff #aki_' + v);
					
					postbox.removeClass('aki-hidden');
					$('#adv-settings .aki_hide_label[for="aki_' + v + '-hide"]').show();
					
					// load fields if needed
					postbox.find('.aki-replace-with-fields').each(function(){
						
						var div = $(this);
						
						$.ajax({
							url: ajaxurl,
							data: {
								action : 'aki_input',
								aki_id : v,
								post_id : aki.post_id
							},
							type: 'post',
							dataType: 'html',
							success: function(html){
							
								div.replaceWith(html);
								
								$(document).trigger('aki/setup_fields', postbox);
								
							}
						});
						
					});
				});
				
				// load style
				$.ajax({
					url: ajaxurl,
					data: {
						action : 'get_input_style',
						aki_id : result[0]
					},
					type: 'post',
					dataType: 'html',
					success: function(result){
					
						$('#aki_style').html(result);
						
					}
				});
				
			}
		});
	});

	
	/*
	*  $(document).trigger('aki/update_field_groups'); (Live change events)
	*
	*  @description: call the $(document).trigger('aki/update_field_groups'); event on live events
	*  @created: 1/03/2011
	*/
		
	$('#page_template').live('change', function(){
		
		aki.data.page_template = $(this).val();
		
		$(document).trigger('aki/update_field_groups');
	    
	});
	
	
	$('#parent_id').live('change', function(){
		
		var val = $(this).val();
		
		
		// set page_type / page_parent
		if( val != "" )
		{
			aki.data.page_type = 'child';
			aki.data.page_parent = val;
		}
		else
		{
			aki.data.page_type = 'parent';
			aki.data.page_parent = 0;
		}
		
		
		$(document).trigger('aki/update_field_groups');
	    
	});

	
	$('#post-formats-select input[type="radio"]').live('change', function(){
		
		var val = $(this).val();
		
		if( val == '0' )
		{
			val = 'standard';
		}
		
		aki.data.post_format = val;
		
		$(document).trigger('aki/update_field_groups');
		
	});	
	
	
	// taxonomy / category
	$('.categorychecklist input[type="checkbox"]').live('change', function(){
		
		
		// vars
		var values = [];
		
		
		$('.categorychecklist input[type="checkbox"]:checked').each(function(){
			values.push( $(this).val() );
		});

		
		aki.data.post_category = values;
		aki.data.taxonomy = values;


		$(document).trigger('aki/update_field_groups');
		
	});
	
	
	
})(jQuery);