<?php
/**
 * Advanced Ads Image Ad Type
 *
 * @package   Advanced_Ads
 * @author    Thomas Maier <thomas.maier@webgilde.com>
 * @license   GPL-2.0+
 * @link      http://webgilde.com
 * @copyright 2015 Thomas Maier, webgilde GmbH
 * @since     1.6.10
 *
 * Class containing information about the content ad type
 * this should also work as an example for other ad types
 *
 */
class Advanced_Ads_Ad_Type_Image extends Advanced_Ads_Ad_Type_Abstract{

	/**
	 * ID - internal type of the ad type
	 *
	 * must be static so set your own ad type ID here
	 * use slug like format, only lower case, underscores and hyphens
	 *
	 * @since 1.6.10
	 */
	public $ID = 'image';

	/**
	 * set basic attributes
	 *
	 * @since 1.6.10
	 */
	public function __construct() {
		$this->title = __( 'Image Ad', ADVADS_SLUG );
		$this->description = __( 'Ads in various image formats.', ADVADS_SLUG );
		$this->parameters = array(
			'image_url' => '',
			'image_title' => '',
			'image_alt' => '',
		);
	}

	/**
	 * output for the ad parameters metabox
	 *
	 * @param obj $ad ad object
	 * @since 1.6.10
	 */
	public function render_parameters($ad){
		// load tinymc content exitor
		$id = ( isset( $ad->output['image_id'] ) ) ? $ad->output['image_id'] : '';
		$url =	    ( isset( $ad->url ) ) ? esc_url( $ad->url ) : '';

		?><p><button href="#" class="advads_image_upload button button-secondary" type="button" data-uploader-title="<?php
		    _e( 'Insert File', ADVADS_SLUG ); ?>" data-uploader-button-text="<?php _e( 'Insert', ADVADS_SLUG ); ?>" onclick="return false;"><?php _e( 'select image', ADVADS_SLUG ); ?></button>
		    <a id="advads-image-edit-link" href="<?php if( $id ){ echo get_edit_post_link( $id ); } ?>"><?php _e('edit', ADVADS_SLUG ); ?></a>
		</p>
		<input type="hidden" name="advanced_ad[output][image_id]" value="<?php echo $id; ?>" id="advads-image-id"/>
		<div id="advads-image-preview">
		    <?php $this->create_image_tag( $id ); ?>
		</div>

		<?php // don’t show if tracking plugin enabled
		if( ! defined( 'AAT_VERSION' )) : ?>
		<p><label for="advads-url"><?php _e( 'url', ADVADS_SLUG ); ?></label><br/>
		<input type="url" name="advanced_ad[url]" id="advads-url" value="<?php echo $url; ?>"/></p>
		<p><?php printf(__( 'Open this url in a new window and track impressions and clicks with the <a href="%s" target="_blank">Tracking add-on</a>', ADVADS_SLUG ), ADVADS_URL . 'add-ons/tracking/#utm_source=advanced-ads&utm_medium=link&utm_campaign=edit-image-tracking'); ?></p>
		<?php endif;
	}

	/**
	 * render image tag
	 *
	 * @param int $attachment_id post id of the image
	 * @since 1.6.10
	 */
	public function create_image_tag( $attachment_id ){

		$image = wp_get_attachment_image_src( $attachment_id, 'full' );
		if ( $image ) {
			list( $src, $width, $height ) = $image;
			$hwstring = image_hwstring($width, $height);
			$attachment = get_post($attachment_id);
			$alt = trim(strip_tags( get_post_meta($attachment_id, '_wp_attachment_image_alt', true) ));
			$title = trim(strip_tags( $attachment->post_title )); // Finally, use the title
			
			global $wp_current_filter;
			
			$more_attributes = '';
			// create srcset and sizes attributes if we are in the the_content filter and in WordPress 4.4
			if( isset( $wp_current_filter ) 
				&& in_array( 'the_content', $wp_current_filter ) 
				&& ! defined( 'ADVADS_DISABLE_RESPONSIVE_IMAGES' )){
				if( function_exists( 'wp_get_attachment_image_srcset' ) ){
					$more_attributes .= ' srcset=\'' . wp_get_attachment_image_srcset( $attachment_id, 'full' ) . '\'';
				}
				if( function_exists( 'wp_get_attachment_image_sizes' ) ){
					$more_attributes .= ' sizes=\'' . wp_get_attachment_image_sizes( $attachment_id, 'full' ) . '\'';
				}
			}
			
			$more_attributes = apply_filters( 'advanced-ads-ad-image-tag-attributes', $more_attributes );
			
			echo rtrim("<img $hwstring") . " src='$src' alt='$alt' title='$title' $more_attributes/>";
		}
	}
	
	/**
	 * render image icon for overview pages
	 *
	 * @param int $attachment_id post id of the image
	 * @since 1.7.4
	 */
	public function create_image_icon( $attachment_id ){

		$image = wp_get_attachment_image_src( $attachment_id, 'medium', true );
		if ( $image ) {
			list( $src, $width, $height ) = $image;
			
			// scale down width or height to max 100px
			if( $width > $height ){
			    $height = absint( $height / ( $width / 100 ) );
			    $width = 100;
			} else {
			    $width = absint( $width / ( $height / 100 ) );
			    $height = 100;
			}
			
			$hwstring = image_hwstring($width, $height);
			$attachment = get_post($attachment_id);
			$alt = trim(strip_tags( get_post_meta($attachment_id, '_wp_attachment_image_alt', true) ));
			$title = trim(strip_tags( $attachment->post_title )); // Finally, use the title
			
			echo rtrim("<img $hwstring") . " src='$src' alt='$alt' title='$title'/>";
		}
	}

	/**
	 * prepare the ads frontend output by adding <object> tags
	 *
	 * @param obj $ad ad object
	 * @return str $content ad content prepared for frontend output
	 * @since 1.6.10.
	 */
	public function prepare_output($ad){

		$id = ( isset( $ad->output['image_id'] ) ) ? absint( $ad->output['image_id'] ) : '';
		$url =	    ( isset( $ad->url ) ) ? esc_url( $ad->url ) : '';

		ob_start();
		if( ! defined( 'AAT_VERSION' ) && $url ){ echo '<a href="'. $url .'">'; }
		echo $this->create_image_tag( $id );
		if( ! defined( 'AAT_VERSION' ) && $url ){ echo '</a>'; }

		return ob_get_clean();
	}

}