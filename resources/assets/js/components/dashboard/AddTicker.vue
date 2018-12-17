<template>
    <section id="addticker">
        <div class="grid-container fluid add-asset">
            <form method="POST" action="/dashboard/ticker">
                <input type="hidden" name="_token" :value="csrf">

                <div class="grid-x grid-padding-x">
                    <!-- Header -->
                    <div class="small-12 cell form-container">
                        <p class="h1">Dashboard: New Tracking</p>
                        <p class="lead"><b>Track new asset to your dashboard</b></p>
                    </div>

                    
                    <div class="small-12 cell form-container">
                        <div v-if="validationErrors.asset_symbol">
                           <span class="validation-error" v-for="error in validationErrors.asset_symbol"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Asset</span>
                            <input name="asset_symbol" id="coins" v-model="coinSelected" class="input-group-field number" type="text">
                        </div>
                    </div>

                    <div class="small-12 cell form-container">
                        <button class="hollow button" type="submit">
                           Add Asset
                        </button>
                    </div>
                    
                </div>

            </form>
        </div>
    </section>
</template>

<script>
   export default {
    name: 'add-ticker',
    data: () => {
        return {
            coinSelected: "",
            coin: "",
            updating: false,
            csrf: ""
        }
    },
    props: [
    'coins',
    'validation-errors',
    ],
    computed: {

    },
    watch: {

    },
    mounted() {
        console.log(this.origins);
        let coins =$.map( this.coins, function( a ) {
          return a.toString(); 
        });



        let options = {
            data:  coins,
            list: {
                onClickEvent: () => {
                    this.coinSelected = $("#coins").getSelectedItemData();
                },   
                maxNumberOfElements: 2000,
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

        $("#coins").easyAutocomplete(options);

        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('Component AddAsset mounted.');
    },
    methods: {
        saveName: (function () {

        })
    }
}
</script>


