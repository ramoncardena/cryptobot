<template>
    <div class="trade-list">
        
        <table class="trade" id="rwd-table" width="100%">
            <thead>
                <tr role="row" class="trade-title text-center">
                    <th> Actions</th>
                    <th v-if="opened==true">P/L (%)</th>
                    <th>Pair</th>
                    <th>Status</th>
                    <th>Exchange</th>
                    <th>Position</th>
                    <th>Price</th>
                    <th>Last</th>
                    <th>Amount</th>
                    <th>Total</th>
                    <th>Stop-Loss</th>
                    <th>Take-Profit</th>

                </tr>
            </thead>
            <tbody>
                <trade3 v-for="trade in trades"
                            :status = "trade.status"
                            :exchange = "trade.exchange"
                            :position = "trade.position"
                            :pair = "trade.pair"
                            :price = "trade.price"
                            :amount = "trade.amount"
                            :total = "trade.total"
                            :stop-loss = "trade.stop_loss"
                            :take-profit = "trade.take_profit"
                            :key="trade.id"
                        >
                </trade3>
            </tbody>
        </table>
    </div>
</template>

   <script>
   export default {
    name: 'tradelist3',
    props: [
    'type',
    'trades'
    ],
    data: () => {
        return {
            updating: false,
            profit: 0
        }
    },
    computed: {
        opened: function() {
            if (this.type == "opened") {
                return true;
            }
            else {
                return false;
            }
        },
        waiting: function() {
            if (this.type == "waiting") {
                return true;
            }
            else {
                return false;
            }
        },
        history: function() {
            if (this.type == "history") {
                return true;
            }
            else {
                return false;
            }
        }
    },
    mounted() {
        console.log('Component TradeList mounted.');
    },
    methods: {
        update(exchange, pair, price) {

            this.updating = true;
            if (exchange.toLowerCase() == 'bittrex') {
                let uri = '/api/bittrexapi/getmarketsummary/' + pair;
                axios(uri, {
                    method: 'GET',
                })
                .then(response => {
                    this.marketsummary=response.data[0];  
                    
                    // Calculate percentual diference
                    let decreaseValue = parseFloat(this.marketsummary.Last) - parseFloat(price);
                    decreaseValue = (parseFloat(decreaseValue) / parseFloat(price) * parseFloat(100));
                    this.profit = parseFloat(decreaseValue).toFixed(2) + "%";

                    this.updating = false;
                      console.log("Last: " + last + " - " + this.profit);
            
                  
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


