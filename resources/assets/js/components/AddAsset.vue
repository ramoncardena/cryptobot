<template>
        <div class="grid-container fluid">
            <form method="POST" action="/portfolio/origin">
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
                            <select v-model="origin" name="origin" class="input-group-field">
                                <option disabled value="">Select...</option>
                                <option v-for="origin in origins" :value="origin">{{ origin }} </option>
                            </select>                     
                        </div>
                    </div>
                    

                    <div class="small-12 cell form-container">
                        <div v-if="validationErrors.origin_name">
                           <span class="validation-error" v-for="error in validationErrors.origin_name"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">
                                Asset
                            </span>
                            <input name="origin_name"  class="input-group-field" type="text" >
                        </div>
                    </div>
                    <div class="small-12 cell form-container">
                        <div v-if="validationErrors.origin_address">
                           <span class="validation-error" v-for="error in validationErrors.origin_address"> {{ error }} </span>
                        </div>
                         <div class="input-group">
                            <span class="input-group-label">
                                Amount
                            </span>
                            <input name="origin_address"  class="input-group-field number" type="text" >
                        </div>
                    </div>

                    <div class="small-12 cell form-container">
                        <button class="hollow button" type="submit">
                           Add Origin
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
            exchange: "",
            originType: "",
            updating: false,
            csrf: ""
        }
    },
    props: [
    'exchanges',
    'origin-types',
    'validation-errors'
    ],
    computed: {

    },
    mounted() {
        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('Component AddOrigin mounted.');
    },
    methods: {

    }
}
</script>


