require('./bootstrap');
window.Vue = require('vue');
import Notifications from 'vue-notification'

Vue.use(Notifications);

const app = new Vue({
    el: '#app',
    data: {
        withdrawals: withdrawals,
        user_id: user_id,
        page: 0,
        pages: pages,
        notification_enabled: notification_enabled
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
                    let withdrawal = this.withdrawals.find(withdrawal => withdrawal.id === e.data.withdrawal.id);
                    if (withdrawal === undefined) {
                        e.data.withdrawal.value = Decimal(e.data.withdrawal).toFixed(8).toString();
                        this.withdrawals.unshift(e.data.withdrawal);
                    }
                    else if (withdrawal.status == "requested") {
                        withdrawal.status = "approved";
                    }

                })
                .listen('.WithdrawalApproved', (e) => {
                    this.$notify({
                        group: 'notifications',
                        title: e.data.withdrawal.name + " withdrawal approved",
                        text: "Withdrawal will be processed soon",
                        duration: 3000
                    });
                    let withdrawal = this.withdrawals.find(withdrawal => withdrawal.id === e.data.withdrawal.id);
                    if (withdrawal === undefined) {
                        e.data.withdrawal.value = Decimal(e.data.withdrawal).toFixed(8).toString();
                        this.withdrawals.unshift(e.data.withdrawal);
                    }
                    else if (withdrawal.status == "requested") {
                        withdrawal.status = "approved";
                    }

                })
                .listen('.WithdrawalRequested', (e) => {
                    this.$notify({
                        group: 'notifications',
                        title: e.data.withdrawal.name + " withdrawal requested",
                        text: "Withdrawal will be approved soon",
                        duration: 3000
                    });
                    let withdrawal = this.withdrawals.find(withdrawal => withdrawal.id === e.data.withdrawal.id);
                    if (withdrawal === undefined) {
                        e.data.withdrawal.value = Decimal(e.data.withdrawal).toFixed(8).toString();
                        this.withdrawals.unshift(e.data.withdrawal);
                    }
                    else if (withdrawal.status == "requested") {
                        withdrawal.status = "approved";
                    }

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
                    let wallet = this.wallets.find(wallet => wallet.name === e.data.deposit.name);
                    if (wallet !== undefined) {
                        wallet.balance = (Decimal(wallet.balance).plus(Decimal(e.data.deposit.value))).toFixed(8).toString();
                    }

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
    methods: {
        nextPage() {
            if (app.page != pages) {
                app.page++;
                axios.post('/withdrawalHistory', {
                    page: app.page,
                }).then(function (response) {
                    app.withdrawals = response.data.withdrawals;
                });
            }

        },
        prevPage() {
            if (app.page != 0) {
                app.page--;
                axios.post('/withdrawalHistory', {
                    page: app.page,
                }).then(function (response) {
                    app.withdrawals = response.data.withdrawals;
                });
            }
        },
        firstPage() {
            if (app.page != 0) {
                app.page = 0;
                axios.post('/withdrawalHistory', {
                    page: app.page,
                }).then(function (response) {
                    app.withdrawals = response.data.withdrawals;
                });
            }

        },
        lastPage() {
            if (app.page != app.pages) {
                app.page = app.pages;
                axios.post('/withdrawalHistory', {
                    page: app.page,
                }).then(function (response) {
                    app.withdrawals = response.data.withdrawals;
                });
            }

        },
        cancelWithdrawal(id)
        {
            axios.post('/withdrawalCancel', {
                id: id
            }).then(function (response) {
                if (response.data == 1) {
                    let withdrawal = app.withdrawals.find(withdrawal => withdrawal.id === id);
                    let index = app.withdrawals.indexOf(withdrawal);
                    app.withdrawals.splice(index, 1);
                }
            });
        }
    },
});


