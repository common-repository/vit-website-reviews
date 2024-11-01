/**
 * Basic Javascript for Apex Donut Charts.
 * Used in View/Dashboard.php
 */


// @info || APEXCHART START of code for circular chart (1) "Rating" :

// Fetch rating values from hidden element:
var rating_values = document.getElementById("ratingValues").getAttribute("value");
var rating_value_array = rating_values.split(" ");




// Store every value into a different variable:
var average_rating_percentage = rating_value_array[0];
var average_rating_value = rating_value_array[1];
var rating_exists = rating_value_array[2];
var amountOfRatings = rating_value_array[3];

var avr_rating_options = {
    chart: {
        height: 280,
        type: "radialBar",
    },

    // only accepts percental values so fetch dbresult and multiply by 20.
    // divide by 20 = BEST OPTION to go from 5 based star system to 100% percental display:
    // Ternary operator to check rating:
    series: [rating_exists ? average_rating_percentage : 0],
    labels: ["Average Rating:"],
    colors: ["#20E647", "#F46036"],
    plotOptions: {
        radialBar: {
            hollow: {
                margin: 0,
                size: "70%",
                // original bg: #293450
                background: "#008ffb"
            },
            track: {
                dropShadow: {
                    enabled: true,
                    top: 2,
                    left: 0,
                    blur: 4,
                    opacity: 0.15
                }
            },
            dataLabels: {
                name: {
                    offsetY: -10,
                    color: "#fff",
                    fontSize: "17px"
                },
                value: {
                    // divide by 20 = BEST OPTION to go from 5 based star system to 100% percental display:
                    // toFixed rounds the value to 1 decimal place.
                    formatter: function(val) {
                        return parseFloat(val / 20).toFixed(1);
                    },
                    color: "#fff",
                    // declares the font size of the decimal value
                    fontSize: "30px",
                    show: true
                }
            }
        }
    },
    fill: {
        type: "gradient",
        gradient: {
            shade: "dark",
            type: "vertical",
            gradientToColors: ["#87D4F9"],
            stops: [1, 100]
        }
    },
    stroke: {
        lineCap: "round"
    }
};

var averageRating = new ApexCharts(document.querySelector("#averageRating"), avr_rating_options);

// Renders graph into the view
averageRating.render();

// @info APEXCHART END of code for circular chart (1)  "Rating"



// @info START OF APEX Total Rating circular chart (2) "Total Reviews"


var ratingAmount_options = {
    chart: {
        height: 280,
        type: "radialBar",
    },


    // If there are reviews display full bar: (100%) if not set to zero.
    series: [rating_exists ? 100 : 0],
    labels: ["Total Reviews:"],
    colors: ["#a789f9", "#F46036"],
    plotOptions: {
        radialBar: {
            hollow: {
                margin: 0,
                size: "70%",
                // original bg: #293450
                background: "#008ffb"
            },
            track: {
                dropShadow: {
                    enabled: true,
                    top: 2,
                    left: 0,
                    blur: 4,
                    opacity: 0.15
                }
            },
            dataLabels: {
                name: {
                    offsetY: -10,
                    color: "#fff",
                    fontSize: "17px"
                },
                value: {
                    // Don't really need to format values here:
                    formatter: function(val) {
                        return parseFloat(rating_exists ? amountOfRatings : val);
                    },
                    color: "#fff",
                    // declares the font size of the decimal value.
                    fontSize: "30px",
                    show: true
                }
            }
        }
    },
    fill: {
        type: "gradient",
        gradient: {
            shade: "dark",
            type: "vertical",
            gradientToColors: ["#896af9"],
            stops: [1, 100]
        }
    },
    stroke: {
        lineCap: "round"
    }
};

var totalRatingChart = new ApexCharts(document.querySelector("#totalRating"), ratingAmount_options);

totalRatingChart.render();



// @info END OF APEX Total Rating circular chart (2) "Total Reviews"





