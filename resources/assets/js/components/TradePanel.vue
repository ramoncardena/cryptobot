<template>

    <div class="grid-x grid-padding-x tradepanel">
        <!-- Trade form -->
        <div class="cell large-7 small-order-2 large-order-1">

            <form method="POST" action="/trades">
            <input type="hidden" name="_token" :value="csrf">

                <div class="grid-x grid-padding-x align-middle">
                    
                    <!-- Exchange -->
                    <div class="cell large-6">
                        <div v-if="validationErrors.exchange">
                           <span class="validation-error" v-for="error in validationErrors.exchange"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Exchange</span>
                            <select name="exchange" v-model="exchange" class="input-group-field" v-on:change="getpairs(exchange)">
                                <option disabled value="">Select...</option>
                                <option value="bittrex" selected="true"> Bittrex </option>
                            </select>                     
                        </div>
                        
                    </div>

                    <!-- Pair -->
                    <div class="cell large-6">
                        <div v-if="validationErrors.pair">
                           <span class="validation-error" v-for="error in validationErrors.pair"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label"><i v-show="loadingpairs" class="fa fa-cog fa-spin fa-fw"></i> Pair</span>
                            <input name="pair" id="pairs" v-model="pairselected" class="input-group-field number" type="text">
                            <!-- <select name="pair" v-model="pairselected" class="input-group-field" v-on:change="getmarketsummary(exchange, pairselected)">
                                <option disabled value="">Select...</option>
                                <option v-for="pair in bittrexpairs" :value="pair"> {{ pair }}</option>
                            </select> -->
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="small-12 cell">
                        <div v-if="validationErrors.price">
                           <span class="validation-error" v-for="error in validationErrors.price"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">
                                <i v-show="loadingprice" class="fa fa-cog fa-spin fa-fw"></i>
                                <i v-if="!loadingprice && price!=0" class="fa fa-refresh fa-fw" v-on:click="updateprice(exchange, pairselected, priceselected)"></i>
                                 Price
                            </span>

                            <input name="price" v-model="price" class="input-group-field price number" type="text">
                             <select v-model="priceselected" id="price-select"  v-on:change="updateprice(exchange, pairselected, priceselected)">
                                <option disabled value="">Autofill</option>
                                <option value="last">Last</option>
                                <option value="bid">Bid</option>
                                <option value="ask">Ask</option>
                            </select>
                              
                        </div>
                        
                    </div>

                    <!-- Available balance -->
                    <div v-if="availableBalance!='0'" class="large-12 cell align-self-top">
                        <div class="float-right"><small v-model="availableBalance"> Available: {{ availableBalance }} </small></div>
                    </div>

                    <!-- Amount -->
                    <div class="large-6 cell">
                        <div v-if="validationErrors.amount">
                           <span class="validation-error" v-for="error in validationErrors.amount"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Amount</span>
                            <input name="amount" v-model="amount" class="input-group-field number" type="text">
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="large-6 cell">
                        <div v-if="validationErrors.total">
                           <span class="validation-error" v-for="error in validationErrors.total"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Total</span>
                            <input name="total" v-model="total" class="input-group-field number" type="text">
                        </div>
                    </div>
                    
                    <!-- Condition -->
                    <div class="medium-2 cell">
                        <div class="switch small">
                            <input name="conditionalSwitch" v-model="conditionalSwitch" class="switch-input" id="conditionalSwitch" type="checkbox">
                            <label class="switch-paddle" for="conditionalSwitch">
                                <span class="switch-active" aria-hidden="true">On</span>
                                <span class="switch-inactive" aria-hidden="true">Off</span>
                            </label>
                        </div>
                    </div>
                    <div class="medium-10 cell">
                        <div v-if="validationErrors.condition_price">
                           <span class="validation-error" v-for="error in validationErrors.condition_price"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <select name="condition" v-model="conditionselected" id="condition-select">
                                <option disabled value="now">Condition</option>
                                <option value="greater">When price >= </option>
                                <option value="less">When price <= </option>
                            </select>
                            <input name="condition_price" v-model="conditionprice" class="input-group-field number" type="text">
                        </div>
                    </div>

                    <!-- Stop Loss -->
                    <div class="medium-2 cell">
                        <div class="switch small">
                            <input name="slSwitch" v-model="slSwitch" class="switch-input" id="stopLossSwitch" type="checkbox" >
                            <label class="switch-paddle" for="stopLossSwitch">
                                <span class="switch-active" aria-hidden="true">On</span>
                                <span class="switch-inactive" aria-hidden="true">Off</span>
                            </label>
                        </div>
                    </div>
                    <div class="medium-7 cell">
                        <div v-if="validationErrors.stop_loss">
                           <span class="validation-error" v-for="error in validationErrors.stop_loss"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Stop-Loss</span>
                            <input name="stop_loss" v-model="stoploss" class="input-group-field number" type="text">
                        </div>
                    </div>
                    <div class="medium-3 cell">
                        <div class="input-group">
                            <span class="input-group-label">(-)%</span>
                            <input name="slpercent" v-model="slpercent" class="input-group-field number" type="text">
                        </div>
                    </div>

                    <!-- Take Profit -->
                    <div class="medium-2 cell">
                        <div class="switch small">
                            <input  name="tpSwitch" v-model="tpSwitch" class="switch-input" id="takeProfitSwitch" type="checkbox">
                            <label class="switch-paddle" for="takeProfitSwitch">
                                <span class="switch-active" aria-hidden="true">On</span>
                                <span class="switch-inactive" aria-hidden="true">Off</span>
                            </label>
                        </div>
                    </div>
                    <div class="medium-7 cell">
                        <div v-if="validationErrors.take_profit">
                           <span class="validation-error" v-for="error in validationErrors.take_profit"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Take-Profit</span>
                            <input name="take_profit" v-model="takeprofit"class="input-group-field number" type="text">
                        </div>
                    </div>

                    <!-- Take Profit % -->
                    <div class="medium-3 cell">
                        <div class="input-group">
                            <span class="input-group-label">(+)%</span>
                            <input name="tppercent" v-model="tppercent" class="input-group-field number" type="text">
                        </div>
                    </div>

                    <!-- Open Position Button -->
                    <div class="medium-12 cell">
                        <button class="hollow button" type="submit">
                            Open Trade
                        </button>
                    </div>

                </div>
            </form>
        </div>
      

        <!-- Information panel -->
        <div class="cell large-5 small-order-1 large-order-2">
            <div v-show="marketLoaded == false" class="title-image text-center">
                <img src="/storage/cryptobot-logo-300px.png" alt="">
            </div>
            <div v-show="marketLoaded" class="grid-x grid-margin-x">
                
                <!-- Blank up left corner -->
                <div class="cell small-2 text-center">
                    
                </div>
               
                <!-- Coin Info -->
                <div class="cell small-8 text-center">
                    <img id="cryptologo" v-show="coinlogo != ''" v-model="coinlogo" :src="coinlogo" :alt="coinname.short">
                    <p v-show="coinname != ''" v-model="coinname" class="h3"> {{ coinname.long }} <small> {{ coinname.short }} </small></p>
                </div>
              
                <!-- Refresh -->
                <div class="cell small-2 text-center">
                    <button class="clear button" v-on:click="refreshInfopanel(exchange, pairselected)">
                        <i v-show="!loadinginfo" class="fa fa-refresh loading-info-icon"></i>
                        <i v-show="loadinginfo" class="fa fa-cog fa-spin fa-fw loading-info-icon"></i>
                    </button>
                </div>
                
                <!-- Volume -->
                <div class="cell small-12 text-center volume">
                    <div v-model="volumeC"> Vol: <i class="fa fa-btc" aria-hidden="true"></i> {{ volumeC }}</div>
                </div>
               
                <!-- 24h High -->
                <div class="cell small-6 text-center">
                    <div v-model="highC" class="high-low"> H: <i class="fa fa-btc" aria-hidden="true"></i> {{ highC }}</div>
                </div>
                
                <!-- 24h Low -->
                <div class="cell small-6 text-center">
                    <div v-model="lowC" class="high-low"> L: <i class="fa fa-btc" aria-hidden="true"></i> {{ lowC }} </div>
                </div>
                
                <!-- Bid -->
                <div class="cell small-6 text-center bid">
                    <div v-model="bidC"> BID: <i class="fa fa-btc" aria-hidden="true"></i> {{ bidC }}</div>
                </div>
               
                <!-- Ask -->
                <div class="cell small-6 text-center ask">
                    <div v-model="askC"> ASK: <i class="fa fa-btc" aria-hidden="true"></i> {{ askC }} </div> 
                </div>
               
                <!-- Last -->
                <div class="cell small-12 text-center last">
                    <div v-model="lastC"> LAST: <i class="fa fa-btc" aria-hidden="true"></i> {{ lastC }} </div>
                </div>

            </div>
        </div>       
    </div>
  
