<?php

/*
 * Author URI: https://vincoit.com
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */


require_once(WebsiteReview__PLUGIN_DIR . '/model/Database.php');


class VIT_WR_Integrations
{
    private $database;
    public $ig_settings;

    public function setIntegrationSettings(){

    }
    public function getIntegrationSettings(){
        if(empty($this->ig_settings)){
            $this->ig_settings = 0;
        }

        return $this->ig_settings;
    }

    public function __construct()
    {
        $this->database = new VIT_WR_Database();
        $settings = new VIT_WR_Settings();
        $this->ig_settings = $settings->getShortcodeIntegrationSettings();

    }

    public function vit_wr_getIntegrationsView()
    {


        $this->checkPostData();

        include_once WebsiteReview__PLUGIN_DIR . '/view/backend/Integrations.php';
    }



    private function checkPostData()
    {
        if (isset($_POST['integration_settings'])) {

            if (isset($_POST["global_shortcodes"])) {
                $_POST["global_shortcodes"] = 1;
            } else {
                $_POST["global_shortcodes"] = 0;
            }

        $post_val = $_POST["global_shortcodes"];
        $post_val = strtolower($post_val);

        try{
            if((!in_array($post_val, array("true", "false", "1", "0", "yes", "no", ""), true))){
                throw new Exception("Error. bool Value does not match the desired input. Please try again!");
            }
        }
        catch (Exception $e){
            echo "<h5 class='uk-alert uk-alert-danger uk-text-bold'>" . $e->getMessage() . "</h5>";
            return false;
        }

        if($post_val == "true"){
            $post_val = 1;
        }
            $this->updateIntegrationOptions($post_val);

        }

    }

        private function updateIntegrationOptions($post_data){
            global $wpdb;
            $table_name = $wpdb->prefix . "website_review_custom_settings";
            $data = array('shortcode_ig_settings' => $post_data);
            $where = array('id' => 1);

            $updated = $wpdb->update($table_name, $data, $where);

            if (false === $updated) {
                echo "<script>UIkit.notification({message: 'Woops something went wrong! ', status: 'danger'})</script>";

            } else {
                echo "<div class=\"uk-alert-primary\" uk-alert><a class=\"uk-alert-close\" uk-close></a>  <p>The settings have successfully been updated!</p></div>";
            }

            ?> <script> location.reload();</script><?php

        }

}