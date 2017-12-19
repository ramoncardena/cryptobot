<template>
        <tr>
            <td></td>
            <td v-if="history==false">
                <div class="trade-cancel icons-area">
                    <!-- <i v-show="updating" class="fa fa-cog fa-spin fa-fw loading-icon"></i>  -->
                    <button class="clear button" v-if="tradeStatus=='Opened'" :data-open="'closeTrade' + id"><i class="fa fa-times cancel-icon"></i></button>
                    <button class="clear button"  v-if="tradeStatus=='Waiting'" :data-open="'closeWaitingTrade' + id"><i class="fa fa-times cancel-icon"></i></button>
                    <button class="clear button"  v-if="tradeStatus=='Opening'" :data-open="'closeOpeningTrade' + id"><i class="fa fa-times cancel-icon"></i></button>
                    <button class="clear button"  v-if="tradeStatus=='Opened'" :data-open="'keepTrade' + id"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i></button>
                    <button class="clear button"  v-if="tradeStatus=='Opened'" :data-open="'editTrade' + id"><i class="fa fa-pencil edit-icon" aria-hidden="true"></i></button>
                    <button class="clear button" v-if="((tradeStatus=='Opened' || tradeStatus=='Waiting' || tradeStatus=='Closing' || tradeStatus=='Opening') && updating==false)" v-on:click="update(exchange, pair, price)"> <i class="fa fa-refresh refresh-icon"></i> </button>
                    <button class="clear button" v-if="((tradeStatus=='Opened' || tradeStatus=='Waiting' || tradeStatus=='Closing' || tradeStatus=='Opening') && updating==true)"> <i class="fa fa-refresh fa-spin refresh-icon"></i></button> 
                </div>
            </td>
            <td>{{ (history==true) ? parseFloat(finalProfit).toFixed(2) + '%' : profit }}</td>
            <td>{{ pair }}</td>
            <td class="sorting_1  trade-status"><span :class="'status-' + tradeStatus" v-model="tradeStatus">{{ tradeStatus }}</span></td>
            <td>{{ exchange }}</td>
            <td>{{ position }}</td>
            <td>{{ last.toFixed(8) }}</td>
            <td>{{ parseFloat(price).toFixed(8) }}</td>
            <td>{{ parseFloat(closingPrice).toFixed(8) }}</td>
            <td>{{ parseFloat(amount).toFixed(4) }}</td>
            <td>{{ parseFloat(total).toFixed(8) }}</td>
            <td>{{ parseFloat(stopLoss).toFixed(8) }}</td>
            <td>{{ parseFloat(takeProfit).toFixed(8) }}</td>
            <td>{{ (condition == 'now') ? 'none' : condition + ' than' }} </td>
            <td> {{ parseFloat(conditionPrice).toFixed(8) }}</td>
            <td> {{ date }}</td>
        </tr>
        
</template>

   <script>
   export default {
    name: 'trade',
    data: () => {
        return {
            updating: false,
            profit: 0,
            last: 0,
            tradeStatus: ""
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
    'condition',
    'condition-price',
    "final-profit",
    "type",
    "closing-price",
    "timestamp",
    "id"
    ],
    computed: {
        history: function() {
            if (this.type == "history") {
                return true;
            }
            else {
                return false;
            }
        },
        date: function() {
            let fullDate = new Date(this.timestamp);
            return fullDate.getDate() + "/" + (fullDate.getMonth()+1) + "/" + fullDate.getFullYear();
        }
    },
    mounted() {
        this.tradeStatus = this.status;
        this.update(this.exchange, this.pair, this.price); 

        Echo.private('trades.' + this.id)
        .listen('TradeOpened', (e) => {
            console.log('New status: ' + e.trade.status);
            this.tradeStatus = e.trade.status;
        })
        .listen('TradeClosed', (e) => {
            console.log('New status: ' + e.trade.status);
            this.tradeStatus = e.trade.status;
        })
        .listen('TradeCancelled', (e) => {
            console.log('New status: ' + e.trade.status);
            this.tradeStatus = e.trade.status;
        });;

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
                    this.profit = decreaseValue.toFixed(2) + "%";

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


