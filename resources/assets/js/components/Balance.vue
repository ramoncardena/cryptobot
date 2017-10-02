<template>
 <div class="balance">
        <div class="grid-container fluid">
            <div class="grid-x align-middle">
                <div class="medium-auto cell">
                    <div class="coin-logo">
                        <img class="coin-logo" :src="logo" alt="Cryptocoin logo"> 
                    </div>
                    <div class="coin-name">
                        {{ coin }}
                    </div>
                </div>

                <div class="small-6 medium-auto cell">
                    <div class="balance-title">
                        Balance
                    </div>
                    <div class="coin-balance">
                        {{ balance }}
                    </div>
                </div>

                <div class="small-6 medium-auto cell">
                    <div class="balance-title">
                        Price
                    </div>
                    <div class="coin-price">
                        {{ price }}BTC
                    </div>
                </div>

                <div class="small-6 medium-auto cell">
                    <div class="balance-title">
                        Value (BTC)
                    </div>
                     <div class="btc-value">
                        {{ valuebtc}}BTC
                    </div>
                </div>

                <div v-if="fiat === 'eur'" class="small-6 medium-auto cell">
                    <div class="balance-title">
                        Value (EUR)
                    </div>
                    <div class="eur-value">
                        {{ valueeur }}€
                    </div>
                </div>

                <div v-if="fiat === 'usd'" class="small-6 medium-auto cell">
                    <div class="balance-title">
                        Value (USD)
                    </div>
                    <div class="usd-value">
                        ${{ valueusd }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Balance',
    data: () => {
        return {

        }
    },
    props: ['type', 'coin', 'logo', 'price', 'balance', 'valuebtc', 'valueeur', 'valueusd', 'fiat'],
    mounted() {
        console.log('Component balance mounted.')
    }
}
</script>


