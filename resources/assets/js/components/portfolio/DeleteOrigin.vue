<template>
        <div class="grid-container fluid">
            <form method="POST" :action="'/portfolio/origin/' + originSelected">
                <input type="hidden" name="_token" :value="csrf">
                <input name="_method" type="hidden" value="DELETE">

                <div class="grid-x grid-padding-x">
                    <!-- Header -->
                    <div class="small-12 cell form-container">
                        <p class="h2">Delete Origin</p>
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

                    <div v-if="originSelected != ''" class="small-12 cell form-container">
                        <button class="hollow button" type="submit">
                           Delete Origin
                        </button>
                    </div>
                    
                </div>

            </form>
        </div>
        
</template>

   <script>
   export default {
    name: 'delete-origin',
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


