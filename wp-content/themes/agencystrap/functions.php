<?php
/**
 * agencystrap functions and definitions
 *
 * @package themingpress
 */

if ( ! function_exists( 'agencystrap_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function agencystrap_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on agencystrap, use a find and replace
	 * to change 'agencystrap' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'agencystrap', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
   add_theme_support( 'post-formats', array( 'aside', 'gallery', 'image', 'link' ) );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'agencystrap' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );


}
endif; // agencystrap_setup
add_action( 'after_setup_theme', 'agencystrap_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function agencystrap_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'agencystrap_content_width', 640 );
}
add_action( 'after_setup_theme', 'agencystrap_content_width', 0 );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function agencystrap_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'agencystrap' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title"><span>',
		'after_title'   => '</span></h3>',
	) );
}
add_action( 'widgets_init', 'agencystrap_widgets_init' );

function agencystrap_postnav(){ ?>
    <div class="navigation"><div class="post-nav pri-post"><?php previous_post_link(); ?></div>    <div class="post-nav next-post"><?php next_post_link(); ?></div></div>

 <?php
}


/**
 * Enqueue scripts and styles.
 */
function agencystrap_scripts() {
	wp_enqueue_style( 'agencystrap-style', get_stylesheet_uri() );

    $font1 = get_theme_mod('primary-font');
    if($font1 == null){
      $font1 = 'Roboto';
    }
    $font2 = get_theme_mod('secondary-font');
    if($font2 == null){
      $font2 = 'Roboto+Slab';
    }

  wp_enqueue_style( 'agencystrap_google-fonts', '//fonts.googleapis.com/css?family='. str_replace(" ", "+", $font1) .':400,300,900|'. str_replace(" ", "+", $font2) . ':300');
  wp_enqueue_style( 'font-awesome', get_template_directory_uri().'/css/font-awesome.min.css');
  wp_enqueue_script( 'agencystrap_myscript', get_template_directory_uri().'/js/howljs.js', array( 'jquery' ), '', true);


	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'agencystrap_scripts' );


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom Excerpt Length
 */
function agencystrap__excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'agencystrap__excerpt_length', 999 );

/**
 * HowlFunctions
 */
require get_template_directory() . '/inc/theme-functions.php';

require_once get_template_directory() . '/lib/agencystrap-load-required-plugins.php';

require_once get_template_directory() . '/widgets/icon-select/icon-text.php';
require_once get_template_directory() . '/panel/theme-admin_page.php';

/**
 * Howl Social Buttons
 */
