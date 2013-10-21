<?php
/*
 * Automatically remove tooltips from category and page dropdown menu
 */
function remove_html_title($input) 
{
	return preg_replace_callback('#\stitle=["|\'].*["|\']#',
			create_function(
					'$matches',
					'return "";'),
			$input
	);
}

add_filter('wp_list_categories', 'remove_html_title');
add_filter('wp_list_pages', 'remove_html_title');

/**
 * Display comment author html link,
 * or just the name of the author
 *
 * @custom function that modifies default comment_author_link() function
 * @by Petrov
 */
function neotheme_comment_author_link( $comment_ID = 0 ) {
	$url    = get_comment_author_url( $comment_ID );
	$author = get_comment_author( $comment_ID );

	if ( empty( $url ) || 'http://' == $url )
		$return = '<p>' . $author .'</p>';
	else
		$return = "<a href='$url' rel='external nofollow' target='_blank'>$author</a>";

	echo $return;
}

/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function neotheme_format_comment($comment, $args, $depth)
{
	$GLOBALS['comment'] = $comment;

	switch( $comment->comment_type ):
	case 'pingback':
	case 'trackback':
		?>
			<li class="pings">
				<p class="ping-title"><span><?php _e( 'Pingback by ', WPSP_DOMAIN ); ?></span><?php comment_author_link(); ?></p>
				<p class='ping-content'><?php comment_excerpt(); ?></p>
		<?php
			break;
		default:
		?>
		   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
			   <div id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">
                    <!-- avatar -->
                    <div class="avatar">
                        <?php echo get_avatar($comment,$size='57'); ?>
                    </div>

                    <div class="comment-content">

                        <!-- header -->
                        <div class="comment-header">
                            <?php neotheme_comment_author_link() ?>

                            <div class="comment-metadata">
                                <span class="comment-date"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php echo(get_comment_date()) ?></a></span>
                                <span class="comment-metadata-separator">|</span>
                                <span class="reply-container">
                                    <?php comment_reply_link(array_merge( $args, array('reply_text' => __('Reply', WPSP_DOMAIN),'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
                                </span>
                            </div>
                        </div>

                        <?php if( $comment->comment_approved == '0' ): ?>
                            <div class="moderation"><em><?php _e('Your comment is awaiting moderation.', WPSP_DOMAIN) ?></em></div>
                        <?php endif; ?>

                        <!-- text -->
                        <div class="comment-text">
                            <?php comment_text() ?>
                        </div>

                    </div>

				</div> <!-- end comment-body-->	
<?php
		break;
	endswitch; 
} 


/**
 * Comments count
 * modify the comment counts to only reflect the number of comments minus pings
 */
function neotheme_comment_count( $count ) {
	if ( ! is_admin() ) {
		global $id;
		$get_comments = get_comments('post_id=' . $id);
		$comments_by_type = &separate_comments($get_comments);
		return count($comments_by_type['comment']);
	} else {
		return $count;
	}
}

add_filter('get_comments_number', 'neotheme_comment_count', 0);


/*
 * Add "first" and "last" CSS classes to dynamic sidebar widgets.
 * Also adds numeric index class for each widget (widget-1, widget-2, etc.)
 */
function neotheme_widgets_css($params) {

	global $my_widget_num; // Global a counter array
	$this_id = $params[0]['id']; // Get the id for the current sidebar we're processing
	$arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets

	if(!$my_widget_num) {// If the counter array doesn't exist, create it
		$my_widget_num = array();
	}

	if(!isset($arr_registered_widgets[$this_id]) || !is_array($arr_registered_widgets[$this_id])) { // Check if the current sidebar has no widgets
		return $params; // No widgets in this sidebar... bail early.
	}

	if(isset($my_widget_num[$this_id])) { // See if the counter array has an entry for this sidebar
		$my_widget_num[$this_id] ++;
	} else { // If not, create it starting with 1
		$my_widget_num[$this_id] = 1;
	}

	$class = 'class="widget-' . $my_widget_num[$this_id] . ' '; // Add a widget number class for additional styling options

	if($my_widget_num[$this_id] == 1) { // If this is the first widget
		$class .= 'widget-first ';
	} elseif($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) { // If this is the last widget
		$class .= 'widget-last ';
	}

	$params[0]['before_widget'] = str_replace('class="', $class, $params[0]['before_widget']); // Insert our new classes into "before widget"

	return $params;

}

add_filter('dynamic_sidebar_params', 'neotheme_widgets_css');


/**
 * Custom Pagination
 *
 * @param $pages - default empty string, this parameter is used in custom loops neotheme_pagination($wp_query->max_num_pages)
 * @param $range - default 2
 */
function neotheme_pagination($pages = '', $range = 2)
{
	$showitems = ($range * 2)+1;

	global $paged;
	if(empty($paged)) $paged = 1;

	if($pages == '')
	{
		global $wp_query;
		$pages = $wp_query->max_num_pages;
		if(!$pages)
		{
			$pages = 1;
		}
	}

	if(1 != $pages)
	{
		echo "<div class='pagination'>";
		if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
		if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

		for ($i=1; $i <= $pages; $i++)
		{
		if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
		{
		echo ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
		}
		}

		if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";
		if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
		echo "</div>";
	}
}