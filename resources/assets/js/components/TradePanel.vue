<template>

    <div class="grid-x grid-padding-x">

        <div class="cell large-7 small-order-2 large-order-1">
            <div class="grid-x grid-padding-x align-top">
                <!-- Exchange -->
                <div class="cell large-6">
                    <div class="input-group">
                        <span class="input-group-label">Exchange</span>
                        <select v-model="exchange" class="input-group-field" v-on:change="getpairs(exchange)">
                            <option disabled value="">Select...</option>
                            <option value="bittrex" selected="true"> Bittrex </option>
                        </select>
                    </div>
                </div>
                <!-- Pair -->
                <div class="cell large-6">
                    <div class="input-group">
                        <span class="input-group-label"><i v-show="loadingpairs" class="fa fa-cog fa-spin fa-fw"></i> Pair</span>
                        <select v-model="selected" class="input-group-field" v-on:change="getmarketsummary(exchange, selected)">
                            <option disabled value="">Select...</option>
                            <option v-for="pair in bittrexpairs" :value="pair"> {{ pair }}</option>
                        </select>
                    </div>
                </div>

                <!-- Price -->
                <div class="small-12 cell">
                    <div class="input-group">
                        <span class="input-group-label">
                            <i v-show="loadingprice" class="fa fa-cog fa-spin fa-fw"></i> Price
                        </span>
                        <input v-model="price" class="input-group-field price" type="number">
                         <select v-model="priceselected" id="price-select"  v-on:change="updateprice(exchange, selected, priceselected)">
                            <option disabled value="">Autofill</option>
                            <option value="last">Last</option>
                            <option value="bid">Bid</option>
                            <option value="ask">Ask</option>
                        </select>
                          
                    </div>
                </div>

                <!-- Ammount -->
                <div class="large-6 cell">
                    <div class="input-group">
                        <span class="input-group-label">Ammount</span>
                        <input v-model="ammount" class="input-group-field" type="number">
                    </div>
                </div>

                <!-- Total -->
                <div class="large-6 cell">
                    <div class="input-group">
                        <span class="input-group-label">Total</span>
                        <input v-model="total" class="input-group-field" type="number">
                    </div>
                </div>

                <!-- Stop Loss -->
                <div class="medium-9 cell">
                    <div class="input-group">
                        <span class="input-group-label">Stop-Loss</span>
                        <input v-model="stoploss" class="input-group-field" type="number">
                    </div>
                </div>
                <div class="medium-3 cell">
                    <div class="input-group">
                        <span class="input-group-label">(-)%</span>
                        <input v-model="slpercent" class="input-group-field" type="number">
                    </div>
                </div>
                <!-- Take Profit -->
                <div class="medium-9 cell">
                    <div class="input-group">
                        <span class="input-group-label">Take-Profit</span>
                        <input v-model="takeprofit"class="input-group-field" type="number">
                    </div>
                </div>
                <div class="medium-3 cell">
                    <div class="input-group">
                        <span class="input-group-label">(+)%</span>
                        <input v-model="tppercent" class="input-group-field" type="number">
                    </div>
                </div>

                <!-- Open Position Button -->
                <div class="medium-12 cell">
                    <button class="hollow button" href="#">
                        Open Long
                    </button>
                    <button class="hollow button alert disabled" href="#">
                        Open Short
                    </button>
                </div>
            </div>
        </div>

        <div class="cell large-5 small-order-1 large-order-2">
            <div class="grid-x grid-margin-x">
                <div class="cell small-12 text-center">
                    <img id="cryptologo" v-show="coinlogo != ''" v-model="coinlogo" :src="coinlogo" :alt="coinname.short">
                    <p v-show="coinname != ''" v-model="coinname" class="h2"> {{ coinname.long }} </p>
                    <p v-show="coinname != ''" v-model="coinname" class="h4"> {{ coinname.short }} </p>
                </div>
                <!-- 24h High -->
                <div class="cell small-6 text-center">
                    <div v-model="highC" class="high-low"> {{ highC }}</div>
                    <div>24h High</div>
                </div>
                <!-- 24h Low -->
                <div class="cell small-6 text-center">
                    <div v-model="lowC" class="high-low"> {{ lowC }} </div>
                    <div>24h Low</div>
                </div>
                <!-- Bid -->
                <div class="cell small-6 text-center bid">
                    <div v-model="bidC"> {{ bidC }}</div>
                    <div>BID</div>
                </div>
                <!-- Ask -->
                <div class="cell small-6 text-center ask">
                    <div v-model="askC"> {{ askC }} </div>
                    <div>ASK</div>
                </div>
                <!-- Last -->
                <div class="cell small-12 text-center last">
                    <div v-model="lastC"> {{ lastC }} </div>
                    <div>Last</div>
                </div>
            </div>
        </div>
                 
    </div>
