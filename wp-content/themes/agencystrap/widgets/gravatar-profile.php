<?php
/**
 * Gravatar Widget
 * Taken from Jetpack
 */


/**
 * Register the widget for use in Appearance -> Widgets
 *
 */
add_action( 'widgets_init', 'agencystrap_gravatar_profile_widget_init' );

function agencystrap_gravatar_profile_widget_init() {
	// Return early if Jetpack's widget is loaded
	if( class_exists( 'Jetpack_Gravatar_Profile_Widget' ) ) {
		return;
	}

	register_widget( 'agencystrap_Gravatar_Profile_Widget' );
}

/**
 * Display a widgetized version of your Gravatar Profile
 * http://blog.gravatar.com/2010/03/26/gravatar-profiles/
 */
class agencystrap_Gravatar_Profile_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'grofile',
			__( 'agencystrap: Gravatar Profile', 'agencystrap' ),
			array(
				'classname'   => 'widget-grofile grofile',
				'description' => __( 'Display a mini version of your Gravatar Profile', 'agencystrap' )
			)
		);

		if ( is_admin() ) {
			add_action( 'admin_footer-widgets.php', array( $this, 'admin_script' ) );
		}
	}

	function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( !$instance['email'] ) {
			if ( current_user_can( 'edit_theme_options' ) ) {
				echo $args['before_widget'];
				if ( ! empty( $title ) )
					echo $args['before_title'] . $title . $args['after_title'];
				echo '<p>' . sprintf( __( 'You need to select what to show in this <a href="%s">Gravatar Profile widget</a>.', 'agencystrap' ), admin_url( 'widgets.php' ) ) . '</p>';
				echo $args['after_widget'];
			}
			return;
		}

		echo $args['before_widget'];

		$profile = $this->get_the_author_meta( $instance['email'] );

		if( ! empty( $profile ) ) {
			$profile = wp_parse_args( $profile, array(
				'thumbnailUrl' => '',
				'profileUrl'   => '',
				'displayName'  => '',
				'aboutMe'      => '',
				'urls'         => array(),
				'accounts'     => array(),
			) );
			$gravatar_url = add_query_arg( 's', 200, $profile['thumbnailUrl'] ); // the default grav returned by grofiles is super small

			?>
			<img src="<?php echo esc_url( $gravatar_url ); ?>" class="grofile-thumbnail no-grav" alt="<?php echo esc_attr( $profile['displayName'] ); ?>" />
			<div class="grofile-meta">
				<h4><a href="<?php echo esc_url( $profile['profileUrl'] ); ?>"><?php echo esc_html( $profile['displayName'] ); ?></a></h4>
				<p><?php echo wp_kses_post( $profile['aboutMe'] ); ?></p>
			</div>

			<?php

			if( $instance['show_personal_links'] )
				$this->display_personal_links( (array) $profile['urls'] );

			if( $instance['show_account_links'] )
				$this->display_accounts( (array) $profile['accounts'] );

			?>

			<h4><a href="<?php echo esc_url( $profile['profileUrl'] ); ?>" class="grofile-full-link"><?php echo esc_html( apply_filters( 'agencystrap_gravatar_full_profile_title', __( 'View Full Profile &rarr;', 'agencystrap' ) ) ); ?></a></h4>

			<?php

			do_action( 'jetpack_stats_extra', 'widget', 'grofile' );

		} else {
			if ( current_user_can( 'edit_theme_options' ) ) {
				echo '<p>' . esc_html__( 'Error loading profile', 'agencystrap' ) . '</p>';
			}
		}

		echo $args['after_widget'];
	}

	function display_personal_links( $personal_links = array() ) {
		if ( empty( $personal_links ) )
			return;
		?>

			<h4><?php echo esc_html( apply_filters( 'agencystrap_gravatar_personal_links_title', __( 'Personal Links', 'agencystrap' ) ) ); ?></h4>
			<ul class="grofile-urls grofile-links">

			<?php foreach( $personal_links as $personal_link ) : ?>
				<li>
					<a href="<?php echo esc_url( $personal_link['value'] ); ?>">
						<?php
							$link_title = ( ! empty( $personal_link['title'] ) ) ? $personal_link['title'] : $personal_link['value'];
							echo esc_html( $link_title );
						?>
					</a>
				</li>
			<?php endforeach; ?>
			</ul>

		<?php
	}

	function display_accounts( $accounts = array() ) {
		if ( empty( $accounts ) )
			return;
		?>

		<h4><?php echo esc_html( apply_filters( 'agencystrap_gravatar_verified_services_title', __( 'Verified Services', 'agencystrap' ) ) ); ?></h4>
		<ul class="grofile-urls grofile-accounts">

		<?php foreach( $accounts as $account ) :
			if( $account['verified'] != 'true' )
				continue;

			$sanitized_service_name = $this->get_sanitized_service_name( $account['shortname'] );
			?>

			<li>
				<a href="<?php echo esc_url( $account['url'] ); ?>" title="<?php echo sprintf( _x( '%1$s on %2$s', '1: User Name, 2: Service Name (Facebook, Twitter, ...)', 'agencystrap' ), esc_html( $account['display'] ), esc_html( $sanitized_service_name ) ); ?>">
					<span class="grofile-accounts-logo grofile-accounts-<?php echo esc_attr( $account['shortname'] ); ?> accounts_<?php echo esc_attr( $account['shortname'] ); ?>"></span>
				</a>
			</li>

		<?php endforeach; ?>
		</ul>

		<?php
	}

	function form( $instance ) {

		$title               = isset( $instance['title'] ) ? $instance['title'] : '';
		$email               = isset( $instance['email'] ) ? $instance['email'] : '';
		$email_user          = isset( $instance['email_user'] ) ? $instance['email_user'] : get_current_user_id();
		$show_personal_links = isset( $instance['show_personal_links'] ) ? (bool) $instance['show_personal_links'] : '';
		$show_account_links  = isset( $instance['show_account_links'] ) ? (bool) $instance['show_account_links'] : '';

		if ( defined( 'IS_WPCOM' ) && IS_WPCOM ) {
			$profile_url = admin_url( 'profile.php' );
		} else {
			$profile_url = 'https://gravatar.com/profile/edit';
		}

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'email_user' ); ?>">
				<?php esc_html_e( 'Select a user or pick "custom" and enter a custom email address.', 'agencystrap' ); ?>
				<br />

				<?php wp_dropdown_users( array(
					'show_option_none' => __( 'Custom', 'agencystrap' ),
					'selected'         => $email_user,
					'name'             => $this->get_field_name( 'email_user' ),
					'id'               => $this->get_field_id( 'email_user' ),
					'class'            => 'gravatar-profile-user-select',
				) );?>
			</label>
		</p>

		<p class="gprofile-email-container <?php echo empty( $email_user ) || $email_user == -1 ? '' : 'hidden'; ?>">
			<label for="<?php echo $this->get_field_id( 'email' ); ?>"><?php esc_html_e( 'Custom Email Address', 'agencystrap' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" type="text" value="<?php echo esc_attr( $email ); ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'show_personal_links' ); ?>">
				<input type="checkbox" name="<?php echo $this->get_field_name( 'show_personal_links' ); ?>" id="<?php echo $this->get_field_id( 'show_personal_links' ); ?>" <?php checked( $show_personal_links ); ?> />
				<?php esc_html_e( 'Show Personal Links', 'agencystrap' ); ?>
				<br />
				<small><?php esc_html_e( 'Links to your websites, blogs, or any other sites that help describe who you are.', 'agencystrap' ); ?></small>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'show_account_links' ); ?>">
				<input type="checkbox" name="<?php echo $this->get_field_name( 'show_account_links' ); ?>" id="<?php echo $this->get_field_id( 'show_account_links' ); ?>" <?php checked( $show_account_links ); ?> />
				<?php esc_html_e( 'Show Account Links', 'agencystrap' ); ?>
				<br />
				<small><?php esc_html_e( 'Links to services that you use across the web.', 'agencystrap' ); ?></small>
			</label>
		</p>

		<p><a href="<?php echo esc_url( $profile_url ); ?>" target="_blank" title="<?php esc_attr_e( 'Opens in new window', 'agencystrap' ); ?>"><?php esc_html_e( 'Edit Your Profile', 'agencystrap' )?></a> | <a href="http://gravatar.com" target="_blank" title="<?php esc_attr_e( 'Opens in new window', 'agencystrap' ); ?>"><?php esc_html_e( "What's a Gravatar?", 'agencystrap' ); ?></a></p>

		<?php
	}

	function admin_script() {
		?>
		<script>
		jQuery( function( $ ) {
			$( '.wrap' ).on( 'change', '.gravatar-profile-user-select', function() {
				var $input = $(this).closest('.widget-inside').find('.gprofile-email-container');
				if ( '-1' === this.value.toLowerCase() ) {
					$input.show();
				} else {
					$input.hide();
				}
			});

			$( 'select.gravatar-profile-user-select' ).each( function() {
			    if ( '-1' === this.value.toLowerCase() ) {
			    	$( this ).closest( '.widget-content' ).find( '.gprofile-email-container' ).show();
			    }
			} );
		} );
		</script>
		<?php
	}

	function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title']               = isset( $new_instance['title'] ) ? wp_kses( $new_instance['title'], array() ) : '';
		$instance['email']               = isset( $new_instance['email'] ) ? wp_kses( $new_instance['email'], array() ) : '';
		$instance['email_user']          = isset( $new_instance['email_user'] ) ? intval( $new_instance['email_user'] ) : -1;
		$instance['show_personal_links'] = isset( $new_instance['show_personal_links'] ) ? (bool) $new_instance['show_personal_links'] : false;
		$instance['show_account_links']  = isset( $new_instance['show_account_links'] ) ? (bool) $new_instance['show_account_links'] : false;

		if ( $instance['email_user'] > 0 ) {
			$user = get_userdata( $instance['email_user'] );
			$instance['email'] = $user->user_email;
		}

		$hashed_email = md5( strtolower( trim( $instance['email'] ) ) );
		$cache_key = 'grofile-' . $hashed_email;
		delete_transient( $cache_key );

		return $instance;
	}

	private function get_the_author_meta( $email ) {
		$hashed_email = md5( strtolower( trim( $email ) ) );
		$cache_key = 'grofile-' . $hashed_email;

		if( ! $profile = get_transient( $cache_key ) ) {
			$profile_url = esc_url_raw( sprintf( '%s.gravatar.com/%s.json', ( is_ssl() ? 'https://secure' : 'http://www' ), $hashed_email ), array( 'http', 'https' ) );

			$expire = 300;
			$response = wp_remote_get( $profile_url, array( 'User-Agent' => 'WordPress.com Gravatar Profile Widget' ) );
			$response_code = wp_remote_retrieve_response_code( $response );
			if ( 200 == $response_code ) {
				$profile = wp_remote_retrieve_body( $response );
				$profile = json_decode( $profile, true );

				if ( is_array( $profile ) && ! empty( $profile['entry'] ) && is_array( $profile['entry'] ) ) {
					$expire = 900; // cache for 15 minutes
					$profile = $profile['entry'][0];
				} else {
					// Something strange happened.  Cache for 5 minutes.
					$profile = array();
				}

			} else {
				$expire = 900; // cache for 15 minutes
				$profile = array();
			}

			set_transient( $cache_key, $profile, $expire );
		}
		return $profile;
	}

	private function get_sanitized_service_name( $shortname ) {
		// Some services have stylized or mixed cap names *cough* WP *cough*
		switch( $shortname ) {
			case 'friendfeed':
				return 'FriendFeed';
			case 'linkedin':
				return 'LinkedIn';
			case 'yahoo':
				return 'Yahoo!';
			case 'youtube':
				return 'YouTube';
			case 'wordpress':
				return 'WordPress';
			case 'tripit':
				return 'TripIt';
			case 'myspace':
				return 'MySpace';
			case 'foursquare':
				return 'foursquare';
			case 'google':
				return 'Google+';
			default:
				// Others don't
				$shortname = ucwords( $shortname );
		}
		return $shortname;
	}
}

// END
