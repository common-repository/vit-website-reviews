<?php

/*
 * Author URI: https:
 *
 *
 * File Description:
 * This is the initial view file for the admin page "Dashboard".
 * This page displays all Review information by making use of various graphs.
 * These various graphs are tools used to present the data in a structured but also elegant manner.
 *
 *
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * */
// create instance
$dashboardObj = new VIT_WR_Dashboard();
// define control variables used throughout file:
$amountOfRatings = 0;
$sumOfTotalRatings = 0;
$reviews_exist = true;
?>
<script>
    // Declare the final date-series that will be used by apex graph.
    var dateseries_Final = [];
    var dateseries_Final_points = [];
</script>

<?php 
// Check if results exist:
// $Allreviews is declared in Dashboard/Controller

if ( !empty($allReviews) ) {
    foreach ( $allReviews as $rating ) {
        $amountOfRatings++;
        $sumOfTotalRatings += $rating->rating;
    }
    $averageRating = $sumOfTotalRatings / $amountOfRatings;
    $avrPercentageRating = $dashboardObj->getRatingPercentage( $averageRating );
    // START OF APEXCHART DATETIME PHP
    $creationDateArray = array();
    $ratingArray = array();
    // Process Db values and add them into array:
    foreach ( $allReviews as $review ) {
        // Microtime to Ms. = * 1000
        array_push( $creationDateArray, strtotime( $review->createdOn ) * 1000 );
        array_push( $ratingArray, $review->rating );
    }
    $x = array();
    $y = array();
    $dateSeries_complete = array();
    // Format Db values into separate array:
    for ( $idx = 0 ;  $idx < count( $creationDateArray ) ;  $idx++ ) {
        array_push( $dateSeries_complete, $creationDateArray[$idx] . "," . $ratingArray[$idx] );
    }
    for ( $idx = 0 ;  $idx < count( $creationDateArray ) ;  $idx++ ) {
        array_push( $dateSeries_complete, $creationDateArray[$idx] . "," . $ratingArray[$idx] );
    }
    ?>

    <script type='text/javascript'>
        <?php 
    // encode php data so it can be stored within a Js variable:
    $js_array = json_encode( $dateSeries_complete );
    echo  "var dateSeries_Raw = [" . $js_array . "];  \n " ;
    ?>

        var times = [];
        var ratings = [];

        dateSeries_Raw[0].forEach(function (element) {
            times.push(parseFloat(element.split(',')[0]));
            ratings.push(parseFloat(element.split(',')[1]));

        });



        /*
        * This part normalizes the graph so it has no "hard" turns.
        * */

        var x = [];
        var y = [];
        var entries = [];
        var resolution = 500;
        var std = 1000 * 60 * 60 * 6; // one day of ms
        for (var i = 0; i < resolution + 1; i++) {
            x.push((times[0]+std*12) * (1 - i / resolution) + (i / resolution) * (times[times.length - 1] - std*3));

            var w = 0.0;
            var sum = 0.0;
            for (var j = 0; j < times.length; j++) {
                var t = times[j];
                var rating = ratings[j];
                var diff = (x[i] - t) / std;

                var g = (1 + diff*diff/4)
                var gauss_w = 1/(g * g);  // 3 deg of freedom

                w = w + gauss_w;
                sum = sum + gauss_w * rating;
            }
            if (w > 0.0) {
                var calc = (sum / w).toString();
                y.push(calc);
                entries.push(x[i].toString() + ',' + y[i].toString())
            } else {
                x.pop()
            }
        }

        // END OF Normalisation

        var original_data = [];
        original_data.push(dateSeries_Raw[0]);
        dateSeries_Raw[0] = entries;


        const index = 0;

        // step out of first array loop:
        dateSeries_Raw[index].forEach(function (entry) {

            // for every array value create an extra array with 2 values and push these arrays to another array:
            // to match Apexchart dateserie structure --> check console log.
            var newArray = entry.split(",");
            // string to Int conversion Because Apexcharts ACCEPTS INTs ONLY!
            // Unix Date:
            newArray[0] = parseFloat(newArray[0]);
            // Rating:
            newArray[1] = parseFloat(newArray[1]);

            dateseries_Final.push(newArray);
        });


        // step out of first array loop:
        original_data[index].forEach(function (entry) {

            // for every array value create an extra array with 2 values and push these arrays to another array:
            // to match Apexchart dateserie structure --> check console log.
            var newArray = entry.split(",");
            // string to Int conversion Because Apexcharts ACCEPTS INTs ONLY!
            // Unix Date:
            newArray[0] = parseFloat(newArray[0]);
            // Rating:
            newArray[1] = parseFloat(newArray[1]);

            dateseries_Final_points.push(newArray);
        });



    </script>

    <?php 
} else {
    $reviews_exist = false;
}

