<?php
/**
 * egencypress functions and definitions.
 *
 * Sets up the theme and provides some helper functions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development
 * and http://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 *
 * For more information on hooks, actions, and filters,
 * see http://codex.wordpress.org/Plugin_API
 *
 * @package   egencystrap WordPress Theme
 * @author    Themingstrap
 * @copyright Copyright (c) 2016, themingstrap.com
 * @link      http://www.themingpress.com
 * @since     1.0.0
 */

// Store template directory as var
$dir_child = get_stylesheet_directory_uri();


if (is_admin() && isset($_GET['activated'])){

	wp_redirect(admin_url("admin.php?page=agencystrap-setup"));
}

//cuton css
include ('posttype/posttype-portfolio.php');
include ('posttype/posttype-testimonials.php');
function agencystrap_custom_style() {
	?>
    <style type="text/css">
        .btn-solid {

        <?php if(get_theme_mod("link-color")){?>
            color:#57ad68;
            background: <?php echo esc_html(get_theme_mod("link-color")); ?>;
            border: 2px solid  <?php echo esc_html(get_theme_mod("link-color")); ?>;
        <?php } else {?>
            background: #57ad68;

            border: 2px solid #57ad68;
        <?php }?>
        }
        .btn-light {
        <?php if(get_theme_mod("link-color")){?>
            border: 2px solid <?php echo esc_html(get_theme_mod("link-color")); ?>;
        <?php } else {?>
            border: 2px solid #57ad68;
        <?php }?>
        }
    </style>
<?php }
add_action('wp_head', 'agencystrap_custom_style');

add_action( 'after_setup_theme', 'agencystrap_theme_setup' );
function agencystrap_theme_setup() {
	add_image_size( 'post-thumbs', 800, 400, true );   // (cropped)
	add_image_size( 'single-page-thumb', 1500, 500, true ); // (cropped)
	add_image_size( 'portfolio-icon', 300, 300, true ); // (cropped)
	add_image_size( 'admin-thumb', 200, 100, true ); // (cropped)
}


/*--------------------------------------*/
/* Include functions & classes
/*--------------------------------------*/

wp_enqueue_script( 'agencystrap_sticky', $dir_child.'/js/jquery.sticky.js', array( 'jquery' ), '', true);
wp_enqueue_script( 'agencystrap_bxslider', $dir_child.'/js/jquery.bxslider.js', array( 'jquery' ), '', true);
wp_enqueue_script( 'agencystrap_custom_js', $dir_child.'/js/custom.js', array( 'jquery' ), '', true);

/*--------------------------------------*/
/* cutom customizer control
/*--------------------------------------*/

