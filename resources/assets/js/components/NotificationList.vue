<template>
    <div class="notification-list">
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
                    <td width="10px"  v-if=" notification.read_at == null"> <i class="fa fa-check item-new" aria-hidden="true"></i></td>
                    <td width="20px" class="warning" v-if="notification.read_at != null"> <i class="fa fa-check item-check" aria-hidden="true"></i> </td>
                    <td width="20%">{{ notification.updated_at }}</td>
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
        parseType(type) {
            console.log(type.substr(type.lastIndexOf('\\') + 1));

            return  type.substr(type.lastIndexOf('\\') + 1);

        }
        
    }
}
</script>


