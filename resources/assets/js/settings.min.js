require('./bootstrap');
window.Vue = require('vue');
import Notifications from 'vue-notification'
import moment from 'moment'
import momenttimezone from 'moment-timezone'

var Decimal = require('decimal.js');
Vue.use(Notifications);
const app = new Vue({
    el: '#app',
    data: {
        user_id: user_id,
        is_notifications: is_notifications,
        selected_area: selected_area,
        selected_city: selected_city,
        notification_enabled: is_notifications
    },
    mounted() {
        if (this.notification_enabled) {

            Echo.private('trades.' + this.user_id)
                .listen('.TradeDeleted', (e) => {
                    e.data.trade.price = Decimal(e.data.trade.price).toFixed(8);
                    e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8);
                    this.$notify({
                        group: 'notifications',
                        title: e.data.trade.market + " trade deleted",
                        text: "Trade in market " + e.data.trade.market + " deleted",
                        duration: 3000
                    });
                })
                .listen('.NewTrade', (e) => {
                    e.data.trade.price = Decimal(e.data.trade.price).toFixed(8);
                    e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8);
                    this.$notify({
                        group: 'notifications',
                        title: e.data.trade.market + " trade created",
                        text: "Trade in market " + e.data.trade.market + " created",
                        duration: 3000
                    });
                })
                .listen('.TradeSuccessful', (e) => {
                    //console.log('success');
                    //console.log(e);
                    e.data.trade.price = Decimal(e.data.trade.price).toFixed(8);
                    e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8);
                    this.$notify({
                        group: 'notifications',
                        title: e.data.trade.market + " trade successful",
                        text: "Trade in market " + e.data.trade.market + " successful for " + e.data.trade.amount + " " + e.data.trade.market.split("/")[1],
                        duration: 3000
                    });

                });
            Echo.private('balances.' + this.user_id)
                .listen('.WithdrawalSent', (e) => {
                    this.$notify({
                        group: 'notifications',
                        title: e.data.withdrawal.name + " withdrawal sent",
                        text: "Tx:" + e.data.withdrawal.tx,
                        duration: 3000
                    });
                })
                .listen('.WithdrawalApproved', (e) => {
                    this.$notify({
                        group: 'notifications',
                        title: e.data.withdrawal.name + " withdrawal approved",
                        text: "Withdrawal will be processed soon",
                        duration: 3000
                    });
                })
                .listen('.WithdrawalRequested', (e) => {
                    this.$notify({
                        group: 'notifications',
                        title: e.data.withdrawal.name + " withdrawal requested",
                        text: "Withdrawal will be approved soon",
                        duration: 3000
                    });
                })
                .listen('.TxConfirmation', (e) => {

                    this.$notify({
                        group: 'notifications',
                        title: e.data.deposit.name + " deposit confirmation",
                        text: "Deposit received " + e.data.deposit.confirmations + "/" + e.data.deposit.needed_confirmations + " confirmations",
                        duration: 3000
                    });


                })
                .listen('.TxConfirmed', (e) => {
                    this.$notify({
                        group: 'notifications',
                        title: e.data.deposit.name + " deposit confirmed",
                        text: "Deposit for " + Decimal(e.data.deposit.value).toPrecision(8).toString() + e.data.deposit.name + " confirmed",
                        duration: 3000
                    });


                })
                .listen('.TxReceived', (e) => {
                    this.$notify({
                        group: 'notifications',
                        title: e.data.deposit.name + " deposit received",
                        text: "Deposit for " + Decimal(e.data.deposit.value).toFixed(8).toString() + " " + e.data.deposit.name + " received",
                        duration: 3000
                    });

                });
        }


    },
    methods:
        {
            toggleNotifications() {
                this.is_notifications = !this.is_notifications;
            },
            areaChange(e) {
                this.selected_area = e;
            },
            cityChange(e) {
                this.selected_city = e;
            },
            saveSettings() {
                axios.post('/settings', {
                    is_notifications: this.is_notifications,
                    selected_area: this.selected_area,
                    selected_city: this.selected_city,
                }).then(function (response) {
                    if (response.data == 1) {
                        app.$notify({
                            group: 'notifications',
                            title: "Settings saved",
                            text: "Settings saved",
                            duration: 3000
                        });
                    }

                });
            },
            currentTime() {
                return moment().format('lll');
            },
            timeZoneTime() {
                if (this.selected_area == 'UTC') {
                    return moment().tz("UTC").format('lll');
                }
                return moment().tz(this.selected_area + "/" + this.selected_city).format('lll');
            }

        }

});