if (class_exists('WP_Customize_Control')) {
	class WP_Customize_Category_Control extends WP_Customize_Control {
		/**
		 * Render the control's content.
		 *
		 * @since 3.4.0
		 */
		public function render_content() {
			$dropdown = wp_dropdown_categories(
				array(
					'name'              => '_customize-dropdown-categories-' . $this->id,
					'echo'              => 0,
					'show_option_none'  => __( '&mdash; Select &mdash;' ),
					'option_none_value' => '0',
					'selected'          => $this->value(),
				)
			);

			// Hackily add in the data link parameter.
			$dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

			printf(
				'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
				$this->label,
				$dropdown
			);
		}
	}
}
if (class_exists('WP_Customize_Control')) {
	class WP_Customize_Pages_Control extends WP_Customize_Control {
		/**
		 * Render the control's content.
		 *
		 * @since 3.4.0
		 */
		public function render_content() {
			$dropdownpage = wp_dropdown_pages(
				array(
					'name'              => '_customize-dropdown-pages-' . $this->id,
					'echo'              => 0,
					'show_option_none'  => __( '&mdash; Select Page &mdash;' ),
					'option_none_value' => '0',
					'selected'          => $this->value(),
				)
			);

			// Hackily add in the data link parameter.
			$dropdownpage = str_replace( '<select', '<select ' . $this->get_link(), $dropdownpage );

			printf(
				'<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
				$this->label,
				$dropdownpage
			);
		}
	}
}
/*--------------------------------------*/
/* theme specific customization
/*--------------------------------------*/
function agencystrap_theme_options ( $wp_customize ) {
	$wp_customize->add_panel( 'themesetting', array(
		'title' => __( 'Theme settings', 'agencystrap'),
		'priority' => 140, // Mixed with top-level-section hierarchy.
	) );
	$wp_customize->add_section( 'agencystrap_homepage' , array(
		'title'       => __( 'Home Page Setting', 'agencystrap' ),
		'priority'    => 30,
		'panel' => 'themesetting',

	) );

	$wp_customize->add_section( 'agencystrap_headercta' , array(
		'title'       => __( 'Header Call Action', 'agencystrap' ),
		'priority'    => 30,
		'panel' => 'themesetting',

	) );

	$wp_customize->add_setting(
		'headercta',
		array(
		)
	);

	$wp_customize->add_control(
		'headercta',
		array(
			'type' => 'checkbox',
			'label' => __('Active Call to Action Header', 'agencystrap' ),
			'section' => 'agencystrap_homepage',
		)
	);

	$wp_customize->add_setting(
		'headerctabtnone',
		array(

		)
	);

	$wp_customize->add_control(
		'headerctabtnone',
		array(
			'type' => 'text',
			'label' => __('Button One Text', 'agencystrap' ),
			'section' => 'agencystrap_headercta',
		)
	);
	$wp_customize->add_setting(
		'headerctabtnpage',
		array(
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Pages_Control(
			$wp_customize,
			'headerctabtnpage',
			array(
				'label'    => 'Button Link Pages',
				'settings' => 'headerctabtnpage',
				'section' => 'agencystrap_headercta',
			)
		)
	);
	$wp_customize->add_setting(
		'headerctabtntwo',
		array(

		)
	);
	$wp_customize->add_control(
		'headerctabtntwo',
		array(
			'type' => 'text',
			'label' => __('Button Two Text', 'agencystrap' ),
			'section' => 'agencystrap_headercta',
		)
	);
	$wp_customize->add_setting(
		'headerctabtntwolink',
		array(

		)
	);

	$wp_customize->add_control(
		'headerctabtntwolink',
		array(
			'type' => 'text',
			'label' => __('Button Two link', 'agencystrap' ),
			'section' => 'agencystrap_headercta',
		)
	);

	/* about call to action */

	$wp_customize->add_section( 'agencystrap_aboutcta' , array(
		'title'       => __( 'About Call To Action', 'agencystrap' ),
		'priority'    => 30,
		'panel' => 'themesetting',

	) );

	$wp_customize->add_setting(
		'aboutctaactive',
		array(
		)
	);
	$wp_customize->add_control(
		'aboutctaactive',
		array(
			'type' => 'checkbox',
			'label' => __('Active Call to Action about', 'agencystrap' ),
			'section' => 'agencystrap_homepage',
		)
	);

	$wp_customize->add_setting(
		'aboutctaheading',
		array(
		)
	);
	$wp_customize->add_control(
		'aboutctaheading',
		array(
			'type' => 'text',
			'label' => __('About Call to Action Heading', 'agencystrap' ),
			'section' => 'agencystrap_aboutcta',
		)
	);
	$wp_customize->add_setting(
		'aboutcta_description',
		array(
		)
	);
	$wp_customize->add_control(
		'aboutcta_description',
		array(
			'type' => 'textarea',
			'label' => __('About Call to Action Description', 'agencystrap' ),
			'section' => 'agencystrap_aboutcta',
		)
	);
	$wp_customize->add_setting(
		'aboutcta_btn',
		array(

		)
	);
	$wp_customize->add_control(
		'aboutcta_btn',
		array(
			'type' => 'text',
			'label' => __('Button One Text', 'agencystrap' ),
			'section' => 'agencystrap_aboutcta',
		)
	);

	$wp_customize->add_setting(
		'aboutctabtnpage',
		array(

		)
	);

	$wp_customize->add_control(
		new WP_Customize_Pages_Control(
			$wp_customize,
			'aboutctabtnpage',
			array(
				'label'    => 'Button Link Pages',
				'settings' => 'aboutctabtnpage',
				'section' => 'agencystrap_aboutcta',
			)
		)
	);
	$wp_customize->add_setting(
		'aboutctaimage',
		array(

		)
	);
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'aboutctaimage',
			array(

				'label' => __('About CTA Image', 'agencystrap' ),
				'section' => 'agencystrap_aboutcta',
			) )
	);

	/* portfolio section */
	$wp_customize->add_section( 'agencystrap_portfolio' , array(
		'title'       => __( 'Portfolio section', 'agencystrap' ),
		'priority'    => 30,
		'panel' => 'themesetting',

	) );
	$wp_customize->add_setting(
		'portfolioactive',
		array(
		)
	);
	$wp_customize->add_control(
		'portfolioactive',
		array(
			'type' => 'checkbox',
			'label' => __('Active portfolio section', 'agencystrap' ),
			'section' => 'agencystrap_homepage',
		)
	);
	$wp_customize->add_setting(
		'portfoliosecheading',
		array(
		)
	);

	$wp_customize->add_control(
		'portfoliosecheading',
		array(
			'type' => 'text',
			'label' => __('Porfolio section heading', 'agencystrap' ),
			'section' => 'agencystrap_portfolio',
		)
	);
	$wp_customize->add_setting(
		'portfoliosec_description',
		array(
		)
	);
	$wp_customize->add_control(
		'portfoliosec_description',
		array(
			'type' => 'textarea',
			'label' => __('Porfolio section Description', 'agencystrap' ),
			'section' => 'agencystrap_portfolio',
		)
	);
	$wp_customize->add_setting(
		'portfoliosec_btn',
		array(

		)
	);

	$wp_customize->add_control(
		'portfoliosec_btn',
		array(
			'type' => 'text',
			'label' => __('Portfolio section Button Text', 'agencystrap' ),
			'section' => 'agencystrap_portfolio',
		)
	);

	$wp_customize->add_setting(
		'portfoliopage',
		array(

		)
	);

	$wp_customize->add_control(
		new WP_Customize_Pages_Control(
			$wp_customize,
			'portfoliopage',
			array(
				'label'    => 'Button Link Pages',
				'settings' => 'portfoliopage',
				'section' => 'agencystrap_portfolio',
			)
		)
	);

	/* Icon content section */

	$wp_customize->add_section( 'agencystrap_icon_content' , array(
		'title'       => __( 'Icon Content section', 'agencystrap' ),
		'priority'    => 30,
		'panel' => 'themesetting',

	) );

	$wp_customize->add_setting(
		'iconcontactive',
		array(
		)
	);

	$wp_customize->add_control(
		'iconcontactive',
		array(
			'type' => 'checkbox',
			'label' => __('Active icon content section', 'agencystrap' ),
			'section' => 'agencystrap_homepage',
		)
	);

	$wp_customize->add_setting(
		'iconconheading',
		array(
		)
	);

	$wp_customize->add_control(
		'iconconheading',
		array(
			'type' => 'text',
			'label' => __('Icon content section heading', 'agencystrap' ),
			'section' => 'agencystrap_icon_content',
		)
	);
	$wp_customize->add_setting(
		'iconcondesc',
		array(
		)
	);

	$wp_customize->add_control(
		'iconcondesc',
		array(
			'type' => 'textarea',
			'label' => __('Icon content Description', 'agencystrap' ),
			'section' => 'agencystrap_icon_content',
		)
	);

	/* Call to action Right image */

	$wp_customize->add_section( 'agencystrap_rightimagecta' , array(
		'title'       => __( 'Right Image  Call To Action', 'agencystrap' ),
		'priority'    => 30,
		'panel' => 'themesetting',

	) );

	$wp_customize->add_setting(
		'rightimagectaactive',
		array(
		)
	);

	$wp_customize->add_control(
		'rightimagectaactive',
		array(
			'type' => 'checkbox',
			'label' => __('Active Call to Action Right Image ', 'agencystrap' ),
			'section' => 'agencystrap_homepage',
		)
	);

	$wp_customize->add_setting(
		'rightimagectaheading',
		array(
		)
	);

	$wp_customize->add_control(
		'rightimagectaheading',
		array(
			'type' => 'text',
			'label' => __('Right Image  to Action Heading', 'agencystrap' ),
			'section' => 'agencystrap_rightimagecta',
		)
	);
	$wp_customize->add_setting(
		'rightimagecta_description',
		array(
		)
	);

	$wp_customize->add_control(
		'rightimagecta_description',
		array(
			'type' => 'textarea',
			'label' => __('Right Image Call to Action Description', 'agencystrap' ),
			'section' => 'agencystrap_rightimagecta',
		)
	);

	$wp_customize->add_setting(
		'rightimagecta_btn',
		array(

		)
	);

	$wp_customize->add_control(
		'rightimagecta_btn',
		array(
			'type' => 'text',
			'label' => __('Button Text', 'agencystrap' ),
			'section' => 'agencystrap_rightimagecta',
		)
	);

	$wp_customize->add_setting(
		'rightimagectabtnpage',
		array(

		)
	);

	$wp_customize->add_control(
		new WP_Customize_Pages_Control(
			$wp_customize,
			'rightimagectabtnpage',
			array(
				'label'    => 'Button Link Pages',
				'settings' => 'rightimagectabtnpage',
				'section' => 'agencystrap_rightimagecta',
			)
		)
	);

	$wp_customize->add_setting(
		'rightimagectaimage',
		array(

		)
	);

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'rightimagectaimage',
			array(

				'label' => __('About CTA Image', 'agencystrap' ),
				'section' => 'agencystrap_rightimagecta',
			) )
	);

	/* testimonials section */


	$wp_customize->add_section( 'agencystrap_testimonials' , array(
		'title'       => __( 'Testimonial section', 'agencystrap' ),
		'priority'    => 30,
		'panel' => 'themesetting',

	) );

	$wp_customize->add_setting(
		'testiactive',
		array(
		)
	);

	$wp_customize->add_control(
		'testiactive',
		array(
			'type' => 'checkbox',
			'label' => __('Active testimonial in home page', 'agencystrap' ),
			'section' => 'agencystrap_homepage',
		)
	);

	$wp_customize->add_setting(
		'testiheading',
		array(
		)
	);

	$wp_customize->add_control(
		'testiheading',
		array(
			'type' => 'text',
			'label' => __('Testimonial section heading', 'agencystrap' ),
			'section' => 'agencystrap_testimonials',
		)
	);
	$wp_customize->add_setting(
		'testidesc',
		array(
		)
	);

	$wp_customize->add_control(
		'testidesc',
		array(
			'type' => 'textarea',
			'label' => __('Testimonial section Description', 'agencystrap' ),
			'section' => 'agencystrap_testimonials',
		)
	);


	/* Call to action footer */

	$wp_customize->add_section( 'agencystrap_footercta' , array(
		'title'       => __( 'Footer  Call To Action', 'agencystrap' ),
		'priority'    => 30,
		'panel' => 'themesetting',

	) );

	$wp_customize->add_setting(
		'footerctaactive',
		array(
		)
	);

	$wp_customize->add_control(
		'footerctaactive',
		array(
			'type' => 'checkbox',
			'label' => __('Active Call to Action footer ', 'agencystrap' ),
			'section' => 'agencystrap_homepage',
		)
	);

	$wp_customize->add_setting(
		'footerctaheading',
		array(
		)
	);

	$wp_customize->add_control(
		'footerctaheading',
		array(
			'type' => 'text',
			'label' => __('Footer Call to Action Heading', 'agencystrap' ),
			'section' => 'agencystrap_footercta',
		)
	);
	$wp_customize->add_setting(
		'footercta_description',
		array(
		)
	);

	$wp_customize->add_control(
		'footercta_description',
		array(
			'type' => 'textarea',
			'label' => __('Footer Call to Action Description', 'agencystrap' ),
			'section' => 'agencystrap_footercta',
		)
	);

	$wp_customize->add_setting(
		'footercta_btn',
		array(

		)
	);

	$wp_customize->add_control(
		'footercta_btn',
		array(
			'type' => 'text',
			'label' => __('Button Text', 'agencystrap' ),
			'section' => 'agencystrap_footercta',
		)
	);

	$wp_customize->add_setting(
		'footercta_btnlink',
		array(

		)
	);

	$wp_customize->add_control(
		'footercta_btnlink',
		array(
			'type' => 'text',
			'label' => __('Button Footer CTA link', 'agencystrap' ),
			'section' => 'agencystrap_footercta',
		)
	);

	/* About Page team section */

	$wp_customize->add_section( 'agencystrap_team' , array(
		'title'       => __( 'Team Section', 'agencystrap' ),
		'priority'    => 30,
		'panel' => 'themesetting',

	) );

	$wp_customize->add_setting(
		'teamactive',
		array(
		)
	);

	$wp_customize->add_control(
		'teamactive',
		array(
			'type' => 'checkbox',
			'label' => __('Active Team Section in About Page ', 'agencystrap' ),
			'section' => 'agencystrap_team',
		)
	);

	$wp_customize->add_setting(
		'teamheading',
		array(
		)
	);

	$wp_customize->add_control(
		'teamheading',
		array(
			'type' => 'text',
			'label' => __('Team Heading', 'agencystrap' ),
			'section' => 'agencystrap_team',
		)
	);
	$wp_customize->add_setting(
		'teamdescription',
		array(
		)
	);

	$wp_customize->add_control(
		'teamdescription',
		array(
			'type' => 'textarea',
			'label' => __('Team Description', 'agencystrap' ),
			'section' => 'agencystrap_team',
		)
	);

}
add_action( 'customize_register', 'agencystrap_theme_options' );

