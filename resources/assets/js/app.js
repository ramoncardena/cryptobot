
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

 Vue.component('trade', require('./components/trades/Trade.vue'));
 Vue.component('tradelist', require('./components/trades/TradeList.vue'));
 Vue.component('tradepanel', require('./components/trades/TradePanel.vue'));

 Vue.component('add-origin', require('./components/portfolio/AddOrigin.vue'));
 Vue.component('edit-origin', require('./components/portfolio/EditOrigin.vue'));
 Vue.component('delete-origin', require('./components/portfolio/DeleteOrigin.vue'));
 Vue.component('add-asset', require('./components/portfolio/AddAsset.vue'));
 Vue.component('edit-asset', require('./components/portfolio/EditAsset.vue'));
 Vue.component('delete-asset', require('./components/portfolio/DeleteAsset.vue'));
 Vue.component('add-transaction', require('./components/portfolio/AddTransaction.vue'));
 Vue.component('delete-transaction', require('./components/portfolio/DeleteTransaction.vue'));
 Vue.component('portfolio', require('./components/portfolio/Portfolio.vue'));
 Vue.component('asset', require('./components/portfolio/Asset.vue'));


 Vue.component('coin-card', require('./components/dashboard/CoinCard.vue'));

 Vue.component('notification-list', require('./components/notifications/NotificationList.vue'));
 Vue.component('connections', require('./components/connections/Connections.vue'));
 Vue.component('invite-panel', require('./components/invitations/InvitePanel.vue'));

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
    require('./partials/portfolio');

    
    $.extend( $.fn.dataTable.defaults, {
       responsive: true
   } );


    // var portfolioTable = $('#portfolioTable').DataTable( {
    //     "searching": false,
    //     "paging": false,
    //     "info": false,
    // } );

    $('#activeTradesTable').DataTable();

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
