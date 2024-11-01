<?php

/*
 * Author URI: https://vincoit.com
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
*/
require_once WebsiteReview__PLUGIN_DIR . '/model/Database.php';
require_once WebsiteReview__PLUGIN_DIR . '/model/Defines.php';
require_once WebsiteReview__PLUGIN_DIR . '/model/Settings.php';
class Popup
{
    private  $settings ;
    private static  $hasAlreadyLoaded ;
    protected  $db ;
    public function __construct()
    {
        $this->settings = new VIT_WR_Settings();
        $this->db = new VIT_WR_Database();
    }
    
    private function submitReview( $rating, $review, $fromCountry )
    {
        // @info START input validation for Review:
        /* Only executes the post if rating is chosen --> checks if rating is posted (1,2,3 stars etc.)  * */
        if ( empty($_POST["vit-wr-rate"]) ) {
            return;
        }
        try {
            $maxRating = 5;
            if ( !is_numeric( $rating ) || $rating > $maxRating ) {
                throw new Exception( "Error. Rating value does not match the desired input. Please try again!" );
            }
            // Strips Tags & other unwanted char entities from input.
            // sanitize_text will never return false so no need for further error handling:
            $filtered_review = sanitize_textarea_field( $review );
            $fromCountry_filtered = sanitize_text_field( $fromCountry );
        } catch ( Exception $e ) {
            echo  "<h5 class='uk-alert uk-alert-danger uk-text-bold'>" . $e->getMessage() . "</h5>" ;
            // @todo fix error display on front end - Use this as an alternative for the time being.
            echo  "<script type='text/javascript'>alert('Error. Rating value does not match the desired input. Please refresh this page and try again!'); \r\n             </script>" ;
            return false;
        }
        // @info END OF input validation for review.
        /* pops up the thank you message that is defined in the settings page of the plugin. */
        /*
         * Pushes the review in the database and disables the feedback form so they cannot submit multiple reviews in one go.
         * */
        $db = new VIT_WR_Database();
        $db->addToTableReview( $rating, $filtered_review, $fromCountry_filtered );
        /*
         * Sets the session or cookie so that the same user cannot post multiple reviews anymore. This is also chosen by the user on the settings page.
         * */
        
        if ( $this->settings->getLockSystem() == "1" ) {
            $_SESSION["reviewLocked"] = true;
        } elseif ( $this->settings->getLockSystem() == "2" ) {
        }
    
    }
    
    public function checkAlreadyInjected()
    {
        $this->checkSession();
        
        if ( isset( $_SESSION['vitInjectedUrl'] ) ) {
            
            if ( $_SERVER['REQUEST_URI'] == $_SESSION['vitInjectedUrl'] ) {
                return true;
            } else {
                $_SESSION['virInjectedUrl'] = $_SERVER['REQUEST_URI'];
                return false;
            }
        
        } else {
            $_SESSION['virInjectedUrl'] = $_SERVER['REQUEST_URI'];
            return false;
        }
    
    }
    
    public function checkAlreadyInjectedByTime()
    {
        $this->checkSession();
        
        if ( !isset( $_SESSION["vitPopupLastLoadTime"] ) ) {
            $_SESSION["vitPopupLastLoadTime"] = 0;
        } else {
            /*
             * Preventing multiple injections by checking when the last injection was in milliseconds.
             * If this injection was 500 milliseconds or below it returns nothing.
             * */
            
            if ( round( microtime( true ) * 1000 ) - $_SESSION["vitPopupLastLoadTime"] <= 500 ) {
                $_SESSION["vitPopupLastLoadTime"] = round( microtime( true ) * 1000 );
                return true;
            }
        
        }
        
        $_SESSION["vitPopupLastLoadTime"] = round( microtime( true ) * 1000 );
        return false;
    }
    
