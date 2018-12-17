<template>
        <div class="grid-container fluid">
            <form method="POST" action="/portfolio/origin">
                <input type="hidden" name="_token" :value="csrf">

                <div class="grid-x grid-padding-x">
                    <!-- Header -->
                    <div class="small-12 cell form-container">
                        <p class="h2">New Origin</p>
                        <p class="lead"><b>Add new source to your portfolio</b></p>
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
                    <div v-if="originType=='Exchange'" class="small-12 cell form-container">
                        <div v-if="validationErrors.origin_name">
                           <span class="validation-error" v-for="error in validationErrors.origin_name"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Exchange</span>
                            <select name="origin_name" v-model="exchange" class="input-group-field" v-on:change="getpairs(exchange)">
                                <option disabled value="">Select...</option>
                                <option v-for="exchange in exchanges" :value="exchange" selected="true">{{ exchange }} </option>
                            </select>                     
                        </div>
                    </div>

                    <div v-if="originType!='Exchange'" class="small-12 cell form-container">
                        <div v-if="validationErrors.origin_name">
                           <span class="validation-error" v-for="error in validationErrors.origin_name"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">
                                Name
                            </span>
                            <input name="origin_name"  class="input-group-field" type="text" >
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
                            <input name="origin_address"  class="input-group-field" type="text" >
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
    name: 'add-origin',
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


