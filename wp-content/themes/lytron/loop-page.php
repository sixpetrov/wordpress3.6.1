<?php 
/*
 * The loop that displays a single page
 */
?>

<div class="entry">
<?php if (have_posts()): while (have_posts()): the_post(); ?>

    <!-- Entry header -->
    <header class='entry-header'>
		<h1 class='entry-title entry-title-post'><?php the_title(); ?></h1>
	</header>

    <!-- Entry Content -->
    <div class="entry-content">
		<?php the_content(); ?>
        <?php wp_link_pages( array('before' => '<div class="page-links">' . 'Pages', 'after' => '</div>' ) ); ?>
	</div>

<?php endwhile; ?>
<?php endif; ?>
</div>