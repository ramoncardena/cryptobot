<template>
    <div class="trade">
        <div class="grid-container fluid">
            <div class="grid-x grid-margin-x align-middle">
                <div class="small-6 medium-auto cell">
                    <div :class="status">
                        {{ status }}
                    </div>
                </div>

                <div class="small-6 medium-auto cell">
                    <div class="trade-exchange">
                        {{ exchange }}
                    </div>
                </div>

                <div class="small-6 medium-auto cell">
                    <div class="trade-type">
                        {{ position }}
                    </div>
                </div>

                <div class="small-6 medium-auto cell">
                    <div class="trade-limit">
                       {{ pair }}
                    </div>
                </div>

                <div class="small-6 medium-auto cell">
                    <div class="trade-quantity">
                        {{ price }}
                    </div>
                </div>

                <div class="small-6 medium-auto cell">
                    <div class="trade-reamining">
                        {{ amount }}
                    </div>
                </div>

                <div  class="small-6 medium-auto cell">
                    <div class="trade-limit">
                       {{ total }}
                   </div>
               </div>

               <div class="small-6 medium-auto cell">
                    <div class="trade-limit">
                       {{ stopLoss }}
                   </div>
               </div>

               <div class="small-6 medium-auto cell">
                    <div class="trade-limit">
                       {{ takeProfit }}
                   </div>
               </div>

               <div class="small-6 medium-auto cell">
                    <div v-model="last" class="trade-limit">
                       {{ last }}
                   </div>
               </div>

               <div class="small-6 medium-auto cell">
                    <div v-model="profit" class="trade-limit">
                       {{ profit }}
                   </div>
               </div>

               <div class="small-6 medium-auto cell">
                    <div v-show="opened" class="trade-cancel icons-area">
                        <i v-show="updating" class="fa fa-cog fa-spin fa-fw"></i> 
                        <i v-show="!updating" class="fa fa-times cancel-icon" aria-hidden="true"></i>
                        <i v-on:click="update(exchange, pair, price)" class="fa fa-refresh refresh-icon" aria-hidden="true"></i>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

   <script>
   export default {
    name: 'trade',
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


