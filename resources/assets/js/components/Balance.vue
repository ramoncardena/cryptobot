<template>
    <div class="balance" v-if="type === 'header' " >
        <div class="grid-x grid-margin-x header">
            <div class="large-1 cell text-left">
                Coin
            </div>
            <div class="large-2 cell text-right">
                Balance
            </div>
            <div class="large-2 cell text-right">
                Price
            </div>
            <div class="large-2 cell text-right">
                Value (BTC)
            </div>
            <div class="large-2 cell text-right">
                Value (EUR)
            </div>
            <div class="large-2 cell text-right">
                Value (USD)
            </div>
            <div class="large-1 cell text-right">
                Gain
            </div>

        </div>
    </div>
    <div class="balance" v-else>
        <div class="grid-x grid-margin-x">
            <div class="large-1 cell text-left">
              <img class="coin-logo" :src="logo" alt="Cryptocoin logo"> {{ coin }}
            </div>
            <div class="large-2 cell text-right">
                {{ balance }}
            </div>
            <div class="large-2 cell text-right">
                 {{ price }}BTC
            </div>
            <div class="large-2 cell text-right">
                {{ valuebtc}}BTC
            </div>
            <div class="large-2 cell text-right">
                {{ valueeur }}€
            </div>
            <div class="large-2 cell text-right">
                ${{ valueusd }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Balance',
        data: () => {
            return {
                message: 'This is balance component'
            }
        },
        props: ['type', 'coin', 'logo', 'price', 'balance', 'valuebtc', 'valueeur', 'valueusd'],
        mounted() {
            console.log('Component balance mounted.')
        }
    }
</script>


