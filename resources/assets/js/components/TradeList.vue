<template>
    <div class="trade-list">
        
        <table :id="tableName" class="display compact dataTable trade tradesTable" cellspacing="0" width="100%" role="grid">

            <thead class="dataTable-header">
                <tr role="row" class="trade-title text-center">

                    <th></th>
                    <th v-if="history==false" class="sorting" tabindex="0" rowspan="1" colspan="1"></th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">P/L (%)</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Pair</th>
                    <th class="sorting_asc" tabindex="0" rowspan="1" colspan="1" >Status</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Exchange</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Position</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Last Price</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Open Price</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Close Price</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Amount</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Total</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Stop-Loss</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Take-Profit</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Condition</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Cond. Price</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Date</th>

                </tr>
            </thead>
            <tbody>
                <trade v-for="trade in trades"
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
                            :timestamp="trade.created_at"
                            :key="trade.id"
                        >
                </trade>

                <!-- MODAL: Edit Trade -->
                <div v-for="trade in trades" class="reveal trade-modal" :id="'editTrade' + trade.id" data-reveal>
                    <div class="grid-container fluid">
                        <form method="POST" :action="'/trades/' + trade.id">
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="_token" :value="csrf">

                            <div class="grid-x grid-padding-x">
                                <!-- Header -->
                                <div class="small-8 cell form-container">
                                    <p class="h1">Edit Trade</p>
                                    <p class="lead"><b>{{ trade.pair }} at {{ trade.exchange.toUpperCase() }}</b></p>
                                </div>
                                <div class="small-4 cell form-container close-trade-info text-right">
                                    <div v-on:click="loadinfo(trade.exchange, trade.pair)"> (refresh) </div>  
                                    <div v-model="last"> <b>Last:</b> {{ last.toFixed(8) }} </div>  
                                    <div v-model="bid"> <b>Bid:</b> {{ bid.toFixed(8) }}</div>
                                    <div v-model="ask"> <b>Ask:</b> {{ ask.toFixed(8) }}</div>
                                    <div v-model="low"> <b>Low:</b> {{ low.toFixed(8) }}</div>
                                    <div v-model="high"> <b>High:</b> {{ high.toFixed(8) }}</div>
                                </div>
                                <div class="small-12 cell form-container text-right">
                                   <small> Current Stop-Loss: {{ trade.stop_loss }} <i class="fa fa-clipboard" aria-hidden="true" v-on:click="stoploss=trade.stop_loss"></i> </small>
                                </div>
                                <div class="small-12 cell form-container">
                                    <div v-if="validationErrors.newStopLoss">
                                       <span class="validation-error" v-for="error in validationErrors.newStopLoss"> {{ error }} </span>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-label">
                                            New Stop-Loss
                                        </span>
                                        <input name="newStopLoss" v-model="stoploss" class="input-group-field price number" type="text">
                                    </div>
                                </div>
                                <div class="small-12 cell form-container  text-right">
                                   <small> Current Take-Profit: {{ trade.take_profit }} <i class="fa fa-clipboard" aria-hidden="true" v-on:click="takeprofit=trade.take_profit"></i> </small>
                                </div>
                                <div class="small-12 cell form-container">
                                    <div v-if="validationErrors.newTakeProfit">
                                       <span class="validation-error" v-for="error in validationErrors.newTakeProfit"> {{ error }} </span>
                                    </div>
                                     <div class="input-group">
                                        <span class="input-group-label">
                                            New Take-Profit
                                        </span>
                                        <input name="newTakeProfit" v-model="takeprofit" class="input-group-field price number" type="text" >
                                    </div>
                                </div>
                                <div class="small-12 cell form-container">
                                   <!--  <button class="hollow button" v-on:click="editTrade(trade.id, stoploss, takeprofit)">
                                       Save Trade
                                    </button> -->
                                    <button class="hollow button" type="submit">
                                       Save Trade
                                    </button>
                                </div>
                                
                            </div>

                        </form>
                    </div>
                    <button class="close-button" data-close aria-label="Close modal" type="button" v-on:click="last=0; bid=0; ask=0; high=0; low=0; stoploss=0; takeprofit=0;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- MODAL: Close Trade -->
                <div v-for="trade in trades" class="reveal trade-modal" :id="'closeTrade' + trade.id" data-reveal>
                    <div class="grid-container fluid">
                        <form method="POST" :action="'/trades/' + trade.id">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" :value="csrf">
                            <div class="grid-x grid-padding-x">
                                <!-- Header -->
                                <div class="small-8 cell form-container">
                                    <p class="h1">Closing Trade</p>
                                    <p class="lead"><b>{{ trade.pair }} at {{ trade.exchange.toUpperCase() }}</b></p>
                                </div>
                                <div class="small-4 cell form-container close-trade-info text-right">
                                    <div v-on:click="loadinfo(trade.exchange, trade.pair)"> (refresh) </div>  
                                    <div v-model="last"> <b>Last:</b> {{ last.toFixed(8) }} </div>  
                                    <div v-model="bid"> <b>Bid:</b> {{ bid.toFixed(8) }}</div>
                                    <div v-model="ask"> <b>Ask:</b> {{ ask.toFixed(8) }}</div>
                                    <div v-model="low"> <b>Low:</b> {{ low.toFixed(8) }}</div>
                                    <div v-model="high"> <b>High:</b> {{ high.toFixed(8) }}</div>
                                </div>
                                <div class="small-12 cell form-container">
                                    <div v-if="validationErrors.closingprice">
                                       <span class="validation-error" v-for="error in validationErrors.closingprice"> {{ error }} </span>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-label">
                                            <i v-show="loadingprice" class="fa fa-cog fa-spin fa-fw"></i> 
                                            <i v-show="!loadingprice && closingprice!=0" class="fa fa-refresh fa-fw" v-on:click="updateprice(trade.exchange, trade.pair, priceselected)"></i>
                                            Price
                                        </span>
                                        <input name="closingprice" v-model="closingpriceC" class="input-group-field price number" type="text">
                                        <select v-model="priceselected" id="close-price-select"  v-on:change="updateprice(trade.exchange, trade.pair, priceselected)">
                                            <option disabled value="">Autofill</option>
                                            <option value="last">Last</option>
                                            <option value="bid">Bid</option>
                                            <option value="ask">Ask</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="small-12 cell form-container">
                                    <!-- <button class="hollow button" href="#" v-on:click="closeTrade(trade.id)">
                                       Close Trade
                                    </button> -->
                                    <button class="hollow button" type="submit">
                                       Close Trade
                                    </button>
                                    <p class="text-right">Aprox. profit: {{ calculateProfit(trade.price) }}%</p>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                    <button class="close-button" data-close aria-label="Close modal" type="button" v-on:click="last=0; bid=0; ask=0; high=0; low=0; closingprice=0.00000000; priceselected='';">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- MODAL: Close Waiting Trade -->
                <div v-for="trade in trades" class="reveal trade-modal" :id="'closeWaitingTrade' + trade.id" data-reveal>
                    <div class="grid-container fluid">
                        <form method="POST" :action="'/trades/' + trade.id">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" :value="csrf">
                            <div class="grid-x grid-padding-x">
                                <!-- Header -->
                                <div class="small-8 cell form-container">
                                    <p class="h1">Closing Trade</p>
                                    <p class="lead"><b>{{ trade.pair }} at {{ trade.exchange.toUpperCase() }}</b></p>
                                </div>
                                <div class="small-4 cell form-container close-trade-info text-right">
                                    <div v-on:click="loadinfo(trade.exchange, trade.pair)"> (refresh) </div>  
                                    <div v-model="last"> <b>Last:</b> {{ last.toFixed(8) }} </div>  
                                    <div v-model="bid"> <b>Bid:</b> {{ bid.toFixed(8) }}</div>
                                    <div v-model="ask"> <b>Ask:</b> {{ ask.toFixed(8) }}</div>
                                    <div v-model="low"> <b>Low:</b> {{ low.toFixed(8) }}</div>
                                    <div v-model="high"> <b>High:</b> {{ high.toFixed(8) }}</div>
                                </div>
                                <div class="small-12 cell form-container">
                                    <p>You are going to close a waiting trade, if you proceed the trade will be canceled and no order will be launched. Are you sure?</p>
                                </div>
                                <div class="small-12 cell form-container">
                                    
                                    <button class="hollow button"  type="submit">
                                       Yes, cancel
                                    </button>

                                </div>
                                
                            </div>
                        </form>
                    </div>
                    <button class="close-button" data-close aria-label="Close modal" type="button" v-on:click="last=0; bid=0; ask=0; high=0; low=0; closingprice=0.00000000; priceselected='';">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- MODAL: Keep Trade -->
                <div v-for="trade in trades" class="reveal trade-modal" :id="'keepTrade' + trade.id" data-reveal>
                    <div class="grid-container fluid">
                        <form method="POST" :action="'/trades/' + trade.id">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" :value="csrf">
                            <div class="grid-x grid-padding-x">
                                <!-- Header -->
                                <div class="small-8 cell form-container">
                                    <p class="h1">Keep Trade</p>
                                    <p class="lead"><b>{{ trade.pair }} at {{ trade.exchange.toUpperCase() }}</b></p>
                                </div>
                                <div class="small-4 cell form-container close-trade-info text-right">
                                    <div v-on:click="loadinfo(trade.exchange, trade.pair)"> (refresh) </div>  
                                    <div v-model="last"> <b>Last:</b> {{ last.toFixed(8) }} </div>  
                                    <div v-model="bid"> <b>Bid:</b> {{ bid.toFixed(8) }}</div>
                                    <div v-model="ask"> <b>Ask:</b> {{ ask.toFixed(8) }}</div>
                                    <div v-model="low"> <b>Low:</b> {{ low.toFixed(8) }}</div>
                                    <div v-model="high"> <b>High:</b> {{ high.toFixed(8) }}</div>
                                </div>
                                <div class="small-12 cell form-container">
                                    <p>You are going to keep a trade, if you proceed the trade will be closed and no order will be launched, so you'll keep {{ trade.amount }} {{ trade.pair.substr(trade.pair.indexOf("-") + 1) }}. Are you sure?</p>
                                </div>
                                <div class="small-12 cell form-container">
                                    
                                    <button class="hollow button"  type="submit">
                                       Yes, keep
                                    </button>

                                </div>
                                
                            </div>
                        </form>
                    </div>
                    <button class="close-button" data-close aria-label="Close modal" type="button" v-on:click="last=0; bid=0; ask=0; high=0; low=0; closingprice=0.00000000; priceselected='';">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


            </tbody>
        </table>
    </div>