    public function displayCurrentPopup()
    {
        // @info: Setting disables the plugin for mobile users when selected.
        
        if ( $this->settings->getDisableOnMobile() === "1" ) {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $userIsOnMobile = $this->isViewingOnMobile( $useragent );
            // if user is on mobile then stop displaying popup:
            if ( $userIsOnMobile ) {
                return false;
            }
        }
        
        /*
         * Checks whether the session is started, if not start it.
         * */
        $this->checkSession();
        $currentPage = (( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" )) . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $adminUrl = get_admin_url();
        $loginUrl = wp_login_url();
        // checks if currentpage contains admin or login url:
        // @logic:  if occurence of admin url in currentpage is not false:
        // https://www.php.net/manual/de/function.stripos.php
        if ( stripos( $currentPage, $adminUrl ) !== false || stripos( $currentPage, $loginUrl ) !== false ) {
            return;
        }
        /*
         * If the user choose in the settings to disable the label, it will stop executing the code here and not
         * inject the label in the content.
         * */
        if ( $this->settings->getDisableFeedbackLabel() == "1" ) {
            return;
        }
        
        if ( $this->settings->getLockSystem() == "1" ) {
            /*
             * if the user has session selected as lock system execute the following code.
             * Checks whether the user has posted a review earlier already and if so, do not display the front end popup.
             * */
            if ( !isset( $_SESSION["reviewLocked"] ) ) {
                $_SESSION["reviewLocked"] = false;
            }
            if ( !$_SESSION["reviewLocked"] ) {
                /*
                 * Adds the front end code to the wordpress content.
                 * */
                return $this->displayPopupOne();
            }
        } elseif ( $this->settings->getLockSystem() == "2" ) {
            /*
             * if the user has cookie selected as lock system execute the following code.
             * Checks whether the user has posted a review earlier already and if so, do not display the front end popup.
             * */
            if ( !isset( $_COOKIE["reviewLocked"] ) ) {
                /*
                 * Adds the front end code to the wordpress content.
                 * Checks whether the user has posted a review earlier already and if so, do not display the front end popup.
                 * */
                return $this->displayPopupOne();
            }
        } else {
            /*
             * if the user has nothing selected as lock system execute the following code.
             * */
            /*
             * Adds the front end code to the wordpress content.
             * */
            $result = $this->displayPopupOne();
            return $result;
        }
    
    }
    
    public function checkSession()
    {
        if ( session_status() == PHP_SESSION_NONE ) {
            session_start();
        }
    }
    
    protected function isViewingOnMobile( $user_agent )
    {
        
        if ( preg_match( '/(android|bb\\d+|meego).+mobile|avantgo|bada\\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge 
                |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\\.(browser|link)|vodafone|wap|windows (ce|
                phone)|xda|xiino/i', $user_agent ) || preg_match( '/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\\-)|ai(ko|
                rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|
                bw\\-(n|u)|c55\\/|capi|ccwa|cdm\\-|cell|chtm|cldc|cmd\\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\\-s|devi|dica|dmob|do(c|p)o|ds(12|\\-d)|el(49|ai)
                |em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\\-|_)|g1 u|g560|gene|gf\\-5|g\\-mo|go(\\.w|od)|gr(ad|un)|haie|hcit|hd\\-(m|p|t)|hei\\-|
                hi(pt|ta)|hp( i|ip)|hs\\-c|ht(c(\\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\\-(20|go|ma)|i230|iac( |\\-|\\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|
                jbro|jemu|jigs|kddi|keji|kgt( |\\/)|klon|kpt |kwc\\-|kyo(c|k)|le(no|xi)|lg( g|\\/(k|l|u)|50|54|\\-[a-w])|libw|lynx|m1\\-w|m3ga|m50\\/|ma(te|ui|xo)|mc(01|
                21|ca)|m\\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|
                1)|10)|ne((c|m)\\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\\-2|po(ck|
                rt|se)|prox|psio|pt\\-g|qa\\-a|qc(07|12|21|32|60|\\-[2-7]|i\\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\\-|oo|p\\-)|sdk\\/|
                se(c(\\-|0|1)|47|mc|nd|ri)|sgh\\-|shar|sie(\\-|m)|sk\\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\\-|v\\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|
                tcl\\-|tdg\\-|tel(i|m)|tim\\-|t\\-mo|to(pl|sh)|ts(70|m\\-|m3|m5)|tx\\-9|up(\\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\\-v)|vm40|voda|vulc|vx(52|53|60|
                61|70|80|81|83|85|98)|w3c(\\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\\-|your|zeto|zte\\-/i', substr( $user_agent, 0, 4 ) ) ) {
            return true;
        } else {
            return false;
        }
    
    }
    
    public function displayPopupOne()
    {
        /*
         * Prevents the front end part of the plugin from injecting itself multiple times in the front end if the user
         * (or another plugin used by the user) calls the content variable multiple times.
         * */
        //todo: or different URL
        
        if ( $this->checkAlreadyInjectedByTime() ) {
            return;
        } else {
        }
        
        /*
         * To make sure malious external parties cannot send multiple POSTS towards the users website and trigger this
         * plugin to spam the database full of reviews or potential threats. This is an important security feature.
         * */
        
        if ( !isset( $_SESSION['viSecretKey'] ) ) {
            $secretKey = substr( md5( rand() ), 0, 20 );
            $_SESSION['viSecretKey'] = $secretKey;
        }
        
        /*
         * Starts collecting lines of code to push in a variable. This is used so it can be added to the content variable.
         * */
        ob_start();
        if ( isset( $_POST["vit-secret-key"] ) ) {
            /*
             * This is where we check whether the post comes from the users website instead of a malious external party.
             * */
            
            if ( $_SESSION['viSecretKey'] == $_POST["vit-secret-key"] ) {
                $ip = $_SERVER['REMOTE_ADDR'];
                // This will contain the ip of the request
                /*
                 * If the user is logged on localhost, write a public test ip adress in the database so the user can still
                 * see the reviews in the backend as they are supposed to be viewed.
                 * This is in turn used to add a country flag to reviews.
                 * */
                
                if ( $ip == "::1" ) {
                    // $ip = "157.230.149.189";
                    $ip = "2.58.239.255";
                    // use dutch for testing.
                }
                
                /*
                 * This is used to request the visitor's country of origin. This will send the visitor's ip to
                 * www.geoplugin.net.
                 * */
                // @todo change to Wordpress file get contents function:
                // @info call getReviewCountryInfo() here to obtain country & flag info &add them to the post stack.
                // Connect to api to retrieve posters country
                $poster_countryname = $this->db->vit_wr_getReviewCountryInfo( $ip )[0];
                // $_POST['review'] contains review message (vit-wr-rate) contains rating.
                $this->submitReview( $_POST["vit-wr-rate"], $_POST["review"], $poster_countryname );
                unset( $_POST["vit-wr-rate"], $_POST["review"], $poster_countryname );
            }
        
        }
        /*
         * This is where the front end, the popup, part of our plugin is defined.
         * */
        ?>
        <div class="vit-popup-area">
            <div class="vit-popup-alert vit-popup-alert-success">
                <span class="vit-popup-alert-title vit-success-title"></span>
                <span class="vit-success"></span> 
            </div>
            <div class="vit-popup-alert vit-popup-alert-error">
                <span class="vit-popup-alert-title vit-error-title"></span>
                <span class="vit-error"></span>
            </div>
        </div>

        <div id="vit-popup-front-end-feedback-container">

            <style>

                #vit-popup-front-end-feedback-container{
                    display: none;
                }

                .vit-wr-label {
                    background-color: <?php 
        echo  $this->settings->getStyleColorOne() ;
        ?>;
                    color: <?php 
        echo  $this->settings->getStyleColorFour() ;
        ?>;

                }
                .vit-wr-defaultFont {
                    font-size: 18px;
                }
                .vit-wr-smallFont {
                    font-size: 15px;
                    padding: 10px;
                }
                #vit-wr-slideout {
                    background-color: <?php 
        echo  $this->settings->getStyleColorOne() ;
        ?>;
                }
                #vit-wr-slideout_inner {
                    border-right: 2px solid <?php 
        echo  $this->settings->getStyleColorTwo() ;
        ?> !important;
                    border-bottom: 2px solid <?php 
        echo  $this->settings->getStyleColorTwo() ;
        ?> !important;
                    border-top: 2px solid <?php 
        echo  $this->settings->getStyleColorTwo() ;
        ?> !important;
                }
                #vit-wr-slideout_inner input[type="submit"] {
                    background-size: 200% 100% !important;
                    background-position: right bottom !important;
                    background: linear-gradient(to right, <?php 
        echo  $this->settings->getStyleColorFive() ;
        ?> 50%, white 50%);
                    border-top: solid 0.5px <?php 
        echo  $this->settings->getStyleColorFive() ;
        ?> !important;
                }
                #vit-wr-slideout_inner {
                    background-color: <?php 
        echo  $this->settings->getStyleColorThree() ;
        ?> !important;
                }
                .vit-wr-intro {
                    background-color: <?php 
        echo  $this->settings->getStyleColorFive() ;
        ?> !important;
                    border-bottom: 0.5px solid  <?php 
        echo  $this->settings->getStyleColorFive() ;
        ?> !important;
                }
                #vit-wr-slideout_inner input[type="submit"] {
                    color: <?php 
        echo  $this->settings->getStyleColorFour() ;
        ?>;
                }
                #vit-wr-slideout_inner textarea {
                    color: <?php 
        echo  $this->settings->getStyleColorFour() ;
        ?>;
                }
                .vit-wr-char-count{
                    color: <?php 
        echo  $this->settings->getStyleColorFour() ;
        ?>;
                }




                <?php 
        /*
         *Converts the style settings set in the settings page to actual front end popup css. This also includes
         *the button location which is the switch case and the numbers 0-5.
         *  */
        if ( $this->settings->getCustomIntroText() == "" ) {
            echo  ".vincoit-form-if-empty{\r\n                      margin-top: -35px;\r\n                   }\r\n                   " ;
        }
        if ( ($this->settings->getReviewButtonLocation() == "5" || $this->settings->getReviewButtonLocation() == "2") && $this->settings->getAllowComments() != 1 ) {
            echo  "#vit-wr-slideout {\r\n                       bottom: 145px !important;\r\n                     }\r\n                     " ;
        }
        
        if ( $this->settings->getReviewButtonLocation() == "6" && $this->settings->getAllowComments() != 1 ) {
            echo  "#vit-wr-slideout:hover {\r\n                       bottom: 125px !important;\r\n                     }\r\n                     " ;
        } elseif ( $this->settings->getReviewButtonLocation() == "6" && $this->settings->getAllowComments() == 1 ) {
            echo  "#vit-wr-slideout:hover {\r\n                        bottom: 250px !important;\r\n                    }\r\n                    #vit-wr-slideout_inner {\r\n                        bottom: -280px !important;\r\n                    }\r\n                     " ;
        }
        
        switch ( $this->settings->getReviewButtonLocation() ) {
            case "0":
                echo  ".logged-in #vit-wr-slideout_inner {\r\n                        margin-top: 32px;\r\n                    }\r\n                    .logged-in #vit-wr-slideout {\r\n                        margin-top: 32px;\r\n                    }\r\n                     #vit-wr-slideout_inner {\r\n                        top: 0 !important;\r\n                        margin-top: 0;\r\n                    }\r\n                    #vit-wr-slideout {\r\n                        top: 0 !important;\r\n                        margin-top: 0;\r\n                    }" ;
                break;
            case "1":
                echo  ".vincoit-review-button-position {\r\n                        margin-top: 20vh;\r\n                    }" ;
                break;
            case "2":
                echo  "#vit-wr-slideout_inner {\r\n                        bottom: 0 !important;\r\n                        margin-top: 0;\r\n                        top: unset !important;\r\n                    }\r\n                    #vit-wr-slideout {\r\n                        top: unset !important;\r\n                        bottom: 249px;\r\n                    }" ;
                break;
            case "3":
                echo  ".vincoit-review-button-position {\r\n                        right: -2px;\r\n                        left: unset !important;\r\n                    }\r\n                    #vit-wr-slideout_inner {\r\n                        top: 0 !important;\r\n                        margin-top: 0;\r\n                        right: -252px;\r\n                        left: unset;\r\n                        border-right: 0 !important;\r\n                        border-left: solid 2px " . $this->settings->getStyleColorTwo() . "  !important;\r\n                    }\r\n                    .logged-in #vit-wr-slideout_inner {\r\n                        margin-top: 32px;\r\n                    }\r\n                    .logged-in #vit-wr-slideout {\r\n                        margin-top: 32px;\r\n                    }\r\n                    .vit-wr-label {\r\n                        transform: rotate(-270.0deg) translateX(26px) !important;\r\n                        -moz-transform: rotate(-270.0deg) translateX(26px) !important;\r\n                        -o-transform: rotate(-270.0deg) translateX(26px) !important;\r\n                        -webkit-transform: rotate(-270.0deg) translateX(26px) !important;\r\n                        margin: 0 0 0 -41px !important;\r\n                    }\r\n                    #vit-wr-slideout:hover > .vit-wr-label {\r\n                        margin: 0 -4F 0 0 !important;\r\n                    }                    \r\n                    #vit-wr-slideout {\r\n                        top: 0 !important;\r\n                        margin-top: 0;\r\n                        right: 0;\r\n                        left: unset !important;\r\n                    }\r\n                    #vit-wr-slideout:hover {\r\n                        right: 248px !important;\r\n                    }\r\n                    #vit-wr-slideout:hover #vit-wr-slideout_inner {\r\n                        left: unset !important;\r\n                        right: 0 !important;\r\n                    }" ;
                break;
            case "4":
                echo  ".vincoit-review-button-position {\r\n                        margin-top: 20vh;\r\n                        right: -2px;\r\n                        left: unset !important;\r\n                    }\r\n                    .vit-wr-label {\r\n                        transform: rotate(-270.0deg) translateX(26px) !important;\r\n                        -moz-transform: rotate(-270.0deg) translateX(26px) !important;\r\n                        -o-transform: rotate(-270.0deg) translateX(26px) !important;\r\n                        -webkit-transform: rotate(-270.0deg) translateX(26px) !important;\r\n                        margin: 0 0 0 -41px !important;\r\n                    }\r\n                    #vit-wr-slideout:hover > .vit-wr-label {\r\n                        margin: 0 -41px 0 0 !important;\r\n                    }                    \r\n                    #vit-wr-slideout_inner {\r\n                        right: -252px;\r\n                        left: unset;\r\n                        border-right: 0 !important;\r\n                        border-left: solid 2px " . $this->settings->getStyleColorTwo() . "  !important;\r\n                    }\r\n                    #vit-wr-slideout {\r\n                        right: 0;\r\n                        left: unset !important;\r\n                    }\r\n                    #vit-wr-slideout:hover {\r\n                        right: 248px !important;\r\n                    }\r\n                    #vit-wr-slideout:hover #vit-wr-slideout_inner {\r\n                        left: unset !important;\r\n                        right: 0 !important;\r\n                    }" ;
                break;
            case "5":
                echo  ".vincoit-review-button-position {\r\n                        right: -2px;\r\n                        left: unset !important;\r\n                    }\r\n                    #vit-wr-slideout_inner {\r\n                        bottom: 0 !important;\r\n                        margin-top: 0;\r\n                        right: -252px;\r\n                        left: unset;\r\n                        top: unset !important;\r\n                        border-right: 0 !important;\r\n                        border-left: solid 2px " . $this->settings->getStyleColorTwo() . "  !important;\r\n                    }\r\n                    .vit-wr-label {\r\n                        transform: rotate(-270.0deg) translateX(26px) !important;\r\n                        -moz-transform: rotate(-270.0deg) translateX(26px) !important;\r\n                        -o-transform: rotate(-270.0deg) translateX(26px) !important;\r\n                        -webkit-transform: rotate(-270.0deg) translateX(26px) !important;\r\n                        margin: 0 0 0 -41px !important;\r\n                    }\r\n                    #vit-wr-slideout:hover > .vit-wr-label {\r\n                        margin: 0 -41px 0 0 !important;\r\n                    }\r\n                    #vit-wr-slideout {\r\n                        top: unset !important;\r\n                        bottom: 249px;\r\n                        right: 0;\r\n                        left: unset !important;\r\n                    }\r\n                    #vit-wr-slideout:hover {\r\n                        right: 248px !important;\r\n                    }\r\n                    #vit-wr-slideout:hover #vit-wr-slideout_inner {\r\n                        left: unset !important;\r\n                        right: 0 !important;\r\n                    }" ;
                break;
            case "6":
                echo  ".vincoit-review-button-position {\r\n                        bottom: -10px !important;\r\n                        right: 0 !important;\r\n                        left: 0 !important;\r\n                        margin: auto;\r\n                    }\r\n                    #vit-wr-slideout_inner {\r\n                        bottom: -386px !important;\r\n                        margin-top: 0;\r\n                        margin: auto;\r\n                        right: 0;\r\n                        left: 0;\r\n                        top: unset !important;\r\n                        border-right: 0 !important;\r\n                        border-left: solid 2px " . $this->settings->getStyleColorTwo() . "  !important;\r\n                        border-right: solid 2px " . $this->settings->getStyleColorTwo() . "  !important;\r\n                    }\r\n                    .vit-wr-label {\r\n                        transform: none !important;\r\n                        margin: 0 0 0 0 !important;\r\n                    }\r\n                    #vit-wr-slideout:hover > .vit-wr-label {\r\n                    }\r\n                    #vit-wr-slideout {\r\n                        top: unset !important;\r\n                        bottom: -45px !important;  \r\n                        right: 0;\r\n                        left: 0;\r\n                        height: 66px !important;\r\n                        width: 125px !important;\r\n                    }\r\n                    \r\n                    #vit-wr-slideout:hover #vit-wr-slideout_inner {\r\n                        bottom: 0 !important;\r\n                        left: 0 !important;\r\n                        right: 0 !important;\r\n                    }" ;
                break;
        }
        ?>
            </style>


            <?php 
        /*
         * for now hard coded but has the possibility to turn into a user defined setting. This is used so the text
         * "Feedback" that is in the label and also user defined, doesnt get too big out of its borders and scales
         * it down to a smaller font if it is.
         * */
        $charLimit = 10;
        $smallFontClass = "vit-wr-smallFont";
        $defaultFontClass = "vit-wr-defaultFont";
        ?>

            <div id="vit-wr-slideout" class="vincoit-review-button-position">
                <p class="<?php 
        echo  ( strlen( $this->settings->getCustomLabelText() ) > $charLimit ? $smallFontClass : $defaultFontClass ) ;
        ?> vit-wr-label "><?php 
        echo  $this->settings->getCustomLabelText() ;
        ?></p>

                <div id="vit-wr-slideout_inner" class="vincoit-review-button-position">
                    <div class="vit-wr-slideout_inner-holder">
                        <p class="vit-wr-intro"><?php 
        echo  $this->settings->getCustomIntroText() ;
        ?></p>
                        <iframe name="vit-wr-iframe-response" style="display:none;"></iframe>
                            <form method="post" onsubmit="return checkSubmitPopupVit();" class="vincoit-form-if-empty" target="vit-wr-iframe-response">
                            <div class="vit-wr-rate" >
                                <input type="radio" id="star5" name="vit-wr-rate" value="5"/>
                                <label for="star5" title="5 stars!">5 stars</label>
                                <input type="radio" id="star4" name="vit-wr-rate" value="4"/>
                                <label for="star4" title="4 stars">4 stars</label>
                                <input type="radio" id="star3" name="vit-wr-rate" value="3"/>
                                <label for="star3" title="3 stars">3 stars</label>
                                <input type="radio" id="star2" name="vit-wr-rate" value="2"/>
                                <label for="star2" title="2 stars">2 stars</label>
                                <input type="radio" id="star1" name="vit-wr-rate" value="1"/>
                                <label for="star1" title="1 star">1 star</label>
                            </div>
                            <?php 
        echo  ( $this->settings->getAllowComments() ? "<textarea id='reviewField'  onkeyup='countChar(this)' name='review' class='uk-textarea' maxlength='200' placeholder='If you have any tips, advice or comments write them here!'></textarea>" : '' ) ;
        ?>
                            <div id="vit-charNum"
                                 class="vit-wr-char-count"><?php 
        echo  ( $this->settings->getAllowComments() ? "200 characters left" : '' ) ;
        ?></div>
                            <input type="hidden" name="vit-secret-key" value="<?php 
        echo  $_SESSION['viSecretKey'] ;
        ?>">
                            <input type="submit" class="revSubmitButton" value="<?php 
        echo  esc_html( 'Send review' ) ;
        ?>">
                        </form>
                    </div>
               </div>
            </div>
        </div>

        <script>
            /* store last submit inside a session  */
            if(sessionStorage['vit-wr_hasSubmitted'] === undefined) {
                sessionStorage['vit-wr_hasSubmitted'] = 0;
            }
            function vit_popup_success(){
                    jQuery(function($) {
                        $(".vit-popup-alert-success").fadeIn(1000);
                        var vit_popup_selectors = [".vit-success-title", ".vit-success"];
                        var customThankYouText = <?php 
        echo  json_encode( $this->settings->getCustomThankYouText() ) ;
        ?>;
                        const vit_popup_bodys = [customThankYouText, '"Your review has been submitted"'];
                        $(vit_popup_selectors).each(function(index){
                            $(vit_popup_selectors[index]).empty();
                            $(vit_popup_selectors[index]).append(vit_popup_bodys[index]);
                        });
                        /* Hide Feedback label after submit, only if user has this option enabled. */
                        var lockSystem_opt = <?php 
        echo  json_encode( $this->settings->getLockSystem() ) ;
        ?>;
                        if(lockSystem_opt != 0){
                            document.getElementById('vit-wr-slideout').style.display = 'none';
                        }
                        $('.vit-popup-front-end-feedback-container').css("display", "none");
                        $('#vit-wr-slideout:hover #vit-wr-slideout_inner').fadeOut("fast");
                        $('#vit-wr-slideout:hover #vit-wr-slideout_inner').show(300);

                        setTimeout(function(){
                            $('.vit-popup-front-end-feedback-container').addClass('vit-wr-display-none');
                            $(".vit-popup-alert-success").fadeOut("slow");
                            sessionStorage['vit-wr_hasSubmitted'] = 0;
                        },3000)
                    });
                    return true;
            }
            /* @params: error body, error title  */
            function vit_popup_error(vit_popup_body, vit_popup_title = "Oops!"){
                jQuery(function($) {
                    $(".vit-popup-alert-error").fadeIn(1000);
                    var vit_popup_selectors = [".vit-error-title", ".vit-error"];
                    const vit_popup_bodys = [vit_popup_title , '"' + vit_popup_body + '"'];
                    $(vit_popup_selectors).each(function(index){
                        $(vit_popup_selectors[index]).empty();
                        $(vit_popup_selectors[index]).append(vit_popup_bodys[index]);
                    });
                    setTimeout(function(){
                        $(".vit-popup-alert-error").fadeOut("slow");
                    },3000)
                });
            }

            document.addEventListener('DOMContentLoaded', function(){
                jQuery(function($) {
                    $("#vit-popup-front-end-feedback-container").show(500);
                });
            });

            /*
             This is used to count the words left in the comment text view so users know exactly how much they still can
              type. checkSubmitPopup makes sure that that are ratings stars selected before the review get posted.
              */

            function countChar(val) {
                var len = val.value.length;
                if (len >= 201) {
                    val.value = val.value.substring(0, 200);
                } else { document.getElementById("vit-charNum").textContent = (200 - len).toString() + " characters left";
                }
            };

            function checkSubmitPopupVit() {
                /* Cancel submit if popup has recently been submitted:  */
                if(sessionStorage['vit-wr_hasSubmitted'] > 0 ){
                    return false;
                }
                console.log(sessionStorage['vit-wr_hasSubmitted']);
                var $elementOnePopupVit = document.getElementsByName('vit-wr-rate')[0];
                var $elementTwoPopupVit = document.getElementsByName('vit-wr-rate')[1];
                var $elementThreePopupVit = document.getElementsByName('vit-wr-rate')[2];
                var $elementFourPopupVit = document.getElementsByName('vit-wr-rate')[3];
                var $elementFivePopupVit = document.getElementsByName('vit-wr-rate')[4];
                var checkedVitOne = $elementOnePopupVit.checked;
                var checkedVitTwo = $elementTwoPopupVit.checked;
                var checkedVitThree = $elementThreePopupVit.checked;
                var checkedVitFour = $elementFourPopupVit.checked;
                var checkedVitFive = $elementFivePopupVit.checked;
                /* Watch out for white space errors ! */
             if(checkedVitOne > 0 || checkedVitTwo > 0 || checkedVitThree > 0 || checkedVitFour > 0 || checkedVitFive > 0){
                /* Display succes notification! */
                 vit_popup_success();
                 sessionStorage['vit-wr_hasSubmitted'] = sessionStorage['vit-wr_hasSubmitted'] + 1;
             }
             else{
                 vit_popup_error("You need to select a rating star first!");
                 return false;
             }
            }
        </script>

        <?php 
        $variable = ob_get_clean();
        /*
         * Trimming all enters out of the code and replacing them with a space.
         * This should avoid wordpress from turning accidental enters into <p> tags
         * */
        $variable = preg_replace( '/\\s+/', ' ', trim( $variable ) );
        echo  $variable ;
        return "";
    }

}