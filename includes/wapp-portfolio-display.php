<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WAPP_Portfolio_Display {

    const VERSION = '1.0.0';

    public function __construct() {
        add_shortcode( 'wapp_portfolio_gallery', array( $this, 'wapp_portfolio_gallery_shortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'wapp_enqueue_scripts' ) );
        add_action( 'wp_ajax_wapp_load_more', array( $this,'wapp_load_more' ) );
        add_action( 'wp_ajax_nopriv_wapp_load_more', array( $this,'wapp_load_more' ) );
        
    }

    function wapp_enqueue_scripts() {
        wp_enqueue_script( 'wapp-ajax-load-more', plugin_dir_url( __FILE__ ) . 'js/ajax-load-more.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'wapp-ajax-load-more', 'ajaxurl', admin_url( 'admin-ajax.php' ) ); // Pass AJAX URL to script
    }

    function wapp_load_more() {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( wp_unslash( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) ), 'wapp_load_more_nonce' ) ) {
            wp_send_json_error( array( 'message' => 'Nonce verification failed' ) );
            wp_die();
        }
    
        $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
        
        $args = array(
            'post_type'      => 'wapp_portfolio',
            'posts_per_page' => 6,
            'post_status'    => 'publish',
            'paged'          => $paged,
        );
        
        $query = new WP_Query( $args );
        
        if ( $query->have_posts() ) {
            ob_start();
            
            while ( $query->have_posts() ) {
                $query->the_post();
                $terms = get_the_terms( get_the_ID(), 'wapp_portfolio_category' );
                $cat_classes = [];
    
                if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
                    foreach ( $terms as $term ) {
                        $cat_classes[] = esc_attr( $term->slug ) . '-' . esc_attr( $term->term_id );
                    }
                }
    
                $class_string = implode( ' ', $cat_classes );
                $image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                $title = get_the_title();
    
                echo '<div class="portfolio-item ' . esc_attr( $class_string ) . '">';
                echo '<a href="' . esc_url( get_permalink( get_the_ID() ) ) . '" class="portfolio-image popup-gallery" title="' . esc_attr( $title ) . '">';
                echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $title ) . '"/>';
                echo '<div class="portfolio-hover-title">';
                echo '<div class="portfolio-content">';
                echo '<h4>' . esc_html( $title ) . '</h4>';
                echo '</div>';
                echo '</div>';
                echo '</a>';
                echo '</div>';
            }
            
            wp_reset_postdata();
            
            $output = ob_get_clean();
            echo wp_kses_post($output);
        }
        
        wp_die();
    }
    
    
    

    public function wapp_portfolio_gallery_shortcode() {
        // Generate a nonce for security
        $nonce = wp_create_nonce( 'wapp_load_more_nonce' );
        
        ob_start();
        ?>
        <div class="wapp-section" data-aos="fade">
            <div class="wapp-portfolio-filter-wrap">
                <ul class="portfolio-filter text-center">
                    <li class="active"><a href="#" data-filter="*"> All</a></li>
                    <?php
                    $categories = get_terms( array(
                        'taxonomy'   => 'wapp_portfolio_category',
                        'hide_empty' => true,
                    ) );
                    if ( !empty( $categories ) && !is_wp_error( $categories ) ) {
                        foreach ( $categories as $category ) {
                            $class = esc_attr( $category->slug ) . '-' . esc_attr( $category->term_id );
                            $filter = '.' . $class;
                            echo '<li><a href="#" data-filter="' . esc_attr( $filter ) . '">' . esc_html( $category->name ) . '</a></li>';
                        }
                    }
                    ?>
                </ul>
            </div>
    
            <div class="portfolio-grid portfolio-gallery grid-4 gutter">
                <?php
                    $args = array(
                        'post_type'      => 'wapp_portfolio',
                        'posts_per_page' => 6,
                        'post_status'    => 'publish',
                        'paged'          => 1,
                    );
                    $query = new WP_Query( $args );
                    if ( $query->have_posts() ) {
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            $terms = get_the_terms( get_the_ID(), 'wapp_portfolio_category' );
                            $cat_classes = [];
    
                            if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
                                foreach ( $terms as $term ) {
                                    $cat_classes[] = esc_attr( $term->slug ) . '-' . esc_attr( $term->term_id );
                                }
                            }
    
                            $class_string = implode( ' ', $cat_classes );
                            $image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
                            $title = get_the_title();
    
                            echo '<div class="portfolio-item ' . esc_attr( $class_string ) . '">';
                            echo '<a href="' . esc_url( get_permalink( get_the_ID() ) ) . '" class="portfolio-image popup-gallery" title="' . esc_attr( $title ) . '">';
                            echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $title ) . '"/>';
                            echo '<div class="portfolio-hover-title">';
                            echo '<div class="portfolio-content">';
                            echo '<h4>' . esc_html( $title ) . '</h4>';
                            echo '</div>';
                            echo '</div>';
                            echo '</a>';
                            echo '</div>';
                        }
                        wp_reset_postdata();
                        echo '<span class="dataload"></span>';
                    }
                ?>
            </div>
    
            <div class="wapp-portfolio-footer">
                <button id="wapp-load-more" class="wapp-portfolio-btn wapp-ajax-btn" data-nonce="<?php echo esc_attr( $nonce ); ?>">
                    <?php esc_html_e( 'Load More', 'ajax-portfolio' );?>
                </button>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    

}

// Initialize the portfolio display class
if ( class_exists( 'WAPP_Portfolio_Display' ) ) {
    new WAPP_Portfolio_Display();
}
