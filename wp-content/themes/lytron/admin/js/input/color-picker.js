/*
*  Color Picker
*
*  @description: 
*  @since: 3.5.8
*  @created: 17/01/13
*/

(function($){
	
	_cp = aki.fields.color_picker;
	
	/*
	*  aki/setup_fields
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$(document).live('aki/setup_fields', function(e, postbox){
		
		// validate
		if( ! $.farbtastic )
		{
			return;
		}
		

		$(postbox).find('input.aki_color_picker').each(function(){
			
			// vars
			var input = $(this);

			
			// is clone field?
			if( aki.helpers.is_clone_field(input) )
			{
				return;
			}
			

			if( input.val() )
			{
				$.farbtastic( input ).setColor( input.val() ).hsl[2] > 0.5 ? color = '#000' : color = '#fff';
				
				input.css({ 
					backgroundColor : input.val(),
					color : color
				});
			}
			
		});
		
	});
	
	
	/*
	*  Input Focus
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$('input.aki_color_picker').live('focus', function(){
		
		var input = $(this);
		
		if( ! input.val() )
		{
			input.val( '#FFFFFF' );
		}
		
		$('#aki_color_picker').css({
			left: input.offset().left,
			top: input.offset().top - $('#aki_color_picker').height(),
			display: 'block'
		});
		
		_cp.farbtastic.linkTo(this);
		
	});
	
	
	/*
	*  Input Blur
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$('input.aki_color_picker').live('blur', function(){
		
		var input = $(this);
		
		
		// reset the css
		if( ! input.val() )
		{
			input.css({ 
				backgroundColor : '#fff',
				color : '#000'
			});
			
		}
		
		
		$('#aki_color_picker').css({
			display: 'none'
		});
						
	});
	
	
	/*
	*  Document Ready
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$(document).ready(function(){
		
		
		/*
		*  Color Picker
		*/

		if( $.farbtastic )
		{
			if( !_cp.farbtastic )
			{
				$('body').append('<div id="aki_color_picker" />');
		
				_cp.farbtastic = $.farbtastic('#aki_color_picker');
			}
		}
		
	});
	

})(jQuery);