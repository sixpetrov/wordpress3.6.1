<?php 
/*
 * The loop that displays the list of posts with 
 * excerpt and (or) thumbnails
 */
?>
	
<?php if (have_posts()): while(have_posts()): the_post(); ?>

<!-- Entry box -->
<article class="entrybox">

    <!-- Entry box header -->
    <header class='entry-header'>
		<h1 class='entry-title entry-title-index'><?php the_title(); ?></h1>

		<p class='entry-byline'>
			Posted by <span class='author'><?php the_author_posts_link(); ?></span> on
			<span class='time'><?php the_time('F j, Y') ?></span>
			in
			<span><?php the_category(', '); ?></span>        
		</p>
	</header>

    <!-- Entry box content -->
	<div class='entrybox-content'>
		<?php if(has_post_thumbnail()): ?>
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail('post-thumbnail', array('class' => 'post-thumb', 'alt' => get_the_title(), 'title'=>'')); ?>
			</a>
		<?php endif; ?>

		<p><?php echo wpsp_trim_content(500) ?></p>

		<a href="<?php the_permalink() ?>" class="readmore">Continue Reading</a>
	</div>

</article>

<?php endwhile; ?>
<?php endif; ?>

<?php neotheme_pagination(); ?>