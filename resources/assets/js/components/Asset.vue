<template>
        <tr>
            <td></td>
            <td> 
                <div class="asset-info nowrap">
                    <a :href="item.info_url" target="_blank"><img class="asset-img" :src="item.logo_url" width="20"></a> <span class="show-for-medium asset-name">{{item.full_name}}</span> <span class="asset-symbol">{{item.symbol}}</span>
                </div>
            </td>
            <td v-if="portfolioCounterValue == 'eur'"> 
                <div class="asset-value nowrap">
                    {{parseFloat(item.counter_value).toFixed(2)}} <i class="fa fa-eur" aria-hidden="true"></i> 
                </div>
            </td>
            <td v-if="portfolioCounterValue == 'usd'"> 
                <div class="asset-value  nowrap">
                    <i class="fa fa-usd" aria-hidden="true"></i> {{parseFloat(item.counter_value).toFixed(2)}} 
                </div>
            </td>
            <td v-if="portfolioCounterValue == 'btc'"> 
                <div class="asset-value  nowrap">
                    <i class="fa fa-btc" aria-hidden="true"></i> {{parseFloat(item.counter_value).toFixed(2)}} 
                </div>
            </td>
            <td> 
                <div class="asset-balance  nowrap">
                    <i class="fa fa-btc" aria-hidden="true"></i> {{parseFloat(item.balance).toFixed(8)}}
                </div>
            </td>
            <td> 
                <div class="asset-amount  nowrap">
                    {{parseFloat(item.amount).toFixed(8)}}
                </div>
            </td>
            <td> 
                <div class="asset-origin  nowrap">
                    {{item.origin_name}}
                </div>
            </td>

        </tr>
        
</template>

   <script>
   export default {
    name: 'asset',
    data: () => {
        return {
            balance: 0
        }
    },
    props: [
    'item',
    'portfolio-counter-value'
    ],
    mounted() {
        console.log('Component Asset mounted.');
    }
}
</script>


