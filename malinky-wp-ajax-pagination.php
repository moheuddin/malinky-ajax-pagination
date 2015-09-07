<?php
/**
 * Plugin Name: WP Ajax Pagination and Infinite Scroll
 * Plugin URI: https://github.com/malinky/malinky-wp-ajax-pagination
 * Description: Choose from infinite scroll, load more button and pagination to load new content with Ajax. Works with posts, pages, custom post types, WooCommerce...or anywhere you'd expect paged content. Different pagination can be setup for different areas of your site.
 * Version: 1.0
 * Author: Malinky
 * Author URI: https://github.com/malinky
 * License: GNU General Public License (GPL) version 3
 * License URI: https://www.gnu.org/licenses/gpl.html
 * Text Domain: malinky-wp-ajax-pagination
 * Domain Path: /languages
 */

class Malinky_Ajax_Pagination
{
	public function __construct()
	{
		// Trailing Slash.
		define( 'MALINKY_AJAX_PAGINATION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		// No Trailing Slash.
		define( 'MALINKY_AJAX_PAGINATION_PLUGIN_URL', plugins_url( basename( plugin_dir_path( __FILE__ ) ) ) );

	    // Includes.
		require_once( 'malinky-wp-ajax-pagination-settings.php' );
		require_once( 'malinky-wp-ajax-pagination-functions.php' );

        // Instantiate settings object.
        $this->settings = new Malinky_Ajax_Pagination_Settings();

	    // Enqueue styles and scripts.
	   	add_action( 'wp_enqueue_scripts', array( $this, 'malinky_ajax_pagination_styles' ), 99 );
	   	add_action( 'wp_enqueue_scripts', array( $this, 'malinky_ajax_pagination_scripts' ), 99 );
	   	add_action( 'admin_enqueue_scripts', array( $this, 'malinky_ajax_pagination_admin_scripts' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function malinky_ajax_pagination_styles()
	{
		// Conditional load, don't include script on singles.
		if ( malinky_is_blog_page( false ) ) {

			wp_register_style(
				'malinky-ajax-pagination',
				MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/css/style.css',
				false,
				NULL
			);
			wp_enqueue_style( 'malinky-ajax-pagination' );

		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function malinky_ajax_pagination_scripts()
	{
		// Conditional load, don't include script on singles.
		if ( malinky_is_blog_page( false ) ) {

			wp_register_script(
				'malinky-ajax-pagination-main-js',
				MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/js/main.js',
				array( 'jquery' ),
				NULL,
				true
			);

			global $wp_query;

			// Saved settings.
			for ( $x = 1; $x <= $this->settings->malinky_ajax_pagination_settings_count_settings(); $x++ ) {
                $malinky_settings[ $x ] = get_option( '_malinky_ajax_pagination_settings_' . $x );
        	}

			// Set ajax loader images.
			foreach ( $malinky_settings as $key => $setting ) {
				$malinky_settings[$key]['ajax_loader'] = malinky_ajax_pagination_ajax_loader( $malinky_settings[$key]['ajax_loader'] );	
			}

			// Settings from the loaded page.
			$malinky_settings['max_num_pages'] 		= $wp_query->max_num_pages;
			$malinky_settings['next_page_number'] 	= get_query_var( 'paged' ) > 1 ? get_query_var( 'paged' ) + 1 : 1 + 1;
			$malinky_settings['next_page_url'] 		= get_next_posts_page_link();

			wp_localize_script( 'malinky-ajax-pagination-main-js', 'malinkySettings', $malinky_settings );
			wp_enqueue_script( 'malinky-ajax-pagination-main-js' );

		}
	}

	/**
	 * Admin enqueue styles and scripts.
	 */
	public function malinky_ajax_pagination_admin_scripts()
	{
		wp_register_style(
			'malinky-ajax-pagination-admin-css',
			MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/css/style-admin.css',
			false,
			NULL
		);
		wp_enqueue_style( 'malinky-ajax-pagination-admin-css' );

		// Get theme defaults.
		$malinky_ajax_pagination_theme_defaults = malinky_ajax_pagination_theme_defaults();

	    wp_register_script(
	    	'malinky-ajax-pagination-admin-main-js',
	    	MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/js/main-admin.js',
	    	array( 'jquery' ),
	    	NULL,
	    	true
	    );
	    wp_localize_script( 'malinky-ajax-pagination-admin-main-js', 'malinkyAjaxPagingThemeDefaults', $malinky_ajax_pagination_theme_defaults );
		wp_enqueue_script( 'malinky-ajax-pagination-admin-main-js' );


		wp_enqueue_media();
	    wp_register_script(
	    	'malinky-ajax-pagination-admin-media-uploader-js',
	    	MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/js/media-uploader.js',
	    	array( 'jquery' ),
	    	NULL,
	    	true
	    );
		wp_enqueue_script( 'malinky-ajax-pagination-admin-media-uploader-js' );
	}
}

$malinky_ajax_pagination = new Malinky_Ajax_Pagination();