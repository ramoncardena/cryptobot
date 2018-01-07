<template>
    <section id="portfolio-widget">
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell small-order-2 medium-order-1">
                <div class="portfolio-assets">
                    <table id="portfolioTable" class="display unstriped" width="100%"></table>
                </div>
            </div>
            <div class="small-12 medium-6 cell small-order-1 medium-order-2">
                <div class="grid-x grid-padding-x align-center-middle text-center dashboard">

                    <div class="small-12 large-2 cell  small-order-1 medium-order-2">
                        <button class="button clear button-refresh" v-on:click="refreshPortfolio()">
                           <i v-show="updatingAsset" class="fa fa-refresh fa-spin fa-fw" aria-hidden="true"></i>
                           <i v-show="!updatingAsset" class="fa fa-refresh "></i>
                        </button>
                    </div>

                    <div class="small-12 large-5 cell small-order-2 medium-order-1">
                        <div class="counter-widget">
                            <div class="title">Total BTC </div> <div class="counter">{{ totalBtc }}</div>
                        </div>
                    </div>
                    
                    <div class="small-12 large-5 cell small-order-3 medium-order-3">
                        <div class="counter-widget">
                             <div class="title">Total {{counterValueSymbol}} </div> <div class="counter">{{ totalFiat}}</div>                  
                        </div>
                    </div>

                    <div class="small-12 cell charts small-order-4 medium-order-4">
                    
                        <ul class="tabs" data-active-collapse="true" data-tabs id="collapsing-tabs">
                          <li class="tabs-title is-active"><a href="#panel1c" aria-selected="true">By Value</a></li>
                          <li class="tabs-title"><a href="#panel2c">By Origin</a></li>
                        </ul>

                        <div class="tabs-content" data-tabs-content="collapsing-tabs">
                            <div class="tabs-panel is-active" id="panel1c">
                                <div class="chart-container">
                                    <canvas id="totalsChart"></canvas>
                                </div>
                            </div>
                            <div class="tabs-panel" id="panel2c">
                                <div class="chart-container" >
                                    <canvas id="originsChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>         
        </div>
    </section>
</template>

