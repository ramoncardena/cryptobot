<template>
    <div class="success callout alert-flash" role="alert" v-show="show" data-closable>
        <p class="alert-body">{{ body }}</p>
        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</template>

<script>
export default {
    name: 'Flash',
    props: ['message'],
    data() {
        return {
            body: '',
            show: false
        }
    },
    created() {
        if (this.message) {
            this.flash(this.message);
        }
        window.events.$on(
            'flash', message => this.flash(message)
            );
    },
    methods: {
        flash(message) {
            this.body = message;
            this.show = true;
            this.hide();
        },
        hide() {
            setTimeout(() => {
                this.show = false;
            }, 5000);
        }
    }
};
</script>

<style>
.alert-flash {
    position: fixed;
    right: 25px;
    bottom: 25px;
}
.alert-body {
    padding-left: 1rem;
    padding-right: 2rem;
    padding-top: 0;
    padding-bottom: 0;
    margin: 0;
}
</style>