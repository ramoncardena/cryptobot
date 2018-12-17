<template>
    <section id="deleteasset">
        <div class="grid-container fluid delete-asset">
            <form method="POST" :action="'/portfolio/asset/' + assetSelected">
                <input type="hidden" name="_token" :value="csrf">
                <input name="_method" type="hidden" value="DELETE">

                <div class="grid-x grid-padding-x">
                    <!-- Header -->
                    <div class="small-12 cell form-container">
                        <p class="h2">Delete Asset</p>
                    </div>
                    <div class="small-12 cell form-container">
                        <div v-if="validationErrors.asset_origin">
                           <span class="validation-error" v-for="error in validationErrors.asset_origin"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Origin</span>
                            <select v-model="originSelected" name="asset_origin" class="input-group-field" v-on:change="loadAssets();assetSelected=''">
                                <option disabled value="">Select...</option>
                                <option v-for="origin in noxchgOrigins" :value="origin.id">{{ origin.name }} </option>
                            </select>      
                            <input id="asset-origin-name" name="asset_origin_name" type="hidden" :value="originSelectedName">               
                        </div>
                    </div>
                    

                    <div class="small-12 cell form-container">
                        <div v-if="validationErrors.asset">
                           <span class="validation-error" v-for="error in validationErrors.asset"> {{ error }} </span>
                        </div>
                         <div class="input-group">
                            <span class="input-group-label">Asset</span>
                            <select v-model="assetSelected" name="asset" class="input-group-field" v-on:change="loadAssetData(assetSelected)">
                                <option disabled value="">Select...</option>
                                <option v-for="asset in assets" :value="asset.id"> {{ asset.symbol }} </option>
                            </select>                   
                        </div>
                    </div>

                    <div v-if="assetSelected != ''" class="small-12 cell form-container">
                        <button class="hollow button" type="submit">
                           Delete Asset
                        </button>
                    </div>
                    
                </div>

            </form>
        </div>
    </section>
</template>

<script>
   export default {
    name: 'delete-asset',
    data: () => {
        return {
            assets: [],
            noxchgOrigins: [],
            assetAmount: 0,
            assetInitialPrice: 0,
            assetSelected: "",
            coinSelected: "",
            coin: "",
            originSelected: "",
            originSelectedName: "",
            updating: false,
            csrf: "",
            errors: []
        }
    },
    props: [
    'portfolio',
    'origins',
    'exchanges',
    'validation-errors'
    ],
    computed: {

    },
    watch: {

    },
    mounted() {

        let coins =$.map( this.coins, function( a ) {
          return a.toString(); 
        });

        for (var i = 0; i < this.origins.length; i++) {    
           if (this.exchanges.indexOf(this.origins[i].name.toLowerCase()) < 0) {
                this.noxchgOrigins.push(this.origins[i]);
           }
        }

        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('Component EditAsset mounted.');
    },
    methods: {
        loadAssets: (function () {
            for (var i = 0; i < this.origins.length; i++) {     
                if (this.origins[i].id == this.originSelected) this.originSelectedName = this.origins[i].name;
            }

            this.assets = [];

            // Call the LoadPortfolio event asyncronously
            let uri = '/api/assets';
            axios(uri, {
                method: 'GET',
            })
            .then(response => {
                for (var i = 0; i < response.data.length; i++) {     
                    if (response.data[i].origin_id == this.originSelected) this.assets.push(response.data[i]);
                }
            })
            .catch(e => {
                this.errors.push(e);
                console.log("Error: " + e.message);
            });

        }),
        loadAssetData: (function (assetId) {
            // Call the LoadPortfolio event asyncronously
            let uri = '/api/assets/'+assetId;
            axios(uri, {
                method: 'GET',
            })
            .then(response => {
                this.assetAmount = response.data.amount;
                this.assetInitialPrice = response.data.initial_price;
                console.log("Retieving asset...");
            })
            .catch(e => {
                this.errors.push(e);
               
                console.log("Error: " + e.message);
            });
        })
    }
}
</script>


