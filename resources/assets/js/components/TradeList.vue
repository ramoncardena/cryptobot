<template>
    <div class="trade-list">
        <div class="trade">
            <div class="grid-container fluid">
                <div class="grid-x grid-margin-x align-middle">
                    <div class="small-6 medium-auto cell">
                        <div class="trade-title">
                            Status
                        </div>
                    </div>
                    <div class="small-6 medium-auto cell">
                        <div class="trade-title">
                            Exchange
                        </div>
                    </div>
                    <div class="small-6 medium-auto cell">
                        <div class="trade-title">
                            Position
                        </div>
                    </div>
                    <div class="small-6 medium-auto cell">
                        <div class="trade-title">
                            Pair
                        </div>
                    </div>
                    <div class="small-6 medium-auto cell">
                        <div class="trade-title">
                            Price
                        </div>
                    </div>
                    <div class="small-6 medium-auto cell">
                        <div class="trade-title">
                            Ammount
                        </div>
                    </div>
                    <div  class="small-6 medium-auto cell">
                        <div class="trade-title">
                            Total
                        </div>
                    </div>
                    <div  class="small-6 medium-auto cell">
                        <div class="trade-title">
                            Stop-Loss
                        </div>
                    </div>
                    <div class="small-6 medium-auto cell">
                        <div class="trade-title">
                           Take-Profit
                        </div>
                    </div>
                    <div class="small-6 medium-auto cell">
                        <div class="trade-title">
                           Last
                        </div>
                    </div>
                    <div class="small-6 medium-auto cell">
                        <div class="trade-title">
                           P/L (%)
                        </div>
                    </div>
                    <div  class="small-6 medium-auto cell">
                         
                    </div>
                </div>
            </div>
        </div>
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
                    :key="trade.id"
                >
        </trade>
    </div>
</template>

   <script>
   export default {
    name: 'tradelist',
    props: [
    'type',
    'trades'
    ],
    data: () => {
        return {
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
    }
}
</script>


