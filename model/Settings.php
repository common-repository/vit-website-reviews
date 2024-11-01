<?php

/*
 * Author URI: https://vincoit.com
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */
require_once WebsiteReview__PLUGIN_DIR . '/model/Database.php';
class VIT_WR_Settings
{
    private static  $id ;
    private static  $allowComments ;
    private static  $styleColorOne ;
    private static  $styleColorTwo ;
    private static  $styleColorThree ;
    private static  $styleColorFour ;
    private static  $styleColorFive ;
    private static  $reviewButtonLocation ;
    private static  $customIntroText ;
    private static  $customThankYouText ;
    private static  $customLabelText ;
    private static  $lockSystem ;
    private static  $cookieTimer ;
    private static  $disableOnMobile ;
    private static  $shortcode_ig_settings ;
    private static  $disableFeedbackLabel ;
    public function __construct()
    {
        $this->vit_wr_initialise();
    }
    
    /**
     * @return mixed
     */
    public static function getId()
    {
        return self::$id;
    }
    
    /**
     * @param mixed $id
     */
    public static function setId( $id )
    {
        self::$id = $id;
    }
    
    /**
     * @return mixed
     */
    public static function getAllowComments()
    {
        return self::$allowComments;
    }
    
    /**
     * @param mixed $allowComments
     */
    public static function setAllowComments( $allowComments )
    {
        self::$allowComments = $allowComments;
    }
    
    /**
     * @return mixed
     */
    public static function getStyleColorOne()
    {
        return self::$styleColorOne;
    }
    
    /**
     * @param mixed $styleColorOne
     */
    public static function setStyleColorOne( $styleColorOne )
    {
        self::$styleColorOne = $styleColorOne;
    }
    
    /**
     * @return mixed
     */
    public static function getStyleColorTwo()
    {
        return self::$styleColorTwo;
    }
    
    /**
     * @param mixed $styleColorTwo
     */
    public static function setStyleColorTwo( $styleColorTwo )
    {
        self::$styleColorTwo = $styleColorTwo;
    }
    
    /**
     * @return mixed
     */
    public static function getStyleColorThree()
    {
        return self::$styleColorThree;
    }
    
    /**
     * @param mixed $styleColorThree
     */
    public static function setStyleColorThree( $styleColorThree )
    {
        self::$styleColorThree = $styleColorThree;
    }
    
    /**
     * @return mixed
     */
    public static function getReviewButtonLocation()
    {
        return self::$reviewButtonLocation;
    }
    
    /**
     * @param mixed $reviewButtonLocation
     */
    public static function setReviewButtonLocation( $reviewButtonLocation )
    {
        self::$reviewButtonLocation = $reviewButtonLocation;
    }
    
    /**
     * @return mixed
     */
    public static function getCustomThankYouText()
    {
        return self::$customThankYouText;
    }
    
    /**
     * @param mixed $customThankYouText
     */
    public static function setCustomThankYouText( $customThankYouText )
    {
        self::$customThankYouText = $customThankYouText;
    }
    
    /**
     * @return mixed
     */
    public static function getCustomIntroText()
    {
        return self::$customIntroText;
    }
    
    /**
     * @param mixed $customIntroText
     */
    public static function setCustomIntroText( $customIntroText )
    {
        self::$customIntroText = $customIntroText;
    }
    
    /**
     * @return mixed
     */
    public static function getStyleColorFour()
    {
        return self::$styleColorFour;
    }
    
    /**
     * @param mixed $styleColorFour
     */
    public static function setStyleColorFour( $styleColorFour )
    {
        self::$styleColorFour = $styleColorFour;
    }
    
    /**
     * @return mixed
     */
    public static function getStyleColorFive()
    {
        return self::$styleColorFive;
    }
    
    /**
     * @param mixed $styleColorFive
     */
    public static function setStyleColorFive( $styleColorFive )
    {
        self::$styleColorFive = $styleColorFive;
    }
    
    /**
     * @return mixed
     */
    public static function getCustomLabelText()
    {
        return self::$customLabelText;
    }
    
    /**
     * @param mixed $customLabelText
     */
    public static function setCustomLabelText( $customLabelText )
    {
        self::$customLabelText = $customLabelText;
    }
    
    /**
     * @return mixed
     */
    public static function getLockSystem()
    {
        return self::$lockSystem;
    }
    
    /**
     * @param mixed $lockSystem
     */
    public static function setLockSystem( $lockSystem )
    {
        self::$lockSystem = $lockSystem;
    }
    
    /**
     * @return mixed
     */
    public static function getDisableFeedbackLabel()
    {
        return self::$disableFeedbackLabel;
    }
    
    /**
     * @param mixed $disableFeedbackLabel
     */
    public static function setDisableFeedbackLabel( $disableFeedbackLabel )
    {
        self::$disableFeedbackLabel = $disableFeedbackLabel;
    }
    
    public static function getDisableOnMobile()
    {
        return self::$disableOnMobile;
    }
    
    /**
     * @param mixed $disableOnMobile
     */
    public static function setDisableOnMobile( $disableOnMobile )
    {
        self::$disableOnMobile = $disableOnMobile;
    }
    
    public static function getShortcodeIntegrationSettings()
    {
        return self::$shortcode_ig_settings;
    }
    
    public static function setShortcodeIntegrationSettings( $shortcode_ig_settings )
    {
        self::$shortcode_ig_settings = $shortcode_ig_settings;
    }
    
    public function vit_wr_initialise()
    {
        $database = new VIT_WR_Database();
        $result = $database->vit_wr_getSettings();
        $this->setId( $result->id );
        $this->setallowComments( $result->allowComments );
        $this->setstyleColorOne( $result->styleColorOne );
        $this->setstyleColorTwo( $result->styleColorTwo );
        $this->setstyleColorThree( $result->styleColorThree );
        $this->setreviewButtonLocation( $result->reviewButtonLocation );
        $this->setCustomIntroText( $result->customIntroText );
        $this->setCustomThankYouText( $result->customThankYouText );
        $this->setDisableFeedbackLabel( $result->disableFeedbackLabel );
        $this->setDisableOnMobile( $result->disableOnMobile );
        $this->setShortcodeIntegrationSettings( $result->shortcode_ig_settings );
        $this->setCustomLabelText( $result->customLabelText );
        $this->setStyleColorFour( $result->styleColorFour );
        $this->setStyleColorFive( $result->styleColorFive );
        $this->setLockSystem( $result->lockSystem );
    }

}