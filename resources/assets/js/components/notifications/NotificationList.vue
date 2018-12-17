<template>
    <div class="notification-list">
        <button class="hollow button"> Mark all as read</button>
        <button class="hollow button"> Delete all</button>
         <table class="display dataTable myTable" width="100%">
            <thead class="dataTable-header">
                <tr role="row">
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1"></th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Time</th>
                    <th class="sorting" tabindex="0" rowspan="1" colspan="1">Message</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="notification in notifications">
                    <td v-if=" notification.read_at == null"> <i class="fa fa-check item-new" aria-hidden="true" v-on:click="markAsRead(notification.id)"></i></td>
                    <td v-if="notification.read_at != null"> <i class="fa fa-check item-check" aria-hidden="true"></i> </td>
                    <td>{{ notification.updated_at }}</td>
                    <td> {{ notification.data.message }}</td>
                </tr>
            </tbody>
        </table>

    </div>
</template>

<script>
   export default {
    name: 'notification-list',
    props: [
        'notifications'
    ],
    data: () => {
        return {
           
        }
    },
    computed: {
        
    },
    mounted() {
        console.log('Component Notifications mounted.');
    },
    methods: {
        markAsRead(id) {
            axios('/api/notifications/' + id + '/markasread', {
                method: 'GET',
            })
            .then(response => {
                $(window).trigger('resize');
                console.log("Success: " + response);

            })
            .catch(e => {
                console.log("Error: " +  e.message);
            });
        }        
    }
}
</script>


