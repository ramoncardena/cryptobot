<template>
    <section id="addasset">
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
                        <div v-if="validationErrors.origin_type">
                           <span class="validation-error" v-for="error in validationErrors.origin_type"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Origin</span>
                            <select v-model="originSelected" name="asset_origin" class="input-group-field" v-on:change="saveName()">
                                <option disabled value="">Select...</option>
                                <option v-for="origin in noxchgOrigins" :value="origin.id">{{ origin.name }} </option>
                            </select>      
                            <input id="asset-origin-name" name="asset_origin_name" type="hidden" :value="originSelectedName">               
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
                        <div v-if="validationErrors.asset_initial_price">
                           <span class="validation-error" v-for="error in validationErrors.asset_initial_price"> {{ error }} </span>
                        </div>
                         <div class="input-group">
                            <span class="input-group-label">
                                Initial Price
                            </span>
                            <input name="asset_initial_price"  class="input-group-field number" type="text" >
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
    name: 'add-asset',
    data: () => {
        return {
            coinSelected: "",
            coin: "",
            noxchgOrigins: [],
            originSelected: "",
            originSelectedName: "",
            updating: false,
            csrf: ""
        }
    },
    props: [
    'coins',
    'origins',
    'validation-errors',
    'exchanges'
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

        for (var i = 0; i < this.origins.length; i++) {    
           if (this.exchanges.indexOf(this.origins[i].name.toLowerCase()) < 0) {
                this.noxchgOrigins.push(this.origins[i]);
           }
        }

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
            for (var i = 0; i < this.origins.length; i++) {     
                if (this.origins[i].id == this.originSelected) this.originSelectedName = this.origins[i].name;
            }
        })
    }
}
</script>