function get_the_twitter_excerpt(){
	$excerpt = get_the_content();
	$excerpt = strip_shortcodes($excerpt);
	$excerpt = strip_tags($excerpt);
	$the_str = substr($excerpt, 0, 100);
	return $the_str;
}


/**
 * Add a sidebar.
 */
function agencystrap_theme_slug_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Home Content', 'agencystrap' ),
		'id'            => 'sidebar-home',
		'description'   => __( 'Widgets in this area will be shown on home page section.', 'textdomain' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widgettitle">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'About Content', 'agencystrap' ),
		'id'            => 'sidebar-about',
		'description'   => __( 'Widgets in this area will be shown on about Page only.', 'textdomain' ),
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget'  => '</li>',
		'before_title'  => '<h3 class="widgettitle">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'agencystrap_theme_slug_widgets_init' );

/*--------------------------------------*/
/* cutom functions
/*--------------------------------------*/

function getPostViews( $postID ) {
	$count_key = 'post_views_count';
	$count = get_post_meta( $postID, $count_key, true );
	if( $count=='' ) {
		delete_post_meta( $postID, $count_key );
		add_post_meta( $postID, $count_key, '0' );
		return "0";
	}
	return $count;
}

function setPostViews($postID)
{
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if ($count =='') {
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    } else {
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}


//function getField(){
//
//}



function agencystrap_home_hero() { ?>

	<?php if ( is_front_page()) { ?>
		<?php
//		echo do_shortcode("[metaslider id=331]");
		?>
        <div class="home-hero hero-area">
            <div class="hero-inner">
                <!--	           --><?php
				//	           echo do_shortcode("[metaslider id=331]");
				//	           ?>
				<?php the_title( '<h1>', '</h1>' ); ?>
				<?php if (function_exists('get_the_subtitle')) {?>
                    <h2 class="subtitle"><?php get_the_subtitle(); ?></h2>
				<?php } ?>
				<?php if( get_theme_mod( 'headercta' ) == '1') { ?>
                    <div class="home-cta">
						<?php if( get_theme_mod( 'headerctabtnone' )) { ?>
                            <a href="<?php echo get_the_permalink(get_theme_mod('headerctabtnpage')); ?>" class="btn btn-solid"><?php echo get_theme_mod('headerctabtnone'); ?></a>
						<?php }?>
						<?php if( get_theme_mod( 'headerctabtntwo' )) { ?>
                            <a href="<?php echo esc_url(get_theme_mod('headerctabtntwolink')); ?>" class="btn btn-solid"><?php echo get_theme_mod('headerctabtntwo'); ?></a>
						<?php }?>
                    </div>

				<?php }?>

            </div>
        </div>
	<?php } elseif ( is_home() ) { ?>
        <div class="page-hero hero-area">
            <!--	        --><?php //echo  'is home' ?>
            <div class="hero-inner">
                <h1>
					<?php echo  wp_title('', true); ?>
                </h1>
				<?php if (function_exists('get_the_subtitle')) {?>
                    <h2 class="subtitle"><?php get_the_subtitle(); ?></h2>
				<?php } ?>
            </div>
        </div>
	<?php } elseif (is_archive() ) { ?>
        <!--        <div class="page-hero hero-area">-->

        <!--           <div class="hero-inner">-->
        <!--               <h1>-->
        <!--                --><?php //echo  wp_title('', true); ?>
        <!--               </h1>-->
        <!--                --><?php //if (function_exists('get_the_subtitle')) {?>
        <!--               <h2 class="subtitle">--><?php //get_the_subtitle(); ?><!--</h2>-->
        <!--               --><?php //} ?>
        <!--           </div>-->
        <!--       </div>-->
		<?php
		echo do_shortcode("[metaslider id=331]");
		?>
        <div class="main_title">  <h1>
				<?php echo  wp_title('', true); ?>
            </h1>
        </div>
	<?php } elseif (is_search() ) { ?>
        <!--           <div class="page-hero hero-area">-->
        <!--              <div class="hero-inner">-->
        <!--                  <h1>-->
        <!--                   --><?php //echo  wp_title('', true); ?>
        <!--                  </h1>-->
        <!--                   --><?php //if (function_exists('get_the_subtitle')) {?>
        <!--                  <h2 class="subtitle">--><?php //get_the_subtitle(); ?><!--</h2>-->
        <!--                  --><?php //} ?>
        <!--              </div>-->
        <!--          </div>-->
		<?php
		echo do_shortcode("[metaslider id=331]");
		?>
        <div class="main_title">  <h1>
				<?php echo  wp_title('', true); ?>
            </h1>
        </div>
	<?php } elseif (is_single() ) { ?>
        <div class="single-hero">
			<?php while ( have_posts() ) : the_post(); ?>
                <div class="post-thumb"><?php the_type_thumbnail(null,'single-page-thumb'); ?></div>
                <!--                <div class="post-thumb">--><?php //the_post_thumbnail('single-page-thumb'); ?><!--</div>-->
                <div class="content-hero-single">
					<?php the_title( '<h1>', '</h1>' ); ?>
					<?php if (function_exists('get_the_subtitle')) {?>
                        <h2 class="subtitle"><?php get_the_subtitle(); ?></h2>
					<?php } ?>

                    <div class="entry-meta">
                        <div class="image-author">
							<?php echo get_avatar( get_the_author_meta( 'email' ), '42' ); ?>
                        </div>
                        <div class="detail-author-date">
                        <span class="author-word" itemscope="itemscope" itemtype="http://schema.org/Person" itemprop="author">
<!--                       <a href="--><?php //echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ); ?><!--" rel="author">-->
                        <span class="author vcard" itemprop="name">
                        <?php echo get_the_author() ?>
                        </span>
                            <!--                        </a>-->
                        </span>
                            <span class="anything-devider">|</span>
							<?php agencystrap_posted_on(); ?>
                        </div>
                    </div>

                </div>
			<?php endwhile; wp_reset_query(); ?>

        </div>
	<?php } else  { ?>
		<?php
		echo do_shortcode("[metaslider id=331]");
		?>
        <div class="main_title">  <h1>
				<?php echo  wp_title('', true); ?>
            </h1>
        </div>
        <!--            <div class="page-hero hero-area">-->
        <!--           <div class="hero-inner">-->
        <!--                --><?php //the_title( '<h1>', '</h1>' ); ?>
        <!--                --><?php //if (function_exists('get_the_subtitle')) {?>
        <!--               <h2 class="subtitle">--><?php //get_the_subtitle(); ?><!--</h2>-->
        <!--               --><?php //} ?>
        <!---->
        <!--           </div>-->
        <!--       </div>-->
	<?php   } ?>
<?php }

function agencystrap_hero() { ?>
	<?php agencystrap_home_hero();?>
<?php }

add_action( 'agencystrap_header_ends', 'agencystrap_hero' );

/* about CTA */

function agencystrap_aboutcta() { ?>
	<?php if( get_theme_mod( 'aboutctaactive' ) == '1') { ?>
        <div class="actions about-cta">
			<?php if( get_theme_mod( 'aboutctaimage' )) { ?>
                <div class="aboutcta-left line-height-none"><img src="<?php echo get_theme_mod('aboutctaimage'); ?>" ></div>
			<?php } ?>

            <div class="aboutcta-right">
                <div class="right-content">
					<?php if( get_theme_mod( 'aboutctaheading' )) { ?>
                        <h2><?php echo get_theme_mod('aboutctaheading'); ?></h2>
					<?php } ?>
					<?php if( get_theme_mod( 'aboutcta_description' )) { ?>
                        <h3><?php echo get_theme_mod('aboutcta_description'); ?></h3>
					<?php } ?>
					<?php if( get_theme_mod( 'aboutcta_btn' )) { ?>
                        <a href="<?php echo get_the_permalink(get_theme_mod('aboutctabtnpage')); ?>" class="btn btn-light"><?php echo get_theme_mod('aboutcta_btn'); ?> </a>
					<?php } ?>
                </div>

            </div>
            <div class="clr"></div>
        </div>

	<?php } };

/* casestudy section */

function agencystrap_casestudy() {
	if( get_theme_mod( 'portfolioactive' ) == '1') { ?>
        <section class="casestudy-section">
            <div class="portfolio-top">
				<?php if( get_theme_mod( 'portfoliosecheading' )) { ?>
                    <h2><?php echo get_theme_mod('portfoliosecheading'); ?></h2>
                    <hr>
				<?php } ?>
				<?php if( get_theme_mod( 'portfoliosec_description' )) { ?>
                    <h3><?php echo get_theme_mod('portfoliosec_description'); ?></h3>
				<?php } ?>
            </div>

			<?php
			$args = array(
				'post_type' => 'portfolio',
				'orderby' => 'rand',
				'order' => 'DSC',
				'posts_per_page' => 4
			);
			$loop = new WP_Query( $args );?>
            <ul class="port-foliolist">

				<?php while ( $loop->have_posts() ) : $loop->the_post();
					?>
                    <li>
                        <div class="casestudy-thumb"><a href="<?php echo get_post_permalink( get_the_id() ); ?>"><?php the_post_thumbnail('portfolio-icon'); ?></a></div>
                        <div class="casestudy-content">
                            <h3><?php the_title(); ?></h3><p><?php echo the_content(); ?></p></div></li>

				<?php endwhile; wp_reset_query(); ?> </ul>
            <div class="portfolio-bottom">

				<?php if( get_theme_mod( 'portfoliosec_btn' )) { ?>
                <a href="<?php echo get_the_permalink(get_theme_mod('portfoliopage')); ?>" class="btn btn-solid"><?php echo get_theme_mod('portfoliosec_btn'); ?> </a></div>
		<?php } ?>

        </section> <?php } }

function agencystrap_iconcontent() {

	if( get_theme_mod( 'iconcontactive' ) == '1') { ?>
        <section class="home-icon-content">
            <div class="portfolio-top">
				<?php if( get_theme_mod( 'iconconheading' )) { ?>
                    <h2><?php echo get_theme_mod('iconconheading'); ?></h2>
                    <hr>
				<?php } ?>
				<?php if( get_theme_mod( 'iconcondesc' )) { ?>
                    <h3><?php echo get_theme_mod('iconcondesc'); ?></h3>
				<?php } ?>
            </div>

            <ul class="icon-wedget">
				<?php dynamic_sidebar( 'sidebar-home' ); ?>
            </ul>
        </section>

	<?php } }


function agencystrap_teamsection() {
	if( get_theme_mod( 'teamactive' ) == '1') { ?>
        <section class="about-widget-list">
            <div class="portfolio-top">
				<?php if( get_theme_mod( 'teamheading' )) { ?>
                    <h2><?php echo get_theme_mod('teamheading'); ?></h2>
                    <hr>
				<?php } ?>
				<?php if( get_theme_mod( 'teamdescription' )) { ?>
                    <h3><?php echo get_theme_mod('teamdescription'); ?></h3>
				<?php } ?>
            </div>

            <ul class="about-widgets">
				<?php dynamic_sidebar( 'sidebar-about' ); ?>
            </ul>
        </section>
	<?php } }



/* Right Image  CTA */

function agencystrap_rightimagecta() { ?>
	<?php if( get_theme_mod( 'rightimagectaactive' ) == '1') { ?>
        <div class="actions about-cta">
            <div class="aboutcta-left">
                <div class="right-content">
					<?php if( get_theme_mod( 'rightimagectaheading' )) { ?>
                        <h2><?php echo get_theme_mod('rightimagectaheading'); ?></h2>
					<?php } ?>
					<?php if( get_theme_mod( 'rightimagecta_description' )) { ?>
                        <h3><?php echo get_theme_mod('rightimagecta_description'); ?></h3>
					<?php } ?>
					<?php if( get_theme_mod( 'rightimagecta_btn' )) { ?>
                        <a href="<?php echo get_the_permalink(get_theme_mod('rightimagectabtnpage')); ?>" class="btn btn-light"><?php echo get_theme_mod('rightimagecta_btn'); ?> </a>
					<?php } ?>
                </div>
            </div>
            <div class="aboutcta-right line-height-none">
				<?php if( get_theme_mod( 'rightimagectaimage' )) { ?>
                    <img src="<?php echo get_theme_mod('rightimagectaimage'); ?>" >
				<?php } ?>
            </div>
            <div class="clr"></div>
        </div>
	<?php } }

/* home testimonials */
function agencystrap_home_testimonials() {
	if( get_theme_mod( 'testiactive' ) == '1') { ?>
        <section class="testi-section">
            <div class="portfolio-top">
				<?php if( get_theme_mod( 'testiheading' )) { ?>
                    <h2><?php echo get_theme_mod('testiheading'); ?></h2>
                    <hr>
				<?php } ?>
				<?php if( get_theme_mod( 'testidesc' )) { ?>
                    <h3><?php echo get_theme_mod('testidesc'); ?></h3>
				<?php } ?>
            </div>
			<?php
			$args = array(
				'post_type' => 'testimonials',
				'orderby' => 'rand',
				'order' => 'DSC',
				'posts_per_page' => 4
			);
			$loop = new WP_Query( $args );?>
            <div class="testimonial-list-container">
                <ul class="testimonial-list">
					<?php while ( $loop->have_posts() ) : $loop->the_post();
						?>
                        <li>
                            <div class="testi-content">
								<?php the_excerpt(); ?>
                                <span>-<?php the_title(); ?></span>
                            </div>
                            <div class="testi-thumb"><?php the_post_thumbnail('portfolio-icon'); ?></div>
                        </li>

					<?php endwhile;?> </ul> </div></section>
	<?php } }

/* footer call to action */

function agencystrap_footercta() { ?>

	<?php if( get_theme_mod( 'footerctaactive' ) == '1') { ?>
        <section class="footer-cta">
            <div class="footer-cta-content">
				<?php if( get_theme_mod( 'footerctaheading' )) { ?>
                    <h2><?php echo get_theme_mod('footerctaheading'); ?></h2>
				<?php } ?>
                <hr>
				<?php if( get_theme_mod( 'footercta_description' )) { ?>
                    <h3><?php echo get_theme_mod('footercta_description'); ?></h3>
				<?php } ?>
				<?php if( get_theme_mod( 'footercta_btn' )) { ?>
                    <a href="<?php echo get_theme_mod('footercta_btnlink'); ?>" class="btn btn-light"><?php echo get_theme_mod('footercta_btn'); ?> </a>
				<?php } ?>

                <div class="clr"></div>
				<?php agencystrap_socialmediafollow(); ?>
            </div>
        </section>


	<?php } }

add_action( 'agencystrap_footer_top', 'agencystrap_footercta' );

/*------------------------------------------------------- */
/* Create widget for testimonial
/*-------------------------------------------------------*/

function agencystrap_testimonials_widget() {
	register_widget( 'Agencystrap_Testimonials_Widget' );
}
add_action( 'widgets_init', 'Agencystrap_Testimonials_Widget' );



/**
 * testimonial widget class
 */
class Agencystrap_Testimonials_Widget extends WP_Widget {


	public function __construct() {

		$widget_ops = array(
			'classname' => 'widget-testimonials',
			'description' => __( 'Display an testimonials.', 'agencystrap' )
		);

		$control_ops = array(
			'id_base' => 'agencystrap_testimonials_widget',
		);
		parent::__construct( 'agencystrap_testimonials_widget', __( 'AgencyStrap: Testimonials', 'agencystrap' ), $widget_ops, $control_ops );
	}


	public function widget( $args, $instance ) {

		/** This filter is documented in wp-inc/default-widgets.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		/**
		 * Filter the content of the Text widget.
		 *
		 * @since 2.3.0
		 *
		 * @param string    $widget_text The widget content.
		 * @param WP_Widget $instance    WP_Widget instance.
		 */
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		echo $args['before_widget']; ?>
		<?php
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} ?>
		<?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?>
		<?php
		$args = array(
			'post_type' => 'testimonials',
			'orderby' => 'rand',
			'order' => 'DSC',
			'posts_per_page' => 4
		);
		$loop = new WP_Query( $args );?>
        <ul class="testimonial-list">
			<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                <li>
                    <div class="testi-content">
						<?php the_excerpt(); ?>
                        <span>-<?php the_title(); ?></span>
                    </div>
                    <div class="testi-thumb"><?php the_post_thumbnail('portfolio-icon'); ?></div>
                </li>
			<?php endwhile;?> </ul>
        </aside>
		<?php
		echo $args['after_widget'];
	}
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'icon_select' => '' ) );
		$title = strip_tags( $instance['title'] ); ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'agencystrap' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
		<?php
	}
}


