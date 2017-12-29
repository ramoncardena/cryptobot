<template>
        <div class="grid-container fluid add-asset">
            <form method="POST" action="/portfolio/asset">
                <input type="hidden" name="_token" :value="csrf">

                <div class="grid-x grid-padding-x">
                    <!-- Header -->
                    <div class="small-12 cell form-container">
                        <p class="h1">Portfolio: New Asset</p>
                        <p class="lead"><b>Add new asset to your portfolio</b></p>
                    </div>
                    <div class="small-12 cell form-container">
                        <div v-if="validationErrors.origin">
                           <span class="validation-error" v-for="error in validationErrors.origin_type"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Origin</span>
                            <select v-model="originSelected" name="asset_origin" class="input-group-field">
                                <option disabled value="">Select...</option>
                                <option v-for="origin in origins" :value="origin.id">{{ origin.name }} </option>
                            </select>                     
                        </div>
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
                        <div v-if="validationErrors.asset_amount">
                           <span class="validation-error" v-for="error in validationErrors.asset_amount"> {{ error }} </span>
                        </div>
                         <div class="input-group">
                            <span class="input-group-label">
                                Amount
                            </span>
                            <input name="asset_amount"  class="input-group-field number" type="text" >
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
        
</template>

<script>
   export default {
    name: 'add-asset',
    data: () => {
        return {
            coinSelected: "",
            coin: "",
            originSelected: "",
            updating: false,
            csrf: ""
        }
    },
    props: [
    'coins',
    'origins',
    'validation-errors'
    ],
    computed: {

    },
    mounted() {
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

    }
}
</script>


