require('./bootstrap');
window.Vue = require('vue');
import Notifications from 'vue-notification'

var Decimal = require('decimal.js');
Vue.use(Notifications);
const app = new Vue({
    el: '#app',
    data: {
        trades:trades,
        user_id: user_id,
        pages: pages,
        page: 0,
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
                    let trade = this.trades.find(trade => trade.id === e.data.trade.id);
                    let index = this.trades.indexOf(trade);
                    this.trades.splice(index, 1);
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
                    this.trades.unshift(e.data.trade);
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
                    let trade = this.trades.find(trade => trade.id === e.data.trade.id);
                    trade.amount_traded = Decimal(e.data.trade.amount_trade).toFixed(8);
                    if (Decimal(trade.amount_traded).equals(Decimal(trade.amount)) || e.data.trade.finished == true) {
                        let index = this.trades.indexOf(trade);
                        this.trades.splice(index, 1);
                    }
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
            deleteTrade: function (id) {
                axios.post('/deleteTrade', {
                    trade: id
                }).then(function (response) {
                    if (response.data == 1 && app.trades.length <= 5) {
                        app.updateTrades();
                    }
                });
            },
            updateTrades() {
                axios.post('/trades', {
                    page: app.page,
                }).then(function (response) {
                    app.trades = response.data.trades;
                });
            },
            nextPage() {
                if (app.page != pages) {
                    app.page++;
                    axios.post('/trades', {
                        page: app.page,
                    }).then(function (response) {
                        app.trades = response.data.trades;
                    });
                }

            },
            prevPage() {
                if (app.page != 0) {
                    app.page--;
                    axios.post('/trades', {
                        page: app.page,
                    }).then(function (response) {
                        app.trades = response.data.trades;
                    });
                }
            },
            firstPage() {
                if (app.page != 0) {
                    app.page = 0;
                    axios.post('/trades', {
                        page: app.page,
                    }).then(function (response) {
                        app.trades = response.data.trades;
                    });
                }

            },
            lastPage() {
                if (app.page != app.pages) {
                    app.page = app.pages;
                    axios.post('/trades', {
                        page: app.page,
                    }).then(function (response) {
                        app.trades = response.data.trades;
                    });
                }


            }
        }

        });


