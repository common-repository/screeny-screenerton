<?php
/**
 * Plugin Name: ScreenyScreenerton
 * Version: 0.1.2
 * Description: Adds a column that displays a screenshot for posts
 * Author: Gary Kovar
 * Author URI: http://binarygary.com
 * Text Domain: screenyscreenteron
 * Domain Path: /languages
 * @package ScreenyScreenerton
 */


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//include php file that queries screenshotmachine
include( plugin_dir_path( __FILE__ ) . 'include/screenshotmachine.php');

/**
 * Add column to put full page screenshots.
 * @since 0.1
 * 
 * @return null
 */
function screeny_column($columns) {
  $columns['screeny'] = 'Screeny Desktop';
	return $columns;
}

/**
 * Get post screenshot and add to column.
 * @since 0.1
 * 
 * @return null
 */
function screeny_columns_screenshot($name) {
  global $post;
	if (get_post_status($post->ID) == 'publish' ) {
		switch($name) {
			case 'screeny':
				$size="S";
				$permalink=get_permalink($post->ID, false);
				echo '<img id="screenythumb" src='.screeny_capture($permalink,$size).' name="screeny"><BR><a href=# class=clearcacheclass data-id="' . get_the_ID() .'">Clear Cache</a>';
		}
	}
}
if ( TRUE == get_option('screeny_size')) {
	add_filter('manage_posts_columns', 'screeny_column');
	add_filter('manage_posts_custom_column', 'screeny_columns_screenshot');
}


/**
 * Add column to put mobile screenshots.
 * @since 0.1
 * 
 * @return null
 */
function screeny_mobile_column($columns) {
    $columns['screeny_mobile'] = 'Screeny Mobile';
	return $columns;
}

/**
 * Get post screenshot and add to column.
 * @since 0.1
 * 
 * @return null
 */
function screeny_mobile_columns_screenshot($name) {
  global $post;
	
	if (get_post_status($post->ID) == 'publish' ) {
		switch($name) {
			case 'screeny_mobile':
				$size="Nmob";
				$permalink=get_permalink($post->ID, false);
				echo '<img id="screenythumb" src='.screeny_capture($permalink,$size)." name='screeny_mobile' width=200 height=333><BR><a href=# class=clearcacheclass data-id=" . get_the_ID() .">Clear Cache</a>";
		}
	}
}
if ( TRUE == get_option('screeny_mobile_size')) {
	add_filter('manage_posts_columns', 'screeny_mobile_column');
	add_filter('manage_posts_custom_column', 'screeny_mobile_columns_screenshot');
}


//Admin page settings

add_action('admin_menu', 'screeny_create_menu');
function screeny_create_menu() {
	//create new top-level menu
	add_menu_page(
		'Screeny Settings', 
		'Screeny Settings', 
		'administrator', 
		__FILE__, 
		'screeny_settings_page', 
		'dashicons-welcome-view-site' );
	//call register settings function
	add_action( 'admin_init', 'screeny_register_settings' );
}

function screeny_register_settings() { // whitelist options
  register_setting( 'screeny-group', 'screeny_key' );
  register_setting( 'screeny-group', 'screeny_size' );
  register_setting( 'screeny-group', 'screeny_mobile_size' );
}

function screeny_settings_page() {
?>
<div class="wrap">
<h2>Screeny Screenerton</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'screeny-group' ); ?>
    <?php do_settings_sections( 'screeny-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Key</th>
        <td><input type="text" name="screeny_key" value="<?php echo esc_attr( get_option('screeny_key') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Dislay a Desktop Thumbnail</th>
				<td><input type="checkbox" name="screeny_size" value="1"<?php checked( 1 == get_option('screeny_size') ); ?> /</td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Display a Mobile Thumbnail</th>
				<td><input type="checkbox" name="screeny_mobile_size" value="1"<?php checked( 1 == get_option('screeny_mobile_size') ); ?> /</td>
        </tr>
			
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } 



add_action( 'admin_enqueue_scripts', 'screeny_enqueue_scripts' );
function screeny_enqueue_scripts() {
	wp_enqueue_style( 'screeny', plugins_url( 'css/screeny.css', __FILE__ ) );
	wp_enqueue_script( 'screeny', plugins_url( '/js/screeny.js', __FILE__ ), array('jquery'), '1.0', true );
	wp_localize_script( 'screeny', 'clearcachescript', array(
		'ajax_url' => admin_url( 'admin-ajax.php' )
	));
}


add_action( 'wp_ajax_clear_cache', 'screeny_clear_cache' );
function screeny_clear_cache() {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) { 
		$permalink=get_permalink($_POST['post_id'], false);
		$uncachedurl=screeny_capture($permalink,'Nmob',TRUE);		
		echo $uncachedurl;
	}
	die();
}


add_action('admin_head', 'screeny_column_width');
function screeny_column_width() {
    echo '<style type="text/css">';
		echo '.column-screeny {width:200px !important;}';
		echo '.column-screeny_mobile {width:200px !important;}';
    echo '</style>';
}