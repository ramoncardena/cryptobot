<template>
    <section id="portfolio-widget">
        <div class="grid-x grid-padding-x">
            <div class="small-12 cell">
                <div class="grid-container fluid">
                    <div class="grid-x grid-padding-x"> 
                        <div class="shrink cell">
                            <div class="counter-widget text-left">
                                <div class="title">Total BTC </div> <div class="counter">{{ totalBtc }}</div>
                            </div>
                        </div>
                        <div class="shrink cell">
                            <div class="counter-widget text-left">
                                 <div class="title">Total {{counterValueSymbol}} </div> <div class="counter">{{ totalFiat}}</div>                  
                            </div>
                        </div>

                        <div class="auto cell text-right">
                            <button class="button hollow button-refresh" v-on:click="refreshPortfolio()">
                               <span v-show="loadingPortfolio"><i class="fa fa-refresh fa-spin fa-fw" aria-hidden="true"></i> <span class="show-for-medium"> Loading...</span></span>
                               <span v-show="!loadingPortfolio"><i class="fa fa-refresh "></i> <span class="show-for-medium">Reload</span></span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="small-12 large-6 cell">
                <div class="portfolio-assets">
                    <table id="portfolioTable" class="display unstriped" width="100%"></table>
                </div>
            </div>

            <div class="small-12 large-6 cell">
                <div class="grid-x grid-padding-x align-center-middle text-center dashboard">
                    <div class="small-12 cell charts">
                         <div v-show="showChart" class="ct-chart-totals ct-golden-section"></div>
                    </div>
                    <div class="small-12 cell charts">
                        <div v-show="showChart" class="ct-chart-origins ct-golden-section"></div>
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
            portfolioAssetCount: 0,
            portfolioCurrentAssetCount: 0,
            showChart: false,
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
            portfolioTable: {},
            loadingPortfolio: false,
            loadingAsset: false,
            updatingAsset: false,
            chartistTotalsData: {labels: [], series: []},
            chartistTotalsChart: {},
            chartistTotalsOptions: [],
            chartistOriginsData: {labels: [], series: []},
            chartistOriginsChart: {},
            chartistOriginsOptions: [],
            responsiveOptions: []
        }
    },
    computed: {

    },
    mounted() {
        this.loadingPortfolio = true;
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
        this.portfolioTable.clear();

        // Setup CHART3
     
        Echo.private('assets.' + this.portfolio.id)
        .listen('PortfolioAssetLoaded', (e) => {
            // ************
            // ASSET LOADED
            // ************
            this.loadingAsset = true;
            // console.log("Portfolio UpdateID: " + this.portfolio.update_id  + " Asset UpdateID: " + e.asset.update_id);
            if (this.portfolio.update_id == e.asset.update_id) {
                this.balance = e.asset.balance; 
                this.counter_value = e.asset.counter_value;
                this.price = e.asset.price;
                
                this.responsiveOptions = [
                  ['screen and (min-width: 200px)', {
                    horizontalBars: true,
                    seriesBarDistance: 5
                  }],
                  // Options override for media > 800px
                  ['screen and (min-width: 800px)', {
                    stackBars: false,
                    seriesBarDistance: 10
                  }],
                  // Options override for media > 1000px
                  ['screen and (min-width: 1000px)', {
                    horizontalBars: false,
                    seriesBarDistance: 15
                  }]
                ];
                this.chartistTotalsOptions = {
                    distributeSeries: true,
                };
                this.chartistOriginsOptions = {
                    distributeSeries: true,
                };
                

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
            }
            this.loadingAsset = false; 
        })
        .listen('PortfolioAssetUpdated', (e) => {
            // ************
            // ASSET UPDATED
            // ************
            this.updatingAsset = true;

            if (this.portfolio.update_id == e.asset.update_id) {
                console.log(e.asset.symbol);
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

                    this.chartistTotalsData.series[indexRepeatedAsset]= parseFloat( parseFloat( this.uniqueAssetsFiat[indexRepeatedAsset]).toFixed(2) );
                  
                }
                else {
                    // If the asset doesn't exists we push it
                    this.uniqueAssetsBtc.push(parseFloat(e.asset.balance));
                    this.uniqueAssetsFiat.push(parseFloat(e.asset.counter_value));
                    this.uniqueAssetsName.push(e.asset.symbol);

                    this.chartistTotalsData.labels.push(e.asset.symbol);
                    this.chartistTotalsData.series.push( parseFloat( parseFloat(e.asset.counter_value).toFixed(2) ));
                }

                // Store consolidated array of ORIGINS
                var indexRepeatedOrigin = this.uniqueAssetsOriginName.indexOf(e.asset.origin_name);
                console.log("Index: " + indexRepeatedOrigin);
                if ( indexRepeatedOrigin >= 0 ) {
                    // If origin is already counted we sum the new value
                    var newBalanceFiat = (parseFloat(this.uniqueAssetsOriginFiat[indexRepeatedOrigin]) + parseFloat(e.asset.counter_value));
                    this.uniqueAssetsOriginFiat[indexRepeatedOrigin] = parseFloat(newBalanceFiat);
                    this.chartistOriginsData.series[indexRepeatedOrigin]= parseFloat( parseFloat( this.uniqueAssetsFiat[indexRepeatedOrigin]).toFixed(2) );

                }
                else {
                   
                    // If the asset doesn't exists we push it
                    this.uniqueAssetsOriginName.push(e.asset.origin_name);
                    this.uniqueAssetsOriginFiat.push(e.asset.counter_value);
                    console.log("Value: " + e.asset.counter_value);
                    this.chartistOriginsData.labels.push(e.asset.origin_name);
                    this.chartistOriginsData.series.push( parseFloat( parseFloat(e.asset.counter_value).toFixed(2) ));
                }
                console.log("ORigins: " + JSON.stringify(this.chartistOriginsData));

                // Locate current coin row in DATATABLE
                var indexes = this.portfolioTable.rows().eq( 0 ).filter( rowIdx => {
                    return this.portfolioTable.cell( rowIdx, 5 ).data() === e.asset.id ? true : false;
                } );

                // Update DATATABLE values (Price, Balance and Counter Value)
                this.portfolioTable.cell(indexes[0], 1).data(this.counter_value).invalidate();
                this.portfolioTable.cell(indexes[0], 2).data(this.balance).invalidate();
                this.portfolioTable.cell(indexes[0], 4).data(this.price).invalidate();
            
                

                // this.portfolioTable.responsive.rebuild();
                // this.portfolioTable.responsive.recalc();
                this.portfolioTable.draw();


                this.portfolioCurrentAssetCount++;

                console.log("Asset Count: " + this.portfolioCurrentAssetCount);
                if (this.portfolioCurrentAssetCount == this.portfolioAssetCount && this.portfolioCurrentAssetCount!=0) {
                    console.log(JSON.stringify(this.chartistTotalsData));
                    console.log(JSON.stringify(this.chartistOriginsData));
                    this.showChart = true;
                    this.chartistTotalsChart = new Chartist.Bar('.ct-chart-totals', this.chartistTotalsData, this.chartistTotalsOptions,this.responsiveOptions);
                    this.chartistOriginsChart = new Chartist.Bar('.ct-chart-origins', this.chartistOriginsData, this.chartistOriginsOptions,this.responsiveOptions);
                }


                
            }
            this.updatingAsset = false;

        });
        Echo.private('portfolios.' + this.portfolio.id)
        .listen('PortfolioLoaded', (e) => {
            // ************
            // PORTFOLIO LOADED
            // ************
            
            console.log("Asset count: " + e.assetCount);
            this.counterValueSymbol = this.portfolio.counter_value.toUpperCase();
            this.portfolioAssetCount = e.assetCount;
            this.loadingPortfolio = false;
        });

        console.log('Component TradeList mounted.');
    },
    methods: {
        refreshPortfolio() {
            this.loadingPortfolio = true;
            this.chartistTotalsChart.detach();
            this.chartistOriginsChart.detach();
            this.totalBtc = 0;
            this.totalFiat = 0;
            this.uniqueAssetsBtc = [];
            this.uniqueAssetsFiat = [];
            this.uniqueAssetsName = [];
            this.uniqueAssetsOriginFiat = [];
            this.uniqueAssetsOriginName = [];
            this.portfolioTable.clear().draw();
            this.portfolioCurrentAssetCount = 0;
            this.portfolioAssetCount = 0;
            this.showChart = false;

            let uri = '/api/portfolio/refresh';
            axios(uri, {
                method: 'GET',
            })
            .then(response => {
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


