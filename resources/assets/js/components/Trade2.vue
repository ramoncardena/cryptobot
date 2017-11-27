<template>

        <tr role="row">
            <td>
                <div v-show="opened" class="trade-cancel icons-area">
                    <i v-show="updating" class="fa fa-cog fa-spin fa-fw"></i> 
                    <i v-show="!updating" class="fa fa-times cancel-icon"></i>
                    <i v-on:click="update(exchange, pair, price)" class="fa fa-refresh refresh-icon"></i>
                </div>
            </td>
            <td >{{profit}}</td>
            <td>{{pair}}</td>
            <td class="sorting_1  trade-status">{{ status }}</td>
            <td>{{exchange}}</td>
            <td>{{position}}</td>
            <td>{{price}}</td>
            <td>{{amount}}</td>
            <td>{{total}}</td>
            <td>{{stopLoss}}</td>
            <td>{{takeProfit}}</td>
            <td>{{last.toFixed(8)}}</td>
        </tr>

</template>

   <script>
   export default {
    name: 'trade2',
    data: () => {
        return {
            updating: false,
            profit: 0,
            last: 0
        }
    },
    props: [
    'status',
    'exchange',
    'position', 
    'pair', 
    'price', 
    'amount', 
    'total', 
    'stop-loss',
    'take-profit',
    ],
    computed: {
        opened: function() {
            if (this.status == "Opened") {
                return true;
            }
            else {
                return false;
            }
        }
    },
    mounted() {
        this.update(this.exchange, this.pair, this.price);
        console.log('Component Trade mounted.');
    },
    methods: {
        update(exchange, pair, price) {
            let percent = 0;
            this.updating = true;
            if (exchange.toLowerCase() == 'bittrex') {
                let uri = '/api/bittrexapi/getmarketsummary/' + pair;
                axios(uri, {
                    method: 'GET',
                })
                .then(response => {
                    this.marketsummary=response.data[0];  
                    this.last = this.marketsummary.Last;
                    
                    // Calculate percentual diference
                    let decreaseValue = this.last - price;
                    decreaseValue = (decreaseValue / price) * 100;
                    this.profit = decreaseValue.toFixed(2).toString() + "%";

                    this.updating = false;
                    //console.log("Last: " + this.last + " - " + (decreaseValue / price) * 100);
                })
                .catch(e => {
                    this.updating = false;
                    console.log("Error: " +  e.message);
                })
            }
        }
    }
}
</script>


