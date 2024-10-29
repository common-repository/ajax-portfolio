<?php
/**
 * Plugin Name:           Ajax Portfolio
 * Plugin URI:            https://wordpress.org/plugins/ajax-portfolio/
 * Description:           This plugin allows you to customize the WordPress login page.
 * Version:               1.1.0
 * Author:                xohanniloy
 * Requires at least:     5.2
 * Requires PHP:          7.2
 * Text Domain:           ajax-portfolio
 * Domain Path:           /languages
 * License:               GPL-2.0+
 * license URI:           http://www.gnu.org/licenses/gpl-2.0.txt
 * Stable tag:            1.1.0
 **/

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WAPP_Portfolio {

    const VERSION = '1.0.0';
    const ASSETS_PUBLIC_DIR = 'assets/public';
    const ASSETS_ADMIN_DIR = 'assets/admin';

    public function __construct() {
        // Define constants
        $this->define_constants();

        // Load includes files
        $this->load_includes();

        // Hooks
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'wapp_enqueue_public_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'wapp_enqueue_admin_assets' ) );
        add_action( 'activated_plugin', array( $this, 'wapp_activated_plugin' ) );
    }

    public function wapp_activated_plugin( $plugin ) {
        // Perform actions when the plugin is activated
        
        // Check if the activated plugin is yours
        if ( $plugin == plugin_basename( __FILE__ ) ) {
            // Redirect to settings page or another task
            wp_safe_redirect( admin_url( 'edit.php?post_type=wapp_portfolio&page=portfolio-settings' ) );
            exit;
        }
    }

    /**
     * Define plugin constants.
     */
    private function define_constants() {
        define( 'WAPP_VERSION', self::VERSION );
        define( 'WAPP_ASSETS_PUBLIC_DIR', plugin_dir_url( __FILE__ ) . self::ASSETS_PUBLIC_DIR );
        define( 'WAPP_ASSETS_ADMIN_DIR', plugin_dir_url( __FILE__ ) . self::ASSETS_ADMIN_DIR );
    }

    /**
     * Include required files.
     */
    private function load_includes() {
        foreach ( glob( plugin_dir_path( __FILE__ ) . 'includes/*.php' ) as $filename ) {
            include $filename;
        }
    }

    /**
     * Load plugin textdomain.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'ajax-portfolio', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Enqueue public assets (CSS/JS).
     */
    public function wapp_enqueue_public_assets() {
        wp_enqueue_style( 'wapp-portfolio', WAPP_ASSETS_PUBLIC_DIR . '/css/portfolio.css', false, self::VERSION, 'all' );
        wp_enqueue_script( 'wapp-isotope-image-load', WAPP_ASSETS_PUBLIC_DIR . '/js/image-load.js', array( 'jquery' ), self::VERSION, true );
        wp_enqueue_script( 'wapp-isotope', WAPP_ASSETS_PUBLIC_DIR . '/js/isotope.pkgd.min.js', array( 'jquery' ), self::VERSION, true );
        wp_enqueue_script( 'wapp-portfolio', WAPP_ASSETS_PUBLIC_DIR . '/js/portfolio.js', array( 'jquery', 'wapp-isotope-image-load', 'wapp-isotope' ), self::VERSION, true );

        // Pass ajaxurl to script.js
       
    }

    /**
     * Enqueue admin assets (CSS/JS).
     */
    public function wapp_enqueue_admin_assets() {
        // Load Dashicons (already done)
        wp_enqueue_style( 'dashicons' );
    
        // Enqueue admin stylesheet
        wp_enqueue_style( 'wapp-admin-style', WAPP_ASSETS_ADMIN_DIR . '/css/admin.css', false, self::VERSION, 'all' );
    
        // Enqueue WordPress color picker (CSS and JS)
        wp_enqueue_style( 'wp-color-picker' ); // This enqueues the color picker CSS
       // wp_enqueue_script( 'wp-color-picker' ); // This enqueues the color picker JS
    
        // Your custom JS that interacts with the color picker (if any)
        wp_enqueue_script( 'wapp-color-picker', WAPP_ASSETS_ADMIN_DIR . '/js/wapp-color-picker.js', array( 'jquery', 'wp-color-picker' ), self::VERSION, true );
    }
    


}

// Initialize the plugin class
if ( class_exists( 'WAPP_Portfolio' ) ) {
    new WAPP_Portfolio();
}
