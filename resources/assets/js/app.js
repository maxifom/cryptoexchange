/*
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
*/

require('./bootstrap');

window.Vue = require('vue');
import Notifications from 'vue-notification'

var Decimal = require('decimal.js');
Vue.use(Notifications);
/*
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
*/
let path = window.location.pathname;
const app = new Vue({
    el: '#app',
    data: {
        user_id: user_id
    },
    mounted() {
        /*Echo.channel('block-tracker')
            .listen('.NewBlock', (e) => {
                this.$notify({
                    group: 'notifications',
                    title: 'New ' + e.data.name + ' block',
                    text: e.data.block,
                    duration: 3000
                });
                console.log(e.data);
                //let wallet = this.wallets.find(wallet => wallet.name === e.data.name);
                //if (wallet != undefined) wallet.name = "Changed";
            });
            */
        Echo.private('balances.' + this.user_id)
            .listen('.WithdrawalSent', (e) => {
                console.log("Sent");
                this.$notify({
                    group: 'notifications',
                    title: e.data.withdrawal.name + " withdrawal sent",
                    text: "Tx:" + e.data.withdrawal.tx,
                    duration: 3000
                });


            })
            .listen('.WithdrawalApproved', (e) => {
                console.log("Approved");
                this.$notify({
                    group: 'notifications',
                    title: e.data.withdrawal.name + " withdrawal approved",
                    text: "Withdrawal will be processed soon",
                    duration: 3000
                });


            })
            .listen('.WithdrawalRequested', (e) => {
                console.log("Requested");
                this.$notify({
                    group: 'notifications',
                    title: e.data.withdrawal.name + " withdrawal requested",
                    text: "Withdrawal will be approved soon",
                    duration: 3000
                });


            })
            .listen('.TxConfirmation', (e) => {
                console.log("Confirmation");
                this.$notify({
                    group: 'notifications',
                    title: e.data.deposit.name + " deposit confirmation",
                    text: "Deposit received " + e.data.deposit.confirmations + "/" + e.data.deposit.needed_confirmations + " confirmations",
                    duration: 3000
                });


            })
            .listen('.TxConfirmed', (e) => {
                console.log("Confirmed");
                this.$notify({
                    group: 'notifications',
                    title: e.data.deposit.name + " deposit confirmed",
                    text: "Deposit for " + Decimal(e.data.deposit.value).toPrecision(8).toString() + e.data.deposit.name + " confirmed",
                    duration: 3000
                });


            })
            .listen('.TxReceived', (e) => {
                console.log("Received");
                this.$notify({
                    group: 'notifications',
                    title: e.data.deposit.name + " deposit received",
                    text: "Deposit for " + Decimal(e.data.deposit.value).toFixed(8).toString() + " " + e.data.deposit.name + " received",
                    duration: 3000
                });


            });
        Echo.private('trades.' + this.user_id)
            .listen('.TradeDeleted', (e) => {
                console.log('delete');
                console.log(e);
            })
            .listen('.NewTrade', (e) => {
                console.log('new');
                console.log(e);
            })
            .listen('.TradeSuccessful', (e) => {
                console.log('success');
                console.log(e);
            });
    },
});


