<?php 
/**
 * The main template file.
 */
?>

<?php get_header(); ?>

<!-- CONTENT -->
<div class="wrapper" id="WrapperContent">
    <div class="container">
        <div class="container-content">

            <!-- Entries -->
            <div class="container-entries">
                <?php
                    if (is_single() && get_post_type() == 'post') {
                        get_template_part('loop', 'single');
                    }
                    elseif (is_page()) {
                        get_template_part('loop', 'page');
                    }
                    elseif (is_404()) {
                        get_template_part('loop', '404');
                    }
                    else {
                        get_template_part('loop', 'index');
                    }
                ?>
            </div>

            <!-- Sidebar -->
            <?php get_sidebar(); ?>

        </div>
    </div>
</div>

<?php get_footer(); ?>