<template>
    <div class="balance">
        <div class="grid-x">
            <div class="cell">
                <div class="panel panel-default">
                    <div class="panel-heading">Example Component</div>

                    <div class="panel-body">
                        {{ message }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Balance',
        data: () => {
            return {
                message: 'This is balance component'
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>


