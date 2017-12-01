<template>
    <div class="grid-x grid-padding-x tradepanel">
        <!-- Trade form -->
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
                        <select v-model="pairselected" class="input-group-field" v-on:change="getmarketsummary(exchange, pairselected)">
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
                         <select v-model="priceselected" id="price-select"  v-on:change="updateprice(exchange, pairselected, priceselected)">
                            <option disabled value="">Autofill</option>
                            <option value="last">Last</option>
                            <option value="bid">Bid</option>
                            <option value="ask">Ask</option>
                        </select>
                          
                    </div>
                </div>
                <!-- Condition -->
                <div class="small-12 cell">
                    <div class="input-group">
                        <select  v-model="conditionselected" id="condition-select">
                            <option disabled value="">Condition</option>
                            <option value="now">Open now</option>
                            <option value="greater">When price >= </option>
                            <option value="less">When price <= </option>
                        </select>
                        <input v-model="conditionprice" class="input-group-field" type="number">
                    </div>
                </div>
                <!-- Amount -->
                <div class="large-6 cell">
                    <div class="input-group">
                        <span class="input-group-label">Amount</span>
                        <input v-model="amount" class="input-group-field" type="number">
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
                <!-- Stop Loss % -->
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
                <!-- Take Profit % -->
                <div class="medium-3 cell">
                    <div class="input-group">
                        <span class="input-group-label">(+)%</span>
                        <input v-model="tppercent" class="input-group-field" type="number">
                    </div>
                </div>
                <!-- Open Position Button -->
                <div class="medium-12 cell">
                    <button class="hollow button" v-on:click="openLong" href="#">
                        Open Long
                    </button>
                    <button class="hollow button alert disabled" href="#">
                        Open Short
                    </button>
                </div>
            </div>
        </div>

        <!-- Information panel -->
        <div class="cell large-5 small-order-1 large-order-2">
            <div v-show="marketLoaded == false" class="title-image">
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
                    <button class="clear button" v-on:click="getmarketsummary(exchange, pairselected)"><i class="fa fa-refresh" aria-hidden="true"></i></button>
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
    props: [],
    data() {
        return {
            autofilled: false,
            stopAtTotal: false,
            stopAtAmount: false,
            filledByAmount: false,
            filledByTotal: false,
            filledByPrice: false,
            loadingpairs: false,
            loadingprice: false,
            marketLoaded: false,
            priceselected: "",
            exchange: "",
            pairselected: "",
            bittrexpairs: [],
            bittrexcoin: [],
            marketsummary: [],
            conditionselected: "now",
            conditionprice: 0.00000000,
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
            tppercent: 0,
            slpercent: 0,
            amount: 0.00000000,
            total: 0.00000000,
            fee: 0.00
        }
    },
    mounted() {
            
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
        stoploss: function() {
            let sl = parseFloat(this.price) - (parseFloat(this.price) * (parseFloat(this.slpercent) / 100));
            return sl.toFixed(8);
        },
        takeprofit: function() {
            let tp = parseFloat(this.price) + (parseFloat(this.price) * (parseFloat(this.tppercent) / 100));
            return tp.toFixed(8);
        }
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
            this.updateTotal();
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
        getpairs(exchange) {
            this.loadingpairs = true;

            //Bittrex
            if (exchange.toLowerCase() == 'bittrex') {
                axios('/api/bittrexapi/getpairs', {
                    method: 'GET',
                })
                .then(response => {
                    this.bittrexpairs=response.data;  
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
        getmarketsummary(exchange, pair) {
            this.loadingpairs = true;
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

                    // Set price for current pair to 0
                    this.stopAtTotal = true;
                    this.price = parseFloat(0.00000000);

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
                    this.basecurrency = res.BaseCurrency;
                    this.marketLoaded = true;
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
        openLong () {
            let uri = 'status=opened&position=long' + '&exchange=' + this.exchange + '&pair=' + this.pairselected + '&price=' + this.price + '&amount=' + this.amount + '&total=' + this.total + '&stop_loss=' + this.stoploss + '&take_profit=' + this.takeprofit + "&condition=" + this.conditionselected + "&condition_price=" + this.conditionprice;
            axios.post('/trades', uri)
            .then(response => {
                console.log("Trade opened!");
                window.location.href = '/trades';
            })
            .catch(error => {
                console.log(error.response.data); 
            });
        }

    }
};
</script>
