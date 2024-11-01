<?php

/**
 * @file-info:
 * File contains source code for the apex-radarChart.
 * (Total Reviews per TimeSpan)
 **/



// Check if the data contains any values:
if (!empty($dailyReviewAmount)) {
    ?>

    <!-- If so data will be extracted and divided into different arrays -->
    <script type='text/javascript'>
        <?php
        // encode php data so it can be stored within a Js variable:
        $js_reviewLabels = json_encode($dailyReviewAmount['labels']);
        $js_reviewData = json_encode($dailyReviewAmount['data']);

        echo "var reviewLabels_unprocessed = [" . $js_reviewLabels . "];  \n ";
        // The amount of reviews per timeSpan will be stored here:
        echo "var reviewData_complete = [" . $js_reviewData . "];  \n ";
        ?>

        var reviewLabels_complete = [];

        // step out of first array loop:
        reviewLabels_unprocessed[index].forEach(function (entry) {
            // Labels for the chart are stored in reviewLabels_complete
            reviewLabels_complete.push(entry);
        });


        // Use this for debugging:
        // console.log(reviewLabels_complete);
        // console.log(reviewLabels_unprocessed);


    </script>

    <?php
}
?>



<script type="text/babel">

    var graphSize = 150;

    function calculateGraphSize(){
            if(screen.width <= 350){
                graphSize = 125;
            }
            else{
                graphSize = 150;
            }
      return graphSize;
    }


    class RadarChart extends React.Component {

        constructor(props) {
            super(props);

            this.state = {
                options: {
                    // Labels for the chart will be loaded here:
                    labels: reviewLabels_complete,
                    plotOptions: {
                        radar: {
                            // Define size of the graph
                            size: calculateGraphSize(),
                            polygons: {
                                strokeColor: '#e9e9e9',
                                fill: {
                                    colors: ['#f8f8f8', '#fff']
                                }
                            }
                        }
                    },
                    title: {
                        text: ''
                    },
                    dataLabels: {
                        enabled: true
                    },
                    colors: ['#0c7efb'],
                    // Code for the blue dot markers:
                    markers: {
                        size: 6,
                        colors: ['#fff'],
                        strokeColor: '#008ffb',
                        strokeWidth: 5,
                    },
                    tooltip: {

                        y: {
                            formatter: function(val) {
                                return val
                            }
                        }
                    },
                    yaxis: {
                        tickAmount: 0,
                        labels: {
                            // Values on y-axis are displayed here:
                            // Return val to fixed:
                            formatter: function(val, i) {
                                // Delete Max Point to avoid styling conflicts
                                if((i % 2 === 0) && (val < Math.max.apply(Math,reviewData_complete[index]))) {
                                    // Round value so it doesn't contain floating values.
                                    return val.toFixed(0);
                                } else {
                                    return ''
                                }
                            }
                        }
                    }
                },
                series: [{
                    name: 'Amount of reviews',
                    // Data series for reviews per timespan will be loaded here.
                    data: reviewData_complete[index],
                }]
            }
        }


        render() {
            return (
                <div>
                    <div id="apex_radarChart">
                        <ReactApexChart options={this.state.options} series={this.state.series} type="radar" height="350" />
                    </div>
                    <div id="html-dist">
                    </div>
                </div>
            );
        }
    }


    // Render chart to corresponding div id.
    const domContainer = document.querySelector('#apex_radarChart');
    ReactDOM.render(React.createElement(RadarChart), domContainer);
</script>

