<template>
    <section id="delete-transaction">
        <div class="grid-container fluid transactions">
                <div class="grid-x grid-padding-x">
                    <!-- Header -->
                    <div class="small-12 cell form-container">
                        <p class="h2">Delete Transaction</p>
                    </div>
                    <div class="small-12 cell form-container">
                        <div v-if="validationErrors.origin_type">
                           <span class="validation-error" v-for="error in validationErrors.origin_type"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Origin</span>
                            <select v-model="originSelected" name="asset_origin" class="input-group-field" v-on:change="loadAssets(); transactionsLoaded=false;">
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
                            <select v-model="assetSelected" name="asset" class="input-group-field" v-on:change="loadAssetData(originSelected, assetSelected)">
                                <option disabled value="">Select...</option>
                                <option v-for="asset in assets" :value="asset.id"> {{ asset.symbol }} </option>
                            </select>                   
                        </div>
                    </div>

                    <div v-if="transactionsLoaded == true" class="small-12 cell form-container">
                       <table class="stack">
                            <thead>
                                <tr>
                                    <th width="50">Type</th>
                                    <th width="150">Amount</th>
                                    <th>Label</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="transaction in transactions">
                                    <td v-if="transaction.type=='in'"><i class="fa fa-arrow-right green" aria-hidden="true"></i></td>
                                    <td v-if="transaction.type=='out'"><i class="fa fa-arrow-left red" aria-hidden="true"></i></td>
                                    <td>{{ transaction.amount }}</td>
                                    <td>{{ transaction.label}}</td>
                                    <td> <i class="fa fa-times red" aria-hidden="true" v-on:click="deleteTransaction(transaction.id)"></i> </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    
                </div>
        </div>
    </section>
</template>

<script>
   export default {
    name: 'delete-transaction',
    data: () => {
        return {
            assets: [],
            noxchgOrigins: [],
            assetAmount: 0,
            transactions: [],
            transactionsLoaded: false,
            transactionOperation: "",
            transactionAmount: 0,
            transactionLabel: "",
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
        console.log('Component Transactions mounted.');
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
                console.log("Retieving assets...");
            })
            .catch(e => {
                this.errors.push(e);
               
                console.log("Error: " + e.message);
            });

        }),
        loadAssetData: (function (originId, assetId) {
            // Call the LoadPortfolio event asyncronously
            let uri = '/api/assets/'+assetId;
            axios(uri, {
                method: 'GET',
            })
            .then(response => {
                this.assetAmount = response.data.amount;
                this.assetInitialPrice = response.data.initial_price;
                
                let uri = '/api/transactions/' +originId + '/' + assetId;
                axios(uri, {
                    method: 'GET',
                })
                .then(response => {
                    console.log(response.data);
                    this.transactions = response.data;
                    this.transactionsLoaded = true;

                    if (response.data.length == 0) {
                        this.transactionsLoaded = false;
                    }
                })
                .catch(e => {
                    this.errors.push(e);
                    console.log("Error: " + e.message);
                });
            })
            .catch(e => {
                this.errors.push(e);
               
                console.log("Error: " + e.message);
            });
        }),
        deleteTransaction: (function (id) {
            let uri = '/api/transactions/' + id;
                axios.delete(uri)
                .then(response => {
                    console.log(response.data);
                    window.location.replace('/portfolio');
                })
                .catch(e => {
                    this.errors.push(e);
                    console.log("Error: " + e.message);
                });
        })
    }
}
</script>


