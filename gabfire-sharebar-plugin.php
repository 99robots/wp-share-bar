<?php
/*
	Plugin Name: Gabfire Sharebar Plugin
	Plugin URI: http://www.gabfirethemes.com
	Description: Display a social sidebar next to posts. 
	Author: Kyle Benk
	Version: 1.0
	Author URI: http://www.kylebenk.com
	
	Copyright 2013 Gabfire Themes (email : info@gabfire.com)
	
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
*/

/*
-
-	Created By Kyle Benk
-	7/13
-
-	As of 8/13
-	Needs css styling and jQuery or Javascript to snap to top of page.
-
*/

register_activation_hook( 	__FILE__, array('Gabfire_Sharebar_Plugin','gsp_activate'));
register_deactivation_hook( __FILE__, array('Gabfire_Sharebar_Plugin','gsp_deactivate'));
register_deactivation_hook( __FILE__, array('Gabfire_Sharebar_Plugin','gsp_uninstall'));

add_action('admin_menu', array('Gabfire_Sharebar_Plugin','gsp_menu'));
add_action('admin_init', array('Gabfire_Sharebar_Plugin','gsp_register_settings'));

add_filter('the_content', array('Gabfire_Sharebar_Plugin','gsp_post_content'), 1);
add_action('wp_footer',array('Gabfire_Sharebar_Plugin','gsp_load_script'));
add_action('wp_enqueue_scripts', array('Gabfire_Sharebar_Plugin','gsp_enqueue_styles'));


class Gabfire_Sharebar_Plugin{
	
	//Constructor
	function __construct(){
		;
	}
	
	/**
	 * Activate the plugin
	 * 
	 * @param network
	 *
	 * @return N/A
	 */
	public static function gsp_activate($networkwide) {
		if ( is_multisite() ) :
	    	global $wpdb;
	      	$site_list = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->blogs ORDER BY blog_id" ) );
			foreach ( (array) $site_list as $site ) :
				switch_to_blog( $site->blog_id );
	
				// Ensure default options are set.
		        $option = get_option( 'gabifre-sharebar-plugin-settings' );
		        if ( ! $option )
		            update_option( 'gabifre-sharebar-plugin-settings', Gabfire_Sharebar_Plugin::gsp_default_options() );
	
				restore_current_blog();
			endforeach;
		else :
	        // Ensure default options are set.
	        $option = get_option( 'gabifre-sharebar-plugin-settings' );
	        if ( ! $option )
	            update_option( 'gabifre-sharebar-plugin-settings', Gabfire_Sharebar_Plugin::gsp_default_options() );
	    endif;
	}
	
	/**
	 * Deactivate the plugin
	 * 
	 * @param N/A
	 *
	 * @return N/A
	 */
	public static function gsp_deactivate(){
		
	}
	
	/**
	 * Uninstall the plugin
	 * 
	 * @param N/A
	 *
	 * @return N/A
	 */
	public static function gsp_uninstall(){
		if ( is_multisite() ) :
			global $wpdb;
	      	$site_list = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->blogs ORDER BY blog_id" ) );
			foreach ( (array) $site_list as $site ) :
				switch_to_blog( $site->blog_id );
				delete_option( 'gabifre-sharebar-plugin-settings' );
				restore_current_blog();
			endforeach;
		else :
	    	delete_option( 'gabifre-sharebar-plugin-settings' );
	    endif;
	}
	
	
	/**
	 * Add options page
	 * 
	 * @param N/A
	 *
	 * @return N/A
	 */
	public static function gsp_menu(){
		add_options_page( 'Gabfire Sharebar Plugin Options', 'Gabfire Sharebar Plugin', 'manage_options', 'gabifre_sharebar_plugin', array('Gabfire_Sharebar_Plugin','gsp_options'));
	}
	