</template>

   <script>
   export default {
    name: 'tradelist',
    props: [
    'validation-errors',
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
            high: 0.00000000,
            low: 0.00000000,
            stoploss: 0,
            takeprofit: 0,
            csrf: ""
        }
    },
    computed: {
        closingpriceC: function() {
            return this.closingprice.toFixed(8);
        },
        history: function() {
            if (this.type == "history") {
                return true;
            }
            else {
                return false;
            }
        },
        tableName: function(){
            if (this.type == "history") {
                return 'historyTradesTable';
            }
            else {
                return 'activeTradesTable';
            }
        }
    },
    mounted() {
        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('Component TradeList mounted.');
    },
    methods: {
        loadinfo(exchange, pair) {
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
                    this.high = parseFloat(this.marketsummary.High);
                    this.low = parseFloat(this.marketsummary.Low);
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
        },
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
                    this.high = parseFloat(this.marketsummary.High);
                    this.low = parseFloat(this.marketsummary.Low);

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
        },
        editTrade(id, stopLoss, takeProfit) {
            let uri = 'newStopLoss=' + stopLoss + "&newTakeProfit=" + takeProfit;
            axios.patch('/trades/' + id + '?' + uri)
            .then(response => {
                console.log("Trade edited!");
                console.log(response.data);
            })
            .catch(error => {
                console.log(error.response.data); 
            });
        },
        closeTrade(id) {
            let uri = 'closingprice=' + this.closingprice + '&keep=false';
            axios.delete('/trades/' + id + '?' + uri)
            .then(response => {
                console.log("Trade closed!");
            })
            .catch(error => {
                console.log(error.response.data); 
            });
        },
        closeWaitingTrade(id) {
            axios.delete('/trades/' + id )
            .then(response => {
                console.log("Trade cancelled!");
            })
            .catch(error => {
                console.log(error.response.data); 
            });
        },
        keepTrade(id) {
            let uri = 'keep=true';
            axios.delete('/trades/' + id + '?' + uri)
            .then(response => {
                console.log("Trade kept!");
            })
            .catch(error => {
                console.log(error.response.data); 
            });
        },
        calculateProfit(price) {
            if (this.closingprice!=0) {
                return ( ( ( parseFloat(this.closingprice)-parseFloat(price) ) / parseFloat(this.closingprice)) * 100 ).toFixed(2);
            }
            else {
                return 0;
            }
        }

    }
}
</script>


