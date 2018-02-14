<template>
    <section id="coin-card">

        <div class="card">
            <div class="card-divider">
                <button class="clear button" v-on:click="toggleCompact()"><i class="fa fa-times green remove-coin" aria-hidden="true"></i></button>
                <div class="grid-x grid-margin-x text-center">
                    <div class="small-12 cell">
                        <i class="fa fa-arrow-down red" aria-hidden="true"></i> <span class="coin-symbol">{{ coin }}</span> (Cardano)
                    </div>
                    <div class="small-4 cell">€0.25</div>
                    <div class="small-4 cell">B0.00032783</div>
                    <div class="small-4 cell">-67.856%</div>
                </div>    
     
              
            </div>
            <div v-if="compactMode == false" class="grid-x grid-passing-x align-middle">
                <div class="auto small-12 cell">
                    <div :class="'ct-chart-coin' + coin + ' ct-double-octave'"></div>
                </div>
            </div>
            <div class="card-section">
                <div class="grid-x grid-padding-x align-middle align-center">
                    <div class="small-4 cell text-center">
                        <div><i class="fa fa-pie-chart" aria-hidden="true"></i></div>
                        <div class="card-section-title">Market Cap</div>
                        <div class="card-section-value">€6.566.732.210</div>
                    </div>
                    <div class="small-4 cell text-center">
                        <div><i class="fa fa-bar-chart" aria-hidden="true"></i></div>
                        <div class="card-section-title">24h Vol.</div>
                        <div class="card-section-value">€514.987.168,00</div>
                    </div>
                    <div class="small-4 cell text-center">
                        <div><i class="fa fa-money" aria-hidden="true"></i></div>
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
    name: 'coin-card',
    data: () => {
        return {
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
       
        if (this.compact) this.compactMode = true;

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
        // this.chartistCoinChart = new Chartist.Line('.ct-chart-coin', this.chartistCoinChartData, this.chartistCoinChartOptions,this.responsiveOptions);
        this.chartistCoinChart = new Chartist.Line('.ct-chart-coin' + this.coin, this.chartistCoinChartData, this.chartistCoinChartOptions);

        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
        console.log('Component CoinCard mounted.');
    },
    methods: {
        toggleCompact: (function () {
            console.log("Toggle");
            if (this.compactMode == true) {
                this.compactMode == false;
                this.chartistCoinChart = new Chartist.Line('.ct-chart-coin' + this.coin, this.chartistCoinChartData, this.chartistCoinChartOptions);
            }
            else {
                this.compactMode == true;
            }
            
        })
    }
}
</script>
