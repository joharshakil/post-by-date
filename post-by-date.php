<?php
/**
 * Plugin Name: Posts By Date
 * Description: This will be used to display a list of posts from certain categories with date and limit filter.
 * Version: 1.0.0
 * Author: Johar
 * Developer: Johar Shakil
 * Text Domain: pbd
 * Domain Path: /languages
 * WordPress tested up to: 6.0.0
 *
 * @package pbd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PBD_URL', plugin_dir_url( __FILE__ ) );
define( 'PBD_PATH', plugin_dir_path( __FILE__ ) );
define( 'PBD_FILE', __FILE__ );
define( 'PBD_VERSION', '1.0.0' );
define( 'PBD_SLUG', 'pbd' );

add_action( 'admin_menu', 'pbd_register_menu_page' );
function pbd_register_menu_page() {
    add_menu_page(
        __( 'Posts By Date', 'pbd' ),
        __( 'Posts By Date', 'pbd' ),
        'manage_options',
        'pbd-setting',
        'pbd_setting_callback',
        'dashicons-filter',
        6
    );
}

function pbd_setting_callback() {
    require_once PBD_PATH . 'includes/pbd-setting.php';
}

add_action( 'admin_enqueue_scripts', 'pbd_register_admin_assets' );
function pbd_register_admin_assets() {
    wp_register_style( 'pbd_admin_style', PBD_URL . 'assets/admin/admin-style.css', array(), PBD_VERSION, 'all' );
    wp_register_script( 'pbd_admin_script', PBD_URL . 'assets/admin/admin-script.js', array( 'jquery' ), PBD_VERSION, true );           
    wp_localize_script( 'pbd_admin_script', 'pbd_admin_data', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'pbd_security' => wp_create_nonce( 'pbd-security' )
    ));

    global $current_screen;         

    if ( isset( $current_screen->id ) && 'pbd-setting' === $current_screen->id ) {
        wp_enqueue_style('pbd_admin_style');
        wp_enqueue_script('pbd_admin_script');
    }
}

add_action( 'wp_enqueue_scripts', 'pbd_register_front_assets' );
function pbd_register_front_assets() {
    wp_register_style( 'pbd_front_style', PBD_URL . 'assets/front/front-style.css', array(), PBD_VERSION, 'all' );
    wp_register_script( 'pbd_front_script', PBD_URL . 'assets/front/front-script.js', array( 'jquery' ), PBD_VERSION, true );           
    wp_localize_script( 'pbd_front_script', 'pbd_front_data', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'pbd_security' => wp_create_nonce( 'pbd-security' ),
        'no_more_posts' => esc_html__('No more posts.', 'pbd')
    ));
}

add_action( 'plugins_loaded', 'pbd_load_textdomain' ); 
function pbd_load_textdomain() {
    load_plugin_textdomain( 'pbd', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

add_action( 'init', 'pbd_register_shortcode' );
function pbd_register_shortcode() {
    add_shortcode( 'posts-by-date', 'pbd_shortcode_callback' );
}

function pbd_shortcode_callback( $atts ) {
    
    wp_enqueue_style('pbd_front_style');

    if ( isset( $atts['category'] ) && ! empty( $atts['category'] ) ) {
        $post_category = $atts['category'];
    } else {
        $post_category = ! empty( get_option('pbd-post-category') ) ? get_option('pbd-post-category') : '';
    }

    if ( isset( $atts['date'] ) && ! empty( $atts['date'] ) ) {
        $post_date = $atts['date'];
    } else {
        $post_date = ! empty( get_option('pbd-post-date') ) ? get_option('pbd-post-date') : '';
    }

    if ( isset( $atts['limit'] ) && ! empty( $atts['limit'] ) ) {
        $post_limit = $atts['limit'];
    } else {
        $post_limit = ! empty( get_option('pbd-post-limit') ) ? get_option('pbd-post-limit') : 5;
    }

    if ( ! empty( $post_category ) ) {        
        wp_enqueue_script('pbd_front_script');
        ?>
        <script>
            var post_limit = "<?php echo esc_attr($post_limit); ?>";
            var post_date = "<?php echo esc_attr($post_date); ?>";
            var post_category = "<?php echo esc_attr($post_category); ?>";
        </script>
        <?php
        require_once PBD_PATH . 'includes/shortcode.php';
    } else {
        echo sprintf( '<p class="pbd-error">%1$s</p>', esc_html__( 'Select category from global setting or add category attribute into shortcode.', 'pbd' ) );
    }
}

function pbd_more_post_ajax() {

    if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'pbd-security' ) ) {

        $ppp = ( isset( $_POST['ppp'])  ) ? sanitize_text_field( $_POST['ppp'] ) : 5;
        $category = ( isset( $_POST['cat'] ) ) ? sanitize_text_field( $_POST['cat'] ) : '';
        $date = ( isset( $_POST['date'] ) ) ? explode( '-', sanitize_text_field( $_POST['date'] ) ) : '';
        $page = ( isset( $_POST['pageNumber'] ) ) ? sanitize_text_field( $_POST['pageNumber'] ) : 1;

        if ( isset( $date[0] ) && isset( $date[1] ) && isset( $date[2] ) ) {
            $date_array = array(
                array(
                    'year'  => $date[0],
                    'month' => $date[1],
                    'day'   => $date[2],
                ),
            );
        }

        $args = array(
            'suppress_filters' => true,
            'post_type' => 'post',
            'posts_per_page' => $ppp,
            'cat'    => $category,
            'order'       => 'ASC',
            'orderby'     => 'title',
            'paged'    => $page,
            'date_query' => @$date_array,
        );

        $loop = new WP_Query( $args );

        $out = '';

        if ($loop -> have_posts()) :        
            while ($loop->have_posts()) : $loop->the_post();
                $out .= '<li>
                    <h3><a href="' . get_the_permalink() . '" target="_blank">'. get_the_title() . '</a></h3>
                    <p>' . get_the_excerpt() . '</p>
                    <span>' . get_the_date() . '</span>
                </li>';
            endwhile;    
        endif;

        wp_reset_postdata();
        wp_die( $out );
    } else {
        exit( 'Unauthorized!' );
    }
}

add_action('wp_ajax_nopriv_more_post_ajax', 'pbd_more_post_ajax');
add_action('wp_ajax_more_post_ajax', 'pbd_more_post_ajax');