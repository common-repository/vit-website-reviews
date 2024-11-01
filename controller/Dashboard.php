<?php
/*
 * Author URI: https://vincoit.com
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * File Description: Controller file for view/Dashboard.php
 *
*/

require_once(WebsiteReview__PLUGIN_DIR . '/model/Database.php');

class VIT_WR_Dashboard
{
    private $database;
    protected $apex_timePeriod;

    public function __construct()
    {
        $this->database = new VIT_WR_Database();
    }

    public function vit_wr_getDashbordView()
    {
        // grab reviews
        $allReviews = $this->database->vit_wr_getAllReviews();
        // grab all reviews perCountry reviews
        $reviewsPerCountry = $this->database->vit_wr_getAmountOfReviewsPerCountry();

        //html will be loaded here
        include_once WebsiteReview__PLUGIN_DIR . '/view/backend/Dashboard.php';
    }

    // Converts current rating to a percentage rating.
    protected function getRatingPercentage($rating){
        // (Rating varies from 0 to 5) 5*20 = 100 = limit.
        return $rating * 20;
    }

    protected function setTimePeriod($timePeriod){
        $this->apex_timePeriod = $timePeriod;
    }

    protected function getTimePeriod(){
        // Define default filter type:
        if(empty($this->apex_timePeriod)){
            $this->apex_timePeriod = "weekly";
        }
         // else return chosen filter type:
         return $this->apex_timePeriod;
      }

     // @function-info: Function Fetches the amount of reviews per time period.
     // @param-info: $period signifies The filter type.
     // @return-type: Assoc INT Array
    protected function getAmountOfReviewsPerPeriod($period){
        // Depending on the given filter type this function will return 2 arrays.
        // (List of last 12 months) ||  (List of last 7 days) etc.
        // (Amount of reviews monthly over that period) || (Amount of reviews daily over that period) etc.
        $results =  $this->database->vit_wr_getReviewsPerTimePeriod($period);
        return $results;
    }

}