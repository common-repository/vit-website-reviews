<?php

/**
* @file-info:
* Code for apex barchart goes here.
* This showcases the amount of reviews you received per country:
* (Only the top 10 highest raters will be displayed in the graph)
**/





// Reviewspercountry is declared in view/dashboard.php
if (!empty($reviewsPerCountry)) {

    $countryseries_complete = array();
    $reviewseries_complete = array();


    foreach ($reviewsPerCountry as $country) {
        if(!empty($country->fromCountry)){
            array_push($countryseries_complete,  $country->fromCountry);
        }
        else{
            array_push($countryseries_complete,  "Unknown origin");
        }
        array_push($reviewseries_complete,  $country->amount);
    }

    ?>


    <script type='text/javascript'>
        <?php
        // encode php data so it can be stored within a Js variable:
        $js_country_data = json_encode($countryseries_complete);
        $js_Countryreviews = json_encode($reviewseries_complete);

        echo "var countrySeries_unprocessed = [" . $js_country_data . "];  \n ";
        echo "var reviewSeries_complete = [" . $js_Countryreviews . "];  \n ";
        ?>

        var countrySeries_complete = [];


        // step out of first array loop:
        countrySeries_unprocessed[index].forEach(function (entry) {
            countrySeries_complete.push(entry);
        });

        //console.log(countrySeries_complete);

    </script>

<?php
}
?>

<script type="text/babel">

    class BarChart extends React.Component {

        constructor(props) {
            super(props);

            this.state = {
                options: {
                    chart: {
                        id: "basic-bar"
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        // Reverse values to display chart from high to low:
                        categories:  countrySeries_complete.reverse()
                    }
                },
                // Grab the idx of of reviewseries_complete to step out of the first array. So data will be displayed correctly.
                series: [{
                        name: "Amount of reviews",
                        // Reverse values to display chart from high to low:
                        data: reviewSeries_complete[index].reverse()
                }],
            }
        }

        render() {
            return (
                <div>
                    <div id="apex-barchart">
                        <ReactApexChart options={this.state.options} series={this.state.series} type="bar" height="350" />
                    </div>
                    <div id="html-dist">
                    </div>
                </div>
            );
        }
    }

    // Render chart to corresponding div id.
    const domContainer = document.querySelector('#apex-barchart');
    ReactDOM.render(React.createElement(BarChart), domContainer);

</script>