// END OF RATING CODE
// @info START OF RADARCHART CODE:
// Sets Radarchart view to default:

if ( !isset( $_SESSION['plugin-view-settings']['review-filter-type'] ) || !isset( $_POST['apex-date-period'] ) ) {
    // Grab current review options:
    $reviewsPeriod = $dashboardObj->getTimePeriod();
    $dailyReviewAmount = $dashboardObj->getAmountOfReviewsPerPeriod( $reviewsPeriod );
}

// If user has chosen a date-filter option --> change filter time to corresponding option.

if ( isset( $_SESSION['plugin-view-settings']['review-filter-type'] ) ) {
    // Grab current review options:
    $dashboardObj->setTimePeriod( $_SESSION['plugin-view-settings']['review-filter-type'] );
    $reviewsPeriod = $dashboardObj->getTimePeriod();
    $dailyReviewAmount = $dashboardObj->getAmountOfReviewsPerPeriod( $reviewsPeriod );
}

// Check filter settings:

if ( isset( $_POST['apex-date-period'] ) ) {
    $time_Period = $_POST['apex-date-period'];
    $_SESSION['plugin-view-settings']['review-filter-type'] = $time_Period;
    // clear any possible whitespace:
    $trimmed_time = trim( $time_Period );
    // define matching results:
    $matches = array( "weekly", "monthly", "annually" );
    // Error handling (prevent injections)
    
    if ( in_array( $trimmed_time, $matches ) ) {
        $dashboardObj->setTimePeriod( $trimmed_time );
        // Grab current review options:
        $reviewsPeriod = $dashboardObj->getTimePeriod();
        $dailyReviewAmount = $dashboardObj->getAmountOfReviewsPerPeriod( $reviewsPeriod );
    } else {
        return "Filter error! Unable to read request. ";
    }

}

?>

<!-- END OF APEXCHART DATETIME Calculations -->


<!doctype html>
<html>
<head>
    <title>Dashboard Page</title>
    <meta charset="UTF-8" name="Dashboard" content="Dashboard page:">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

</head>

<!-- Start of body -->
<body>
<!-- @info START OF APEXCHART Code  -->

<!-- SCRIPT INCLUDES: -->
<!--@info Script includes are enqueued in vit-website-reviews.php-->
<!-- END OF SCRIPT INCLUDES: -->

<!-- @info START OF APEXCHART CLASS-->
<div class="vit-wr-wrapper">
    <div class="uk-grid uk-grid-small uk-child-width-expand@m">
        <div class="uk-card uk-card-body uk-margin-small-right scaled-sm">
            <header>
                <!-- The beginning of the first row -->
                <?php 
?>
                    <div class="header">
                <?php 
?>

                    <h2>Dashboard:</h2>
                    <p class="card-info">
                        Welcome to the Vit Website Reviews dashboard! <br>
                        Statistics of your website will be viewed in the charts on this page. <br>
                        You can use these statistics to optimize your website or anything else you would like.
                    </p>
                    <a href="http://www.vincoit.com" target="_blank" class="vit-logo"><img src="<?php 
echo  plugin_dir_url( dirname( __FILE__ ) ) . '../view/images/logo.png' ;
?>"></a>
                </div>
            </header>
        </div>

            <?php 
?>
    </div>

    <!-- Apex Rating info is stored here -->
    <div id="ratingValues" hidden
         value="<?php 