</template>

<script>
export default {
    name: 'tradepanel',
    props: [],
    data() {
        return {
            autofilled: false,
            loadingpairs: false,
            loadingprice: false,
            priceselected: "",
            exchange: "",
            selected: "",
            bittrexpairs: [],
            bittrexcoin: [],
            marketsummary: [],
            errors: [],
            coinname: { 'long':'','short':''},
            coinlogo: "",
            price: 0,
            last: 0,
            bid: 0,
            ask: 0,
            high: 0,
            low: 0,
            tppercent: 0,
            slpercent: 0,
            ammount: 0,
            total: 0
        }
    },
    mounted() {
            
    },
    computed: {
        highC: function() {
            return this.high.toFixed(8);
        },
        lowC: function() {
            return this.low.toFixed(8) ;
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
        stoploss: function() {
            let sl = this.price - (this.price * (this.slpercent / 100));
            return sl;
        },
        takeprofit: function() {
            let tp = this.price + (this.price * (this.tppercent / 100));
            return tp;
        }
    },
    watch: {
        ammount: function () {
            if (!this.autofilled) {
                this.updateTotal();
            }
            else {
                this.autofilled = false;
            }
        },
        total: function() {
            if (!this.autofilled) {
                this.updateAmmount();
             }
            else {
                this.autofilled = false;
            }
        },
        price: function() {
            this.updateTotal();
        }
    },
    methods: {
        getpairs(exchange) {
            this.loadingpairs = true;
            if (exchange.toLowerCase() == 'bittrex') {
                axios('/api/bittrexapi/getpairs', {
                    method: 'GET',
                })
                .then(response => {
                    this.bittrexpairs=response.data;  
                    this.loadingpairs = false;
                    console.log("Success: " + exchange + " pairs!");
                })
                .catch(e => {
                    this.errors.push(e);
                    this.loadingpairs = false;
                    console.log("Error: " + this.errors.push(e));
                })
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
                    this.last = this.marketsummary.Last;
                    this.bid = this.marketsummary.Bid;
                    this.ask = this.marketsummary.Ask;
                    this.high = this.marketsummary.High;
                    this.low = this.marketsummary.Low;

                    // Set price for current pair to 0
                    this.price = 0;

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
                    this.loadingpairs = false;
                    console.log("Success coin info: " + this.coinname );
                })
                .catch(e => {
                    this.errors.push(e);
                    this.loadingpairs = false;
                    console.log("Error: " +  e.message);
                })
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
                    this.last = this.marketsummary.Last;
                    this.bid = this.marketsummary.Bid;
                    this.ask = this.marketsummary.Ask;

                    if (pricetype.toLowerCase() == "last") {
                        this.price =  this.last;
                    }
                    else if (pricetype.toLowerCase() == "bid") {
                        this.price =  this.bid;
                    }
                    else if (pricetype.toLowerCase() == "ask") {
                        this.price =  this.ask;
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
        updateAmmount: _.debounce(function () {
            console.log("Updating ammount...");
            this.autofilled = true;
            return this.ammount = this.total/this.price;
        }, 500),
        updateTotal: _.debounce(function () {
            this.autofilled = true;
            return this.total = this.ammount * this.price;
        }, 500)

    }
};
</script>
