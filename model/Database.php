<?php

/*
 * Author URI: https://vincoit.com
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once WebsiteReview__PLUGIN_DIR . '/model/Settings.php';
class VIT_WR_Database
{
    private  $WebsiteReviewPlugin_db_version = WebsiteReview_VERSION ;
    private  $WebsiteReviewPlugin_settings_db_version = WebsiteReview_VERSION ;
    public function __construct()
    {
    }
    
    public function vit_wr_createTableReviews()
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_reviews";
        //Compatible with wp 3.5 and above.
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table_name} (\r\n          id mediumint(9) NOT NULL AUTO_INCREMENT,\r\n          rating int(9) NOT NULL,\r\n          comment varchar(258),\r\n          fromCountry varchar(258),\r\n          createdOn datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,\r\n          PRIMARY KEY  (id)\r\n        ) {$charset_collate};";
        add_option( 'WebsiteReviewPlugin_db_version', $this->WebsiteReviewPlugin_db_version );
        dbDelta( $sql );
    }
    
    /*
    * @todo: CreateTableUserSettings functie aanmaken
    * @todo:  Bouw hier functionaliteit in om via http://api.vincoit.com/getKey.php
    een nieuwe key te laten genereren & deze weg te schrijven.
    */
    // Generates user settings table
    public function vit_wr_createTableUserSettings()
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_user_settings";
        //Compatible with wp 3.5 and above.
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table_name} (\r\n          id mediumint(9) NOT NULL AUTO_INCREMENT,\r\n          user_hostname varchar(255) NOT NULL,\r\n          user_privatekey varchar(255) NOT NULL ,\r\n          createdOn datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,\r\n          PRIMARY KEY  (id)\r\n        ) {$charset_collate};";
        add_option( 'WebsiteReviewPlugin_db_version', $this->WebsiteReviewPlugin_db_version );
        dbDelta( $sql );
        // error handling:
        
        if ( $wpdb->last_error !== '' ) {
            $wpdb->print_error();
            echo  "CREATE FAILED" ;
            die;
        }
    
    }
    
    public function vit_wr_createTableCustomSettings()
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_custom_settings";
        //Compatible with wp 3.5 and above.
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE {$table_name} (\r\n          id mediumint(9) NOT NULL AUTO_INCREMENT,\r\n          allowComments BIT NOT NULL,\r\n          styleColorOne varchar(10) NOT NULL,\r\n          styleColorTwo varchar(10) NOT NULL,\r\n          styleColorThree varchar(10) NOT NULL,\r\n          styleColorFour varchar(10) NOT NULL,\r\n          styleColorFive varchar(10) NOT NULL,\r\n          reviewButtonLocation int(2) NOT NULL,\r\n           customIntroText varchar(256) NOT NULL,\r\n             customThankYouText varchar(256) NOT NULL,\r\n             customLabelText varchar(32) NOT NULL,\r\n             lockSystem int(2) NOT NULL,\r\n             disableFeedbackLabel int(1) NOT NULL,\r\n             disableOnMobile int(1) NOT NULL,\r\n             shortcode_ig_settings int(1) NOT NULL,\r\n             cookieTimer int(5),\r\n          \r\n          createdOn datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,\r\n          PRIMARY KEY  (id)\r\n        ) {$charset_collate};";
        // Adds a db option in the wp_options table
        add_option( 'WebsiteReviewPlugin_settings_db_version', $this->WebsiteReviewPlugin_settings_db_version );
        dbDelta( $sql );
    }
    
    // Configure basic user settings:
    public function vit_wr_baseConfigPushUserSettings( $disableCheck = false )
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_user_settings";
        /*
         * Check if data is already set so it wont create multiple rows of base settings
         * */
        
        if ( is_null( $this->vit_wr_getUserSettings() ) || $disableCheck == true ) {
            /*
             * baseconfig is defined here
             * */
            // @info Test the following script by activating the plugin with error reporting on.
            $destAdress = "https://api.vincoit.com/getKey.php?website=" . $_SERVER['SERVER_NAME'];
            $returnValue = json_decode( wp_remote_get( $destAdress )["body"], true );
            $wpdb->insert( $table_name, array(
                'id'              => 0,
                'user_hostname'   => $_SERVER['SERVER_NAME'],
                'user_privatekey' => $returnValue["apiKey"],
                'createdOn'       => current_time( 'mysql', 1 ),
            ) );
        }
    
    }
    
    // Basic config for custom settings:
    public function vit_wr_baseConfigPushCustomSettings( $disableCheck = false )
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_custom_settings";
        /*
         * Check if data is already set so it wont create multiple rows of base settings
         * */
        if ( is_null( $this->vit_wr_getSettings() ) || $disableCheck == true ) {
            /*
             * baseconfig is defined here
             * */
            $wpdb->insert( $table_name, array(
                'id'                    => 0,
                'allowComments'         => 'true',
                'styleColorOne'         => '#ffffff',
                'styleColorTwo'         => '#18dcff',
                'styleColorThree'       => '#ffffff',
                'styleColorFour'        => '#000000',
                'styleColorFive'        => '#18dcff',
                'reviewButtonLocation'  => '5',
                'customIntroText'       => 'Please leave your rating',
                'customThankYouText'    => 'Thank you for leaving your feedback!',
                'customLabelText'       => 'Feedback',
                'lockSystem'            => '1',
                'disableFeedbackLabel'  => '0',
                'disableOnMobile'       => '0',
                'shortcode_ig_settings' => '0',
            ) );
        }
    }
    
    public function addToTableReview( $rating, $comment, $fromCountry )
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . 'website_review_reviews';
        $wpdb->insert( $table_name, array(
            'rating'      => $rating,
            'comment'     => $comment,
            'fromCountry' => $fromCountry,
            'createdOn'   => current_time( 'mysql' ),
        ) );
        return;
    }
    
    public function vit_wr_deleteReview( $id )
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . 'website_review_reviews';
        $wpdb->delete( $table_name, array(
            'id' => $id,
        ) );
    }
    
    public function vit_wr_getSettings()
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_custom_settings";
        $result = $wpdb->get_results( "\r\n    SELECT * \r\n    FROM " . $table_name . " order by id desc LIMIT 1\r\n" );
        foreach ( $result as $result ) {
            return $result;
        }
    }
    
    public function vit_wr_getUserSettings()
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_user_settings";
        $result = $wpdb->get_results( "\r\n    SELECT * \r\n    FROM " . $table_name . " order by id desc LIMIT 1\r\n" );
        foreach ( $result as $result ) {
            return $result;
        }
    }
    
    // returns user privateKey --> id&LIMIT= 1 because there can only be one key.
    public function vit_wr_getUserPrivateKey()
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_user_settings";
        $result = $wpdb->get_results( "\r\n        SELECT user_privatekey\r\n        FROM " . $table_name . " WHERE id = 1 LIMIT 1\r\n    " );
        if ( !$result && $wpdb->last_error !== '' ) {
            $wpdb->print_error();
        }
        // return the key
        foreach ( $result as $resultSet ) {
            return $resultSet->user_privatekey;
        }
    }
    
    // returns user hostname --> id&LIMIT= 1 because there can only be one key.
    public function vit_wr_getUserHostName()
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_user_settings";
        $result = $wpdb->get_results( "\r\n        SELECT user_hostname\r\n        FROM " . $table_name . " WHERE id = 1 LIMIT 1\r\n    " );
        if ( !$result && $wpdb->last_error !== '' ) {
            $wpdb->print_error();
        }
        // return the key
        foreach ( $result as $resultSet ) {
            return $resultSet->user_hostname;
        }
    }
    
    public function vit_wr_getIntegrationSettings()
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_custom_settings";
        $result = $wpdb->get_results( "\r\n        SELECT shortcode_ig_settings\r\n        FROM " . $table_name . " WHERE id = 1\r\n    " );
        if ( !$result && $wpdb->last_error !== '' ) {
            $wpdb->print_error();
        }
        // return the key
        foreach ( $result as $resultSet ) {
            return $resultSet->shortcode_ig_settings;
        }
    }
    
    public function vit_wr_updateSettings( $id, $data )
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_custom_settings";
        $updated = $wpdb->update( $table_name, wp_unslash( $data ), array(
            'id' => $id,
        ) );
        
        if ( false === $updated ) {
            echo  "<script>UIkit.notification({message: 'Woops something went wrong! ', status: 'danger'})</script>" ;
        } else {
            echo  "<div class=\"uk-alert-primary\" uk-alert><a class=\"uk-alert-close\" uk-close></a>  <p>The settings have successfully been updated!</p></div>" ;
        }
    
    }
    
    public function vit_wr_getAllReviews()
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_reviews";
        $result = $wpdb->get_results( "\r\n    SELECT * \r\n    FROM " . $table_name . " ORDER BY createdOn DESC" );
        return $result;
    }
    
    public function vit_wr_getNewestReviews( $limit = 100 )
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_reviews";
        $result = $wpdb->get_results( "\r\n    SELECT * \r\n    FROM " . $table_name . " ORDER BY createdOn DESC LIMIT " . $limit );
        return $result;
    }
    
    // Function fetches all db results based on specified int limits
    // --> Used for pagination in back-end.
    public function vit_wr_getLimitedReviews( $start_from, $per_page )
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_reviews";
        $result = $wpdb->get_results( "\r\n    SELECT * \r\n    FROM " . $table_name . " ORDER BY createdOn DESC LIMIT {$start_from}, {$per_page}" );
        return $result;
    }
    
    // Fetches the amount of reviews that were accumulated over a specified period of time.
    public function vit_wr_getReviewsPerTimePeriod( $period )
    {
        // Daily rowCount is stored here:
        $reviewArray = array();
        // Limit changes depending on period.
        $limit = "";
        $time_filter = "";
        // Y-m-d OR Y-m
        $date_format = "";
        // Last Few days are stored here:
        $chartLabels = array();
        // Fetch current time:
        $start = current_time( 'mysql' );
        // Switch Timeperiod
        switch ( $period ) {
            case "weekly":
                $date_format = "Y-m-d";
                $time_filter = 'days';
                // Reformat:
                $start = date( 'Y-m-d', strtotime( $start ) );
                // Subtract 7 days from current DateTime To provide the limit.
                $limit = date( 'Y-m-d', strtotime( '-7 days', strtotime( $start ) ) );
                // Fetch last 7 days info:
                for ( $i = 0 ;  $i < 7 ;  $i++ ) {
                    // l Returns A full textual representation of the day of the week
                    $dayOfTheWeek = date( 'l', strtotime( '-' . $i . $time_filter, strtotime( $start ) ) );
                    array_push( $chartLabels, $dayOfTheWeek );
                }
                break;
            case "monthly":
                // Reformat:
                $date_format = "Y-m";
                $time_filter = 'months';
                $start = date( 'Y-m', strtotime( $start ) );
                $limit = date( 'Y-m', strtotime( '-12 months', strtotime( $start ) ) );
                // Fetch last 12 months info:
                for ( $i = 0 ;  $i < 12 ;  $i++ ) {
                    // F Returns A full textual representation of the month
                    $nameOfTheMonth = date( 'F', strtotime( '-' . $i . $time_filter, strtotime( $start ) ) );
                    array_push( $chartLabels, $nameOfTheMonth );
                }
                break;
                // Yearly:
            // Yearly:
            case "annually":
                // Reformat:
                $date_format = "Y";
                $time_filter = 'year';
                // Show last 5 years: including this year:
                $start = date( 'Y', strtotime( $start ) );
                $limit = date( 'Y', strtotime( '-5 year', strtotime( $start ) ) );
                // Fetch last 5 years info:
                for ( $i = 0 ;  $i <= 5 ;  $i++ ) {
                    // F Returns A full textual representation of the month
                    $nameOfTheYear = date( 'Y', strtotime( '-' . $i . $time_filter, strtotime( $start ) ) );
                    array_push( $chartLabels, $nameOfTheYear );
                }
                break;
            default:
                return "Filter error! Unable to read request. ";
                break;
        }
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_reviews";
        $iterator = true;
        // Start value decreases each iteration.
        // as long as values do not match.
        $i = 1;
        while ( $iterator ) {
            // Use prepared statements to bind wildcard otherwise query won't read properly.
            // Use %s placeholders for String format %d for INT format
            $prepared_statement = $wpdb->prepare( "SELECT * FROM {$table_name}\r\n                    WHERE createdOn LIKE %s ORDER BY createdOn DESC", $start . '%' );
            $reviewCount = $wpdb->query( $prepared_statement );
            
            if ( $reviewCount ) {
                $rowcount = $reviewCount;
            } else {
                $rowcount = 0;
            }
            
            array_push( $reviewArray, $rowcount );
            // keep subtracting days until specified history limit is reached.
            // Annual filter works differently interpreting time values:
            
            if ( $period == "annually" ) {
                $start = date( $date_format ) - $i;
                $i++;
                // @todo dit evt weghalen:
                if ( $i > 10 ) {
                    return "Error resolving data values for chart";
                }
            } else {
                // If months --> Subtract months || If Days --> Subtract days.
                $start = date( $date_format, strtotime( '-1' . $time_filter, strtotime( $start ) ) );
            }
            
            // Use StringCompare To match given output:
            
            if ( strcmp( $start, $limit ) === 0 ) {
                // When Limiter is reached break out of loop:
                $iterator = false;
                $returnValues = array(
                    "labels" => $chartLabels,
                    "data"   => $reviewArray,
                );
                return $returnValues;
            }
        
        }
        // END OF While
    }
    
    // Calculates the number of existing reviews at that current moment.
    public function vit_wr_getReviewRowCount()
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_reviews";
        $wpdb->get_results( "SELECT * FROM {$table_name} " );
        $rowcount = $wpdb->num_rows;
        return $rowcount;
    }
    
    // Returns the top 10 highest rating countries
    public function vit_wr_getAmountOfReviewsPerCountry()
    {
        global  $wpdb ;
        $table_name = $wpdb->prefix . "website_review_reviews";
        $result = $wpdb->get_results( "SELECT   fromCountry, count(*) as \"amount\" \r\n        FROM   {$table_name}\r\n        GROUP BY fromCountry\r\n        ORDER BY amount DESC  \r\n        LIMIT 10\r\n        \r\n        " );
        return $result;
    }
    
    // Yields the country name & associated flag from the posted reviewers country.
    public function vit_wr_getReviewCountryInfo( $reviewerIP )
    {
        $ip = $reviewerIP;
        $key = $this->vit_wr_getUserPrivateKey();
        $host = $this->vit_wr_getUserHostName();
        // connect to the api:
        $destAdress = "https://api.vincoit.com/iplookup.php?key=" . $key . "&ip=" . $ip . "&host=" . $host;
        try {
            // try to retrieve values from api:
            $returnValue = json_decode( wp_remote_get( $destAdress )["body"], true );
            if ( empty($returnValue) ) {
                throw new Exception( "Error fetching country data. Unable to connect to API services" );
            }
        } catch ( Exception $e ) {
            echo  "<h5 class='uk-alert uk-alert-danger uk-text-bold'>" . $e->getMessage() . "</h5>" ;
            return false;
        }
        return $returnValue;
    }

}