/*------------------------------------------------------- */
/* Create widget for Team
/*-------------------------------------------------------*/

function agencystrap_team_widget() {
	register_widget( 'Agencystrap_Teams_Widget' );
}
add_action( 'widgets_init', 'agencystrap_team_widget' );

/**
 * Team widget class
 */
class Agencystrap_Teams_Widget extends WP_Widget {


	public function __construct() {

		$widget_ops = array(
			'classname' => 'widget-teams',
			'description' => __( 'Display an Team Member.', 'agencystrap' )
		);

		$control_ops = array(
			'id_base' => 'agencystrap_teams_widget',
		);
		parent::__construct( 'agencystrap_teams_widget', __( 'AgencyStrap: Team', 'agencystrap' ), $widget_ops, $control_ops );
		add_action('admin_enqueue_scripts', array($this, 'upload_scripts'));
	}

	public function upload_scripts()
	{
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('agencystrap_teams_widget', get_stylesheet_directory_uri() . '/js/upload-media.js', array('jquery'));
		wp_enqueue_style('thickbox');
	}
	public function widget( $args, $instance ) {

		/** This filter is documented in wp-inc/default-widgets.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$roll = apply_filters( 'widget_roll', empty( $instance['roll'] ) ? '' : $instance['roll'], $instance, $this->id_base );
		$image = apply_filters( 'widget_image', empty( $instance['image'] ) ? '' : $instance['image'], $instance, $this->id_base );
		$facebook = apply_filters( 'widget_facebook', empty( $instance['facebook'] ) ? '' : $instance['facebook'], $instance, $this->id_base );
		$linkedin = apply_filters( 'widget_linkedin', empty( $instance['linkedin'] ) ? '' : $instance['linkedin'], $instance, $this->id_base );
		$twitter = apply_filters( 'widget_twitter', empty( $instance['twitter'] ) ? '' : $instance['twitter'], $instance, $this->id_base );
		$github = apply_filters( 'widget_github', empty( $instance['github'] ) ? '' : $instance['github'], $instance, $this->id_base );
		$dribbble = apply_filters( 'widget_dribbble', empty( $instance['dribbble'] ) ? '' : $instance['dribbble'], $instance, $this->id_base );
		/**
		 * Filter the content of the Text widget.
		 *
		 * @since 2.3.0
		 *
		 * @param string    $widget_text The widget content.
		 * @param WP_Widget $instance    WP_Widget instance.
		 */
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['image'], $instance );
		echo $args['before_widget']; ?>
		<?php
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} ?>
		<?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?>
		<?php
		if ( ! empty( $image ) ) { ?>
            <img src="<?php echo $image ;?>">
		<?php	} ?>
		<?php
		if ( ! empty( $roll ) ) { ?>
            <div class="roll"><?php echo $roll ;?></div>
		<?php	} ?>
		<?php
		if ( ! empty( $facebook ) ) { ?>
            <a href="<?php echo esc_url($facebook); ?>"><i class="fa fa-facebook"></a></i>
		<?php	} ?>
		<?php
		if ( ! empty( $twitter ) ) { ?>
            <a href="<?php echo esc_url($twitter); ?>"><i class="fa fa-twitter"></a></i>
		<?php	} ?>

		<?php
		if ( ! empty( $linkedin ) ) { ?>
            <a href="<?php echo esc_url($linkedin); ?>"><i class="fa fa-linkedin"></a></i>
		<?php	} ?>
		<?php
		if ( ! empty( $github ) ) { ?>
            <a href="<?php echo esc_url($github); ?>"><i class="fa fa-github"></a></i>
		<?php	} ?>

		<?php
		if ( ! empty( $dribbble ) ) { ?>
            <a href="<?php echo esc_url($dribbble); ?>"><i class="fa fa-dribbble"></a></i>
		<?php	} ?>




		<?php
		echo $args['after_widget'];
	}
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['roll'] = strip_tags( $new_instance['roll'] );
		$instance['facebook'] = strip_tags( $new_instance['facebook'] );
		$instance['linkedin'] = strip_tags( $new_instance['linkedin'] );
		$instance['twitter'] = strip_tags( $new_instance['twitter'] );
		$instance['github'] = strip_tags( $new_instance['github'] );
		$instance['dribbble'] = strip_tags( $new_instance['dribbble'] );
		$instance['image'] = strip_tags( $new_instance['image'] );
		return $instance;
	}
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'roll' => '', 'facebook' => '', 'linkedin' => '', 'twitter' => '', 'github' => '', 'dribbble' => '', 'image' => '' ) );
		$title = strip_tags( $instance['title'] );
		$roll = strip_tags( $instance['roll'] );
		$facebook = strip_tags( $instance['facebook'] );
		$linkedin = strip_tags( $instance['linkedin'] );
		$twitter = strip_tags( $instance['twitter'] );
		$github = strip_tags( $instance['github'] );
		$dribbble = strip_tags( $instance['dribbble'] );
		$image = strip_tags( $instance['image'] );
		?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'agencystrap' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('roll'); ?>"><?php _e( 'Roll', 'agencystrap' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'roll' ); ?>" name="<?php echo $this->get_field_name( 'roll' ); ?>" type="text" value="<?php echo esc_attr( $roll ); ?>" />
        </p>


        <p>
            <label for="<?php echo $this->get_field_id('facebook'); ?>"><?php _e( 'Facebook URL:', 'agencystrap' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'facebook' ); ?>" name="<?php echo $this->get_field_name( 'facebook' ); ?>" type="text" value="<?php echo esc_attr( $facebook ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('linkedin'); ?>"><?php _e( 'Linkedin URL:', 'agencystrap' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'linkedin' ); ?>" name="<?php echo $this->get_field_name( 'linkedin' ); ?>" type="text" value="<?php echo esc_attr( $linkedin  ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e( 'Twitter URL:', 'agencystrap' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'twitter' ); ?>" name="<?php echo $this->get_field_name( 'twitter' ); ?>" type="text" value="<?php echo esc_attr( $twitter  ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('github'); ?>"><?php _e( 'Github URL:', 'agencystrap' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'github' ); ?>" name="<?php echo $this->get_field_name( 'github' ); ?>" type="text" value="<?php echo esc_attr( $github ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('dribbble'); ?>"><?php _e( 'Dribbble URL:', 'agencystrap' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'dribbble' ); ?>" name="<?php echo $this->get_field_name( 'dribbble' ); ?>" type="text" value="<?php echo esc_attr( $dribbble ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_name( 'image' ); ?>"><?php _e( 'Image:' ); ?></label>
            <input name="<?php echo $this->get_field_name( 'image' ); ?>" id="<?php echo $this->get_field_id( 'image' ); ?>" class="widefat" type="text" size="36"  value="<?php echo esc_url( $image ); ?>" />
            <input class="upload_image_button button button-primary" type="button" value="Upload Image" />
        </p>
		<?php
	}
}
/**
 * agencystrap scripts for customizer
 */
