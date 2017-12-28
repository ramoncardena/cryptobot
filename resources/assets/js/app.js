
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

 require('./bootstrap');

 window.Vue = require('vue');

/**
 * Passport componets 
 */
 Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue')
    );

 Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue')
    );

 Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue')
    );


/**
 * Custom components
 */
 Vue.component('balance', require('./components/Balance.vue'));
 Vue.component('order', require('./components/Order.vue'));
 Vue.component('trade', require('./components/Trade.vue'));
 Vue.component('tradelist', require('./components/TradeList.vue'));
 Vue.component('tradepanel', require('./components/TradePanel.vue'));
 Vue.component('notification-list', require('./components/NotificationList.vue'));
 Vue.component('add-origin', require('./components/AddOrigin.vue'));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
 const app = new Vue({
    el: '#app'
});

 $(document).ready(function () {

    require('./partials/notifications');
    require('./partials/alerts');
    
    $.extend( $.fn.dataTable.defaults, {
     responsive: true
 } );



    $('#activeTradesTable').DataTable();
    // $('#historyTradesTable').DataTable( {
    //     initComplete: function () {
    //         this.api().columns().every( function () {
    //             var column = this;
    //             var select = $('<select><option value=""></option></select>')
    //                 .appendTo( $(column.header()).empty() )
    //                 .on( 'change', function () {
    //                     var val = $.fn.dataTable.util.escapeRegex(
    //                         $(this).val()
    //                     );

    //                     column
    //                         .search( val ? '^'+val+'$' : '', true, false )
    //                         .draw();
    //                 } );

    //             column.data().unique().sort().each( function ( d, j ) {
    //                 select.append( '<option value="'+d+'">'+d+'</option>' )
    //             } );
    //         } );
    //     }
    // } );
    
    var historyTable = $('#historyTradesTable').DataTable();
    $("#historyTradesTable tfoot th").each( function ( i ) {
        if (i==2 || i==3 || i==4 || i==5) {
            var select = $('<select><option value=""></option></select>')
            .appendTo( $(this).empty() )
            .on( 'change', function () {
                historyTable.column( i )
                .search( $(this).val() )
                .draw();
            } );
            
            historyTable.column( i ).data().unique().sort().each( function ( d, j ) {
                select.append( '<option value="'+d+'">'+d+'</option>' )
            } );
        }
    } );



});
