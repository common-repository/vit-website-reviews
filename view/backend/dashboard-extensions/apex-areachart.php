<?php
/**
 * @file-info:
 * File contains source code for the apex area-datetime chart.
 *
 **/

?>

<!--   @info START OF APEXCHART AREA DATETIME SCRIPT (STATISTIC TIMELINE)  -->

<script type="text/babel">

    // declare constants for Unix stamps:
    const unix_oneday = 86400000;
    const unix_oneweek = 604800000;
    const unix_onemonth = 2592000000;
    const unix_sixmonths = 15768000000;
    const unix_oneyear = 31536000000;


    function getCurrentYear(){
        return new Date().getFullYear().toString();
    }

    class AreaChart extends React.Component {

        constructor(props) {
            super(props);

            this.state = {
                // default state:
                selection: 'all',
                options: {
                    // START OF Y-axis settings
                    yaxis: {
                        min: 0,
                        max: 5,
                        floating: false,
                        decimalsInFloat: 1,

                        title: {
                            text: 'Website-Rating',
                            style: {
                                fontSize: '20px',
                                fontFamily: 'Helvetica, Arial, sans-serif',
                                cssClass: 'apexcharts-yaxis-style',
                            },
                        },
                    },
                    // END OF Y-axis settings

                    annotations: {
                        yaxis: [{
                            y: 30,
                            // min & max value of y-axis
                            min: 1,
                            floating: true,
                            max: 5,
                            borderColor: '#999',
                            label: {
                                show: true,
                                text: 'Website-Rating',
                                style: {
                                    color: "#fff",
                                    fontSize: '20px',
                                    fontFamily: 'Helvetica, Arial, sans-serif',
                                    cssClass: 'apexcharts-yaxis-style',
                                    background: '#00E396'
                                }
                            },
                        }],

                        xaxis: [{
                            x: new Date().getTime(),
                            borderColor: '#999',
                            yAxisIndex: 0,
                            label: {
                                show: true,
                                text: 'Today',
                                style: {
                                    color: "#fff",
                                    background: '#775DD0'
                                }
                            }
                        }]
                        // END OF X-axis settings

                    },

                    // Change animations here:
                    chart: {
                        type: 'line',
                        animations: {
                            enabled: true,
                            easing: 'easeout', // linear, easeout, easein, easeinout, swing, bounce, elastic
                            speed: 5,
                            animateGradually: {
                                delay: 5000,
                                enabled: true
                            },
                            dynamicAnimation: {
                                enabled: true,
                                speed: 850
                            }
                        }
                    },

                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: "straight",

                    },
                    markers: {
                        size: [0, 3],
                        color: ['#F44336', '#e923bc']

                        // style: 'hollow',
                    },
                    xaxis: {
                        type: 'datetime',
                        min: new Date().getTime(),
                        tickAmount: 6,
                    },
                    tooltip: {
                        x: {
                            format: 'dd MMM yyyy'
                        }
                    },
                    fill: {
                        type: 'gradient',

                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.9,
                            stops: [0, 100]
                        }
                    }
                },
                // @info data param -> = Use this parameter to load your preferred data serie array.
                series: [{
                    name: "Average Rating",
                    // type: 'line',
                    // markers: {
                    //     size:0,
                    //     type:'hollow'
                    // },
                    data: dateseries_Final
                }
                    // , {
                    //     name: 'Points',
                    //     type: 'scatter',
                    //
                    //     data: dateseries_Final_points
                    // }
                ],
            }
        }

        updateData(timeline) {
            this.setState({
                selection: timeline
            })

            // (Switches timeline view based on chosen filter)
            switch (timeline) {

                case 'one_week':
                    this.setState({
                        options: {
                            xaxis: {
                                min: new Date().getTime() - unix_oneweek,
                                max: new Date().getTime(),
                            }
                        }
                    })
                    break;

                case 'one_month':
                    this.setState({
                        options: {
                            xaxis: {
                                min: new Date().getTime() - unix_onemonth,
                                max: new Date().getTime(),
                            }
                        }
                    })
                    break;

                case 'six_months':
                    this.setState({
                        options: {
                            xaxis: {
                                min: new Date().getTime() - unix_sixmonths,
                                max: new Date().getTime(),
                            }
                        }
                    })
                    break;
                case 'one_year':
                    this.setState({
                        options: {
                            xaxis: {
                                min: new Date().getTime() - unix_oneyear,
                                max: new Date().getTime(),
                            }
                        }
                    })
                    break;
                case 'ytd':
                    this.setState({
                        options: {
                            xaxis: {

                                // Start at 1 Jan of the current year:
                                min: new Date('01 Jan' + ' ' + getCurrentYear()).getTime(),
                                max: new Date().getTime(),
                            }
                        }
                    })
                    break;
                case 'all':
                    this.setState({
                        options: {
                            xaxis: {
                                min: undefined,
                                max: undefined,
                            }
                        }
                    })
                    break;
                default:

            }
        }



        render() {
                // Render filter button - used for filtering on a specific Timespan
            return (
                <div>
                    <div id="datetimeChart">
                        <div className="toolbar">
                            <button onClick={() => this.updateData('one_week')} id="one_week"
                                    className={(this.state.selection === 'one_week' ? 'active' : '')}>1W
                            </button>
                            <button onClick={() => this.updateData('one_month')} id="one_month"
                                    className={(this.state.selection === 'one_month' ? 'active' : '')}>1M
                            </button>
                            <button onClick={() => this.updateData('six_months')} id="six_months"
                                    className={(this.state.selection === 'six_months' ? 'active' : '')}>6M
                            </button>
                            <button onClick={() => this.updateData('one_year')} id="one_year"
                                    className={(this.state.selection === 'one_year' ? 'active' : '')}>1Y
                            </button>
                            <button onClick={() => this.updateData('ytd')} id="ytd"
                                    className={(this.state.selection === 'ytd' ? 'active' : '')}>YTD
                            </button>
                            <button onClick={() => this.updateData('all')} id="all"
                                    className={(this.state.selection === 'all' ? 'active' : '')}>ALL
                            </button>
                        </div>
                        <ReactApexChart options={this.state.options} series={this.state.series} type="area"
                                        height="350"/>
                    </div>
                    <div id="html-dist">
                    </div>
                </div>
            );
        }
    }

    // @info: END OF APEXCHART Area Class

    const domContainer = document.querySelector('#apexchart_datetime_graph');
    ReactDOM.render(React.createElement(AreaChart), domContainer);



    // Fixes loading issue by forcing apexchart to load:
    window.onload = function (e) {
        var forcebtn = document.getElementById("all");
        forcebtn.click();
    }


</script>

<!-- @info END OF APEXCHART coding block  -->