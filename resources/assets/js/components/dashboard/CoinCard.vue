<template>
    <section id="coin-card">

        <div class="card">
            <div class="card-divider">
                <div class="grid-x grid-padding-x align-middle align-center">
                    <div class="small-6 cell">
                        <span class="coin-symbol">{{ coin }}</span> (Cardano)
                    </div>
                    <div class="small-3 cell">
                        €0.25
                    </div>
                    <div class="small-3 cell">
                        -67.856%
                    </div>
                </div>
            </div>
            <div class="ct-chart-coin ct-golden-section"></div>
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
            updating: false,
            csrf: ""
        }
    },
    props: [
    'coin',
    ],
    computed: {

    },
    watch: {

    },
    mounted() {
        // Set Chart options
        // this.responsiveOptions = [];
        // this.chartistCoinChartData.labels = [1, 2, 3, 4, 5, 6, 7, 8];
        // this.chartistCoinChartData.series = [5, 9, 7, 8, 5, 3, 5, 4];
        // this.chartistCoinChartOptions = {
        //     low: 0, 
        //     showArea: true
        // };
        // this.chartistCoinChart = new Chartist.Line('.ct-chart-coin', this.chartistCoinChartData, this.chartistCoinChartOptions,this.responsiveOptions);
        new Chartist.Line('.ct-chart-coin', {
            labels: [1, 2, 3, 4, 5, 6, 7, 8],
            series: [
            [5, 9, 7, 8, 5, 3, 5, 4]
            ]
        }, {
            low: 0,
            showArea: true
        });

        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('Component CoinCard mounted.');
    },
    methods: {
        saveName: (function () {

        })
    }
}
</script>
