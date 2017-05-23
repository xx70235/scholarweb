<?php

    /*
    * Author Credit
    */

    function agencystrap_threecolumnfooter(){?>
    <footer id="colophon" class="site-footer" itemscope="itemscope" itemtype="http://schema.org/WPFooter">
        <div class="container">
        <div class="three-column-footer">
            <?php dynamic_sidebar( "footer-1" ); ?>
          </div>
        </div>
       </footer><!-- #colophon -->
     <div class="footer-credit-important">
       <div class="container">
       <div class="copyright-text">

        <?php if(get_theme_mod("footer_copy")){
        echo get_theme_mod('footer_copy');
    } else {?>

            &copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>

           <?php } ?>
       </div>
       <div class="back-top">
    <a href="#" id="back-to-top" title="Back to top"><?php _e( 'Back To Top', 'agencystrap' )?> <i class="fa fa-arrow-circle-o-up"></i></a>
       </div>
       </div>
       </div>
    </div><!-- #page -->
    <?php
    }



    /*-------------------------------------------------
    * Customizer Settings
    *------------------------------------------------*/

    function agencystrap_theme_customizer( $wp_customize ) {

    $wp_customize->add_panel( 'styling', array(
      'title' => __( 'Styling', 'agencystrap'),
      'priority' => 60, // Mixed with top-level-section hierarchy.
    ) );
    $wp_customize->add_section( 'agencystrap_headerbg' , array(
        'title'       => __( 'Header Background', 'agencystrap' ),
        'priority'    => 30,
        'panel' => 'styling',

    ) );

    $wp_customize->add_setting(
        'headerbg',
        array(
            'default' => 'dark',
            'sanitize_callback' => 'sanitize_key',
        )
    );

    $wp_customize->add_control(
        'headerbg',
        array(
            'type' => 'radio',
            'label' => __('Header Background', 'agencystrap' ),
            'section' => 'agencystrap_headerbg',
            'choices' => array(
                'dark' => 'Dark',
                'light' => 'Light',
            ),
        )
    );

    $wp_customize->add_section( 'agencystrap_theme_color' , array(
        'title'       => __( 'Global Color', 'agencystrap' ),
        'priority'    => 30,
        'panel' => 'styling',
    ) );

    $wp_customize->add_setting(
        'color-setting',
        array(
            'default' => '#57ad68',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'color-setting',
            array(
                'label' => __('Primary Color', 'agencystrap' ),
                'section' => 'agencystrap_theme_color',
                'settings' => 'color-setting',
            )
        )
    );


    $wp_customize->add_setting(
        'background-color',
        array(
            'default' => '#f5f5f5',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'background-color',
            array(
                'label' => __('Background Color', 'agencystrap' ),
                'section' => 'agencystrap_theme_color',
                'settings' => 'background-color',
            )
        )
    );


    $wp_customize->add_setting(
        'link-color',
        array(
            'default' => '#57ad68',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'link-color',
            array(
                'label' => __('Link Color', 'agencystrap' ),
                'section' => 'agencystrap_theme_color',
                'settings' => 'link-color',
            )
        )
    );




    $wp_customize->add_section( 'agencystrap_postbox_color' , array(
        'title'       => __( 'Post Box Color', 'agencystrap' ),
        'priority'    => 30,
        'panel' => 'styling',
    ) );

    $wp_customize->add_setting(
        'postbox-color',
        array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'postbox-color',
            array(
                'label' => __('Background Color', 'agencystrap' ),
                'section' => 'agencystrap_postbox_color',
                'settings' => 'postbox-color',
            )
        )
    );
    $wp_customize->add_setting(
        'ptitle-color',
        array(
            'default' => '#464646',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'ptitle-color',
            array(
                'label' => __('Title Color', 'agencystrap' ),
                'section' => 'agencystrap_postbox_color',
                'settings' => 'ptitle-color',
            )
        )
    );
    $wp_customize->add_setting(
        'bfont-color',
        array(
            'default' => '#5D5D5D',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'bfont-color',
            array(
                'label' => __('Font Color', 'agencystrap' ),
                'section' => 'agencystrap_postbox_color',
                'settings' => 'bfont-color',
            )
        )
    );

    $wp_customize->add_setting(
        'pmeta-color',
        array(
            'default' => '#b5b5b5',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'pmeta-color',
            array(
                'label' => __('Meta Color', 'agencystrap' ),
                'section' => 'agencystrap_postbox_color',
                'settings' => 'pmeta-color',
            )
        )
    );
    $wp_customize->add_setting(
        'plmeta-color',
        array(
            'default' => '#939393',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'plmeta-color',
            array(
                'label' => __('Meta Link Color', 'agencystrap' ),
                'section' => 'agencystrap_postbox_color',
                'settings' => 'plmeta-color',
            )
        )
    );




    $wp_customize->add_section( 'agencystrap_sidebar_color' , array(
        'title'       => __( 'Sidebar Color', 'agencystrap' ),
        'priority'    => 30,
        'panel' => 'styling',
    ) );

    $wp_customize->add_setting(
        'sidebar-color',
        array(
            'default' => '#ffffff',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'sidebar-color',
            array(
                'label' => __('Background Color', 'agencystrap' ),
                'section' => 'agencystrap_sidebar_color',
                'settings' => 'sidebar-color',
            )
        )
    );

    $wp_customize->add_setting(
        'sbtitle-color',
        array(
            'default' => '#000000',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'sbtitle-color',
            array(
                'label' => __('Title Color', 'agencystrap' ),
                'section' => 'agencystrap_sidebar_color',
                'settings' => 'sbtitle-color',
            )
        )
    );

    $wp_customize->add_setting(
        'sblink-color',
        array(
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'sblink-color',
            array(
                'label' => __('Widget Text Color', 'agencystrap' ),
                'section' => 'agencystrap_sidebar_color',
                'settings' => 'sblink-color',
            )
        )
    );

    /*----------------------------------------------
    * Typography
    *---------------------------------------------*/
    $wp_customize->add_panel( 'typo', array(
      'title' => __( 'Typography', 'agencystrap'),
      'priority' => 80, // Mixed with top-level-section hierarchy.
    ) );

    /**
     * Adds textarea support to the theme customizer
     */
    class agencystrap_Customize_Textarea_Control extends WP_Customize_Control {
        public $type = 'fontsize';

        public function render_content() {
            ?>
                <label>
                    <input type="number" min="1" <?php $this->link(); ?> />
                </label>
            <?php
        }
    }

    $wp_customize->add_section( 'howl-themes_fontsss' , array(
        'title'       => __( 'Font size', 'agencystrap' ),
        'priority'    => 30,
        'panel' => 'typo',
    ) );
    $wp_customize->add_setting(
      'fontsize',
        array(
            'default' => '18',
            'sanitize_callback' => 'sanitize_text_field',
        )
      );

    $wp_customize->add_control(
        new agencystrap_Customize_Textarea_Control(
            $wp_customize,
            'fontsize',
            array(
                'section' => 'howl-themes_fontsss',
                'settings' => 'fontsize'
            )
        )
    );

    $wp_customize->add_section( 'howl-themes_typography' , array(
        'title'       => __( 'Font Family', 'agencystrap' ),
        'priority'    => 30,
        'panel' => 'typo',
    ) );
    $wp_customize->add_setting(
       'primary-font',
        array(
            'default' => 'Roboto',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
         'primary-font',
        array(
            'type' => 'select',
            'label' => __('Primary Font', 'agencystrap' ),
            'section' => 'howl-themes_typography',
            'choices' => array(

    'Open Sans' => 'Open Sans',
    'Raleway' => 'Raleway',
    'Josefin Sans' => 'Josefin Sans',
    'Oswald' => 'Oswald',
    'PT Sans' => 'PT Sans',
    'Merriweather' => 'Merriweather',
    'Lato' => 'Lato',
    'Ubuntu' => 'Ubuntu',
    'Montserrat' => 'Montserrat',
    'Bitter' => 'Bitter',
    'Rajdhani' => 'Rajdhani',
    'Droid Sans' => 'Droid Sans',
    'Dosis' => 'Dosis',
    'Roboto' => 'Roboto',
            ),
        )
    );

    $wp_customize->add_setting(
       'secondary-font',
        array(
            'default' => 'Roboto Slab',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
         'secondary-font',
        array(
            'type' => 'select',
            'label' => __('Secondary Font', 'agencystrap' ),
            'section' => 'howl-themes_typography',
            'choices' => array(

    'Open Sans' => 'Open Sans',
    'Lato' => 'Lato',
    'Roboto Condensed' => 'Roboto Condensed',
    'Source Sans Pro' => 'Source Sans Pro',
    'Raleway' => 'Raleway',
    'Open Sans Condensed' => 'Open Sans Condensed',
    'Roboto Slab' => 'Roboto Slab',
    'Merriweather' => 'Merriweather',
    'Titillium Web' => 'Titillium Web',
    'Dosis' => 'Dosis',
    'Oxygen' => 'Oxygen',
    'Hind' => 'Hind',
    'Alegreya Sans' => 'Alegreya Sans',
    'Exo 2' => 'Exo 2',
    'Merriweather Sans' => 'Merriweather Sans',
    'Fira Sans' => 'Fira Sans',
    'Josefin Sans' => 'Josefin Sans',
    'Ubuntu' => 'Ubuntu',
    'Rajdhani' => 'Rajdhani',
    'Roboto' => 'Roboto',
            ),
        )
    );




    /*----------------------------
    * footer copy
    *---------------------------*/
    $wp_customize->add_section(
            'footer_copy',
            array(
                'title' =>  __( 'Footer Copy', 'agencystrap' ),
                'description' => __( 'Footer Copy text', 'agencystrap' ),
                'priority' => 80,
            )
        );
        $wp_customize->add_setting(
        'footer_copy',
        array(
        'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control(
        'footer_copy',
        array(
            'label' => __( 'Footer Copy text', 'agencystrap' ),
            'section' => 'footer_copy',
            'type' => 'text',

        )
    );



    /*----------------------------
    * Social
    *---------------------------*/
    $wp_customize->add_section(
            'social_icons',
            array(
                'title' =>  __( 'Social', 'agencystrap' ),
                'description' => __( 'Add URLs', 'agencystrap' ),
                'priority' => 80,
            )
        );
        $wp_customize->add_setting(
        'fsocial_url',
        array(
        'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control(
        'fsocial_url',
        array(
            'label' => __( 'Facebook', 'agencystrap' ),
            'section' => 'social_icons',
            'type' => 'text',
        )
    );
         $wp_customize->add_setting(
        'tsocial_url',
        array(
        'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control(
        'tsocial_url',
        array(
            'label' => __( 'Twitter', 'agencystrap' ),
            'section' => 'social_icons',
            'type' => 'text',
        )
    );
      $wp_customize->add_setting(
        'gsocial_url',
        array(
        'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(
        'gsocial_url',
        array(
            'label' => __( 'Google+', 'agencystrap' ),
            'section' => 'social_icons',
            'type' => 'text',
        )
    );
      $wp_customize->add_setting(
        'psocial_url',
        array(
        'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(
        'psocial_url',
        array(
            'label' => __( 'Pinterest', 'agencystrap' ),
            'section' => 'social_icons',
            'type' => 'text',
        )
    );
      $wp_customize->add_setting(
        'isocial_url',
        array(
        'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(
        'isocial_url',
        array(
            'label' => __( 'Instagram', 'agencystrap' ),
            'section' => 'social_icons',
            'type' => 'text',
        )
    );
      $wp_customize->add_setting(
        'lsocial_url',
        array(
        'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(
        'lsocial_url',
        array(
            'label' => __( 'Linkedin', 'agencystrap' ),
            'section' => 'social_icons',
            'type' => 'text',
        )
    );
      $wp_customize->add_setting(
        'ysocial_url',
        array(
        'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(
        'ysocial_url',
        array(
            'label' =>  __( 'Youtube', 'agencystrap' ),
            'section' => 'social_icons',
            'type' => 'text',
        )
    );
      $wp_customize->add_setting(
        'rsocial_url',
        array(
        'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(
        'rsocial_url',
        array(
            'label' =>  __( 'RSS', 'agencystrap' ),
            'section' => 'social_icons',
            'type' => 'text',
        )
    );
      $wp_customize->add_setting(
        'tumsocial_url',
        array(
        'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(
        'tumsocial_url',
        array(
            'label' =>  __( 'Tumblr', 'agencystrap' ),
            'section' => 'social_icons',
            'type' => 'text',
        )
    );
          $wp_customize->add_setting(
        'sondsocial_url',
        array(
        'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(
        'sondsocial_url',
        array(
            'label' =>  __( 'Soundcloud', 'agencystrap' ),
            'section' => 'social_icons',
            'type' => 'text',
        )
    );
    }
    add_action( 'customize_register', 'agencystrap_theme_customizer' );

    /*--------------------------------
    * Custom CSS
    *--------------------------------*/
    function agencystrap_custom_css() {
    ?>
    <style>

    html{
      <?php if(get_theme_mod('primary-font')) { ?>
    font-family:<?php echo esc_html(get_theme_mod('primary-font')); ?>;
    <?php } else{?>
      font-family: 'Roboto';
    <?php } ?>
    }

    button,
    input[type="button"],
    input[type="reset"],
    input[type="submit"],
    .calltobtn{
      <?php if(get_theme_mod("color-setting")){ ?>
    background: <?php echo esc_html(get_theme_mod("color-setting")); ?>;
    <?php } else{ ?>
    background: #57ad68;
    <?php } ?>
    }
    .nav-links a:hover, .nav-links span {
     <?php if(get_theme_mod("headerbg")){ ?>
    background: <?php echo esc_html(get_theme_mod("color-setting")); ?> !important;
    border: 1px solid <?php echo esc_html(get_theme_mod("color-setting")); ?> !important;
    color:#fff !important;
    <?php } else{ ?>
    background: #57ad68 !important;
    border: 1px solid #57ad68 !important;
    color:#fff !important;
    <?php } ?>
      }

    a:link, a:visited{
      <?php if(get_theme_mod("link-color")){?>
    color:<?php echo esc_html(get_theme_mod("link-color")); ?>;
    <?php } else{?>
     color:#57ad68;
    <?php } ?>
    }

    .entry-title, .entry-title a{
      <?php if(get_theme_mod("ptitle-color")) { ?>
    color:<?php echo esc_html(get_theme_mod("ptitle-color")); ?>;
    <?php } else{?>
     color:#000000;
    <?php } ?>
    }

    .entry-content{
    <?php if(get_theme_mod("bfont-color")) { ?>
    color:<?php echo esc_html(get_theme_mod("bfont-color")); ?>;
    <?php } else{?>
     color:#5D5D5D;
    <?php } ?>
    <?php if(get_theme_mod('secondary-font')) { ?>
    font-family:<?php echo esc_html(get_theme_mod('secondary-font')); ?>;
    <?php } else{?>
     font-family:'Roboto';
    <?php } ?>
    }
    .entry-meta{
       <?php if(get_theme_mod("pmeta-color")) { ?>
    color:<?php echo esc_html(get_theme_mod("pmeta-color")); ?>;
    <?php } else{?>
     color:#b5b5b5;
    <?php } ?>
    }
    .entry-meta a{
       <?php if(get_theme_mod("plmeta-color")) { ?>
    color:<?php echo esc_html(get_theme_mod("plmeta-color")); ?>;
    <?php } else{?>
     color:#939393;
    <?php } ?>
    }
    h3.widget-title{
       <?php if(get_theme_mod("sbtitle-color")) { ?>
    color:<?php echo esc_html(get_theme_mod("sbtitle-color")); ?>;
    <?php } else{?>
     color:#000000;
    <?php } ?>
    }
    #recentcomments li:before, .widget_categories ul li:before, ol.recenthowl a, .recenthowl li::before, #recentcomments a, .widget_archive a, .widget_categories a, .widget_archive ul li:before{
    <?php if(get_theme_mod("sblink-color")) { ?>
    color:<?php echo esc_html(get_theme_mod("sblink-color")); ?> !important;
    <?php } ?>
    }
    .recenthowl li:before, h3.widget-title{
      border-color:<?php echo esc_html(get_theme_mod("sblink-color")); ?> !important;
    }

    <?php if(get_theme_mod('fontsize')) { ?>
    .entry-content p{
    font-size:<?php echo esc_html(get_theme_mod('fontsize')); ?>px;
    }
    <?php } else{?>
      .entry-content p{
    font-size: 18px;
    }
    <?php }if(get_theme_mod("headerbg") == 'light') { ?>

    #masthead {
        background: #fff !important;
        color: #333;
    }
    #masthead .site-title a, #masthead .site-title, .main-navigation ul li a{
        color: #333 !important;
    }
    .main-navigation ul li a:hover{
        background: #efefef !important;
    }
    .sub-menu {
        background: #efefef !important;
        border: 1px solid #ccc !important;
    }
    #mobile-header a {
        color: #333 !important;
    }
    <?php } if(get_theme_mod("sidebar-color")){?>
    .widget-area {
    background:<?php echo esc_html(get_theme_mod("sidebar-color")) ?> !important;
    }
    <?php } if(get_theme_mod("postbox-color")){?>
    .content-area article{
    background:<?php echo esc_html(get_theme_mod("postbox-color")) ?> !important;
    }

    <?php } if(get_theme_mod("background-color")){?>
    body{
      background:<?php echo esc_html(get_theme_mod("background-color")) ?> !important;
    }
    <?php } ?>
      </style>
      <?php }
      add_action('wp_head', 'agencystrap_custom_css');
