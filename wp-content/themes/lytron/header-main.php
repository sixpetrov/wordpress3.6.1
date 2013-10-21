
<!-- header -->
<div class="wrapper" id="WrapperHeader">
    <div class="container">

        <header id="header-main">
            <div id="logo_wrapper">
                <h1><a class="sprites logo" href="<?php echo home_url('/') ?>"><?php bloginfo('name'); ?></a></h1>
            </div>

            <div id="headerRight">
                <div class="headerRight_inner">
                    <?php do_action('header_right'); ?>
                </div>
            </div>

            <!-- Navigation -->
            <div id="navbarWrapper">
                <div id="navbar">
                    <?php
                    wp_nav_menu( array(
                        'theme_location'	=> 'main_navigation',
                        'container'			=> '',
                        'menu_id'			=> 'navbar-menu',
                        'menu_class'		=> 'dropdown',
                        'depth'				=>  0,
                        'fallback_cb'		=>  ''
                    ));
                    ?>
                    <?php do_action('navbar') ?>
                </div>
            </div>
        </header>

    </div>
</div>