<?php
    /**
     * The header for our theme.
     *
     * Displays all of the <head> section and everything up till <div id="content">
     *
     * @package agencystrap
     */
?><!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

    <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?> itemscope="itemscope" itemtype="http://schema.org/WebPage">
        <?php do_action("agencystrap_budy_starts");?>
    <div id="page" class="hfeed site">
    <header id="masthead" class="site-header" itemscope="itemscope" itemtype="http://schema.org/WPHeader">
            <?php do_action("agencystrap_header_starts");?>
        <div class="header-inner">
            <div class="site-branding">
            <?php 
                $logo_image = '';
                if (function_exists('get_custom_logo')) {
                $logo_image = has_custom_logo(); 
                $output_logo = get_custom_logo();
                } 
                if(empty($logo_image)){?>
                <?php if (is_single() || is_page()) { ?>
                <h2 class="site-title" itemprop="headline"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h2>
                <?php } else{?>
                <h1 class="site-title" itemprop="headline"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                <?php } ?>
                <h2 class="site-description" itemprop="description"><?php bloginfo( 'description' ); ?></h2>
                <?php }
                else{
                echo $output_logo;
                }?>
            </div><!-- .site-branding -->

    <div id="respo-navigation">
         <?php do_action("agencystrap_navbar_before");?>
            <nav id="site-navigation" class="main-navigation" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">
                <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
            </nav><!-- #site-navigation -->
        <?php do_action("agencystrap_navbar_after");?>

    </div>
    <div id="mobile-header">
        <a id="responsive-menu-button" href="#sidr-main">
            <span class="top"></span>
            <span class="middle"></span>
            <span class="bottom"></span>
        </a>
    </div>
    </div>
        <div class="clr"></div>
        
    </header><!-- #masthead -->
        <?php do_action("agencystrap_header_ends");?>
        <div id="content" class="site-content">
