<template>
    <div class="order">
        <div v-if="header" class="grid-container fluid">
            <div class="grid-x align-middle">
                <div class="small-6 medium-auto cell">
                    <div class="order-title">
                        Pair
                    </div>
                </div>
                <div class="small-6 medium-auto cell">
                    <div class="order-title">
                        Type
                    </div>
                </div>
                <div v-if="limit" class="small-6 medium-auto cell">
                    <div class="order-title">
                        Bid/Ask
                    </div>
                </div>
                <div class="small-6 medium-auto cell">
                    <div class="order-title">
                        Units
                    </div>
                </div>
                <div class="small-6 medium-auto cell">
                    <div class="order-title">
                        Remaining
                    </div>
                </div>
                <div v-if="price" class="small-6 medium-auto cell">
                    <div class="order-title">
                        Price
                    </div>
                </div>
                <div v-if="priceunit" class="small-6 medium-auto cell">
                    <div class="order-title">
                        Price Per Unit
                    </div>
                </div>
                <div v-if="closed" class="small-6 medium-auto cell">
                    <div class="order-title">
                        Commission
                    </div>
                </div>
                <div v-if="opened" class="small-6 medium-auto cell">
                    <div class="order-title">
                        Cancel
                    </div>
                </div>
            </div>
        </div>
        <div class="grid-container fluid">
            <div class="grid-x align-middle">

                <div class="small-6 medium-auto cell">
                    <div class="order-pair">
                        {{ pair }}
                    </div>
                </div>

                <div class="small-6 medium-auto cell">
                    <div class="order-type">
                        {{ type }}
                    </div>
                </div>

                <div v-if="limit" class="small-6 medium-auto cell">
                    <div class="order-limit">
                       {{ limit }}
                    </div>
                </div>

                <div class="small-6 medium-auto cell">
                    <div class="order-quantity">
                        {{ quantity }}
                    </div>
                </div>

                <div class="small-6 medium-auto cell">
                    <div class="order-reamining">
                        {{ remaining }}
                    </div>
                </div>

                <div v-if="price" class="small-6 medium-auto cell">
                    <div class="order-limit">
                       {{ price }}
                   </div>
               </div>

               <div v-if="priceunit" class="small-6 medium-auto cell">
                    <div class="order-limit">
                       {{ priceunit }}
                   </div>
               </div>

               <div v-if="closed" class="small-6 medium-auto cell">
                    <div class="order-limit">
                       {{ commission }}
                   </div>
               </div>

               <div v-if="opened" class="small-6 medium-auto cell">
                    <div v-if="opened" class="order-cancel">
                       <a href="#" class="clear button large alert"><i class="fa fa-times" aria-hidden="true"></i></a>
                   </div>
               </div>
            </div>
        </div>
    </div>
</template>

   <script>
   export default {
    name: 'Order',
    data: () => {
        return {
        }
    },
    props: [
    'header',
    'opened',
    'pair', 
    'type', 
    'quantity', 
    'limit', 
    'price', 
    'priceunit',
    'commission', 
    'closed', 
    'remaining'
    ],
    mounted() {
        console.log('Component Order mounted.')
    }
}
</script>


