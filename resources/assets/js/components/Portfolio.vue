<template>
    <div class="portfolio-assets">
        <table id="portfolioTable" class="unstriped" cellspacing="0" width="100%" role="grid">
            <thead>
                <tr role="row">
                    <th></th>
                    <th class="sorting">Coin</th>
                    <th v-if="portfolio.counter_value == 'eur'" class="sorting nowrap">Value (<i class="fa fa-eur" aria-hidden="true"></i>)</th>
                    <th v-if="portfolio.counter_value == 'usd'" class="sorting nowrap">Value (<i class="fa fa-usd" aria-hidden="true"></i>)</th>
                    <th v-if="portfolio.counter_value == 'btc'" class="sorting nowrap">Value (<i class="fa fa-btc" aria-hidden="true"></i>)</th>
                    <th class="sorting">Value (<i class="fa fa-btc" aria-hidden="true"></i>)</th>
                    <th class="sorting">Amount</th>
                    
                </tr>
            </thead>
            <tbody>
                <tr v-for="asset in assets">
                    <td></td>
                    <td> 
                        <div class="asset-info nowrap">
                            <a :href="asset.info_url" target="_blank"><img class="asset-img" :src="asset.logo_url" width="20"></a> <span class="show-for-medium asset-name">{{asset.full_name}}</span> <span class="asset-symbol">{{asset.symbol}}</span>
                        </div>
                    </td>
                    <td v-if="portfolio.counter_value == 'eur'"> 
                        <div class="asset-value nowrap">
                            {{asset.counter_value.toFixed(2)}} <i class="fa fa-eur" aria-hidden="true"></i> 
                        </div>
                    </td>
                    <td v-if="portfolio.counter_value == 'usd'"> 
                        <div class="asset-value  nowrap">
                            <i class="fa fa-usd" aria-hidden="true"></i> {{asset.counter_value.toFixed(2)}} 
                        </div>
                    </td>
                    <td v-if="portfolio.counter_value == 'btc'"> 
                        <div class="asset-value  nowrap">
                            <i class="fa fa-btc" aria-hidden="true"></i> {{asset.counter_value.toFixed(2)}} 
                        </div>
                    </td>
                    <td> 
                        <div class="asset-balance  nowrap">
                            <i class="fa fa-btc" aria-hidden="true"></i> {{asset.balance.toFixed(8)}}
                        </div>
                    </td>
                    <td> 
                        <div class="asset-amount  nowrap">
                            {{asset.amount}}
                        </div>
                    </td>
                    
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: 'portfolio',
    props: [
    'validation-errors',
    'portfolio',
    'origins',
    'assets'
    ],
    data: () => {
        return {
            csrf: ""
        }
    },
    computed: {

    },
    mounted() {
        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('Component TradeList mounted.');
    },
    methods: {


    }
}
</script>


