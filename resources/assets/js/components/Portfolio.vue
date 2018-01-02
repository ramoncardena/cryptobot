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
                    <canvas id="myChart" width="400" height="400"></canvas>
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
        var portfolioTable = $('#portfolioTable').DataTable( {
            "searching": false,
            "responsive": true,
            "paging": false,
            "info": false,
            "columnDefs": [
            { "visible": false, "targets": 5 },
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
        var ctx = $("#myChart");

        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["BTC", "ETH", "LTC", "XRP", "NEO", "NXT"],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {

            }
        });

        Echo.private('assets.' + this.portfolio.id)
        .listen('PortfolioAssetLoaded', (e) => {
            //this.assets.push(e.asset);
            this.balance = e.asset.balance;
            this.counter_value = e.asset.counter_value;
            this.price = e.asset.price;
            var coin = '<div class="asset-info nowrap"><a href="' + e.asset.info_url + '" target="_blank"><img class="asset-img" src="' + e.asset.logo_url + '" width="20"></a> <span class="show-for-medium asset-name">' + e.asset.full_name + '</span> <span class="asset-symbol">' + e.asset.symbol + '</span></div>';

            var amount = '<div class="asset-amount  nowrap">' + parseFloat(e.asset.amount).toFixed(8) + '</div>'
            var origin = '<div class="asset-origin  nowrap">' + e.asset.origin_name + '</div>';
            portfolioTable.row.add( [
                coin,
                parseFloat(this.counter_value ).toFixed(2),
                parseFloat(this.balance).toFixed(8),
                amount,
                parseFloat(this.price).toFixed(8),
                e.asset.id,
                origin
                ] ).order( [ 6, 'asc' ] ).draw();
        })
        .listen('PortfolioAssetUpdated', (e) => {
            this.balance = parseFloat(e.asset.balance).toFixed(8);
            this.price = parseFloat(e.asset.price).toFixed(8);
            this.counter_value = parseFloat(e.asset.counter_value).toFixed(2);
            
            var indexes = portfolioTable.rows().eq( 0 ).filter( function (rowIdx) {
                return portfolioTable.cell( rowIdx, 5 ).data() === e.asset.id ? true : false;
            } );

            portfolioTable.cell(indexes[0], 1).data(this.counter_value);
            portfolioTable.cell(indexes[0], 2).data(this.balance);
            portfolioTable.cell(indexes[0], 4).data(this.price);
            
        });
        Echo.private('portfolios.' + this.portfolio.id)
        .listen('PortfolioTotalsCalculated', (e) => {
            this.totalBtc = parseFloat(e.portfolio.balance).toFixed(8);
            this.totalFiat = parseFloat(e.portfolio.balance_counter_value).toFixed(2);
            this.counterValue = this.portfolio.counter_value.toUpperCase();
        });
        console.log('Component TradeList mounted.');
    },
    methods: {


    }
}
</script>