</template>

<script>
export default {
    name: 'tradepanel',
    props: [
        'validation-errors'
    ],
    data() {
        return {
            pirceChanged: false,
            tpChanged: false,
            tppChanged: false,
            slChanged: false,
            slpChanged: false,
            stopAtTotal: false,
            stopAtAmount: false,
            loadingpairs: false,
            loadingprice: false,
            loadinginfo: false,
            marketLoaded: false,
            slSwitch: false,
            tpSwitch: false,
            slpercent: 0,
            stoploss: 0,
            tppercent: 0,
            takeprofit: 0,
            priceselected: "",
            exchange: "",
            pairselected: "",
            bittrexpairs: [],
            bittrexcoin: [],
            marketsummary: [],
            conditionselected: "now",
            conditionprice: 0.00000000,
            conditionalSwitch: false,
            errors: [],
            basecurrency: "",
            coinname: { 'long':'','short':''},
            coinlogo: "",
            volume: 0.0000,
            price: 0.00000000,
            last: 0.00000000,
            bid: 0.00000000,
            ask: 0.00000000,
            high: 0.00000000,
            low: 0.00000000,
            amount: 0.00000000,
            total: 0.00000000,
            fee: 0.00,
            availableBalance: "0",
            csrf: ""
        }
    },
    mounted() {
        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log(this.validationErrors);
    },
    computed: {
        highC: function() {
            return this.high.toFixed(8);
        },
        lowC: function() {
            return this.low.toFixed(8);
        },
        bidC: function() {
            return this.bid.toFixed(8);
        },
        askC: function() {
            return this.ask.toFixed(8);
        },
        lastC: function() {
            return this.last.toFixed(8);
        },
        volumeC: function() {
            return this.volume.toFixed(4);
        },
        priceC: function() {
            return this.price.toFixed(8);
        },
    },
    watch: {
        amount: function () {
            console.log("Watch amount fired! (amount=" + this.amount + ")");
            if (this.stopAtAmount == false) {
                this.stopAtTotal = true;
                this.updateTotal();
            }
            else {
                this.stopAtAmount = false;
            }
        },
        total: function() {
            console.log("Watch total fired! (total=" + this.total + ")");
            if (this.stopAtTotal == false) {
                this.stopAtAmount = true;
                this.updateAmount();
             }
            else {
                this.stopAtTotal = false;
            }
        },
        price: function() {
            this.stopAtTotal = true;
            this.pirceChanged = true;
            this.updateTotal();
        },
        takeprofit: function() {
            if ( this.tpSwitch ) {
                
                if (this.tppChanged) {
                    this.tppChanged = false;
                }
                else{
                    this.tpChanged = true;
                    this.updateTpPercent();
                }
                
            }
            else {
                return 0.00000000;
            }
        },
        tppercent: function() {
            if (this.tpSwitch) {

                if (this.tpChanged) {
                    this.tpChanged = false;
                }
                else {
                    this.tppChanged = true;
                    this.updateTakeProfit();
                }
    
            }
            else {
                return 20;
            }
        },
        stoploss: function() {
            if ( this.slSwitch ) {
                
                if (this.slpChanged) {
                    this.slpChanged = false;
                }
                else{
                    this.slChanged = true;
                    this.updateSlPercent();
                }
                
            }
            else {
                return 0.00000000;
            }
        },
        slpercent: function() {
            if (this.slSwitch) {

                if (this.slChanged) {
                    this.slChanged = false;
                }
                else {
                    this.slpChanged = true;
                    this.updateStopLoss();
                }
    
            }
            else {
                return 20;
            }
        }
    },
    methods: {
        updateAmount: (function () {
            let amount = ( parseFloat(this.total) - (parseFloat(this.total) * parseFloat(this.fee) / 100) )/parseFloat(this.price);
            console.log("Amount:" + parseFloat(amount));
            return this.amount = parseFloat(amount).toFixed(8);
        }),
        updateTotal: (function () {
            let total = (parseFloat(this.amount) * parseFloat(this.price));
            total = total + (total * parseFloat(this.fee) / 100);
            console.log("Total: " + parseFloat(total));
            return this.total = parseFloat(total).toFixed(8);
        }),
        updateTakeProfit: (function () {
            let tp = parseFloat(this.price) + (parseFloat(this.price) * (parseFloat(this.tppercent) / 100));
            return this.takeprofit = parseFloat(tp).toFixed(8);
        }),
        updateTpPercent: (function () {
            let percent = ((parseFloat(this.takeprofit) - parseFloat(this.price)) * 100) / parseFloat(this.price);
            return this.tppercent = parseFloat(percent).toFixed(2);
        }),
        updateStopLoss: (function () {
            let sl = parseFloat(this.price) - (parseFloat(this.price) * (parseFloat(this.slpercent) / 100));
            return this.stoploss = parseFloat(sl).toFixed(8);
        }),
        updateSlPercent: (function () {
            let percent = -(((parseFloat(this.stoploss) - parseFloat(this.price)) * 100) / parseFloat(this.price));
            return this.slpercent = parseFloat(percent).toFixed(2);
        }),
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
                    this.volume = parseFloat(this.marketsummary.BaseVolume);

                    if (pricetype.toLowerCase() == "last") {
                        this.price =  parseFloat(this.last);
                    }
                    else if (pricetype.toLowerCase() == "bid") {
                        this.price =  parseFloat(this.bid);
                    }
                    else if (pricetype.toLowerCase() == "ask") {
                        this.price =  parseFloat(this.ask);
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
        getmarketsummary(exchange, pair) {
            this.loadingpairs = true;
            if (exchange.toLowerCase() == 'bittrex') {
                let uri = '/api/bittrexapi/getmarketsummary/' + pair;
                axios(uri, {
                    method: 'GET',
                })
                .then(response => {
                    this.marketsummary=response.data[0];  
                    this.priceselected = "";
                    this.last =  parseFloat(this.marketsummary.Last);
                    this.bid =  parseFloat(this.marketsummary.Bid);
                    this.ask =  parseFloat(this.marketsummary.Ask);
                    this.high =  parseFloat(this.marketsummary.High);
                    this.low =  parseFloat(this.marketsummary.Low);
                    this.volume =  parseFloat(this.marketsummary.BaseVolume);

                    // Set price for current pair to 0
                    this.stopAtTotal = true;
                    this.price = parseFloat(0.00000000);


                    // TODO Get balance for the selected base currency
                    this.getbalance(exchange, pair);
                    
                    // Get coin info from api call getmarkets
                    this.getmarkets(exchange, pair);

                    
                    this.loadingpairs = false;
                    console.log("Success: " + this.marketsummary.MarketName);
                })
                .catch(e => {
                    this.errors.push(e);
                    this.loadingpairs = false;
                    console.log("Error: " +  e.message);
                })
            }
        },
        getpairs(exchange) {
            this.loadingpairs = true;

            //Bittrex
            if (exchange.toLowerCase() == 'bittrex') {
                axios('/api/bittrexapi/getpairs', {
                    method: 'GET'
                })
                .then(response => {
                    this.bittrexpairs = response.data;  

                    let options = {
                        data:  this.bittrexpairs,
                        list: {
                            onClickEvent: () => {
                                this.pairselected = $("#pairs").getSelectedItemData();
                                this.getmarketsummary(exchange,this.pairselected)
                            },   
                            maxNumberOfElements: 999,
                            match: {
                                enabled: true
                            },
                            showAnimation: {
                                type: "fade", //normal|slide|fade
                                time: 400,
                                callback: function() {}
                            },
                            hideAnimation: {
                                type: "fade s", //normal|slide|fade
                                time: 400,
                                callback: function() {}
                            }
                        },
                        theme: "square"
                    };

                    $("#pairs").easyAutocomplete(options);


                    console.log("Success: " + exchange + " pairs!");

                    // Get fee fot the exchange
                    axios('/api/exchange/' + exchange.toLowerCase() + '/fee', {
                        method: 'GET',
                    })
                    .then(response => {
                        this.fee = parseFloat(response.data);
                        console.log("Success: " + exchange + " fee! (" + this.fee + ")");
                        this.loadingpairs = false;
                    })
                    .catch(e => {
                        this.errors.push(e);
                        this.loadingpairs = false;
                        console.log("Error: " +  e.message);
                    });

                })
                .catch(e => {
                    this.errors.push(e);
                    this.loadingpairs = false;
                    console.log("Error: " +  e.message);
                });
            }
        },
        refreshInfopanel(exchange, pair){
            this.loadinginfo = true;
            if (exchange.toLowerCase() == 'bittrex') {
                let uri = '/api/bittrexapi/getmarketsummary/' + pair;
                axios(uri, {
                    method: 'GET',
                })
                .then(response => {
                    this.marketsummary=response.data[0];  
                    this.last =  parseFloat(this.marketsummary.Last);
                    this.bid =  parseFloat(this.marketsummary.Bid);
                    this.ask =  parseFloat(this.marketsummary.Ask);
                    this.high =  parseFloat(this.marketsummary.High);
                    this.low =  parseFloat(this.marketsummary.Low);
                    this.volume =  parseFloat(this.marketsummary.BaseVolume);
                    
                    this.loadinginfo = false;
                    console.log("Success loading info!");
                })
                .catch(e => {
                    this.errors.push(e);
                    this.loadinginfo = false;
                    console.log("Error: " +  e.message);
                })
            }
        },
        getmarkets(exchange, pair) {
            this.loadingpairs = true;
            this.coinname = {'long': 'loading', 'short':'...'};
            this.coinlogo = "";

            if (exchange.toLowerCase() == 'bittrex') {
                let coin = pair.split("-");
                axios('/api/bittrexapi/getmarkets/' + coin[1], {
                    method: 'GET',
                })
                .then(response => {
                    let res = response.data[0];  
                    this.coinname = {'long': res.MarketCurrencyLong, 'short':res.MarketCurrency};
                    this.coinlogo = res.LogoUrl;
                    this.basecurrency = res.BaseCurrency;
                    this.marketLoaded = true;
                    this.loadingpairs = false;
                    // console.log("Success coin info: " + this.coinname );
                })
                .catch(e => {
                    this.errors.push(e);
                    this.loadingpairs = false;
                    console.log("Error: " +  e.message);
                })
            }
        },
        getbalance (exchange, pair) {
            this.loadingpairs = true;

            if (exchange.toLowerCase() == 'bittrex') {
                let coin = pair.split("-");
                
                axios('/api/bittrexapi/getbalance/' + coin[0], {
                    method: 'GET',
                })
                .then(response => {
                    console.log("Success balance: " + response.data.Available);
                    let res = response.data[0];  
                    response.data.Available ? this.availableBalance = response.data.Available + " " + response.data.Currency : this.availableBalance = "0 ";
                    this.loadingpairs = false;
                    // console.log("Success balance");
                })
                .catch(e => {
                    this.errors.push(e);
                    this.loadingpairs = false;
                    console.log("Error: " +  e.message);
                })
            }
        }
    }
};
</script>
