<template>
    <div class="portfolio-assets">
        <table id="portfolioTable" class="unstriped stack" cellspacing="0" width="100%" role="grid">
            <thead>
                <tr role="row">
                    <th></th>
                    <th class="sorting">Coin</th>
                    <th v-if="portfolio.counter_value == 'eur'" class="sorting nowrap">Value (<i class="fa fa-eur" aria-hidden="true"></i>)</th>
                    <th v-if="portfolio.counter_value == 'usd'" class="sorting nowrap">Value (<i class="fa fa-usd" aria-hidden="true"></i>)</th>
                    <th v-if="portfolio.counter_value == 'btc'" class="sorting nowrap">Value (<i class="fa fa-btc" aria-hidden="true"></i>)</th>
                    <th class="sorting">Value (<i class="fa fa-btc" aria-hidden="true"></i>)</th>
                    <th class="sorting">Amount</th>
                    <th class="sorting">Origin</th>
                    
                </tr>
            </thead>
            <tbody>
                 <tr is="asset" v-for="item in assets" :item="item" :portfolio-counter-value="portfolio.counter_value"></tr>
            </tbody>
        </table>
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
            assets: []
        }
    },
    computed: {

    },
    mounted() {

        Echo.private('assets.' + this.portfolio.id)
        .listen('PortfolioAssetLoaded', (e) => {
            this.assets.push(e.asset);
            $(window).trigger('resize');
            console.log('New asset: ' + e.asset.symbol);
        })

        console.log('Component TradeList mounted.');
    },
    methods: {


    }
}
</script>


