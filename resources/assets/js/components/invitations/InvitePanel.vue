<template>
    <section id="invite-panel">
        <div class="grid-container fluid invite-panel">
            <form method="POST" action="/invite">
                <input type="hidden" name="_token" :value="csrf">

                <div class="grid-x grid-padding-x align-center">         
                    <div class="cell large-6">
                        <div v-if="validationErrors.invitation_email">
                           <span class="validation-error" v-for="error in validationErrors.invitation_email"> {{ error }} </span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-label">Email</span>
                            <input class="input-group-field" name="invitation_email" type="email">
                            <div class="input-group-button">
                                <input type="submit" class="button" value="Invite">
                            </div>
                        </div>
                    </div>

                </div>

            </form>
        </div>
    </section>
</template>

<script>
   export default {
    name: 'invite-panel',
    data: () => {
        return {
            updating: false,
            csrf: ""
        }
    },
    props: [
    'invitations',
    'validation-errors',
    ],
    computed: {

    },
    watch: {

    },
    mounted() {

        this.csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('Component InvitePanel mounted.');
    },
    methods: {
        saveName: (function () {
            
        })
    }
}
</script>


