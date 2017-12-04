<template>
    <div class="trade-list">

        <table class="display compact dataTable trade myTable" cellspacing="0" width="100%" role="grid">

            <thead class="dataTable-header">
                <tr role="row" class="trade-title text-center mytr">
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1"> </th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1"></th>
                    <th v-if="opened==true || history==true" class="sorting" tabindex="0" rowspan="1" colspan="1">P/L (%)</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Pair</th>
                    <th class="sorting_asc" tabindex="0" rowspan="1" colspan="1" >Status</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Exchange</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Position</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Price</th>
                    <th v-if="history == false" class="sorting" tabindex="0" rowspan="1" colspan="1">Last</th>
                    <th v-if="history == true" class="sorting" tabindex="0" rowspan="1" colspan="1">Final Price</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Amount</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Total</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Stop-Loss</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Take-Profit</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Condition</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Cond. Price</th>

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
                            :condition =  "trade.condition"
                            :condition-price = "trade.condition_price"
                            :final-profit = "trade.profit"
                            :closing-price ="trade.closing_price"
                            :type = "type"
                            :id = "trade.id"
                            :key="trade.id"
                        >
                </trade2>

                <!--MODAL: Close Trade -->
                <div v-for="trade in trades" class="reveal trade-modal" :id="'closeTrade' + trade.id" data-reveal>
                    <h1>Closing Trade</h1>
                    <p class="lead">{{ trade.pair }} at {{ trade.exchange.toUpperCase() }}</p>
                    <div class="input-group">
                        <span class="input-group-label">
                            <i v-show="loadingprice" class="fa fa-cog fa-spin fa-fw"></i> 
                            <i v-show="!loadingprice && closingprice!=0" class="fa fa-refresh fa-fw" v-on:click="updateprice(trade.exchange, trade.pair, priceselected)"></i>
                            Price
                        </span>
                        <input v-model="closingprice" class="input-group-field price" type="number">
                        <select v-model="priceselected" id="close-price-select"  v-on:change="updateprice(trade.exchange, trade.pair, priceselected)">
                            <option disabled value="">Autofill</option>
                            <option value="last">Last</option>
                            <option value="bid">Bid</option>
                            <option value="ask">Ask</option>
                        </select>
                          
                    </div>
                    <button class="hollow button" href="#">
                           Close Trade
                        </button>
                    <button class="close-button" data-close aria-label="Close modal" type="button" v-on:click="closingprice=0.00000000; priceselected='';">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

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
            loadingprice: false,
            updating: false,
            profit: 0,
            marketsummary: [],
            exchange: "",
            priceselected: "",
            closingprice: 0.0000000,
            last: 0.00000000,
            bid: 0.00000000,
            ask: 0.00000000,
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
        updateprice(exchange, pair, pricetype) {
            this.loadingprice = true;
            
            if (exchange.toLowerCase() == 'bittrex') {
                let uri = '/api/bittrexapi/getmarketsummary/' + pair;
                axios(uri, {
                    method: 'GET',
                })
                .then(response => {
                    this.marketsummary=response.data[0];  
                    this.last = parseFloat(this.marketsummary.Last);
                    this.bid = parseFloat(this.marketsummary.Bid);
                    this.ask = parseFloat(this.marketsummary.Ask);

                    if (pricetype.toLowerCase() == "last") {
                        this.closingprice =  parseFloat(this.last);
                    }
                    else if (pricetype.toLowerCase() == "bid") {
                        this.closingprice =  parseFloat(this.bid);
                    }
                    else if (pricetype.toLowerCase() == "ask") {
                        this.closingprice =  parseFloat(this.ask);
                    }
                    this.loadingprice = false;
                })
                .catch(e => {
                    this.errors.push(e);
                    this.loadingprice = false;
                    console.log("Error: " + e.message);
                })
            }
            else {
                this.loadingprice = false;
            }
        }
    }
}
</script>


