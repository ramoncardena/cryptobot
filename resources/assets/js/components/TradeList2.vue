<template>
    <div class="trade-list">

    <table id="myTable2" class="display compact dataTable trade" cellspacing="0" width="100%" role="grid">
            <thead class="dataTable-header">
                <tr role="row" class="trade-title text-center">
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1"></th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">P/L (%)</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Pair</th>
                    <th class="sorting_asc" tabindex="0" rowspan="1" colspan="1" >Status</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Exchange</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Position</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Price</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Amount</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Total</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Stop-Loss</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Take-Profit</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Last</th>

                </tr>
            </thead>
            <tbody>
                <trade2 v-for="trade in trades"
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
                </trade2>
            </tbody>
        </table>
    </div>
</template>

   <script>
   export default {
    name: 'tradelist2',
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


