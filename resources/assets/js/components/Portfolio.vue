<template>

    <div class="grid-x grid-margin-x">
        <div class="small-12 medium-8 cell">
            <div class="portfolio-assets">
                <table id="portfolioTable" class="display unstriped" width="100%"></table>
            </div>
        </div>
        <div class="small-12 medium-4 cell">
            <div class="grid-x grid-margin-x">
                <div class="small-12 cell">
                    <div class="counter-widget">
                        <div class="title">Total BTC </div> <div class="counter">{{ totalBtc }}</div>
                      
                    </div>
                </div>
                <div class="small-12 cell">
                    <div class="counter-widget">
                         <div class="title">Total {{counterValue}} </div> <div class="counter">{{ totalFiat}}</div>
                       
                    </div>
                </div>
                <div class="small-12 cell">
                    <canvas id="portfolioChart" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
    
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
            balance: 0,
            counter_value: 0,
            price: 0,
            totalBtc: 0,
            totalFiat: 0,
            counterValue: ''
        }
    },
    computed: {

    },
    mounted() {
        this.totalBalance = 0;
        this.totalCounterValue = 0;

        var portfolioTable = $('#portfolioTable').DataTable( {
            "searching": false,
            "responsive": true,
            "paging": false,
            "info": false,
            "columnDefs": [
            { "visible": false, "targets": 6 }
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


        var ctx = $("#portfolioChart");

        Chart.defaults.global.legend.position="bottom";

        var portfolioChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    label: 'Coins',
                    data: [],
                    backgroundColor: [],
                    borderColor: [],
                    borderWidth: 0
                }]
            },
            options: {
            }
        });

        
        var that = this;

        Echo.private('assets.' + this.portfolio.id)
        .listen('PortfolioAssetLoaded', (e) => {
        
            this.balance = e.asset.balance; 
            this.counter_value = e.asset.counter_value;
            this.price = e.asset.price;

            var coin = '<div class="asset-info nowrap"><a href="' + e.asset.info_url + '" target="_blank"><img class="asset-img" src="' + e.asset.logo_url + '" width="20"></a> <span class="show-for-medium asset-name">' + e.asset.full_name + '</span> <span class="asset-symbol">' + e.asset.symbol + '</span></div>';

            var amount = '<div class="asset-amount  nowrap">' + parseFloat(e.asset.amount).toFixed(8) + '</div>'
            var origin = '<div class="asset-origin  nowrap">' + e.asset.origin_name + '</div>';

            // Add row to the table with the new asset
            portfolioTable.row.add( [
                coin,
                parseFloat(this.counter_value ).toFixed(2),
                parseFloat(this.balance).toFixed(8),
                amount,
                parseFloat(this.price).toFixed(8),
                e.asset.id,
                origin
                ] ).order( [ 6, 'asc' ] ).invalidate().draw();
            
            
        })
        .listen('PortfolioAssetUpdated', (e) => {
      
            this.totalBtc = (parseFloat(this.totalBtc) + parseFloat(e.asset.balance)).toFixed(8);
            this.totalFiat = (parseFloat(this.totalFiat) + parseFloat(e.asset.counter_value)).toFixed(2);


            this.balance = parseFloat(e.asset.balance).toFixed(8);
            this.price = parseFloat(e.asset.price).toFixed(8);
            this.counter_value = parseFloat(e.asset.counter_value).toFixed(2);
            
            var indexes = portfolioTable.rows().eq( 0 ).filter( function (rowIdx) {
                return portfolioTable.cell( rowIdx, 5 ).data() === e.asset.id ? true : false;
            } );
            // console.log("Indexes (" + e.asset.id +"): " + indexes[0]);

            portfolioTable.cell(indexes[0], 1).data(this.counter_value).invalidate();
            portfolioTable.cell(indexes[0], 2).data(this.balance).invalidate();
            portfolioTable.cell(indexes[0], 4).data(this.price).invalidate();

            portfolioChart.data.labels.push(e.asset.symbol);
            portfolioChart.data.datasets.forEach((dataset) => {
                dataset.data.push(parseFloat(this.counter_value ).toFixed(2));
            });

            var randomColorPlugin = {
                // We affect the `beforeUpdate` event
                beforeUpdate: function(chart) {
                    var backgroundColor = [];
                    var borderColor = [];
                    
                    // For every data we have ...
                    for (var i = 0; i < chart.config.data.datasets[0].data.length; i++) {
                    
                        // We generate a random color
                       //
                       var color = "rgba( 214," + + Math.floor(Math.random() * 255) + "," + Math.floor(Math.random() * 255) + ",";

                        // We push this new color to both background and border color arrays
                        //var color = that.srtingToColor(e.asset.symbol);
                        console.log("Color: " + color);
                        backgroundColor.push(color + "0.5)");
                        borderColor.push(color + "0.5)");
                    }
                    
                    // We update the chart bars color properties
                    chart.config.data.datasets[0].backgroundColor = backgroundColor;
                    chart.config.data.datasets[0].borderColor = borderColor;
                }
            };
            // We now register the plugin to the chart's plugin service to activate it
            Chart.pluginService.register(randomColorPlugin);
            portfolioChart.update();
            
        });
        Echo.private('portfolios.' + this.portfolio.id)
        .listen('PortfolioLoaded', (e) => {
            this.counterValue = this.portfolio.counter_value.toUpperCase();
        });

        console.log('Component TradeList mounted.');
    },
    methods: {
       srtingToColor(str, prc) {

            // Check for optional lightness/darkness
            var prc = typeof prc === 'number' ? prc : -10;

            // Generate a Hash for the String
            var hash = function(word) {
                var h = 0;
                for (var i = 0; i < word.length; i++) {
                    h = word.charCodeAt(i) + ((h << 5) - h);
                }
                return h;
            };

            // Change the darkness or lightness
            var shade = function(color, prc) {
                var num = parseInt(color, 16),
                    amt = Math.round(2.55 * prc),
                    R = (num >> 16) + amt,
                    G = (num >> 8 & 0x00FF) + amt,
                    B = (num & 0x0000FF) + amt;
                return (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
                    (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
                    (B < 255 ? B < 1 ? 0 : B : 255))
                    .toString(16)
                    .slice(1);
            };

            // Convert init to an RGBA
            var int_to_rgba = function(i) {
                var color = ((i >> 24) & 0xFF).toString(16) +
                    ((i >> 16) & 0xFF).toString(16) +
                    ((i >> 8) & 0xFF).toString(16) +
                    (i & 0xFF).toString(16);
                return color;
            };

            return shade(int_to_rgba(hash(str)), prc);
        }
    }
}
</script>