function agencystrap_customizer_js() {
	wp_enqueue_script( 'agencystrap_customizer_script', get_stylesheet_directory_uri() . '/js/agencystrap_customizer.js', array("jquery"), 'false', true  );

	wp_localize_script( 'agencystrap_customizer_script', 'agencystrap_customizer_obj', array(

		'pro' => __('View support Documatation','agencystrap')

	) );
}
add_action( 'customize_controls_enqueue_scripts', 'agencystrap_customizer_js' );

/**
 * agencystrap scripts for customizer
 */
function agencystrap_theme_details() { ?>
    <h1 style="text-align: center; text-transform:uppercase;">Thanks For Using <a href="http://themingpress.com">ThemingPress</a> Theme!</a></h1>
    <p style="text-align:center;"><img src="http://demos.themingpress.com/agencystrap/wp-content/uploads/sites/3/2016/06/Macbook_Mockup.png" width="700"></p>
<?php }
add_action( 'agencystrap_theme_name', 'agencystrap_theme_details' );
?>

<?php //移除Wordpress后台顶部左上角的W图标
function annointed_admin_bar_remove() {
	global $wp_admin_bar;
	/* Remove their stuff */
	$wp_admin_bar->remove_menu('wp-logo');
}
add_action('wp_before_admin_bar_render', 'annointed_admin_bar_remove', 0);
?>
