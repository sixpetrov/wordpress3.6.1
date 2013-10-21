	
<!-- footer -->
<div class="wrapper" id="WrapperFooter">
    <div class="wrapper" id="WrapperFooterSidebar">
        <div class="container">
            <?php if (is_active_sidebar('sidebar-footer')) get_sidebar('footer'); ?>
            <?php do_action('footer_sidebar'); ?>
        </div>
    </div>

    <?php do_action('footer_main', TRUE); ?>
</div>

<div class="wrapper">
    <div class="container">
        <?php wp_footer(); ?>
    </div>
</div>

</body>
</html>