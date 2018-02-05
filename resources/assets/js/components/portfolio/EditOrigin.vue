<template>
        <div class="grid-container fluid">
            <form method="POST" :action="'/portfolio/origin/' + originSelected">
                <input type="hidden" name="_token" :value="csrf">
                <input name="_method" type="hidden" value="PATCH">

                <div class="grid-x grid-padding-x">
                    <!-- Header -->
                    <div class="small-12 cell form-container">
                        <p class="h2">Edit Origin</p>
                    </div>
                    <div class="small-12 cell form-container">
                        <div v-if="validationErrors.origin_type">
                           <span class="validation-error" v-for="error in validationErrors.origin_type"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Origin</span>
                            <select v-model="originSelected" name="origin" class="input-group-field" v-on:change="loadOrigin()">
                                <option disabled value="">Select...</option>
                                <option v-for="origin in noxchgOrigins" :value="origin.id">{{ origin.name }} </option>
                            </select>       
                            <input v-model="originSelectedName" id="asset-origin-name" name="asset_origin_name" type="hidden">              
                        </div>
                    </div>
                    <div class="small-12 cell form-container">
                        <div v-if="validationErrors.origin_type">
                           <span class="validation-error" v-for="error in validationErrors.origin_type"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Origin Type</span>
                            <select v-model="originType" name="origin_type" class="input-group-field">
                                <option disabled value="">Select...</option>
                                <option v-for="originType in originTypes" :value="originType">{{ originType }} </option>
                            </select>                     
                        </div>
                    </div>

                    <div class="small-12 cell form-container">
                        <div v-if="validationErrors.origin_name">
                           <span class="validation-error" v-for="error in validationErrors.origin_name"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">
                                Name
                            </span>
                            <input v-model="originName" name="origin_name"  class="input-group-field" type="text" >
                        </div>
                    </div>

                    <div v-if="originType!='Exchange'" class="small-12 cell form-container">
                        <div v-if="validationErrors.origin_address">
                           <span class="validation-error" v-for="error in validationErrors.origin_address"> {{ error }} </span>
                        </div>
                         <div class="input-group">
                            <span class="input-group-label">
                                Address
                            </span>
                            <input v-model="originAddress" name="origin_address"  class="input-group-field" type="text" >
                        </div>
                    </div>

                    <div class="small-12 cell form-container">
                        <button class="hollow button" type="submit">
                           Save Origin
                        </button>
                    </div>
                    
                </div>

            </form>
        </div>
        
</template>

   <script>
   export default {
    name: 'edit-origin',
    data: () => {
        return {
            exchange: "",
            noxchgOrigins: [],
            origin: {},
            originSelected: "",
            originSelectedName: "",
            originType: "",
            originName: "",
            originAddress: "",
            updating: false,
            csrf: "",
            errors: []
        }
    },
    props: [
    'exchanges',
    'origins',
    'origin-types',
    'validation-errors'
    ],
    computed: {

    },
    mounted() {

        for (var i = 0; i < this.origins.length; i++) {    
           if (this.exchanges.indexOf(this.origins[i].name.toLowerCase()) < 0) {
                this.noxchgOrigins.push(this.origins[i]);
           }
        }


        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('Component AddOrigin mounted.');
    },
    methods: {
        loadOrigin: (function () {
            console.log("Loading Origin...");
            // for (var i = 0; i < this.origins.length; i++) {     
            //     if (this.origins[i].id == this.originSelected) this.originSelectedName = this.origins[i].name;
            // }

            for (var i = 0; i < this.origins.length; i++) {     
                if (this.origins[i].id == this.originSelected) this.origin = this.origins[i];
            }
            this.originType = this.origin.type;
            this.originName = this.origin.name;
            this.originAddress = this.origin.address;

        }),
        saveName: (function () {
            
        })
    }
}
</script>


