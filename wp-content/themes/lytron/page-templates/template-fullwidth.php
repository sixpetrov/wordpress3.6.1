<?php
/*
 * Template Name: Full width
 */
$tpl_name = "tpl_fullwidth";
?>

<?php include_once(WPSP_DIR . '/header.php') ?>

<!-- CONTENT -->
<div class="wrapper" id="WrapperContent">
    <div class="container">
        <div class="container-content">

            <!-- Entries -->
            <div class="container-entries-full">
                <div class='entries'>
                    <?php get_template_part('loop', 'page'); ?>
                </div>
            </div>

        </div>
    </div>
</div>

<?php get_footer(); ?>