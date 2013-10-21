/*
*  Tab
*
*  @description: 
*  @since: 3.5.8
*  @created: 17/01/13
*/

(function($){
	
	/*
	*  aki/setup_fields
	*
	*  @description: 
	*  @since: 3.5.8
	*  @created: 17/01/13
	*/
	
	$(document).live('aki/setup_fields', function(e, postbox){
		
		$(postbox).find('.aki-tab').each(function(){
			
			// vars
			var tab = $(this),
				id = tab.attr('data-id'),
				label = tab.html(),
				postbox = tab.closest('.aki_postbox'),
				inside = postbox.children('.inside');
			

			
			// only run once for each tab
			if( tab.hasClass('aki-tab-added') )
			{
				return;
			}
			tab.addClass('aki-tab-added');
			
			
			// create tab group if it doesnt exist
			if( ! inside.children('.aki-tab-group').exists() )
			{
				inside.children('.field-tab:first').before('<ul class="hl clearfix aki-tab-group"></ul>');
			}
			
			
			// add tab
			inside.children('.aki-tab-group').append('<li><a class="aki-tab-button" href="#" data-id="' + id + '">' + label + '</a></li>');
			
			
		});
		
		
		// trigger
		$(postbox).find('.aki-tab-group').each(function(){
			
			$(this).find('li:first a').trigger('click');
			
		});

	
	});
	
	
	/*
	*  Tab group click
	*
	*  @description: 
	*  @since: 2.0.4
	*  @created: 14/12/12
	*/
	
	$('.aki-tab-button').live('click', function(){
		
		// vars
		var a = $(this),
			id = a.attr('data-id'),
			ul = a.closest('ul'),
			inside = ul.closest('.aki_postbox').children('.inside');
		
		
		// classes
		ul.find('li').removeClass('active');
		a.parent('li').addClass('active');
		
		
		// hide / show
		inside.children('.field-tab').each(function(){
			
			var tab = $(this);
			
			if( tab.hasClass('field-' + id) )
			{
				tab.nextUntil('.field-tab').removeClass('aki-tab_group-hide').addClass('aki-tab_group-show');
			}
			else
			{
				tab.nextUntil('.field-tab').removeClass('aki-tab_group-show').addClass('aki-tab_group-hide');
			}
			
		});

		$(this).trigger('blur');
		
		return false;
		
	});
		

})(jQuery);