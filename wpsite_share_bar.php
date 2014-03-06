<?php
/*
Plugin Name: WPsite Sharebar Beta
plugin URI: wpsite-sharebar
Description: Display a social sidebar next to posts.
version: 1.0
Author: Kyle Benk
Author URI: http://kylebenkapps.com
License: GPL2
*/

/** 
 * Global Definitions 
 */

/* Plugin Name */

if (!defined('WPSITE_SHAREBAR_PLUGIN_NAME'))
    define('WPSITE_SHAREBAR_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

/* Plugin directory */

if (!defined('WPSITE_SHAREBAR_PLUGIN_DIR'))
    define('WPSITE_SHAREBAR_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . WPSITE_SHAREBAR_PLUGIN_NAME);

/* Plugin url */

if (!defined('WPSITE_SHAREBAR_PLUGIN_URL'))
    define('WPSITE_SHAREBAR_PLUGIN_URL', WP_PLUGIN_URL . '/' . WPSITE_SHAREBAR_PLUGIN_NAME);
  
/* Plugin verison */

if (!defined('WPSITE_SHAREBAR_VERSION_NUM'))
    define('WPSITE_SHAREBAR_VERSION_NUM', '1.0.0');
 
 
/** 
 * Activatation / Deactivation 
 */  

register_activation_hook( __FILE__, array('WPsiteSharebar', 'register_activation'));

/** 
 * Hooks / Filter 
 */
 
add_action('init', array('WPsiteSharebar', 'load_textdoamin'));
add_action('admin_menu', array('WPsiteSharebar', 'wpsite_sharebar_menu_page'));
add_filter('the_content', array('WPsiteSharebar','wpsite_sharebar_display_bar'));

/** 
 *  WPsiteSharebar main class
 *
 * @since 1.0.0
 * @using Wordpress 3.8
 */

class WPsiteSharebar {

	/* Properties */
	
	private static $jquery_latest = 'http://code.jquery.com/jquery-latest.min.js';
	
	private static $text_domain = 'wpsite-sharebar';
	
	private static $prefix = 'wpsite_sharebar_';
	
	private static $settings_page = 'wpsite-sharebar-admin-menu-settings';
	
	private static $usage_page = 'wpsite-sharebar-admin-menu-usage';
	
	private static $default = array(
        'title'     		=> '',
        'layout'     		=> 'vertical',
        'twitter'     		=> '',
        'facebook'     		=> '',
        'google'     		=> '',
        'pinterest'     	=> '',
        'linkedin'     		=> '',
        'twitter_username'  => ''
    );

	/**
	 * Load the text domain 
	 * 
	 * @since 1.0.0
	 */
	static function load_textdoamin() {
		load_plugin_textdomain(self::$text_domain, false, WPSITE_SHAREBAR_PLUGIN_DIR . '/languages');
	}
	
	/**
	 * Hooks to 'init' 
	 * 
	 * @since 1.0.0
	 */
	static function register_activation() {
	
		/* Check if multisite, if so then save as site option */
		
		if (is_multisite()) {
			add_site_option('wpsite_sharebar_version', WPSITE_SHAREBAR_VERSION_NUM);
		} else {
			add_option('wpsite_sharebar_version', WPSITE_SHAREBAR_VERSION_NUM);
		}
	}
	
	/**
	 * Hooks to 'the_content' 
	 * 
	 * @since 1.0.0
	 */
	static function wpsite_sharebar_display_bar($content) {
	
		global $post;
		
		$settings = get_option('wpsite_sharebar_settings');
			
		/* Default values */
		
		if ($settings === false) 
			$settings = self::$default;
	
		/* CSS */
		
		wp_register_style('wpsite_sharebar_css', WPSITE_SHAREBAR_PLUGIN_URL . '/include/css/wpsite_sharebar.css');
		wp_enqueue_style('wpsite_sharebar_css');
		
		/* Javascript */
	
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('wpsite_sharebar_js', WPSITE_SHAREBAR_PLUGIN_URL . '/include/js/wpsite_sharebar.js');
		
		$wpsite_sharebar_layout_array = array(
			'wpsite_sharebar_layout' => $settings['layout']
		);
		
	    wp_localize_script('wpsite_sharebar_js', 'wpsite_sharebar_layout_array', $wpsite_sharebar_layout_array );
	
		
		?>
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
		
		<!-- Check if any social channels are set if so then display bar -->
		
		<?php if ((isset($settings['twitter']) || isset($settings['google']) || isset($settings['facebook']) || isset($settings['pinterest']) || isset($settings['linkedin'])) && is_singular($post)):
		
			/* Horizontal */
			
			if ($settings['layout'] == 'horizontal'):?>
				<div class="wpsite_sharebar_horizontal">
				<span><?php echo isset($settings['title']) ? $settings['title'] : ''; ?></span>
					
					<?php if (isset($settings['twitter']) && $settings['twitter']):?>
						<div class="twitter_location_horizontal">
							<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo get_permalink( $post->ID ); ?>" data-via="<?php echo isset($twitter_username) ? $twitter_username : ''; ?>" data-lang="en" data-related="anywhereTheJavascriptAPI" data-count="horizontal"><?php _e('Tweet', self::$text_domain); ?></a>
						</div>
					<?php endif;
					
					if (isset($settings['google']) && $settings['google']):?>
						<div class="google_location_horizontal"> <div class="g-plusone"></div> </div>
					<?php endif;
					
					if (isset($settings['facebook']) && $settings['facebook']):?>
					<div class="facebook_location_horizontal"> <div class="fb-like facebook_location_horizontal" data-href="<?php echo get_permalink( $post->ID ); ?>" data-width="450" data-layout="button_count" data-show-faces="true" data-send="false"></div> </div>
					<?php endif;
					
					if (isset($settings['pinterest']) && $settings['pinterest']):?>
						<div class="pinterest_location_horizontal">
							<a href="//pinterest.com/pin/create/button/?url=<?php echo get_permalink( $post->ID ); ?>" data-pin-do="buttonPin" data-pin-config="beside"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>
						</div>
					<?php endif;
					
					if (isset($settings['linkedin']) && $settings['linkedin']):?>
						<div class="linkedin_location_horizontal">
							<script type="IN/Share" data-url="<?php echo get_permalink( $post->ID ); ?>" data-counter="right"></script>
						</div>
					<?php endif; ?>
				</div>
			<?php endif;
			
			/* Vertical */
			
			if ($settings['layout'] == 'vertical'): ?>
				<div class="wpsite_sharebar_vertical">
				<label class="wpsite_sharebar_title"><?php echo isset($settings['title']) ? $settings['title'] : ''; ?></label>
					<?php if (isset($settings['twitter']) && $settings['twitter']):?>
						<div class="twitter_location_vertical wpsite_sharebar_center">
							<a href="https://twitter.com/share" class="twitter-share-button wpsite_sharebar_center" data-url="<?php echo get_permalink( $post->ID ); ?>" data-via="<?php echo isset($twitter_username) ? $twitter_username : ''; ?>" data-lang="en" data-related="anywhereTheJavascriptAPI" data-count="vertical"><?php _e('Tweet', self::$text_domain); ?></a>
						</div>
					<?php endif;
					
					if (isset($settings['google']) && $settings['google']):?>
						<div class="google_location_vertical wpsite_sharebar_center"><div class="g-plusone wpsite_sharebar_center" data-size="tall"></div></div>
					<?php endif;
					
					if (isset($settings['facebook']) && $settings['facebook']):?>
						<div class="facebook_location_vertical wpsite_sharebar_center"><div class="fb-like wpsite_sharebar_center" data-href="<?php echo get_permalink( $post->ID ); ?>" data-width="450" data-layout="box_count" data-show-faces="true" data-send="false"></div></div>
					<?php endif;
					
					if (isset($settings['pinterest']) && $settings['pinterest']):?>
						<div class="pinterest_location_vertical wpsite_sharebar_center">
							<a class="wpsite_sharebar_center" href="//pinterest.com/pin/create/button/?url=<?php echo get_permalink( $post->ID ); ?>" data-pin-do="buttonPin" data-pin-config="above"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>
						</div>
					<?php endif;
					
					if (isset($settings['linkedin']) && $settings['linkedin']):?>
						<div class="linkedin_location_vertical wpsite_sharebar_center">
							<script class="wpsite_sharebar_center" type="IN/Share" data-url="<?php echo get_permalink( $post->ID ); ?>" data-counter="top"></script>
						</div>
					<?php endif; ?>
				</div>
			<?php endif;
		endif;
		
		return $content;
	}
	
	/**
	 * Hooks to 'admin_menu' 
	 * 
	 * @since 1.0.0
	 */
	static function wpsite_sharebar_menu_page() {
	
	    /* Cast the first sub menu to the settings menu */
	    
	    $page_hook_suffix = add_submenu_page(
	    	'options-general.php', 										// parent slug
	    	__('WPsite Sharebar', self::$text_domain), 						// Page title
	    	__('WPsite Sharebar', self::$text_domain), 						// Menu name
	    	'manage_options', 											// Capabilities
	    	self::$settings_page, 										// slug
	    	array('WPsiteSharebar', 'wpsite_sharebar_admin_settings')	// Callback function
	    );
	    
	    add_action('admin_print_styles-' . $page_hook_suffix, array('WPsiteSharebar', 'wpsite_sharebar_include_page_scripts'));
	    add_action('admin_print_scripts-' . $page_hook_suffix, array('WPsiteSharebar', 'wpsite_sharebar_include_page_scripts'));
	}
	
	/**
	 * Displays the HTML for the 'wpsite-sharebar-admin-menu-settings' admin page
	 * 
	 * @since 1.0.0
	 */
	static function wpsite_sharebar_admin_settings() {
		
		$settings = get_option('wpsite_sharebar_settings');
			
		/* Default values */
		
		if ($settings === false) 
			$settings = self::$default;
	
		/* Save data nd check nonce */
		
		if (isset($_POST['submit']) && check_admin_referer('wpsite_sharebar_admin_settings')) {
			
			$settings = get_option('wpsite_sharebar_settings');
			
			/* Default values */
			
			if ($settings === false) 
				$settings = self::$default;
				
			$settings = array(
				'title'				=> isset($_POST['wpsite_sharebar_settings_title']) ? stripcslashes(sanitize_text_field($_POST['wpsite_sharebar_settings_title'])) : '',
		        'layout'     		=> isset($_POST['wpsite_sharebar_settings_layout']) ? stripcslashes(sanitize_text_field($_POST['wpsite_sharebar_settings_layout'])) : '',
		        'twitter'     		=> isset($_POST['wpsite_sharebar_settings_twitter']) && $_POST['wpsite_sharebar_settings_twitter'] ? true : false,
		        'facebook'     		=> isset($_POST['wpsite_sharebar_settings_facebook']) && $_POST['wpsite_sharebar_settings_facebook'] ? true : false,
		        'google'     		=> isset($_POST['wpsite_sharebar_settings_google']) && $_POST['wpsite_sharebar_settings_google'] ? true : false,
		        'pinterest'     	=> isset($_POST['wpsite_sharebar_settings_pinterest']) && $_POST['wpsite_sharebar_settings_pinterest'] ? true : false,
		        'linkedin'     		=> isset($_POST['wpsite_sharebar_settings_linkedin']) && $_POST['wpsite_sharebar_settings_linkedin'] ? true : false,
		        'twitter_username'  => ''
			);
			
			update_option('wpsite_sharebar_settings', $settings);
		}
		
		?>
		
		<h1><?php _e('WPsite Sharebar', self::$text_domain); ?></h1>
		
		<form method="post">
		
			<h3><?php _e('Display Settings', self::$text_domain); ?></h3>
			
			<table>
				<tbody>
				
					<!-- Title -->
				
					<tr>
						<th class="wpsite_sharebar_admin_table_th">
							<label><?php _e('Title', self::$text_domain); ?></label>
							<td class="wpsite_sharebar_admin_table_td">
								<input id="wpsite_sharebar_settings_title" name="wpsite_sharebar_settings_title" type="text" size="60" value="<?php echo esc_attr($settings['title']); ?>">
							</td>
						</th>
					</tr>
					
					<!-- Layout -->
					
					<tr>
						<th class="wpsite_sharebar_admin_table_th">
							<label><?php _e('Layout', self::$text_domain); ?></label>
							<td class="wpsite_sharebar_admin_table_td">
								<input id="wpsite_sharebar_settings_layout" name="wpsite_sharebar_settings_layout" type="radio" value="vertical" <?php echo isset($settings['layout']) && $settings['layout'] == 'vertical' ? "checked" : ""; ?>><label><?php _e('Vertical', self::$text_domain); ?></label><br>
								<input id="wpsite_sharebar_settings_layout" name="wpsite_sharebar_settings_layout" type="radio" value="horizontal" <?php echo isset($settings['layout']) && $settings['layout'] == 'horizontal' ? "checked" : ""; ?>><label><?php _e('Horizontal', self::$text_domain); ?></label><br>
							</td>
						</th>
					</tr>
				
				</tbody>
			</table>
			
			<h3><?php _e('Social Media Channels', self::$text_domain); ?></h3>
			
			<table>
				<tbody>
					
					<!-- Twitter -->
				
					<tr>
						<th class="wpsite_sharebar_admin_table_th">
							<label><?php _e('Twitter', self::$text_domain); ?></label>
							<td class="wpsite_sharebar_admin_table_td">
								<input id="wpsite_sharebar_settings_twitter" name="wpsite_sharebar_settings_twitter" type="checkbox" <?php echo isset($settings['twitter']) && $settings['twitter'] ? 'checked="checked"' : ''; ?>>
							</td>
						</th>
					</tr>
					
					<!-- Facebook -->
				
					<tr>
						<th class="wpsite_sharebar_admin_table_th">
							<label><?php _e('Facebook', self::$text_domain); ?></label>
							<td class="wpsite_sharebar_admin_table_td">
								<input id="wpsite_sharebar_settings_facebook" name="wpsite_sharebar_settings_facebook" type="checkbox" <?php echo isset($settings['facebook']) && $settings['facebook'] ? 'checked="checked"' : ''; ?>>
							</td>
						</th>
					</tr>
					
					<!-- Google -->
				
					<tr>
						<th class="wpsite_sharebar_admin_table_th">
							<label><?php _e('Google+', self::$text_domain); ?></label>
							<td class="wpsite_sharebar_admin_table_td">
								<input id="wpsite_sharebar_settings_google" name="wpsite_sharebar_settings_google" type="checkbox" <?php echo isset($settings['google']) && $settings['google'] ? 'checked="checked"' : ''; ?>>
							</td>
						</th>
					</tr>
					
					<!-- Pinterest -->
				
					<tr>
						<th class="wpsite_sharebar_admin_table_th">
							<label><?php _e('Pinterest', self::$text_domain); ?></label>
							<td class="wpsite_sharebar_admin_table_td">
								<input id="wpsite_sharebar_settings_pinterest" name="wpsite_sharebar_settings_pinterest" type="checkbox" <?php echo isset($settings['pinterest']) && $settings['pinterest'] ? 'checked="checked"' : ''; ?>>
							</td>
						</th>
					</tr>
					
					<!-- LinkedIn -->
				
					<tr>
						<th class="wpsite_sharebar_admin_table_th">
							<label><?php _e('LinkedIn', self::$text_domain); ?></label>
							<td class="wpsite_sharebar_admin_table_td">
								<input id="wpsite_sharebar_settings_linkedin" name="wpsite_sharebar_settings_linkedin" type="checkbox" <?php echo isset($settings['linkedin']) && $settings['linkedin'] ? 'checked="checked"' : ''; ?>>
							</td>
						</th>
					</tr>
				
				</tbody>
			</table>
			
		<?php wp_nonce_field('wpsite_sharebar_admin_settings'); ?>
		
		<?php submit_button(); ?>
		
		</form>
		
		<?php
	}
	
	/**
	 * Hooks to 'admin_print_scripts' and 'admin_print_styles' 
	 * 
	 * @since 1.0.0
	 */
	static function wpsite_sharebar_include_page_scripts() {
		
		/* CSS */
		
		wp_register_style('wpsite_sharebar_admin_css', WPSITE_SHAREBAR_PLUGIN_URL . '/include/css/wpsite_sharebar_admin.css');
		wp_enqueue_style('wpsite_sharebar_admin_css');
	
		/* Javascript */
		
		wp_register_script('wpsite_sharebar_admin_js', WPSITE_SHAREBAR_PLUGIN_URL . '/include/js/wpsite_sharebar_admin.js');
		wp_enqueue_script('wpsite_sharebar_admin_js');
	}
}

?>