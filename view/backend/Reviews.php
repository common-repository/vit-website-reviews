<?php
/*
 * Author URI: https://vincoit.com
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * File Description: This is the initial view for the admin page "Reviews". There are two separate views which will be loaded in and can be
 * found under view/backend/reviews.
 *
*/


/*
 * Saves the setting of list view or card view in the session so the user doesnt loses it's selected view on page switch.
 * */
if (isset($_GET["view"])) {
    $_SESSION['plugin-view-settings']['list-view-type'] = $_GET["view"];
}

?>
    <style>
        /*override uikit white bg*/
        html.wp-toolbar {
            background: transparent !important;
        }

 /*Content styling for reviews: */
    .onoffswitch {
        position: relative;
        width: 55px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        margin-top: 1vh;
    }

    .onoffswitch-checkbox {
        display: none;
    }

    .onoffswitch-label {
        display: block;
        overflow: hidden;
        cursor: pointer;
        height: 20px;
        padding: 0;
        line-height: 20px;
        border: 2px solid #FFFFFF;
        border-radius: 20px;
        background-color: #9E9E9E;
        transition: background-color 0.3s ease-in;
    }

    .onoffswitch-label:before {
        content: "";
        display: block;
        width: 20px;
        margin: 0px;
        background: #FFFFFF;
        position: absolute;
        top: 0;
        bottom: 0;
        right: 33px;
        border: 2px solid #FFFFFF;
        border-radius: 20px;
        transition: all 0.3s ease-in 0s;
    }

    .onoffswitch-checkbox:checked + .onoffswitch-label {
        background-color: #42A5F5;
    }

    .onoffswitch-checkbox:checked + .onoffswitch-label, .onoffswitch-checkbox:checked + .onoffswitch-label:before {
        border-color: #42A5F5;
    }

    .onoffswitch-checkbox:checked + .onoffswitch-label:before {
        right: 0px;
    }

    .viewSelect {
        float: right;
        margin-top: -50px;
    }

    @media screen and (max-width: 768px) {
        .uk-search-icon-flip {
            display: none;
        }
    }

    @media screen and (max-width: 500px) {
        .vit-wr-formsubmitbtn-delete-all-selected {
            margin-left: 0 !important;
            margin-top: 10px;
        }
        .viewSelect {
            margin-top: -100px;
        }
    }

</style>
<div style="padding: 35px;">
    <h2>All Reviews</h2>
    <span>All reviews that were submitted are listed here.</span>

    <?php
    // Checks if session exists if so determine current list view:
    $list_view_style = "";

    if(isset($_SESSION['plugin-view-settings']['list-view-type'])){
        $list_view_style = $_SESSION['plugin-view-settings']['list-view-type'];
    }
    ?>

    <div id="viewContainer">
        <div class="viewSelect">
            <span>List view: </span>
            <div class="onoffswitch">
                <input type="checkbox" hidden name="onoffswitch" class="onoffswitch-checkbox"
                       id="myonoffswitch" <?= ($list_view_style == "list") ? "checked" : "" ?> >
                <label class="onoffswitch-label" for="myonoffswitch"></label>
            </div>
        </div>
        <hr/>


        <?php
        /*
         * Load in the previous view the user was on. If there wasn't one load in the default card view.
         * */
        if ((isset($_SESSION['plugin-view-settings']['list-view-type']))) {

            if ($_SESSION['plugin-view-settings']['list-view-type'] == "list") {
                include_once WebsiteReview__PLUGIN_DIR . '/view/backend/reviews/ReviewsList.php';
            } else {
                include_once WebsiteReview__PLUGIN_DIR . '/view/backend/reviews/ReviewsCards.php';
            }
        } else {
            include_once WebsiteReview__PLUGIN_DIR . '/view/backend/reviews/ReviewsCards.php';
        }
        ?>
    </div>

    <script>
        /*
        * Gives the switch to switch between views a redirect and make it set GET parameters.
        * */
        jQuery('#myonoffswitch').change(function () {
            if (this.checked) {

                jQuery(location).attr('href', '?page=vit-wr-my-reviews&view=list&review_page=1');
            } else {

                jQuery(location).attr('href', '?page=vit-wr-my-reviews&view=grid');
            }

        });
    </script>