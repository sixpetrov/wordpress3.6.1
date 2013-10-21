<?php 
/*
 * Template Name: Home
 */
?>

<?php get_header(); ?>

<!-- slider -->
<div class="wrapper" id="WrapperSlider">
    <?php $wpspSlider->includes(); ?>
</div>

<!-- home content -->
<div class="wrapper" id="WrapperHome">
    <div class="container">
        <div class="container-content">
            <!-- entry -->
            <div class="container-entries" id="container-entries-home">
                <div class="entry">
                    <?php if (have_posts()): while (have_posts()): the_post(); ?>
                        <!-- Entry Content -->
                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <aside id="sidebar-home" class="sidebar">
                <?php dynamic_sidebar('sidebar-home'); ?>
            </aside>
        </div>
    </div>
</div>

<?php get_footer(); ?>