<script>
export default {
    name: 'portfolio',
    props: [
    'portfolio',
    ],
    data: () => {
        return {
            assets: [],
            uniqueAssetsBtc: [],
            uniqueAssetsFiat: [],
            uniqueAssetsName: [],
            uniqueAssetsOriginName: [],
            uniqueAssetsOriginFiat: [],
            mostValuableName: "",
            mostValuableValue: "",
            balance: 0,
            counter_value: 0,
            price: 0,
            totalBtc: 0,
            totalFiat: 0,
            counterValueSymbol: '',
            totalsChart: {},
            originsChart: {},
            portfolioTable: {},
            loadingPortfolio: false,
            loadingAsset: false,
            updatingAsset: false
        }
    },
    computed: {

    },
    mounted() {
        this.totalBtc = 0;
        this.totalFiat = 0;

        // Setup DATATABLE
        this.portfolioTable = $('#portfolioTable').DataTable( {
            "searching": false,
            "responsive": true,
            "paging": false,
            "info": false,
            "columnDefs": [
            { "visible": false, "targets": 5 }
            ],
            columns: [
            { title: '<div class="sorting nowrap">Coin</div>'},
            { title: '<div class="sorting nowrap">Value (Fiat)</div>' },
            { title: '<div class="sorting nowrap">Value (BTC)</div>' },
            { title: '<div class="sorting nowrap">Amount</div>' },
            { title: '<div class="sorting nowrap">Price</div>' },
            { title: '<div class="sorting nowrap">Asset ID</div>' },
            { title: '<div class="sorting_asc nowrap">Origin</div>' }
            ],
            "drawCallback": function ( settings ) {
                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=null;

                api.column(6, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class="group"><td colspan="6">'+group+'</td></tr>'
                            );

                        last = group;
                    }
                } );
            }
        } );

        // Setup CHART
        var totalsChart = $("#totalsChart");
        var originsChart = $("#originsChart");
        Chart.defaults.global.legend.position = "bottom";

        var CSS_COLOR_NAMES =['rgba(230, 25, 75, 0.5)', 'rgba(60, 180, 75, 0.5)', 'rgba(255, 225, 25, 0.5)', 'rgba(0, 130, 200, 0.5)', 'rgba(245, 130, 48, 0.5)', 'rgba(145, 30, 180, 0.5)', 'rgba(70, 240, 240, 0.5)', 'rgba(240, 50, 230, 0.5)', 'rgba(210, 245, 60, 0.5)', 'rgba(250, 190, 190, 0.5)', 'rgba(0, 128, 128, 0.5)', 'rgba(230, 190, 255, 0.5)', 'rgba(170, 110, 40, 0.5)', 'rgba(255, 250, 200, 0.5)', 'rgba(128, 0, 0, 0.5)',' rgba(170, 255, 195, 0.5)', 'rgba(128, 128, 0, 0.5)', 'rgba(255, 215, 180, 0.5)', 'rgba(0, 0, 128, 0.5)', 'rgba(128, 128, 128, 0.5)', 'rgba(255, 255, 255, 0.5)'];

        // Chart.pluginService.register({
        //     beforeDraw: function (chart) {
        //         if (chart.config.options.elements.center) {
        //             //Get ctx from string
        //             var ctx = chart.chart.ctx;

        //             //Get options from the center object in options
        //             var centerConfig = chart.config.options.elements.center;
        //             var fontStyle = centerConfig.fontStyle || 'Arial';
        //             var txt = centerConfig.text;
        //             var color = centerConfig.color || '#000';
        //             var sidePadding = centerConfig.sidePadding || 20;
        //             var sidePaddingCalculated = (sidePadding/100) * (chart.innerRadius * 2)
        //             //Start with a base font of 30px
        //             ctx.font = "5px " + fontStyle;

        //             //Get the width of the string and also the width of the element minus 10 to give it 5px side padding
        //             var stringWidth = ctx.measureText(txt).width;
        //             var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;

        //             // Find out how much the font can grow in width.
        //             var widthRatio = elementWidth / stringWidth;
        //             var newFontSize = Math.floor(5 * widthRatio);
        //             var elementHeight = (chart.innerRadius * 2);

        //             // Pick a new font size so it will not be larger than the height of label.
        //             var fontSizeToUse = Math.min(newFontSize, elementHeight);

        //             //Set font settings to draw it correctly.
        //             ctx.textAlign = 'center';
        //             ctx.textBaseline = 'middle';
        //             var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
        //             var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
        //             ctx.font = fontSizeToUse+"px " + fontStyle;
        //             ctx.fillStyle = color;

        //             //Draw text in center
        //             ctx.fillText(txt, centerX, centerY);
        //         }
        //     }
        // });

        
        
        var that = this;

        Echo.private('assets.' + this.portfolio.id)
        .listen('PortfolioAssetLoaded', (e) => {
            // ************
            // ASSET LOADED
            // ************
            this.loadingAsset = true;
            this.balance = e.asset.balance; 
            this.counter_value = e.asset.counter_value;
            this.price = e.asset.price;

            this.totalsChart = new Chart(totalsChart, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'By Value',
                            data: [],
                            backgroundColor: CSS_COLOR_NAMES,
                            borderColor: '#FEFEFA',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            stacked: true,
                            gridLines: {
                                display: true,
                                color: "rgba(255,99,132,0.2)"
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }]
                    },
                    responsive: true,
                    legend: {
                        display: false
                    },
                    // elements: {
                    //     center: {
                    //     text: 'Totals ',
                    //     color: '#36A2EB', //Default black
                    //     fontStyle: 'Helvetica', //Default Arial
                    //     sidePadding: 15 //Default 20 (as a percentage)
                    //     }
                    //}
                }
            });
            this.originsChart = new Chart(originsChart, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'By Origin',
                            data: [],
                            backgroundColor: CSS_COLOR_NAMES,
                            borderColor: '#FEFEFA',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        display: true
                    },
                    // elements: {
                    //     center: {
                    //     text: 'Origins',
                    //     color: '#36A2EB', //Default black
                    //     fontStyle: 'Helvetica', //Default Arial
                    //     sidePadding: 15 //Default 20 (as a percentage)
                    //     }
                    // }
                }
            });

            var coin = '<div class="asset-info nowrap"><a href="' + e.asset.info_url + '" target="_blank"><img class="asset-img" src="' + e.asset.logo_url + '" width="20"></a> <span class="show-for-medium asset-name">' + e.asset.full_name + '</span> <span class="asset-symbol">' + e.asset.symbol + '</span></div>';

            var amount = '<div class="asset-amount  nowrap">' + parseFloat(e.asset.amount).toFixed(8) + '</div>'
            var origin = '<div class="asset-origin  nowrap">' + e.asset.origin_name + '</div>';

            // Add row to the table with the new asset
            this.portfolioTable.row.add( [
                coin,
                parseFloat(this.counter_value ).toFixed(2),
                parseFloat(this.balance).toFixed(8),
                amount,
                parseFloat(this.price).toFixed(8),
                e.asset.id,
                origin
            ] ).order( [ 6, 'asc' ] ).invalidate().draw();   

            this.loadingAsset = false; 
        })
        .listen('PortfolioAssetUpdated', (e) => {
            // ************
            // ASSET UPDATED
            // ************
            this.updatingAsset = true;
            console.log("Updating: " + this.updatingAsset);

            // Calculate current TOTAL balances (btc and fiat)
            this.totalBtc = (parseFloat(this.totalBtc) + parseFloat(e.asset.balance)).toFixed(8);
            this.totalFiat = (parseFloat(this.totalFiat) + parseFloat(e.asset.counter_value)).toFixed(2);

            // Store and show TOTAL balances
            this.balance = parseFloat(e.asset.balance).toFixed(8);
            this.price = parseFloat(e.asset.price).toFixed(8);
            this.counter_value = parseFloat(e.asset.counter_value).toFixed(2);

            // Store a consolidated array by VALUE
            var indexRepeatedAsset = this.uniqueAssetsName.indexOf(e.asset.symbol);

            if ( indexRepeatedAsset >= 0 ) {
                // If asset is already counted we sum the new value
                var newBalanceBtc = (parseFloat(this.uniqueAssetsBtc[indexRepeatedAsset]) + parseFloat(e.asset.balance));
                var newBalanceFiat = (parseFloat(this.uniqueAssetsFiat[indexRepeatedAsset]) + parseFloat(e.asset.counter_value));
                this.uniqueAssetsBtc[indexRepeatedAsset] = parseFloat(newBalanceBtc);
                this.uniqueAssetsFiat[indexRepeatedAsset] = parseFloat(newBalanceFiat);

                this.totalsChart.data.datasets[0].data[indexRepeatedAsset] = parseFloat(this.uniqueAssetsFiat[indexRepeatedAsset]).toFixed(2);
              
            }
            else {
                // If the asset doesn't exists we push it
                this.uniqueAssetsBtc.push(parseFloat(e.asset.balance));
                this.uniqueAssetsFiat.push(parseFloat(e.asset.counter_value));
                this.uniqueAssetsName.push(e.asset.symbol);

                this.totalsChart.data.labels.push(e.asset.symbol);
                this.totalsChart.data.datasets[0].data.push(parseFloat(e.asset.counter_value ).toFixed(2));
            }

            // Store consolidated array of ORIGINS
            var indexRepeatedOrigin = this.uniqueAssetsOriginName.indexOf(e.asset.origin_name);

            if ( indexRepeatedOrigin >= 0 ) {
                // If origin is already counted we sum the new value
                var newBalanceFiat = (parseFloat(this.uniqueAssetsOriginFiat[indexRepeatedOrigin]) + parseFloat(e.asset.counter_value));
                this.uniqueAssetsOriginFiat[indexRepeatedOrigin] = parseFloat(newBalanceFiat);

                this.originsChart.data.datasets[0].data[indexRepeatedOrigin] = parseFloat(newBalanceFiat).toFixed(2);
            }
            else {
               
                // If the asset doesn't exists we push it
                this.uniqueAssetsOriginName.push(e.asset.origin_name);
                this.uniqueAssetsOriginFiat.push(e.asset.counter_value);

                this.originsChart.data.labels.push(e.asset.origin_name);
                this.originsChart.data.datasets[0].data.push(parseFloat(e.asset.counter_value ).toFixed(2));
            }

            // Locate current coin row in DATATABLE
            var indexes = this.portfolioTable.rows().eq( 0 ).filter( rowIdx => {
                return this.portfolioTable.cell( rowIdx, 5 ).data() === e.asset.id ? true : false;
            } );

            // Update DATATABLE values (Price, Balance and Counter Value)
            this.portfolioTable.cell(indexes[0], 1).data(this.counter_value).invalidate();
            this.portfolioTable.cell(indexes[0], 2).data(this.balance).invalidate();
            this.portfolioTable.cell(indexes[0], 4).data(this.price).invalidate();
        
            this.totalsChart.update();
            this.originsChart.update();

            this.updatingAsset = false;
            console.log("Updating: " + this.updatingAsset);

        });
        Echo.private('portfolios.' + this.portfolio.id)
        .listen('PortfolioLoaded', (e) => {
            // ************
            // PORTFOLIO LOADED
            // ************
            this.counterValueSymbol = this.portfolio.counter_value.toUpperCase();
        });

        console.log('Component TradeList mounted.');
    },
    methods: {
        refreshPortfolio() {
            this.loadingPortfolio = true;
            this.totalBtc = 0;
            this.totalFiat = 0;
            this.totalsChart.destroy();;
            this.originsChart.destroy();;
            this.portfolioTable.clear().draw();

            let uri = '/api/portfolio/refresh';
            axios(uri, {
                method: 'GET',
            })
            .then(response => {
                
                this.loadingPortfolio = false;
                console.log("Reloading portfolio...");
            })
            .catch(e => {
                this.errors.push(e);
                this.loadingPortfolio = false;
                console.log("Error: " + e.message);
            });
        } 
    }
}
</script>


