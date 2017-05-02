<?php
if ( !class_exists( 'WP_Connect_Login_Widget' ) ) {

add_action( 'widgets_init', 'wp_connect_login_load_widgets' );

// Register widget.
function wp_connect_login_load_widgets() {
	register_widget( 'WP_Connect_Login_Widget' );
}

// Widget class.
class WP_Connect_Login_Widget extends WP_Widget {

	// Widget setup.
	function WP_Connect_Login_Widget() {
		/* Widget settings. */
		$widget_options = array( 'classname' => 'widget_wp_connect_login', 'description' => 'WordPress连接微博 边栏登录按钮' );

		/* Create the widget. */
		$this->WP_Widget( 'wp-connect-login-widget', '连接微博(登录)', $widget_options);
	}

	// outputs the content of the widget
	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title'] );
		$meta = $instance['meta'];
		if(!is_user_logged_in() || (is_user_logged_in() && $meta)) {
		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		if (!is_user_logged_in()) {
			wp_connect();
		} else { $url = is_singular() ? get_permalink() : ''; ?>
			<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout($url); ?></li>
			</ul>
        <?php }
		echo $after_widget;
		}
	}

	// processes widget options to be saved
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['meta'] = $new_instance['meta'];
		return $instance;
	}

    // outputs the options form on admin
	function form( $instance ) {
		$title = esc_attr($instance['title']);
		$meta = esc_attr($instance['meta']);
?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">标题：</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" />
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id( 'meta' ); ?>" name="<?php echo $this->get_field_name( 'meta' ); ?>"  value="1" <?php if($meta) echo "checked "; ?> />
			<label for="<?php echo $this->get_field_id( 'meta' ); ?>">登录后显示站点管理和退出链接</label>
		</p>
	<?php
	}
}
}
?>