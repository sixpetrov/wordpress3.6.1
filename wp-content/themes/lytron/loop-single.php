<?php 
/*
 * The loop that displays a single post
 */
?>

<div class="entry">
<?php while(have_posts()): the_post(); ?>

    <!-- Entry header -->
    <header class='entry-header'>
		<h1 class='entry-title entry-title-post'><?php the_title(); ?></h1>

		<p class='entry-byline'>
			Posted by <span class='author'><?php the_author_posts_link(); ?></span> on
			<span class='time'><?php the_time('F j, Y') ?></span>
			in
			<span><?php the_category(', '); ?></span>        
		</p>
	</header>

    <!-- Entry Content -->
    <div class="entry-content">
        <?php the_content(); ?>
        <?php wp_link_pages( array('before' => '<div class="page-links">' . 'Pages', 'after' => '</div>' ) ); ?>
    </div>

    <!-- Tags -->
    <?php $post_tags = wp_get_post_tags($post->ID); ?>
    <?php if (! empty($post_tags)): ?>
    <div class="entry-tags">
        <p><span class="sprites tag-blue"></span><span class="entry-tags-title">Tags:</span> <?php the_tags('', ',', ''); ?> </p>
    </div>
    <?php endif; ?>

    <!-- Comments -->
    <?php comments_template('', true); ?>

<?php endwhile; ?>
</div>