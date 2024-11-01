<?php

/*
 * Author URI: https://vincoit.com
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */


require_once(WebsiteReview__PLUGIN_DIR . '/model/Database.php');

class VIT_WR_Reviews
{
    private $database;

    public function __construct()
    {
        $this->database = new VIT_WR_Database();


    }


    public function vit_wr_getAllReviewsView()
    {

        if (isset($_GET["delete"])) {
            $this->database->vit_wr_deleteReview($_GET["delete"]);

        }
        $allReviews = $this->database->vit_wr_getNewestReviews();
        $ReviewRowCount = $this->database->vit_wr_getReviewRowCount();

        include_once WebsiteReview__PLUGIN_DIR . '/view/backend/Reviews.php';


    }

    public function vit_wr_deleteSelectedReviews($arrayContainingIds)
    {
        if (isset($arrayContainingIds)) {
            if (sizeof($arrayContainingIds) > 0) {
                foreach ($arrayContainingIds as $reviewId) {
                    $this->database->vit_wr_deleteReview($reviewId);
                }
                echo "<script>alert('The selected reviews are successfully deleted.')</script>";
            }
        }else{
            echo "<script>alert('There aren\'t any reviews selected to delete. Please select the reviews you wish to delete in order to delete them.')</script>";
        }
    }



}