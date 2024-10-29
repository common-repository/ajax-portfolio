<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WAPP_Portfolio_Post_Type {

    public function __construct() {

        //$this->wapp_load_includes();

        add_action( 'init', array( $this, 'wapp_register_portfolio_post_type' ) );
        add_action( 'init', array( $this, 'wapp_register_portfolio_taxonomy' ) ); // Register the taxonomy
        add_action( 'admin_menu', array( $this, 'wapp_add_portfolio_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'wapp_register_settings' ) );
        add_action('wp_head', array($this, 'wapp_generate_dynamic_styles'));

        add_action('wp_enqueue_scripts', array($this, 'wapp_enqueue_styles'));
    }

    /**
     * Register Custom Post Type: Portfolio
     */
    public function wapp_register_portfolio_post_type() {

        $labels = array(
            'name'                  => esc_html_x( 'Portfolios', 'Post Type General Name', 'ajax-portfolio' ),
            'singular_name'         => esc_html_x( 'Portfolio', 'Post Type Singular Name', 'ajax-portfolio' ),
            'menu_name'             => esc_html__( 'Portfolios', 'ajax-portfolio' ),
            'name_admin_bar'        => esc_html__( 'Portfolio', 'ajax-portfolio' ),
            'archives'              => esc_html__( 'Portfolio Archives', 'ajax-portfolio' ),
            'attributes'            => esc_html__( 'Portfolio Attributes', 'ajax-portfolio' ),
            'parent_item_colon'     => esc_html__( 'Parent Portfolio:', 'ajax-portfolio' ),
            'all_items'             => esc_html__( 'All Portfolios', 'ajax-portfolio' ),
            'add_new_item'          => esc_html__( 'Add New Portfolio', 'ajax-portfolio' ),
            'add_new'               => esc_html__( 'Add New', 'ajax-portfolio' ),
            'new_item'              => esc_html__( 'New Portfolio', 'ajax-portfolio' ),
            'edit_item'             => esc_html__( 'Edit Portfolio', 'ajax-portfolio' ),
            'update_item'           => esc_html__( 'Update Portfolio', 'ajax-portfolio' ),
            'view_item'             => esc_html__( 'View Portfolio', 'ajax-portfolio' ),
            'view_items'            => esc_html__( 'View Portfolios', 'ajax-portfolio' ),
            'search_items'          => esc_html__( 'Search Portfolio', 'ajax-portfolio' ),
            'not_found'             => esc_html__( 'Not found', 'ajax-portfolio' ),
            'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'ajax-portfolio' ),
            'featured_image'        => esc_html__( 'Featured Image', 'ajax-portfolio' ),
            'set_featured_image'    => esc_html__( 'Set featured image', 'ajax-portfolio' ),
            'remove_featured_image' => esc_html__( 'Remove featured image', 'ajax-portfolio' ),
            'use_featured_image'    => esc_html__( 'Use as featured image', 'ajax-portfolio' ),
            'insert_into_item'      => esc_html__( 'Insert into portfolio', 'ajax-portfolio' ),
            'uploaded_to_this_item' => esc_html__( 'Uploaded to this portfolio', 'ajax-portfolio' ),
            'items_list'            => esc_html__( 'Portfolios list', 'ajax-portfolio' ),
            'items_list_navigation' => esc_html__( 'Portfolios list navigation', 'ajax-portfolio' ),
            'filter_items_list'     => esc_html__( 'Filter portfolios list', 'ajax-portfolio' ),
        );
    
        $args = array(
            'label'                 => esc_html__( 'Portfolio', 'ajax-portfolio' ),
            'description'           => esc_html__( 'Custom Post Type for Portfolios', 'ajax-portfolio' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_icon'             => 'dashicons-portfolio',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'rewrite'               => array( 'slug' => 'portfolio' ),
        );
    
        register_post_type( 'wapp_portfolio', $args );
    }
    

    /**
     * Register Custom Taxonomy for Portfolio Post Type
     */
    public function wapp_register_portfolio_taxonomy() {

        $labels = array(
            'name'              => esc_html_x( 'Portfolio Categories', 'taxonomy general name', 'ajax-portfolio' ),
            'singular_name'     => esc_html_x( 'Portfolio Category', 'taxonomy singular name', 'ajax-portfolio' ),
            'search_items'      => esc_html__( 'Search Portfolio Categories', 'ajax-portfolio' ),
            'all_items'         => esc_html__( 'All Portfolio Categories', 'ajax-portfolio' ),
            'parent_item'       => esc_html__( 'Parent Portfolio Category', 'ajax-portfolio' ),
            'parent_item_colon' => esc_html__( 'Parent Portfolio Category:', 'ajax-portfolio' ),
            'edit_item'         => esc_html__( 'Edit Portfolio Category', 'ajax-portfolio' ),
            'update_item'       => esc_html__( 'Update Portfolio Category', 'ajax-portfolio' ),
            'add_new_item'      => esc_html__( 'Add New Portfolio Category', 'ajax-portfolio' ),
            'new_item_name'     => esc_html__( 'New Portfolio Category Name', 'ajax-portfolio' ),
            'menu_name'         => esc_html__( 'Category', 'ajax-portfolio' ),
        );
    
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'portfolio-category' ),
        );
    
        register_taxonomy( 'wapp_portfolio_category', array( 'wapp_portfolio' ), $args );
    }    

    public function wapp_add_portfolio_admin_menu() {
        // Add a custom settings page under the "Portfolios" menu.
        add_submenu_page(
            'edit.php?post_type=wapp_portfolio',          // Parent slug (Custom Post Type)
            esc_html__( 'Portfolio Settings', 'ajax-portfolio' ), // Page title
            esc_html__( 'Settings', 'ajax-portfolio' ),          // Menu title
            'manage_options',                                    // Capability
            'portfolio-settings',                                // Menu slug
            array( $this, 'wapp_portfolio_settings_page_callback' ) // Callback function
        );
    }
    
    
    public function wapp_portfolio_settings_page_callback() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Portfolio Settings', 'ajax-portfolio'); ?></h1>
            <?php settings_errors(); ?>
            
            <?php
            // Get the active tab
            $active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'tab_options'; // Default to 'tab_options'
            ?>
            
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=wapp_portfolio&page=portfolio-settings&tab=tab_options')); ?>" class="nav-tab <?php echo esc_attr($active_tab == 'tab_options' ? 'nav-tab-active' : ''); ?>">
                    <?php esc_html_e('Tab Options', 'ajax-portfolio'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=wapp_portfolio&page=portfolio-settings&tab=content_options')); ?>" class="nav-tab <?php echo esc_attr($active_tab == 'content_options' ? 'nav-tab-active' : ''); ?>">
                    <?php esc_html_e('Content Options', 'ajax-portfolio'); ?>
                </a>
            </h2>
            
            <form method="post" action="options.php">
                <?php
                if ($active_tab == 'tab_options') {
                    settings_fields('wapp_portfolio_options');
                    do_settings_sections('wapp-portfolio-settings');
                } elseif ($active_tab == 'content_options') {
                    settings_fields('wapp_portfolio_content_options_group');
                    do_settings_sections('wapp-portfolio-content-settings');
                }
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
    
    

    public function wapp_register_settings() {
        // Register the setting
        register_setting(
            'wapp_portfolio_options', // Option group
            'wapp_portfolio_options',  // Option name
            array($this, 'sanitize_portfolio_options') // Sanitization callback
        );
        register_setting(
            'wapp_portfolio_content_options_group', // Option group
            'wapp_portfolio_content_options_group',  // Option name
            array($this, 'sanitize_content_options') // Sanitization callback
        );
    
        // Add a section for the settings
        add_settings_section(
            'wapp_portfolio_section', // ID
            __('Portfolio Tab Settings', 'ajax-portfolio'), // Title
            null, // Callback
            'wapp-portfolio-settings' // Page (settings page slug)
        );
    
        // Add a field for color picker

        add_settings_field(
            'wapp_portfolio_tab_color', // ID
            __('Color', 'ajax-portfolio'), // Title
            array($this, 'wapp_tabcolor_picker_callback'), // Callback
            'wapp-portfolio-settings', // Page
            'wapp_portfolio_section', // Section
            array('label_for' => 'wapp_portfolio_tab_color') // Custom args (add 'label_for')
        );
        // Add a field for color picker active hover
        add_settings_field(
            'wapp_portfolio_tab_active', // ID
            __('Active Color', 'ajax-portfolio'), // Title
            array($this, 'wapp_tab_active_color_callback'), // Callback
            'wapp-portfolio-settings', // Page
            'wapp_portfolio_section', // Section
            array('label_for' => 'wapp_portfolio_tab_active') // Custom args (add 'label_for')
        );

        // Add a field for BG color picker
        add_settings_field(
            'wapp_portfolio_tab_bg_color', // ID
            __('Background Color', 'ajax-portfolio'), // Title
            array($this, 'wapp_bgcolor_picker_callback'), // Callback
            'wapp-portfolio-settings', // Page
            'wapp_portfolio_section', // Section
            array('label_for' => 'wapp_portfolio_tab_bg_color') // Custom args (add 'label_for')
        );

        // Add a field for BG color picker
        add_settings_field(
            'wapp_portfolio_tab_bgactive_color', // ID
            __('Background Active Color', 'ajax-portfolio'), // Title
            array($this, 'wapp_portfolio_tab_bgactive_color'), // Callback
            'wapp-portfolio-settings', // Page
            'wapp_portfolio_section', // Section
            array('label_for' => 'wapp_portfolio_tab_bgactive_color') // Custom args (add 'label_for')
        );
    
        // Add a field for font family
        add_settings_field(
            'wapp_portfolio_font_family', // ID
            __('Font Family', 'ajax-portfolio'), // Title
            array($this, 'wapp_font_family_callback'), // Callback
            'wapp-portfolio-settings', // Page
            'wapp_portfolio_section', // Section
            array('label_for' => 'wapp_portfolio_font_family') // Custom args
        );
    
        // Add a field for font size
        add_settings_field(
            'wapp_portfolio_font_size', // ID
            __('Font Size (px)', 'ajax-portfolio'), // Title
            array($this, 'wapp_font_size_callback'), // Callback
            'wapp-portfolio-settings', // Page
            'wapp_portfolio_section', // Section
            array('label_for' => 'wapp_portfolio_font_size') // Custom args
        );

        // Add a field for font size
        add_settings_field(
            'wapp_portfolio_tab_gap', // ID
            __('Item Gap (px)', 'ajax-portfolio'), // Title
            array($this, 'wapp_tab_gap_callback'), // Callback
            'wapp-portfolio-settings', // Page
            'wapp_portfolio_section', // Section
            array('label_for' => 'wapp_portfolio_tab_gap') // Custom args
        );

        // Add a field for font size
        add_settings_field(
            'wapp_portfolio_tab_padding', // ID
            __('Padding (px)', 'ajax-portfolio'), // Title
            array($this, 'wapp_tab_padding_callback'), // Callback
            'wapp-portfolio-settings', // Page
            'wapp_portfolio_section', // Section
            array('label_for' => 'wapp_portfolio_tab_padding') // Custom args
        );

         // Add settings sections
        add_settings_section(
            'wapp_portfolio_csection', // ID
            __('Portfolio Content Settings', 'ajax-portfolio'), // Title
            null, // Callback
            'wapp-portfolio-content-settings' // Page (settings page slug)
        );
        add_settings_field(
            'wapp_portfolio_content_color', // ID
            __('Color', 'ajax-portfolio'), // Title
            array($this, 'wapp_portfolio_content_color'), // Callback
            'wapp-portfolio-content-settings', // Page
            'wapp_portfolio_csection', // Section
            array('label_for' => 'wapp_portfolio_content_color') // Custom args
        );
        add_settings_field(
            'wapp_portfolio_content_bgcolor', // ID
            __('Overlay Color', 'ajax-portfolio'), // Title
            array($this, 'wapp_portfolio_content_bgcolor'), // Callback
            'wapp-portfolio-content-settings', // Page
            'wapp_portfolio_csection', // Section
            array('label_for' => 'wapp_portfolio_content_bgcolor') // Custom args
        );
        add_settings_field(
            'wapp_portfolio_content_font_size', // ID
            __('Font Size (px)', 'ajax-portfolio'), // Title
            array($this, 'wapp_portfolio_content_font_size'), // Callback
            'wapp-portfolio-content-settings', // Page
            'wapp_portfolio_csection', // Section
            array('label_for' => 'wapp_portfolio_content_font_size') // Custom args
        );
         // Add a field for font family
         add_settings_field(
            'wapp_portfolio_content_font_family', // ID
            __('Font Family', 'ajax-portfolio'), // Title
            array($this, 'wapp_portfolio_content_font_family'), // Callback
            'wapp-portfolio-content-settings', // Page
            'wapp_portfolio_csection', // Section
            array('label_for' => 'wapp_portfolio_content_font_family') // Custom args
        );

    }

    public function wapp_tabcolor_picker_callback($args) {
        $options = get_option('wapp_portfolio_options');
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        echo '<input type="text" id="' . esc_attr($args['label_for']) . '" name="wapp_portfolio_options[' . esc_attr($args['label_for']) . ']" value="' . esc_attr($value) . '" class="wapp_portfolio_tab_color" />';
    }

    public function wapp_tab_active_color_callback($args) {
        $options = get_option('wapp_portfolio_options');
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        echo '<input type="text" id="' . esc_attr($args['label_for']) . '" name="wapp_portfolio_options[' . esc_attr($args['label_for']) . ']" value="' . esc_attr($value) . '" class="wapp_portfolio_tab_active" />';
    }

    public function wapp_bgcolor_picker_callback($args) {
        $options = get_option('wapp_portfolio_options');
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        echo '<input type="text" id="' . esc_attr($args['label_for']) . '" name="wapp_portfolio_options[' . esc_attr($args['label_for']) . ']" value="' . esc_attr($value) . '" class="wapp_portfolio_tab_bg_color" />';
    }
    public function wapp_portfolio_tab_bgactive_color($args) {
        $options = get_option('wapp_portfolio_options');
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        echo '<input type="text" id="' . esc_attr($args['label_for']) . '" name="wapp_portfolio_options[' . esc_attr($args['label_for']) . ']" value="' . esc_attr($value) . '" class="wapp_portfolio_tab_bgactive_color" />';
    }
    // Define the font families array as a class property
    private $font_families = array(
        'Arial' => 'Arial',
        'Verdana' => 'Verdana',
        'Helvetica' => 'Helvetica',
        'Georgia' => 'Georgia',
        'Times New Roman' => 'Times New Roman',
        'Courier New' => 'Courier New',
        'Trebuchet MS' => 'Trebuchet MS',
        'Roboto' => 'Roboto',
        'Open Sans' => 'Open Sans',
        'Lato' => 'Lato',
        'Montserrat' => 'Montserrat',
        'Raleway' => 'Raleway',
        'Poppins' => 'Poppins',
        'Oswald' => 'Oswald',
        'PT Sans' => 'PT Sans',
        'Merriweather' => 'Merriweather'
    );

    public function wapp_font_family_callback( $args ) {
        // Get the options from the database
        $options = get_option('wapp_portfolio_options');
        
        // Check if the specific option exists and set the value, otherwise, set it to an empty string
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        ?>
        <select id="<?php echo esc_attr($args['label_for']); ?>" name="wapp_portfolio_options[<?php echo esc_attr($args['label_for']); ?>]">
            <?php
            // Accessing the class property $font_families correctly
            foreach ( $this->font_families as $font_key => $font_value ) : ?>
                <option value="<?php echo esc_attr($font_key); ?>" <?php selected($value, $font_key); ?>>
                    <?php echo esc_html($font_value); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
    
    public function wapp_font_size_callback($args) {
        $options = get_option('wapp_portfolio_options');
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        ?>
        <input type="number" id="<?php echo esc_attr($args['label_for']); ?>" name="wapp_portfolio_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo esc_attr($value); ?>" min="10" max="100" />
        <?php
    }

    public function wapp_tab_gap_callback($args) {
        $options = get_option('wapp_portfolio_options');
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        ?>
        <input type="number" id="<?php echo esc_attr($args['label_for']); ?>" name="wapp_portfolio_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo esc_attr($value); ?>" min="10" max="100" />
        <?php
    }
    public function wapp_tab_padding_callback($args) {
        $options = get_option('wapp_portfolio_options');
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        ?>
        <input type="text" id="<?php echo esc_attr($args['label_for']); ?>" name="wapp_portfolio_options[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo esc_attr($value); ?>" />
        <?php
    }

    // Portfolio Content-----------------------------

    public function wapp_portfolio_content_color($args) {
        $options = get_option('wapp_portfolio_content_options_group');
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        echo '<input type="text" id="' . esc_attr($args['label_for']) . '" name="wapp_portfolio_content_options_group[' . esc_attr($args['label_for']) . ']" value="' . esc_attr($value) . '" class="wapp_portfolio_content_color" />';
    }
    public function wapp_portfolio_content_bgcolor($args) {
        $options = get_option('wapp_portfolio_content_options_group');
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        echo '<input type="text" id="' . esc_attr($args['label_for']) . '" name="wapp_portfolio_content_options_group[' . esc_attr($args['label_for']) . ']" value="' . esc_attr($value) . '" class="wapp_portfolio_content_bgcolor" />';
    }
    public function wapp_portfolio_content_font_size($args) {
        $options = get_option('wapp_portfolio_content_options_group');
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        ?>
        <input type="number" id="<?php echo esc_attr($args['label_for']); ?>" name="wapp_portfolio_content_options_group[<?php echo esc_attr($args['label_for']); ?>]" value="<?php echo esc_attr($value); ?>" min="10" max="100" />

        <?php
    }
    public function wapp_portfolio_content_font_family( $args ) {
        // Get the options from the database
        $options = get_option('wapp_portfolio_content_options_group');
        
        // Check if the specific option exists and set the value, otherwise, set it to an empty string
        $value = isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : '';
        ?>
        <select id="<?php echo esc_attr($args['label_for']); ?>" name="wapp_portfolio_content_options_group[<?php echo esc_attr($args['label_for']); ?>]">
            <?php
            // Accessing the class property $font_families correctly
            foreach ( $this->font_families as $font_key => $font_value ) : ?>
                <option value="<?php echo esc_attr($font_key); ?>" <?php selected($value, $font_key); ?>>
                    <?php echo esc_html($font_value); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
    
    public function wapp_enqueue_styles() {
        // Register and enqueue the main stylesheet for your plugin
        wp_register_style(
            'wapp-portfolio-style', // Handle
            plugins_url('/assets/public/css/style.css', __FILE__), // Path to your main CSS file
            array(), // Dependencies
            '1.0.0', // Version
            'all' // Media type
        );
        wp_enqueue_style('wapp-portfolio-style');
    
        // Add dynamic styles using wp_add_inline_style
        $inline_css = $this->wapp_generate_dynamic_styles();
        wp_add_inline_style('wapp-portfolio-style', $inline_css);
    }

    public function sanitize_portfolio_options($input) {
        $sanitized = array();
    
        if (isset($input['wapp_portfolio_tab_color'])) {
            $sanitized['wapp_portfolio_tab_color'] = sanitize_hex_color($input['wapp_portfolio_tab_color']);
        }
    
        if (isset($input['wapp_portfolio_tab_active'])) {
            $sanitized['wapp_portfolio_tab_active'] = sanitize_hex_color($input['wapp_portfolio_tab_active']);
        }
    
        if (isset($input['wapp_portfolio_tab_bg_color'])) {
            $sanitized['wapp_portfolio_tab_bg_color'] = sanitize_hex_color($input['wapp_portfolio_tab_bg_color']);
        }
    
        if (isset($input['wapp_portfolio_tab_bgactive_color'])) {
            $sanitized['wapp_portfolio_tab_bgactive_color'] = sanitize_hex_color($input['wapp_portfolio_tab_bgactive_color']);
        }
    
        if (isset($input['wapp_portfolio_font_family'])) {
            $sanitized['wapp_portfolio_font_family'] = sanitize_text_field($input['wapp_portfolio_font_family']);
        }
    
        if (isset($input['wapp_portfolio_font_size'])) {
            $sanitized['wapp_portfolio_font_size'] = absint($input['wapp_portfolio_font_size']);
        }
    
        if (isset($input['wapp_portfolio_tab_gap'])) {
            $sanitized['wapp_portfolio_tab_gap'] = absint($input['wapp_portfolio_tab_gap']);
        }
    
        if (isset($input['wapp_portfolio_tab_padding'])) {
            $sanitized['wapp_portfolio_tab_padding'] = sanitize_text_field($input['wapp_portfolio_tab_padding']);
        }
    
        return $sanitized;
    }

    public function sanitize_content_options($input) {
        $sanitized = array();
    
        if (isset($input['wapp_portfolio_content_color'])) {
            $sanitized['wapp_portfolio_content_color'] = sanitize_hex_color($input['wapp_portfolio_content_color']);
        }
    
        if (isset($input['wapp_portfolio_content_bgcolor'])) {
            $sanitized['wapp_portfolio_content_bgcolor'] = sanitize_hex_color($input['wapp_portfolio_content_bgcolor']);
        }
    
        if (isset($input['wapp_portfolio_content_font_size'])) {
            $sanitized['wapp_portfolio_content_font_size'] = absint($input['wapp_portfolio_content_font_size']);
        }
    
        if (isset($input['wapp_portfolio_content_font_family'])) {
            $sanitized['wapp_portfolio_content_font_family'] = sanitize_text_field($input['wapp_portfolio_content_font_family']);
        }
    
        return $sanitized;
        
    }

    public function wapp_generate_dynamic_styles() {
        $options = get_option('wapp_portfolio_options');

        $tab_color = isset($options['wapp_portfolio_tab_color']) ? sanitize_hex_color($options['wapp_portfolio_tab_color']) : '#000';
        $tab_active_color = isset($options['wapp_portfolio_tab_active']) ? sanitize_hex_color($options['wapp_portfolio_tab_active']) : '#ff7575';
        $bg_color = isset($options['wapp_portfolio_tab_bg_color']) ? sanitize_hex_color($options['wapp_portfolio_tab_bg_color']) : '#f1efef';
        $bgactive_color = isset($options['wapp_portfolio_tab_bgactive_color']) ? sanitize_hex_color($options['wapp_portfolio_tab_bgactive_color']) : '#fff';
        $font_family = isset($options['wapp_portfolio_font_family']) ? sanitize_text_field($options['wapp_portfolio_font_family']) : 'Arial';
        $font_size = isset($options['wapp_portfolio_font_size']) ? absint($options['wapp_portfolio_font_size']) : 13;
        $tab_gap = isset($options['wapp_portfolio_tab_gap']) ? absint($options['wapp_portfolio_tab_gap']) : 10;
        $tab_padding = isset($options['wapp_portfolio_tab_padding']) ? sanitize_text_field($options['wapp_portfolio_tab_padding']) : '10px 22px';

        // Portfolio Content
        $optionsContent = get_option('wapp_portfolio_content_options_group');
        $content_color = isset($optionsContent['wapp_portfolio_content_color']) ? sanitize_hex_color($optionsContent['wapp_portfolio_content_color']) : '#fff';
        $content_bgcolor = isset($optionsContent['wapp_portfolio_content_bgcolor']) ? sanitize_hex_color($optionsContent['wapp_portfolio_content_bgcolor']) : '#222';
        $font_size_content = isset($optionsContent['wapp_portfolio_content_font_size']) ? absint($optionsContent['wapp_portfolio_content_font_size']) : 22;
        $font_family_content = isset($optionsContent['wapp_portfolio_content_font_family']) ? sanitize_text_field($optionsContent['wapp_portfolio_content_font_family']) : 'Arial';
        
        $inline_css = "
        .wapp-portfolio-filter-wrap .portfolio-filter li a {
            color: {$tab_color};
            background-color: {$bg_color};
            font-family: {$font_family};
            font-size: {$font_size}px;
            padding: {$tab_padding};
        }

        .wapp-portfolio-filter-wrap .portfolio-filter li.active a,
        .wapp-portfolio-filter-wrap .portfolio-filter li a:hover {
            color: {$tab_active_color};
            background-color: {$bgactive_color};
        }

        .wapp-portfolio-filter-wrap .portfolio-filter {
            gap: {$tab_gap}px;
        }

        /* Portfolio Content */
        .portfolio-grid .portfolio-item .portfolio-image .portfolio-hover-title .portfolio-content h4 {
            color: {$content_color}!important;
            font-size: {$font_size_content}px!important;
            font-family: {$font_family_content}!important;
            margin: 0;
        }

        .portfolio-grid .portfolio-item .portfolio-image .portfolio-hover-title {
            background-color: {$content_bgcolor}!important;
        }
    ";

    return $inline_css;
    }
   

}

// Initialize the custom post type class
if ( class_exists( 'WAPP_Portfolio_Post_Type' ) ) {
    new WAPP_Portfolio_Post_Type();
}