	/**
	 * Display options header
	 * 
	 * @param N/A
	 *
	 * @return N/A
	 */
	public static function gsp_options() {
		?>
	    <div class="wrap">
	        <h2>Gabfire Sharebar Options</h2>
	        <form action="options.php" method="POST">
	            <?php settings_fields( 'gabfire-sharebar-plugin-group' ); ?>
	            <?php do_settings_sections( 'gabifre_sharebar_plugin' ); ?>
	            <?php submit_button(); ?>
	        </form>
	    </div>
	    <?php
	}
	
	
	/**
	 * Register settings
	 * 
	 * @param N/A
	 *
	 * @return N/A
	 */
	public static function gsp_register_settings() {
	  register_setting( 'gabfire-sharebar-plugin-group', 'gabifre-sharebar-plugin-settings');
	  add_settings_section('gabfire_sharebar_main','Gabfire Sharebar Settings','','gabifre_sharebar_plugin');
	  add_settings_field('gabfire_sharebar_settings_field','<br />', array('Gabfire_Sharebar_Plugin','gabfire_sharebar_setting_array'),'gabifre_sharebar_plugin','gabfire_sharebar_main');
	}
	
	
	/**
	 * Default options
	 * 
	 * @param N/A
	 *
	 * @return N/A
	 */
	public static function gsp_default_options() {
	    return array(
	        'title'     => '',
	        'layout'     => 'vertical',
	        'twitter'     => '',
	        'facebook'     => '',
	        'google'     => '',
	        'pinterest'     => '',
	        'linkedin'     => '',
	        'twitter_username'     => ''
	    );
	}
	
