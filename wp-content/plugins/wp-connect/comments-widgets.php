<?php
if (!class_exists('WP_Connect_Comment_Widget')) {
	add_action('widgets_init', 'WP_Connect_Comment_load_widgets'); 
	// Register widget.
	function WP_Connect_Comment_load_widgets() {
		register_widget('WP_Connect_Comment_Widget');
	} 
	// Widget class.
	class WP_Connect_Comment_Widget extends WP_Widget {
		// Widget setup.
		function WP_Connect_Comment_Widget() {
			$widget_options = array('classname' => 'widget_WP_Connect_Comment', 'description' => '灯鹭社会化评论 最新评论');
			$this -> WP_Widget('wp-connect-comment-widget', '灯鹭最新评论', $widget_options);
		} 
		// outputs the content of the widget
		function widget($args, $instance) {
			extract($args);
			$title = apply_filters('widget_title', $instance['title']);
			$number = $instance['number'] ? absint($instance['number']) : 5;
			$avatar = $instance['avatar'];
			echo $before_widget;
			if ($title)
				echo $before_title . $title . $after_title;
			if (function_exists('local_recent_comments')) {
				local_recent_comments($number, $avatar);
			} else {
				$recentComments = get_denglu_recent_comments();
				if ($recentComments['comments']) {
					$comments = php_array_slice($recentComments['comments'], 0, $number, true);
					denglu_recent_comments($comments);
				}
			}
			echo $after_widget;
		} 
		// processes widget options to be saved
		function update($new_instance, $old_instance) {
			$instance = $old_instance;

			$instance['title'] = strip_tags($new_instance['title']);
			$instance['number'] = absint($new_instance['number']);
			$instance['avatar'] = absint($new_instance['avatar']);
			return $instance;
		} 
		// outputs the options form on admin
		function form($instance) {
			$title = isset($instance['title']) ? esc_attr($instance['title']) : '最新评论';
			$number = isset($instance['number']) ? absint($instance['number']) : 5;
			$avatar = isset($instance['avatar']) ? absint($instance['avatar']) : '';
			?>
		<p><label for="<?php echo $this -> get_field_id('title');?>"><?php _e('Title:');?></label>
		<input class="widefat" id="<?php echo $this -> get_field_id('title');?>" name="<?php echo $this -> get_field_name('title');?>" type="text" value="<?php echo $title;?>" /></p>
		<p><label for="<?php echo $this -> get_field_id('number');?>"><?php _e('Number of comments to show:');?></label>
		<input id="<?php echo $this -> get_field_id('number');?>" name="<?php echo $this -> get_field_name('number');?>" type="text" value="<?php echo $number;?>" size="3" /></p><p><input type="checkbox" id="<?php echo $this->get_field_id( 'avatar' ); ?>" name="<?php echo $this->get_field_name( 'avatar' ); ?>"  value="1" <?php if($avatar) echo "checked "; ?> /><label for="<?php echo $this->get_field_id( 'avatar' ); ?>"> 显示评论者头像</label></p>
	<?php
		} 
	} 
} 

?>