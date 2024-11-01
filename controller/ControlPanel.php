<?php

/*
 * Author URI: https://vincoit.com
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */
require_once WebsiteReview__PLUGIN_DIR . '/model/Defines.php';
require_once WebsiteReview__PLUGIN_DIR . '/model/Database.php';
require_once WebsiteReview__PLUGIN_DIR . '/model/Settings.php';
class VIT_WR_ControlPanel
{
    private  $database ;
    private  $settings ;
    private  $buttonLocations ;
    public function __construct()
    {
        $this->database = new VIT_WR_Database();
        $this->settings = new VIT_WR_Settings();
        /*
         * These are used for the front end feedback label locations.
         * */
        $this->buttonLocations = [
            [ 0, "Left Top" ],
            [ 1, "Left Middle" ],
            [ 2, "Left Bottom" ],
            [ 3, "Right Top" ],
            [ 4, "Right Middle" ],
            [ 5, "Right Bottom" ],
            [ 6, "Bottom Center" ]
        ];
    }
    
    public function vit_wr_getControlPanelView()
    {
        /*
         * Check if the user changed settings
         * */
        $this->checkForPost();
        include_once WebsiteReview__PLUGIN_DIR . '/view/backend/Settings.php';
    }
    
    private function checkForPost()
    {
        /*
         * Four different POST checks. One for each section on the settings page and a general reset to defaults function.
         * General options, layout options, custom options and the reset to default.
         * */
        
        if ( isset( $_POST["generalOptions"] ) ) {
            /*
             * To prevent undefined notice if the checkboxes are NOT checked.
             * */
            
            if ( !isset( $_POST["allowComments"] ) ) {
                $_POST["allowComments"] = false;
            } else {
                $_POST["allowComments"] = true;
            }
            
            // make ints from bool values:
            
            if ( !isset( $_POST["disableFeedbackLabel"] ) ) {
                $_POST["disableFeedbackLabel"] = 0;
            } else {
                $_POST["disableFeedbackLabel"] = 1;
            }
            
            
            if ( !isset( $_POST["disableOnMobile"] ) ) {
                $_POST["disableOnMobile"] = 0;
            } else {
                $_POST["disableOnMobile"] = 1;
            }
            
            $generalOptions = [
                "allowComments",
                "reviewLockSystem",
                "disableFeedbackLabel",
                "disableOnMobile"
            ];
            try {
                foreach ( $_POST as $index => $value ) {
                    // For every generalOptions inside $_POST:
                    if ( in_array( $index, $generalOptions ) ) {
                        
                        if ( $index == "reviewLockSystem" || $index == "disableFeedbackLabel" ) {
                            // locksystem & feedback == int value // therefore check if int:
                            sanitize_text_field( $value );
                            $this->checkIntValue( $value );
                        } else {
                            $this->checkBoolValue( $value );
                        }
                    
                    }
                }
            } catch ( Exception $e ) {
                echo  "<h5 class='uk-alert uk-alert-danger uk-text-bold'>" . $e->getMessage() . "</h5>" ;
                return false;
            }
            if ( website_review_plugin_sdk()->is_not_paying() ) {
                $this->updateGeneralOptions(
                    $_POST["allowComments"],
                    $_POST["reviewLockSystem"],
                    $_POST["disableFeedbackLabel"],
                    $_POST["disableOnMobile"]
                );
            }
        } else {
            
            if ( isset( $_POST["layoutOptions"] ) ) {
                $colorOptions = [
                    "colorOne",
                    "colorTwo",
                    "colorThree",
                    "colorFour",
                    "colorFive"
                ];
                // Start of error handling:
                try {
                    // int validation:
                    $this->checkIntValue( $_POST["buttonLocation"] );
                    // For every colorOption inside $_POST:
                    foreach ( $_POST as $index => $value ) {
                        // if colorOptions are posted in $_POST
                        if ( in_array( $index, $colorOptions ) ) {
                            // If value is not hexadecimal returns NULL:
                            if ( is_null( sanitize_hex_color( $_POST[$index] ) ) ) {
                                throw new Exception( "Error changing layout options. Colors do not match the desired input. Please try again!" );
                            }
                        }
                    }
                } catch ( Exception $e ) {
                    echo  "<h5 class='uk-alert uk-alert-danger uk-text-bold'>" . $e->getMessage() . "</h5>" ;
                    return false;
                }
                $this->updateLayoutOptions(
                    $_POST["buttonLocation"],
                    $_POST["colorOne"],
                    $_POST["colorTwo"],
                    $_POST["colorThree"],
                    $_POST["colorFour"],
                    $_POST["colorFive"]
                );
            } else {
                
                if ( isset( $_POST["customOptions"] ) ) {
                } else {
                    if ( isset( $_POST["ResetAllSettingsToDefaults"] ) ) {
                        $this->database->vit_wr_baseConfigPushCustomSettings( true );
                    }
                }
            
            }
        
        }
        
        $this->settings->vit_wr_initialise();
    }
    
    private function updateGeneralOptions(
        $allowComments,
        $disableFeedbackLabel,
        $disableOnMobile,
        $lockSystem
    )
    {
        try {
            $data = array(
                'allowComments'        => $this->checkBoolValue( $allowComments ),
                'lockSystem'           => sanitize_text_field( $lockSystem ),
                'disableFeedbackLabel' => $this->checkIntValue( $disableFeedbackLabel ),
                'disableOnMobile'      => $this->checkIntValue( $disableOnMobile ),
            );
        } catch ( Exception $e ) {
            echo  "<h5 class='uk-alert uk-alert-danger uk-text-bold'>" . $e->getMessage() . "</h5>" ;
            return false;
        }
        $this->database->vit_wr_updateSettings( $this->settings->getId(), $data );
    }
    
    private function updateLayoutOptions(
        $buttonLocation,
        $styleColorOne,
        $styleColorTwo,
        $styleColorThree,
        $styleColorFour,
        $styleColorFive
    )
    {
        /*
         * This is always accessible.
         *  */
        
        if ( website_review_plugin_sdk()->is_free_plan() ) {
            $data = array(
                'reviewButtonLocation' => $buttonLocation,
            );
            $this->database->vit_wr_updateSettings( $this->settings->getId(), $data );
        }
    
    }
    
    private function updateCustomOptions( $customReviewText, $customThankYouText, $customLabelText )
    {
        $data = array(
            'customIntroText'    => $customReviewText,
            'customThankYouText' => $customThankYouText,
            'customLabelText'    => $customLabelText,
        );
        $this->database->vit_wr_updateSettings( $this->settings->getId(), $data );
    }
    
    // checks if value is numeric:
    private function checkIntValue( $val )
    {
        if ( !is_numeric( $val ) ) {
            throw new Exception( "Error. Value does not match the desired input. Please try again!" );
        }
        // return if value is correct:
        return $val;
    }
    
    // Checks if value if given value is boolean:
    private function checkBoolValue( $val )
    {
        $needle = strtolower( $val );
        // "" allows empty fields to be posted as well
        if ( !in_array( $needle, array(
            "true",
            "false",
            "1",
            "0",
            "yes",
            "no",
            ""
        ), true ) ) {
            throw new Exception( "Error. bool Value does not match the desired input. Please try again!" );
        }
        // return if value is correct:
        return $val;
    }

}