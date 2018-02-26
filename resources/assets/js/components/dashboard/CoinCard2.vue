<template>
    <section id="coin-card">

        <div class="card" v-on:click="compactMode = !compactMode">
            
            <div class="card-divider">

                <div class="grid-container fluid">
                    <div class="grid-x grid-padding-x align-middle">
                        <div class="small-6 cell">
                            <div class="nowrap coin-symbol"><img :src="coinObj.imageurl" alt="" width="24"> {{ coinObj.symbol }} <i class="fa fa-arrow-up green" aria-hidden="true"></i></div>
                        </div>
                        <div class="small-6 cell">
                            <div class="coin-name"> {{ coinObj.coinname }}</div>
                            <span class="coin-card-close" aria-hidden="true">&times;</span>
                        </div>
                        <!-- <span class="coin-card-close" aria-hidden="true">&times;</span> -->
                    </div>    
                </div>
              
            </div>
            <div class="grid-x grid-passing-x align-middle">
                <div class="small-8 cell">
                    <div :class="'ct-chart-coin' + coinObj.symbol + ' ct-major-eleventh'"></div>
                </div>

                <div class="small-4 cell text-center">
                            <span class="coin-data-change profit-box"> +67.856% </span> 
                            <div class="coin-data-fiat">€8235.47</div>
                            <div class="coin-data-btc">B0.00032783</div>
                        </div>
                </div>
            <div class="card-section">
                <div class="grid-x grid-padding-x align-middle align-center">
                    <div class="small-4 cell text-center">
                        <!-- <div><i class="fa fa-pie-chart" aria-hidden="true"></i></div> -->
                        <div class="card-section-title">Market Cap</div>
                        <div class="card-section-value">€6.566.732.210</div>
                    </div>
                    <div class="small-4 cell text-center">
                        <!-- <div><i class="fa fa-bar-chart" aria-hidden="true"></i></div> -->
                        <div class="card-section-title">24h Vol.</div>
                        <div class="card-section-value">€514.987.168,00</div>
                    </div>
                    <div class="small-4 cell text-center">
                        <!-- <div><i class="fa fa-money" aria-hidden="true"></i></div> -->
                        <div class="card-section-title">Supply</div>
                        <div class="card-section-value">€25.927.070.538</div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</template>

<script>
export default {
    name: 'coin-card2',
    data: () => {
        return {
            coinObj: {},
            chartClass: "",
            chartistCoinChart: {},
            responsiveOptions: [],
            chartistCoinChartData: {labels: [], series: []},
            chartistCoinChartOptions: {},
            compactMode: false,
            updating: false,
            csrf: ""
        }
    },
    props: [
    'coin',
    'compact'
    ],
    computed: {

    },
    watch: {

    },
    mounted() {
        this.coinObj = JSON.parse(this.coin);

        if (this.compact) this.compactMode = true;

        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
        console.log('Component CoinCard mounted.');
    },
    updated(){
        this.chartistCoinChartOptions = {
            low: 0,
            showArea: true,
            showLine: true,
            showPoint: false,
            fullWidth: true,
            chartPadding: { 
                top: 5,
                bottom: -35,
                right: 5,
                left: -35 
            },
            axisX: {
                showLabel: false,
                showGrid: false
            },
            axisY: {
                showLabel: false,
                showGrid: false
            }
        };
        this.chartistCoinChartData.series = [
            [5, 9, 7, 8, 15, 11, 9, 14]
            ]
        // this.chartistCoinChart = new Chartist.Line('.ct-chart-coin', this.chartistCoinChartData, this.chartistCoinChartOptions,this.responsiveOptions);ç
   

        this.chartClass = '.ct-chart-coin' + this.coinObj.symbol;
        console.log(this.chartClass);

        this.chartistCoinChart = new Chartist.Line(this.chartClass, this.chartistCoinChartData, this.chartistCoinChartOptions);
    },
    methods: {
        toggleCompact: (function () {
            console.log("Toggle");
            if (this.compactMode == true) {
                this.compactMode == false;
                this.chartistCoinChart = new Chartist.Line(this.chartClass, this.chartistCoinChartData, this.chartistCoinChartOptions);
            }
            else {
                this.compactMode == true;
            }
            
        })
    }
}
</script>
