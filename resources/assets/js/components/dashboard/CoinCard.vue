<template>
    <section id="coin-card">
        <div class="grid-container fluid">

            <div class="grid-x grid-padding-x align-center">         
                <div class="cell large-4">
                    <div class="card">
                        <div class="card-divider">
                            ADA (Cardano)
                        </div>
                        <img src="/storage/cryptobot-logo-300px.png">
                        <div class="card-section">
                            <h4>This is a card.</h4>
                            <p>It has an easy to override visual style, and is appropriately subdued.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</template>

<script>
   export default {
    name: 'coin-card',
    data: () => {
        return {
            updating: false,
            csrf: ""
        }
    },
    props: [
    'coin',
    ],
    computed: {

    },
    watch: {

    },
    mounted() {

        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('Component CoinCard mounted.');
    },
    methods: {
        saveName: (function () {
            
        })
    }
}
</script>
