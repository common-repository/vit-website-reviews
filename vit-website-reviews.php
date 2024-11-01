<?php

/**
 * Plugin Name: Vit Website Reviews
 * Plugin URI: https://www.vincoit.com/website-reviews/
 * Description: Accomplishing what your rivals do not, getting that extra information to get ahead. This is exactly where we step in! Vit Website Reviews is a fast and lightweight plugin that lets your visitors rate and comment on your website.
 * Version: 2.1.0
 * Author: VincoIT
 * Author URI: https://www.vincoit.com/
 **/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'website_review_plugin_sdk' ) ) {
    website_review_plugin_sdk()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'website_review_plugin_sdk' ) ) {
        /*
         * Enable and set freemius SDK.
         * */
        
        if ( !function_exists( 'website_review_plugin_sdk' ) ) {
            // Create a helper function for easy SDK access.
            function website_review_plugin_sdk()
            {
                global  $website_review_plugin_sdk ;
                
                if ( !isset( $website_review_plugin_sdk ) ) {
                    // Include Freemius SDK.
                    require_once dirname( __FILE__ ) . '/freemius/start.php';
                    $website_review_plugin_sdk = fs_dynamic_init( array(
                        'id'             => '3911',
                        'slug'           => 'vit-website-reviews',
                        'premium_slug'   => 'vit-website-reviews-premium',
                        'type'           => 'plugin',
                        'public_key'     => 'pk_c18976b6029db333f1043a4409989',
                        'is_premium'     => false,
                        'premium_suffix' => 'Premium',
                        'has_addons'     => false,
                        'has_paid_plans' => true,
                        'trial'          => array(
                        'days'               => 7,
                        'is_require_payment' => false,
                    ),
                        'menu'           => array(
                        'slug'    => 'vit-wr-dashboard',
                        'support' => false,
                    ),
                        'is_live'        => true,
                    ) );
                }
                
                return $website_review_plugin_sdk;
            }
            
            // Init Freemius.
            website_review_plugin_sdk();
            // Signal that SDK was initiated.
            do_action( 'website_review_plugin_sdk_loaded' );
        }
    
    }
    require_once plugin_dir_path( __FILE__ ) . "/model/Defines.php";
    require_once WebsiteReview__PLUGIN_DIR . "/model/Database.php";
    require_once WebsiteReview__PLUGIN_DIR . "/controller/Integrations.php";
    require_once WebsiteReview__PLUGIN_DIR . "/controller/Dashboard.php";
    require_once WebsiteReview__PLUGIN_DIR . "/controller/Reviews.php";
    require_once WebsiteReview__PLUGIN_DIR . "/controller/ControlPanel.php";
    require_once WebsiteReview__PLUGIN_DIR . "/controller/Integrations.php";
    require_once WebsiteReview__PLUGIN_DIR . "/view/Popup.php";
    /*
     * Add in sessions the proper way.
     * */
    add_action( 'init', 'vit_wr_startSession', 1 );
    add_action( 'wp_logout', 'vit_wr_endSession' );
    add_action( 'wp_login', 'vit_wr_endSession' );
    function vit_wr_startSession()
    {
        if ( !session_id() ) {
            session_start();
        }
    }
    
    function vit_wr_endSession()
    {
        session_destroy();
    }
    
    /*
     * Add scripts the proper way.
     * */
    function mw_plugin_scripts()
    {
        
        if ( is_admin() ) {
            wp_enqueue_script(
                'admin_js_uitkit',
                plugins_url( 'view/js/uikit.min.js', __FILE__ ),
                false,
                '3.0.3',
                false
            );
            wp_enqueue_style(
                'admin_css_uitkit',
                plugins_url( 'view/css/uikit.min.css', __FILE__ ),
                true,
                '3.0.3',
                'all'
            );
            /*	Apexcharts Includes:  */
            wp_enqueue_script(
                'admin_js_apex',
                plugins_url( 'view/js/apexcharts.min.js', __FILE__ ),
                false,
                '3.6.7',
                false
            );
            wp_enqueue_style(
                'admin_css_apex',
                plugins_url( 'view/css/apexcharts.css', __FILE__ ),
                true,
                '3.6.7',
                'all'
            );
        }
    
    }
    
    add_action( 'admin_enqueue_scripts', 'mw_plugin_scripts' );
    // Hooks our custom function into WP's wp_enqueue_scripts function
    add_action( 'admin_enqueue_scripts', 'enqueue_plugin_backend_scripts' );
    // @info enqueues all backend plugin scripts & stylesheets.
    function enqueue_plugin_backend_scripts()
    {
        // Strips wp version parameter from enqueue url.
        // remove wp version param from any enqueued scripts
        function vc_remove_wp_ver_css_js( $src )
        {
            if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
                $src = remove_query_arg( 'ver', $src );
            }
            return $src;
        }
        
        // Alter wordpress build in functions to load stylesheets & scripts more smoothly.
        // Whenever this WP action is called, execute the custom bound function:
        // @params: $tag, $function to add, $priority
        add_filter( 'style_loader_src', 'vc_remove_wp_ver_css_js', 9999 );
        add_filter( 'script_loader_src', 'vc_remove_wp_ver_css_js', 9999 );
        $currentPage = '';
        if ( isset( $_GET['page'] ) ) {
            $currentPage = $_GET['page'];
        }
        // If page contains Dashboard admin url:
        
        if ( preg_match( '/^vit-wr-dashboard.*/', $currentPage ) ) {
            /**
             * @info Registers the scripts into the project but doesn't directly load them:
             * @params: Handle,Path to source,Library dependency,VersionNr,put in footer (bool)
             */
            wp_register_script(
                'apexLatest',
                plugin_dir_url( __FILE__ ) . './view/js/apexcharts.min.js',
                '',
                '',
                false
            );
            wp_register_script(
                'ajax-minified',
                plugin_dir_url( __FILE__ ) . './view/js/ajax/browser.min.js',
                '',
                '',
                false
            );
            // Can only function after react core lib has loaded: --> Therefore use the wp-element dependency
            wp_register_script(
                'react-prop-types',
                plugin_dir_url( __FILE__ ) . './view/js/react/prop-types.min.js',
                [ 'wp-element' ],
                '',
                false
            );
            wp_register_script(
                'react-apex-minified',
                plugin_dir_url( __FILE__ ) . './view/js/react/react-apexcharts.iife.min.js',
                [ 'wp-element' ],
                '',
                false
            );
            wp_register_script(
                'apex-custom-js',
                plugin_dir_url( __FILE__ ) . './view/js/apex-custom.js',
                [ 'wp-element' ],
                '',
                true
            );
            // Register stylesheets:
            wp_register_style(
                'apexchart-custom-style',
                plugin_dir_url( __FILE__ ) . './view/css/apexcharts.css',
                '',
                '',
                false
            );
            wp_register_style(
                'dashboard-custom-style',
                plugin_dir_url( __FILE__ ) . './view/css/dashboard-custom-style.css',
                '',
                null,
                false
            );
            /**
             * @info Enqueue loads the scripts into the project:
             *  as long as the scripts have been registered, it will enqueue the dependencies prior if they haven't already.
             */
            // Enqueue Dashboard libs:
            wp_enqueue_script( 'apexLatest' );
            wp_enqueue_script( 'apex-custom-js' );
            wp_enqueue_script( 'ajax-minified' );
            wp_enqueue_script( 'react-prop-types' );
            wp_enqueue_script( 'react-apex-minified' );
            // Enqueue Dashboard styles:
            wp_enqueue_style( 'apexchart-custom-style' );
            wp_enqueue_style( 'dashboard-custom-style' );
        }
        
        // enqueue jquery lib:
        wp_enqueue_script( 'jquery' );
    }
    
    // @info Proper Hook for front-end scripts: - https://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_scripts
    add_action( 'wp_enqueue_scripts', 'enqueue_frontend_scripts' );
    // Enqueue Front-end scripts:
    function enqueue_frontend_scripts()
    {
        /* Enqueues for popup are added here */
        /* @todo - Under construction not compatible with pop-up source code: */
        
        if ( !is_admin() ) {
            wp_register_style(
                'popup-css',
                plugin_dir_url( __FILE__ ) . './view/css/popup.css',
                '',
                null,
                false
            );
            wp_enqueue_style( 'popup-css' );
            // enqueue jquery lib:
            wp_enqueue_script( 'jquery' );
        }
    
    }
    
    /*
     * Create admin menu in the backend.
     * */
    function addToMenu()
    {
        $controlPanel = new VIT_WR_ControlPanel();
        $integrations_menu_page = new VIT_WR_Integrations();
        $overview = new VIT_WR_Dashboard();
        $reviews = new VIT_WR_Reviews();
        add_menu_page(
            WebsiteReview_PAGE_TITLE,
            WebsiteReview_TITLE,
            'manage_options',
            'vit-wr-dashboard',
            '',
            'dashicons-star-half'
        );
        // First submenu redirects to main-menu ^
        add_submenu_page(
            'vit-wr-dashboard',
            'Integrations',
            'Integrations',
            'manage_options',
            'vit-wr-integrations',
            array( $integrations_menu_page, 'vit_wr_getIntegrationsView' )
        );
        add_submenu_page(
            'vit-wr-dashboard',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'vit-wr-dashboard',
            array( $overview, 'vit_wr_getDashbordView' )
        );
        add_submenu_page(
            'vit-wr-dashboard',
            'Reviews',
            'Reviews',
            'manage_options',
            'vit-wr-my-reviews',
            array( $reviews, 'vit_wr_getAllReviewsView' )
        );
        add_submenu_page(
            'vit-wr-dashboard',
            'Settings',
            'Settings',
            'manage_options',
            'vit-wr-settings',
            array( $controlPanel, 'vit_wr_getControlPanelView' )
        );
    }
    
    add_action( 'admin_menu', 'addToMenu' );
    /*
     * Injects the code for the popup in the content variable.
     */
    $db_inst = new VIT_WR_Database();
    $integration_settings = $db_inst->vit_wr_getIntegrationSettings();
    // Register function for the shortcode:
    function displayPopup()
    {
        $db_inst = new VIT_WR_Database();
        $integration_settings = $db_inst->vit_wr_getIntegrationSettings();
        
        if ( $integration_settings !== 1 ) {
            $popup = new Popup();
            $popup->displayCurrentPopup();
        }
    
    }
    
    function embed_plugin_globally( $content )
    {
        $content .= do_shortcode( '[vit_wr_popup]' );
        return $content;
    }
    
    add_shortcode( 'vit_wr_popup', 'displayPopup' );
    
    if ( $integration_settings == 1 ) {
        add_filter( 'the_content', 'embed_plugin_globally', 10 );
        embed_plugin_globally( '' );
    }
    
    //        $popup->displayCurrentPopup();
    /*
     * Creating database Tables if they don't exist already.
     */
    function vit_website_reviews_activate()
    {
        $db = new VIT_WR_Database();
        /*
         * Create reviews table in database.
         */
        $db->vit_wr_createTableReviews();
        // Api extensions: adds tables for every user
        $db->vit_wr_createTableUserSettings();
        $db->vit_wr_baseConfigPushUserSettings();
        // Create setting table in database and push the default settings.
        $db->vit_wr_createTableCustomSettings();
        $db->vit_wr_baseConfigPushCustomSettings();
    }
    
    register_activation_hook( __FILE__, 'vit_website_reviews_activate' );
    website_review_plugin_sdk()->add_action( 'after_uninstall', 'vit_website_reviews_uninstall' );
    // @info: CHECK FOR PLUGIN VERSION UPDATES:
    // @params: hook, function, priority (10) == low.
    // Alternative: upgrader_process_complete
    add_action( 'plugins_loaded', 'vit_wr_check_for_updates', 10 );
    function vit_wr_check_for_updates()
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "options";
        // get_var fetches single value:
        $vit_wr_currentVersion = $wpdb->get_var( "select option_value from {$table_name} WHERE option_name = 'WebsiteReviewPlugin_db_version'" );
        // if user version is not equal to the latest version:
        if ( $vit_wr_currentVersion !== WebsiteReview_VERSION ) {
            try {
                global  $wpdb ;
                // add extra column:
                $table_name = $wpdb->prefix . "website_review_custom_settings";
                $sql = "ALTER TABLE {$table_name} ADD disableOnMobile INT (1) NOT NULL AFTER disableFeedbackLabel";
                $wpdb->query( $sql );
                // add in integrations
                $sql = "ALTER TABLE {$table_name} ADD shortcode_ig_settings INT (1) NOT NULL AFTER disableOnMobile";
                $wpdb->query( $sql );
                // Update version number: (after pushing update)
                $table_name = $wpdb->prefix . "options";
                $sql = "UPDATE {$table_name} SET option_value = '" . WebsiteReview_VERSION . "' WHERE option_name = 'WebsiteReviewPlugin_db_version'";
                $wpdb->query( $sql );
                if ( $wpdb->last_error !== '' ) {
                    throw new Exception( $wpdb->print_error() );
                }
            } catch ( Exception $e ) {
                echo  "<h5 class='uk-alert uk-alert-danger uk-text-bold'>" . $e->getMessage() . "</h5>" ;
                return false;
            }
        }
    }
    
    // @info: updates database to latest version:
    // Use this to prevent Freemius conflict with Uninstall hook
    function vit_website_reviews_uninstall()
    {
        // @todo: make this optional
        $enableHardDelete = true;
        if ( $enableHardDelete ) {
            vit_wr_hard_delete();
        }
    }
    
    // Deletes all plugin data:
    function vit_wr_hard_delete()
    {
        global  $wpdb ;
        $tableNames = [ "website_review_custom_settings", "website_review_reviews", "website_review_user_settings" ];
        foreach ( $tableNames as $tableName ) {
            $vit_wr_table = $wpdb->prefix . $tableName;
            $wpdb->query( sprintf( "DROP TABLE IF EXISTS %s", $vit_wr_table ) );
        }
    }

}