function agencystrap_socialmediafollow(){
	if(get_theme_mod("fsocial_url") || get_theme_mod("tsocial_url") || get_theme_mod("gsocial_url") || get_theme_mod("psocial_url") || get_theme_mod("isocial_url") || get_theme_mod("ysocial_url") || get_theme_mod("rsocial_url") || get_theme_mod("tumsocial_url") || get_theme_mod("sondsocial_url") || get_theme_mod("lsocial_url")){
echo '<div class="drag-social-button"><h3 class="social-title"><span>Follow Us</span></h3><ul>';
	}
   if(get_theme_mod("fsocial_url")){
    echo'<li><a class="fblinkbtn" href="'.esc_url(get_theme_mod("fsocial_url")).'" target="blank"><i class="fa fa-facebook"></i></a></li>';
}
   if(get_theme_mod("tsocial_url")){
echo'<li><a class="twlinkbtn" href="'.esc_url(get_theme_mod("tsocial_url")).'" target="blank"><i class="fa fa-twitter"></i></a></li>';
}
   if(get_theme_mod("gsocial_url")){
echo'
<li><a class="gplinkbtn" href="'.esc_url(get_theme_mod("gsocial_url")).'" target="blank"><i class="fa fa-google-plus"></i></a></li>';
}
if(get_theme_mod("isocial_url")){
echo'
 <li><a class="inslinkbtn" href="'.esc_url(get_theme_mod("isocial_url")).'" target="blank"><i class="fa fa-instagram"></i></a></li>';
}
if(get_theme_mod("psocial_url")){
echo'
 <li><a class="pilinkbtn" href="'.esc_url(get_theme_mod("psocial_url")).'" target="blank"><i class="fa fa-pinterest-p"></i></a></li>';
}
if(get_theme_mod("ysocial_url")){
echo' <li><a class="yolinkbtn" href="'.esc_url(get_theme_mod("ysocial_url")).'" target="blank"><i class="fa fa-youtube"></i></a></li>';
}
if(get_theme_mod("lsocial_url")){
echo'<li><a class="lilinkbtn" href="'.esc_url(get_theme_mod("lsocial_url")).'" target="blank"><i class="fa fa-linkedin"></i></a></li>';
}
if(get_theme_mod("rsocial_url")){
echo' <li><a class="rslinkbtn" href="'.esc_url(get_theme_mod("rsocial_url")).'" target="blank"><i class="fa fa-rss"></i></a></li>';
}
if(get_theme_mod("tumsocial_url")){
echo' <li><a class="tulinkbtn" href="'.esc_url(get_theme_mod("tumsocial_url")).'" target="blank"><i class="fa fa-tumblr"></i></a></li>';
}
if(get_theme_mod("sondsocial_url")){
echo' <li><a class="sndlinkbtn" href="'.esc_url(get_theme_mod("sondsocial_url")).'" target="blank"><i class="fa fa-soundcloud"></i></a></li>';
}
	if(get_theme_mod("fsocial_url") || get_theme_mod("tsocial_url") || get_theme_mod("gsocial_url") || get_theme_mod("psocial_url") || get_theme_mod("isocial_url") || get_theme_mod("ysocial_url") || get_theme_mod("rsocial_url") || get_theme_mod("tumsocial_url") || get_theme_mod("sondsocial_url") || get_theme_mod("lsocial_url")){
echo "</ul></div>";
	}
}
/*
 * Registering Footer Area
*/
function agencystrap_fwidgets_init() {
  register_sidebar( array(
    'name'          => __( 'Footer', 'agencystrap' ),
    'id'            => 'footer-1',
    'description'   => '',
    'before_widget' => '<aside id="%1$s" class="fwidget %2$s">',
    'after_widget'  => '</aside>',
    'before_title'  => '<h4 class="fwidget-title">',
    'after_title'   => '</h4>',
  ) );
}
add_action( 'widgets_init', 'agencystrap_fwidgets_init' );
/*-----------------------
* Custom Logo Support
*----------------------*/
function agencystrap_custom_logo() {
    add_theme_support('custom-logo');
}

add_action('after_setup_theme', 'agencystrap_custom_logo');

class WPCTAWidget{
	var $wpl_opt_version=array();
	public function __construct(){
		add_shortcode( 'WP_CTA_Widget', array($this, 'WP_CTA_Widget_shortcode') );
		add_action( 'in_widget_form', array($this, 'WP_CTA_Widget_shortcode_form'));
		add_action ( 'widgets_init', create_function ( '', 'register_widget( "WP_CTA_Widget" );' ) );
		new WP_CTA_Widget();
	}

	public function WP_CTA_Widget_shortcode( $atts,$content=null ){
		global $wp_registered_widgets;
		$atts['echo'] = false;
		extract( shortcode_atts( array('id' => '','title' => true, /* wheather to display the widget title */
		'before_widget' => '','before_title' => '','after_title' => '','after_widget' => ''), $atts));
		if( empty( $id ) || ! isset( $wp_registered_widgets[$id] ) )
			return;

		// get the widget instance options
		preg_match( '/(\d+)/', $id, $number );
		$options = get_option( $wp_registered_widgets[$id]['callback'][0]->option_name );
		$instance = $options[$number[0]];
		$class = get_class( $wp_registered_widgets[$id]['callback'][0] );
		if( ! $instance || ! $class )
			return;

		// set this title to something arbitrary so we can remove it later on
		if( $title == false ) {
			$atts['before_title'] = '<div class="wsh-title">';
			$atts['after_title'] = '</div>';
		}

		ob_start();
		the_widget( $class, $instance, $atts );
		$content = ob_get_clean();
		if( $title == false ) $content = preg_replace( '/<h3 class="wsh-title">(.*?)<\/h3>/', '', $content );
		return $content;
	}
	function WP_CTA_Widget_shortcode_form( $widget) {
		if($widget->id_base=='wp-call-to-action-widget')
			echo '<p>' . __( 'Shortcode' ) . ': ' . ( ( $widget->number == '__i__' ) ? __( 'Please save this first.' ) : '<code>[WP_CTA_Widget id="'. $widget->id .'"]</code>' ) . '</p>';
	}
}
new WPCTAWidget();
class WP_CTA_Widget extends WP_Widget{