	/**
	 * Display general options
	 * 
	 * @param N/A
	 *
	 * @return N/A
	 */
	public static function gabfire_sharebar_setting_array() {
		//Get all the admin options
		$options = (array) get_option('gabifre-sharebar-plugin-settings');
		if(array_key_exists('title', $options)) $title = esc_attr( $options['title'] );
		if(array_key_exists('layout', $options)) $layout = esc_attr( $options['layout'] );
		if(array_key_exists('twitter', $options)) $twitter = esc_attr( $options['twitter']);
		if(array_key_exists('google', $options)) $google = esc_attr( $options['google'] );
		if(array_key_exists('facebook', $options)) $facebook = esc_attr( $options['facebook'] );
		if(array_key_exists('pinterest', $options)) $pinterest = esc_attr( $options['pinterest'] );
		if(array_key_exists('linkedin', $options)) $linkedin = esc_attr( $options['linkedin'] );
		if(array_key_exists('twitter_username', $options)) $twitter_username = esc_attr( $options['twitter_username'] );
		
		//Display admin content
		?>
		<table>
			<tr>
				<td>
					<label>Title</label>
				</td>
				<td>
					<input name='gabifre-sharebar-plugin-settings[title]' size='40' type='text' value='<?php echo $title; ?>' />
				</td>
			</tr>
			
			<tr>
				<td>
					<label>Layout</label>
				</td>
				<td>
					<label>Vertical</label>
					<input name='gabifre-sharebar-plugin-settings[layout]' size='40' type='radio' value="vertical" <?php if ($layout == 'vertical') echo 'checked="checked"'; ?> />
				</td>
				<td>
					<label>Horizontal</label>
					<input name='gabifre-sharebar-plugin-settings[layout]' size='40' type='radio' value="horizontal" <?php if ($layout == 'horizontal') echo 'checked="checked"'; ?> />
				</td>
			</tr>
				
			<tr>
				<td>
					<label>Twitter</label>
				</td>
				<td>
					<input name='gabifre-sharebar-plugin-settings[twitter]' type='checkbox' <?php if (isset($twitter)) echo 'checked="checked"'; ?> />
				</td>
			</tr>
			
			<tr>
				<td>
					<label>Google</label>
				</td>
				<td>
					<input name='gabifre-sharebar-plugin-settings[google]' type='checkbox' <?php if (isset($google)) echo 'checked="checked"'; ?> />
				</td>
			</tr>
			
			<tr>
				<td>
					<label>Facebook</label>
				</td>
				<td>
					<input name='gabifre-sharebar-plugin-settings[facebook]' type='checkbox' <?php if (isset($facebook)) echo 'checked="checked"'; ?> />
				</td>
			</tr>
			
			<tr>
				<td>
					<label>Pinterest</label>
				</td>
				<td>
					<input name='gabifre-sharebar-plugin-settings[pinterest]' type='checkbox' <?php if (isset($pinterest)) echo 'checked="checked"'; ?> />
				</td>
			</tr>
			
			<tr>
				<td>
					<label>LinkedIn</label>
				</td>
				<td>
					<input name='gabifre-sharebar-plugin-settings[linkedin]' type='checkbox' <?php if (isset($linkedin)) echo 'checked="checked"'; ?> />
				</td>
			</tr>
			
			<tr>
				<td>
					<label>Twitter Username</label>
				</td>
				<td>
					<input name="gabifre-sharebar-plugin-settings[twitter_username]" size='40' type="text" value="<?php echo $twitter_username; ?>" />
				</td>
			</tr>
		</table>
		<?php
	}
	
	
	/**
	 * All the social media scripts that grab data async
	 * 
	 * @param N/A
	 *
	 * @return N/A
	 */
	public static function gsp_load_script(){?>
		<!-- 
		*
		*	All the scripts to get data asnyc from all sources
		*	
		-->
		
		<!-- Gabfire Sharebar -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script type="text/javascript" src="<?php echo plugins_url('js/gabfire_sharebar.js',__FILE__); ?>"></script>
	
		<!-- Facebook -->
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
		
		<!-- Twitter -->
		<script>!function(d,s,id){
			var js,fjs=d.getElementsByTagName(s)[0];
			if(!d.getElementById(id)){
				js=d.createElement(s);
				js.id=id;
				js.src="https://platform.twitter.com/widgets.js";
				fjs.parentNode.insertBefore(js,fjs);
			}
		}(document,"script","twitter-wjs");</script>
		
		<!-- Google -->
		<script type="text/javascript">
		  (function() {
		    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		    po.src = 'https://apis.google.com/js/plusone.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
		</script>
		
		<!-- Pinterest -->
		<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
		
		<!-- LinkedIn -->
		<script src="//platform.linkedin.com/in.js" type="text/javascript">
		 lang: en_US
		</script>
	<?php
	}
	
	
	/**
	 * Tied to the_content function which displays data in the posts content
	 * 
	 * @param N/A
	 *
	 * @return N/A
	 */
	public static function gsp_post_content($content) {
		global $post;
		
		//Make sure the index is defined and accessible
		$options = (array) get_option('gabifre-sharebar-plugin-settings');
		if(array_key_exists('title', $options)) $title = esc_attr($options['title']);
		if(array_key_exists('layout', $options)) $layout = esc_attr($options['layout']);
		if(array_key_exists('twitter', $options)) $twitter = esc_attr($options['twitter']);
		if(array_key_exists('google', $options)) $google = esc_attr($options['google']);
		if(array_key_exists('facebook', $options)) $facebook = esc_attr($options['facebook']);
		if(array_key_exists('pinterest', $options)) $pinterest = esc_attr($options['pinterest']);
		if(array_key_exists('linkedin', $options)) $linkedin = esc_attr($options['linkedin']);
		if(array_key_exists('twitter_username', $options)) $twitter_username = esc_attr($options['twitter_username']);
		
		if ((isset($twitter) || isset($google) || isset($facebook) || isset($pinterest) || isset($linkedin)) && get_post_type($post) == 'post' && is_singular($post)):
			if ($layout == 'horizontal'):?>
				<div class="gabfire_sharebar_horizontal">
				<span><?php if(isset($title)) echo $title; ?></span>
					<?php if (isset($twitter)):?>
						<div class="twitter_location_horizontal">
							<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo get_permalink( $post->ID ); ?>" data-via="<?php if($twitter_username) echo $twitter_username; ?>" data-lang="en" data-related="anywhereTheJavascriptAPI" data-count="horizontal">Tweet</a>
						</div>
					<?php endif;
					
					if (isset($google)):?>
						<div class="google_location_horizontal"> <div class="g-plusone"></div> </div>
					<?php endif;
					
					if (isset($facebook)):?>
					<div class="facebook_location_horizontal"> <div class="fb-like facebook_location_horizontal" data-href="<?php echo get_permalink( $post->ID ); ?>" data-width="450" data-layout="button_count" data-show-faces="true" data-send="false"></div> </div>
					<?php endif;
					
					if (isset($pinterest)):?>
						<div class="pinterest_location_horizontal">
							<a href="//pinterest.com/pin/create/button/?url=<?php echo get_permalink( $post->ID ); ?>" data-pin-do="buttonPin" data-pin-config="beside"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>
						</div>
					<?php endif;
					
					if (isset($linkedin)):?>
						<div class="linkedin_location_horizontal">
							<script type="IN/Share" data-url="<?php echo get_permalink( $post->ID ); ?>" data-counter="right"></script>
						</div>
					<?php endif; ?>
				</div>
			<?php endif;
			if ($layout == 'vertical'): ?>
				<div class="gabfire_sharebar_vertical">
				<label class="gsp_title"><?php if(isset($title)) echo $title; ?></label>
					<?php if (isset($twitter)):?>
						<div class="twitter_location_vertical gsp_center">
							<a href="https://twitter.com/share" class="twitter-share-button gsp_center" data-url="<?php echo get_permalink( $post->ID ); ?>" data-via="<?php if($twitter_username) echo $twitter_username; ?>" data-lang="en" data-related="anywhereTheJavascriptAPI" data-count="vertical">Tweet</a>
						</div>
					<?php endif;
					
					if (isset($google)):?>
						<div class="google_location_vertical gsp_center"><div class="g-plusone gsp_center" data-size="tall"></div></div>
					<?php endif;
					
					if (isset($facebook)):?>
						<div class="facebook_location_vertical gsp_center"><div class="fb-like gsp_center" data-href="<?php echo get_permalink( $post->ID ); ?>" data-width="450" data-layout="box_count" data-show-faces="true" data-send="false"></div></div>
					<?php endif;
					
					if (isset($pinterest)):?>
						<div class="pinterest_location_vertical gsp_center">
							<a class="gsp_center" href="//pinterest.com/pin/create/button/?url=<?php echo get_permalink( $post->ID ); ?>" data-pin-do="buttonPin" data-pin-config="above"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>
						</div>
					<?php endif;
					
					if (isset($linkedin)):?>
						<div class="linkedin_location_vertical gsp_center">
							<script class="gsp_center" type="IN/Share" data-url="<?php echo get_permalink( $post->ID ); ?>" data-counter="top"></script>
						</div>
					<?php endif; ?>
				</div>
			<?php endif;
		endif;
		
		return $content;
	}
	
	
	/**
	 * Register scripts
	 * 
	 * @param N/A
	 *
	 * @return N/A
	 */
	public static function gsp_enqueue_styles() {
		wp_enqueue_style('gsp_social_css', plugins_url('css/gabfire_sharebar.css',__FILE__ ));
		wp_enqueue_script( 'gsp_js', plugins_url( 'js/gabfire_sharebar.js', __FILE__ ) );
		
		 //Used to change location and color in javascript
	    $options = (array) get_option('gabifre-sharebar-plugin-settings');
		if(array_key_exists('layout', $options)) $layout = esc_attr( $options['layout'] );
	    
	    $gsp_layout_array = array(
			'gsp_layout' => $layout
		);
	    wp_localize_script( 'gsp_js', 'gsp_layout_array', $gsp_layout_array );
	}

}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	