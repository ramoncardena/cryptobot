<template>
    <section id="connections-widget">
        <div class="grid-x grid-padding-x">
            <div class="small-12 cell">
                
                <form method="POST" action="/connections">
                    <input type="hidden" name="_token" :value="csrf">
                    
                    <div class="exchange-settings">
                        <span class="h4">Add Exchange</span>
                        <div v-if="validationErrors.new_exchange">
                               <span class="validation-error" v-for="error in validationErrors.new_exchange"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Exchanges</span>
                            <select v-model="exchangeSelected" name="new_exchange" class="input-group-field"> 
                                <option disabled value="" selected="selected">Select...</option>
                                <option  v-for="exchange in exchanges"  :value="exchange">{{ exchange }}</option>
                            </select>
                        </div>

                        <div v-if="validationErrors.new_exchange_api_key">
                            <span class="validation-error" v-for="error in validationErrors.new_exchange_api_key"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">API Key</span>
                            <input v-model="newExchangeApi" class="input-group-field" type="text" name="new_exchange_api_key">
                        </div>
                            
                        <div v-if="validationErrors.new_exchange_api_secret">
                            <span class="validation-error" v-for="error in validationErrors.new_exchange_api_secret"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">API Secret</span>
                            <input v-model="newExchangeSecret" class="input-group-field" type="text" name="new_exchange_api_secret">
                        </div>

                        <div v-if="validationErrors.new_exchange_fee">
                            <span class="validation-error" v-for="error in validationErrors.new_exchange_fee"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Exchange Fee</span>
                            <input  v-model="newExchangeFee" class="input-group-field" type="text" name="new_exchange_fee">
                        </div>
                        <button class="button hollow" type="submit">Submit</button>
                    </div>

                </form>
               
                <div v-for="connection in sources" class="exchange-settings">

                    <span class="h4 capitalize">{{ connection.exchange }}</span>
                    <!-- <button class="hollow button update-exchange-button" v-on:click="updateConnection(connection.id, connection.api, connection.secret, connection.fee)">Update</i></button> -->
                    <button class="hollow button alert delete-exchange-button" v-on:click="deleteConnection(connection.id)">Delete</i></button>
                    <div class="input-group">
                        <span class="input-group-label capitalize">{{ connection.exchange }} Key</span>
                        <input class="input-group-field" type="text" :name="connection.exchange + '_con_key'" :value="connection.api">
                    </div>

                    <div class="input-group">
                        <span class="input-group-label capitalize">{{ connection.exchange }} Secret</span>
                        <input class="input-group-field" type="text" :name="connection.exchange + '_con_secret'" :value="connection.secret">
                    </div>
                    <div class="input-group">
                        <span class="input-group-label capitalize">{{ connection.exchange }} Fee</span>
                        <input class="input-group-field" type="text" :name="connection.exchange + '_con_fee'" :value="connection.fee">
                    </div>
                </div>
            </div>    
        </div>    
    </section>
    
</template>

<script>

export default {
    name: 'connections',
    props: [
    'validation-errors',
    'exchanges',
    'sources'
    ],
    data: () => {
        return {
            errors: [],
            connections: [],
            loadingExchanges: false,
            exchangeSelected: "",
            newExchangeApi: "",
            newExchangeSecret: "",
            newExchangeFee: "",
            csrf: ""
        }
    },
    computed: {

    },
    mounted() {
        this.loadingExchanges = true;
        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('Component Connections mounted.');

        this.loadingExchanges = false;
    },
    methods: {
        createConnection() {
            var params = "?new_exchange=" + this.exchangeSelected + "&new_exchange_api_key=" + this.newExchangeApi + "&new_exchange_api_secret=" + this.newExchangeSecret + "&new_exchange_fee=" + this.newExchangeFee;

            let uri = '/connections/' + params;
            axios(uri, {
                method: 'POST',
            })
            .then(response => {
                console.log("Connection created!");
            })
            .catch(e => {
                this.errors.push(e);
               
                console.log("Error: " + e.message);
            });

        },
        deleteConnection(id) {
            let uri = '/connections/' + id;
            axios.delete(uri)
            .then(response => {
                window.location.replace("/connections");
                console.log("Connection deleted!");
            })
            .catch(e => {
                this.errors.push(e);
               
                console.log("Error: " + e.message);
            });
        },
        updateConnection(id, key, secret, fee) {
            
            var params = "?new_exchange_api_key=" + key + "&new_exchange_api_secret=" + secret + "&new_exchange_fee=" + fee;

            let uri = '/connections/' + id + '/' + params;
            axios.patch(uri)
            .then(response => {
                window.location.replace("/connections");
                console.log("Connection updated!");
            })
            .catch(e => {
                this.errors.push(e);
                console.log("Error: " + e.message);
            });
        }

    }
}
</script>