echo  $avrPercentageRating . ' ' . $averageRating . ' ' . $reviews_exist . ' ' . $amountOfRatings ;
?>">
    </div>

    <div id="dashboard-container">
        <!-- apex reads out this ID for graph -- Stastistic graph is loaded here -->
        <div class="uk-grid uk-grid-small uk-child-width-expand@m">
            <div class="uk-card uk-card-default uk-card-body scaled-sm ">
                <h4 class="card-header">Extensive Average Rating</h4>
                <div id="apexchart_datetime_graph"></div>
            </div>
        </div>
        <div class="uk-grid uk-grid-small uk-child-width-expand@m">
            <div class="uk-card uk-card-default uk-margin-small-right uk-card-body scaled-sm ">
                <h4 class="card-header">Total Reviews Per Timespan</h4>
                <!-- Display graph (only) if results exist -->
                <?php 

if ( $reviews_exist ) {
    ?>

                    <p class="card-info">
                        Hovering over each dot will indicate the amount
                        of reviews posted on that given day/month/year.
                    </p>
                    <p class="card-info">
                        Current filter:

                        <?php 
    // Switch content text message based on selected filter value:
    switch ( ( isset( $_SESSION['plugin-view-settings']['review-filter-type'] ) ? $_SESSION['plugin-view-settings']['review-filter-type'] : '' ) ) {
        case "weekly":
            echo  "Past 7 days." ;
            break;
        case "monthly":
            echo  "Past 12 months." ;
            break;
        case "annually":
            echo  "Past 5 years." ;
            break;
        default:
            echo  "Past 7 days." ;
            break;
    }
    ?>

                    </p>

                    <form class="apex-date-filter" name="apex-date-filter" action="admin.php?page=vit-wr-dashboard&#apex_radarChart" method="POST">
                        <select name="apex-date-period" onchange="this.form.submit()">
                            <!-- @todo Session in selected zetten:  -->
                            <option disabled selected style="display:none;"  value="<?php 
    echo  ( isset( $_SESSION['plugin-view-settings']['review-filter-type'] ) ? $_SESSION['plugin-view-settings']['review-filter-type'] : '' ) ;
    ?>">
                            <?php 
    echo  ( isset( $_SESSION['plugin-view-settings']['review-filter-type'] ) ? $_SESSION['plugin-view-settings']['review-filter-type'] : 'Filter Data' ) ;
    ?></option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option  value="annually">Anually</option>
                        </select>
                        <!-- other single input form boxes follow this select  -->
                    </form>
                    <div id="apex_radarChart"></div>
                <?php 
} else {
    echo  "<p>It appears there are no reviews yet. Have someone post a review and statistics will appear!</p>" ;
}

?>
            </div>
            <!--  END OF RADARCHART-->
            <?php 
?>
                    <div class="uk-card uk-card-default uk-card-body scaled-sm">
                        <h4 class="card-header">Current Average Rating:</h4>
                        <p class="card-info">
                            Your current website rating based on all-time results:
                        </p>
                        <div class="vit-wr-rating-statistics ">
                            <!-- Circular Chart (2) is loaded here  -->
                            <div id="totalRating" class="vit-wr-donuts"></div>
                            <!-- Circular Chart (1) is loaded here  -->
                            <div id="averageRating" class="vit-wr-donuts"></div>
                        </div>
                    </div>
                <?php 
?>
        </div>
    </div>
</div>
<!--    Text apart in een div:   -->


<!-- @info START OF APEX rating statistic Areachart  -->
<?php 
include_once plugin_dir_path( dirname( __FILE__ ) ) . '../view/backend/dashboard-extensions/apex-areachart.php';
?>
<!-- @info END OF APEX rating statistic Areachart   -->

<!-- @info START OF APEX radarChart (Heatmap) - Reviews per day -->
<?php 
if ( $reviews_exist ) {
    include_once plugin_dir_path( dirname( __FILE__ ) ) . '../view/backend/dashboard-extensions/apex-radarchart.php';
}
?>

<!-- @info END OF APEX radarChart (Heatmap) - Reviews per day -->

<!-- @info START OF APEX COUNTRY BAR CHART -->

<?php 
if ( $reviews_exist && website_review_plugin_sdk()->can_use_premium_code__premium_only() ) {
    include_once plugin_dir_path( dirname( __FILE__ ) ) . '../view/backend/dashboard-extensions/apex-barchart.php';
}
?>

<!-- @info END OF APEX COUNTRY BAR CHART -->


</body>
<footer>
</footer>

</html>