	function __construct(){
		parent::__construct('AS-call-to-action-widget',
							__('AS Call To Action Widget','as-call-to-action-widget'),
							array('description'=>__('A text widget with a call to action button.','wp-call-to-action-widget'))
		);
	}

	function widget($args, $instance){
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$subtitle = apply_filters('widget_subtitle', $instance['subtitle']);
		$description = apply_filters('widget_description', $instance['description']);
		$buttontext = apply_filters('widget_buttontext', $instance['buttontext']);
		$buttonurl = apply_filters('widget_buttonurl', $instance['buttonurl']);

		echo $before_widget;

		if( !empty( $title ) ){
			echo "<h2 class='title'>".$title."</h2>";
		}


		if( !empty( $subtitle ) ){
			echo "<h3 class='subtitle'>".$subtitle."</h3>";
		}
		if( !empty( $description ) ){
			echo "<p class='description'>".$description."</p>";
		}
		?>
			<a href="<?php echo $buttonurl; ?>"><?php echo $buttontext;?></a>

		<?php
        echo $after_widget;
	}

	public function form($instance){
		$wptitle = get_option('title');
		$wpsubtitle = get_option('subtitle');
		$wpdescription = get_option('description');
		$wpbuttontext = get_option('buttontext');
		$wpbuttonurl = get_option('buttonurl');

		?>

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $wptitle;?>">
 		</p>
 		<p>
		<label for="<?php echo $this->get_field_id( 'subtitle' ); ?>"><?php _e( 'Subtitle:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'subtitle' ); ?>" name="<?php echo $this->get_field_name( 'subtitle' ); ?>" type="text" value="<?php echo $wpsubtitle;?>">
 		</p>
 		<p>
 		<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description:' ); ?></label>
            </p>
<p>
		<textarea rows="5" style="width:100%" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" ><?php echo $wpdescription; ?></textarea>
 		</p>
 		<p>
 			<label for="<?php echo $this->get_field_id('buttontext')?>"><?php _e('Button text:')?></label><span class="required"></span>
 			<input type='text' class="widefat button-text" name="<?php echo $this->get_field_name('buttontext')?>" id="<?php echo $this->get_field_id('buttontext')?>" value="<?php echo $wpbuttontext?>">
 			<label class='buttontext_required error' style='display:none;'><?php _e('This Field is required.')?></label>
 		</p>
 		<p>
 			<label for="<?php echo $this->get_field_id('buttonurl')?>" ><?php _e('Button url:')?></label>
 			<input type='text' class="widefat" name="<?php echo $this->get_field_name('buttonurl')?>" id="<?php echo $this->get_field_id('buttonurl')?>" value="<?php echo $wpbuttonurl?>" >
 		</p>
		<?php
	}

	public function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] =  strip_tags($new_instance['title']);
		$instance['subtitle'] = strip_tags($new_instance['subtitle']);
		$instance['description'] = strip_tags($new_instance['description']);
		$instance['buttontext'] = strip_tags($new_instance['buttontext']);
		$instance['buttonurl'] = strip_tags($new_instance['buttonurl']);

		update_option('title', $instance['title']);
		update_option('subtitle', $instance['subtitle']);
		update_option('description', $instance['description']);
		update_option('buttontext',$instance['buttontext']);
		update_option('buttonurl', $instance['buttonurl']);
		return $instance;
	}


}
