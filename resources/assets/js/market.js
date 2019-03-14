require('./bootstrap');
window.Vue = require('vue');
import Notifications from 'vue-notification'

var Decimal = require('decimal.js');
Vue.use(Notifications);
const app = new Vue({
    el: '#app',
    data: {
        user_id: user_id,
        market_id: market_id,
        buy_trades: buy_trades,
        sell_trades: sell_trades,
        sell_trade: {price: 0, amount: 0, amount_base: 0},
        buy_trade: {price: 0, amount: 0, amount_base: 0},
        market_trades: market_trades,
        user_trades: user_trades,
        sell_updating: false,
        buy_updating: false,
        markets: markets,
        wallet_base: wallet_base,
        wallet_trade: wallet_trade,
        notification_enabled: notification_enabled,
        status: 0,
        fee:parseFloat(fee),
        minus:1-parseFloat(fee),
        plus:1+parseFloat(fee)

    },
    mounted() {
        this.calculate_totals();
        if (this.notification_enabled && this.user_id != null) {

            Echo.private('balances.' + this.user_id)
                .listen('.WithdrawalSent', (e) => {
                    //console.log("Sent");
                    this.$notify({
                        group: 'notifications',
                        title: e.data.withdrawal.name + " withdrawal sent",
                        text: "Tx:" + e.data.withdrawal.tx,
                        duration: 3000
                    });

                })
                .listen('.WithdrawalApproved', (e) => {
                    //console.log("Approved");
                    this.$notify({
                        group: 'notifications',
                        title: e.data.withdrawal.name + " withdrawal approved",
                        text: "Withdrawal will be processed soon",
                        duration: 3000
                    });


                })
                .listen('.WithdrawalRequested', (e) => {
                    //console.log("Requested");
                    this.$notify({
                        group: 'notifications',
                        title: e.data.withdrawal.name + " withdrawal requested",
                        text: "Withdrawal will be approved soon",
                        duration: 3000
                    });


                })
                .listen('.TxConfirmation', (e) => {
                    //console.log("Confirmation");
                    this.$notify({
                        group: 'notifications',
                        title: e.data.deposit.name + " deposit confirmation",
                        text: "Deposit received " + e.data.deposit.confirmations + "/" + e.data.deposit.needed_confirmations + " confirmations",
                        duration: 3000
                    });


                })
                .listen('.TxConfirmed', (e) => {
                    //console.log("Confirmed");
                    this.$notify({
                        group: 'notifications',
                        title: e.data.deposit.name + " deposit confirmed",
                        text: "Deposit for " + Decimal(e.data.deposit.value).toPrecision(8).toString() + e.data.deposit.name + " confirmed",
                        duration: 3000
                    });
                    if (e.data.deposit.name == this.wallet_base.name) {
                        this.wallet_base.balance = Decimal(this.wallet_base.balance).add(Decimal(e.data.deposit.value)).toFixed(8).toString();

                    } else if (e.data.deposit.name == this.wallet_trade.name) {
                        this.wallet_trade.balance = Decimal(this.wallet_trade.balance).add(Decimal(e.data.deposit.value)).toFixed(8).toString();

                    }

                })
                .listen('.TxReceived', (e) => {
                    //console.log("Received");
                    this.$notify({
                        group: 'notifications',
                        title: e.data.deposit.name + " deposit received",
                        text: "Deposit for " + Decimal(e.data.deposit.value).toFixed(8).toString() + " " + e.data.deposit.name + " received",
                        duration: 3000
                    });


                });
            Echo.private('trades.' + this.user_id)
                .listen('.NewUserTrade', (e) => {
                    e.data.trade.price = Decimal(e.data.trade.price).toFixed(8);
                    e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8);
                    if (e.data.trade.amount >= 0.00000001) {
                        this.user_trades.unshift(e.data.trade);
                        if (this.user_trades.length > 10) {
                            this.user_trades.pop();
                        }
                    }
                    let str = "";
                    if (e.data.trade.type == 'sell') {
                        str = "bought";
                    }
                    else if (e.data.trade.type == 'buy') {
                        str = "sold";
                    }
                    this.$notify({
                        group: 'notifications',
                        title: e.data.trade.market + " trade successful",
                        text: "Successfully " + str + " " + e.data.trade.amount + e.data.trade.market.split("/")[1],
                        duration: 3000
                    });
                    if (e.data.trade.type == 'sell') {
                        this.wallet_base.balance = Decimal(this.wallet_base.balance).sub(Decimal(e.data.trade.amount).mul(Decimal(e.data.trade.price)).mul(this.plus)).toFixed(8).toString();
                        this.wallet_trade.balance = Decimal(this.wallet_trade.balance).add(Decimal(e.data.trade.amount)).toFixed(8).toString();

                    }
                    else if (e.data.trade.type == 'buy') {
                        this.wallet_trade.balance = Decimal(this.wallet_trade.balance).sub(Decimal(e.data.trade.amount)).toFixed(8).toString();
                        this.wallet_base.balance = Decimal(this.wallet_base.balance).add(Decimal(e.data.trade.amount).mul(Decimal(e.data.trade.price)).mul(this.minus)).toFixed(8).toString();
                    }
                })
                .listen('.TradeDeleted', (e) => {
                    //console.log('delete');
                    //console.log(e);
                    e.data.trade.price = Decimal(e.data.trade.price).toFixed(8);
                    e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8);
                    this.$notify({
                        group: 'notifications',
                        title: e.data.trade.market + " trade deleted",
                        text: "Trade in market " + e.data.trade.market + " deleted",
                        duration: 3000
                    });
                    if (e.data.trade.type == 'sell') {
                        this.wallet_trade.balance = Decimal(this.wallet_trade.balance).add(Decimal(e.data.trade.amount)).toFixed(8).toString();
                    }
                    else if (e.data.trade.type == 'buy') {
                        this.wallet_base.balance = Decimal(this.wallet_base.balance).add(Decimal(e.data.trade.amount).mul(Decimal(e.data.trade.price)).mul(this.plus)).toFixed(8).toString();
                    }
                })
                .listen('.NewTrade', (e) => {
                    //console.log('new');
                    //console.log(e);
                    e.data.trade.price = Decimal(e.data.trade.price).toFixed(8);
                    e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8);
                    this.$notify({
                        group: 'notifications',
                        title: e.data.trade.market + " trade created",
                        text: "Trade in market " + e.data.trade.market + " created",
                        duration: 3000
                    });
                    if (e.data.trade.type == 'sell') {
                        this.wallet_trade.balance = Decimal(this.wallet_trade.balance).sub(Decimal(e.data.trade.amount)).toFixed(8).toString();
                    }
                    else if (e.data.trade.type == 'buy') {
                        this.wallet_base.balance = Decimal(this.wallet_base.balance).sub(Decimal(e.data.trade.amount).mul(Decimal(e.data.trade.price)).mul(this.plus)).toFixed(8).toString();
                    }
                })
                .listen('.TradeSuccessful', (e) => {
                    //console.log('success');
                    //console.log(e);
                    e.data.trade.price = Decimal(e.data.trade.price).toFixed(8);
                    e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8);
                    let str = "";
                    if (e.data.trade.type == 'sell') {
                        str = "sold";
                    }
                    else if (e.data.trade.type == 'buy') {
                        str = "bought";
                    }
                    this.$notify({
                        group: 'notifications',
                        title: e.data.trade.market + " trade successful",
                        text: "Successfully " + str + " " + e.data.trade.amount + " " + e.data.trade.market.split("/")[1],
                        duration: 3000
                    });
                    if (e.data.trade.type == 'sell') {
                        this.wallet_base.balance = Decimal(this.wallet_base.balance).add(Decimal(e.data.trade.amount).mul(Decimal(e.data.trade.price)).mul(this.minus)).toFixed(8).toString();
                    }
                    else if (e.data.trade.type == 'buy') {
                        this.wallet_trade.balance = Decimal(this.wallet_trade.balance).add(Decimal(e.data.trade.amount)).toFixed(8).toString();
                    }
                    if (e.data.trade.amount >= 0.00000001) {
                        this.user_trades.unshift(e.data.trade);
                        if (this.user_trades.length > 10) {
                            this.user_trades.pop();
                        }
                    }
                });
        }

        Echo.channel('market.' + this.market_id)

            .listen('.TradeDeleted', (e) => {
                //console.log('market delete');
                //console.log(e);
                if (e.data.trade.type == 'sell') {
                    let trade = this.sell_trades.find(trade => trade.price == e.data.trade.price);
                    if (trade !== undefined) {
                        trade.amount = Decimal(trade.amount).sub(Decimale.data.trade.amount).toFixed(8);
                        if (trade.amount == 0) {
                            let index = this.sell_trades.indexOf(trade);
                            this.sell_trades.splice(index, 1);
                        }
                    }
                    this.sell_trades_update();
                }
                else if (e.data.trade.type == 'buy') {
                    let trade = this.buy_trades.find(trade => trade.price == e.data.trade.price);
                    if (trade !== undefined) {
                        trade.amount = Decimal(trade.amount).sub(Decimal(e.data.trade.amount)).toFixed(8);
                        if (trade.amount == 0) {
                            let index = this.buy_trades.indexOf(trade);
                            this.buy_trades.splice(index, 1);
                        }
                    }
                    this.buy_trades_update();
                }

            })


            .listen('.NewTrade', (e) => {
                //console.log('market new');
                //console.log(e);
                e.data.trade.price = Decimal(e.data.trade.price).toFixed(8);
                e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8);
                if (e.data.trade.type == 'sell') {
                    let trade = this.sell_trades.find(trade => trade.price == e.data.trade.price);
                    if (trade !== undefined) {
                        trade.amount = Decimal(trade.amount).add(Decimal(e.data.trade.amount)).toFixed(8);
                    }
                    else {
                        if (this.sell_trades.length > 0) {
                            let a = Decimal(e.data.trade.price).sub(Decimal(this.sell_trades[0].price));
                            if (a.lt(0)) {
                                this.sell_trades.unshift(e.data.trade);
                            }

                            else if (Decimal(e.data.trade.price).sub(Decimal(this.sell_trades[this.sell_trades.length - 1].price)).gt(0)) {
                                this.sell_trades.push(e.data.trade);
                            }
                            else {
                                let i = 0;
                                while (i < this.sell_trades.length) {
                                    let a = Decimal(e.data.trade.price).sub(Decimal(this.sell_trades[i].price));
                                    if (a.lt(0)) {
                                        //console.log(i);
                                        this.sell_trades.splice(i, 0, e.data.trade);
                                        break;
                                    }
                                    i++;
                                }

                            }
                        }
                        else {
                            this.sell_trades.push(e.data.trade);
                        }


                    }
                    this.sell_trades_update();
                }
                else if (e.data.trade.type == 'buy') {
                    let trade = this.buy_trades.find(trade => trade.price == e.data.trade.price);
                    if (trade !== undefined) {
                        trade.amount = Decimal(trade.amount).add(Decimal(e.data.trade.amount)).toFixed(8);
                    }
                    else {
                        if (this.buy_trades.length > 0) {
                            let a = Decimal(e.data.trade.price).sub(Decimal(this.buy_trades[0].price));
                            if (a.gt(0)) {
                                this.buy_trades.unshift(e.data.trade);
                            }
                            else if (Decimal(e.data.trade.price).sub(Decimal(this.buy_trades[this.buy_trades.length - 1].price)).lt(0)) {
                                this.buy_trades.push(e.data.trade);
                            }
                            else {
                                let i = 0;
                                while (i < this.buy_trades.length) {
                                    let a = Decimal(e.data.trade.price).sub(this.buy_trades[i].price);
                                    if (a.gt(0)) {
                                        //console.log(i);
                                        this.buy_trades.splice(i, 0, e.data.trade);
                                        break;
                                    }
                                    i++;
                                }
                            }
                        }
                        else {
                            this.buy_trades.push(e.data.trade);
                        }

                    }
                    this.buy_trades_update();
                }
            })
            .listen('.TradeSuccessful', (e) => {
                //console.log('market success');
                //console.log(e);
                e.data.trade.price = Decimal(e.data.trade.price).toFixed(8);
                e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8);
                if (e.data.trade.type == 'sell') {
                    let trade = this.sell_trades.find(trade => trade.price == e.data.trade.price);
                    if (trade !== undefined) {
                        //console.log("e.amount" + e.data.trade.amount);
                        //console.log("Trade.amount" + trade.amount);
                        trade.amount = Decimal(trade.amount).sub(Decimal(e.data.trade.amount_traded)).toFixed(8);
                        if (trade.amount == 0) {
                            let index = this.sell_trades.indexOf(trade);
                            this.sell_trades.splice(index, 1);
                        }
                    }
                    this.sell_trades_update();
                }
                else if (e.data.trade.type == 'buy') {
                    let trade = this.buy_trades.find(trade => trade.price == e.data.trade.price);
                    if (trade !== undefined) {
                        //console.log("e.amount" + e.data.trade.amount);
                        //console.log("Trade.amount" + trade.amount);
                        trade.amount = Decimal(trade.amount).sub(Decimal(e.data.trade.amount_traded)).toFixed(8);
                        if (trade.amount == 0) {
                            let index = this.buy_trades.indexOf(trade);
                            this.buy_trades.splice(index, 1);
                        }
                    }
                    this.buy_trades_update();
                }
                //console.log(e.data.trade);
                if (e.data.trade.amount>=0.00000001)
                {
                    this.market_trades.unshift(e.data.trade);
                    if (this.market_trades.length > 50) {
                        this.market_trades.pop();
                    }
                }

            });
    },
    methods: {
        buy: function () {
            let a = new FormData(this.$refs.buy_form);
            axios({
                method: 'post',
                url: '/exchange',
                data: {
                    price: a.get('price'),
                    amount: a.get('amount'),
                    market_id: a.get('market_id'),
                    type: a.get('type')
                }
            }).then(function (response) {
                if (response.data.status) {
                    app.status = response.data.status;
                }
            });
        },
        sell: function () {
            let a = new FormData(this.$refs.sell_form);
            axios({
                method: 'post',
                url: '/exchange',
                data: {
                    price: a.get('price'),
                    amount: a.get('amount'),
                    market_id: a.get('market_id'),
                    type: a.get('type')
                }
            }).then(function (response) {
                if (response.data.status) {
                    app.status = response.data.status;
                }
            });
        },
        printNormal: function (e) {
            if (typeof(e) == 'number') {
                return e.toFixed(8);
            }
            else return e;
        },
        buy_click: function (trade) {
            this.sell_trade.amount = Decimal(trade.total_amount).toFixed(8);
            this.sell_trade.price = trade.price;
            this.sell_trade.amount_base = Decimal(trade.total_amount_base).toFixed(8);
        },
        sell_click: function (trade) {
            this.buy_trade.amount = Decimal(trade.total_amount).toFixed(8);
            this.buy_trade.price = trade.price;
            this.buy_trade.amount_base = Decimal(trade.total_amount_base).toFixed(8);
        },
        buy_amount: function () {
            if (this.$refs.buy_amount.value > 0) {
                this.buy_trade.amount = Decimal(this.$refs.buy_amount.value).toFixed(8);
                if (this.$refs.buy_price.value > 0)
                    this.buy_trade.amount_base = Decimal(this.buy_trade.amount * this.buy_trade.price).toFixed(8);
            }
        },
        buy_price: function () {
            if (this.$refs.buy_price.value > 0) {
                this.buy_trade.price = Decimal(this.$refs.buy_price.value).toFixed(8);
                if (this.$refs.buy_amount.value > 0)
                    this.buy_trade.amount_base = Decimal(this.buy_trade.price * this.buy_trade.amount).toFixed(8);
            }

        },
        buy_total: function () {
            if (this.$refs.buy_total.value > 0)
                this.buy_trade.amount_base = Decimal(this.$refs.buy_total.value / this.plus).toFixed(8);
            if (this.$refs.buy_price.value > 0)
                this.buy_trade.amount = Decimal(this.buy_trade.amount_base / this.buy_trade.price).toFixed(8);
        },
        sell_amount: function () {
            if (this.$refs.sell_amount.value > 0) {
                this.sell_trade.amount = Decimal(this.$refs.sell_amount.value).toFixed(8);
                if (this.$refs.sell_price.value > 0)
                    this.sell_trade.amount_base = Decimal(this.sell_trade.amount * this.sell_trade.price).toFixed(8);
            }
        },
        sell_price() {
            if (this.$refs.sell_price.value > 0) {
                this.sell_trade.price = Decimal(this.$refs.sell_price.value).toFixed(8);
                if (this.$refs.sell_amount.value > 0)
                    this.sell_trade.amount_base = Decimal(this.sell_trade.price * this.sell_trade.amount).toFixed(8);
            }

        },
        sell_total: function () {
            if (this.$refs.sell_total.value > 0)
                this.sell_trade.amount_base = Decimal(this.$refs.sell_total.value / this.minus).toFixed(8);
            if (this.$refs.sell_price.value > 0)
                this.sell_trade.amount = Decimal(this.sell_trade.amount_base / this.sell_trade.price).toFixed(8);
        },
        calculate_totals: function () {
            if (this.sell_trades.length > 0) {
                this.sell_trades[0].total_amount = Decimal(this.sell_trades[0].amount).toFixed(8);
                this.sell_trades[0].total_amount_base = Decimal(this.sell_trades[0].amount).mul(Decimal(this.sell_trades[0].price)).toFixed(8);
                let q = 1;
                while (q < this.sell_trades.length) {
                    this.sell_trades[q].total_amount = Decimal(this.sell_trades[q - 1].total_amount).add(Decimal(this.sell_trades[q].amount)).toFixed(8);
                    this.sell_trades[q].total_amount_base = Decimal(this.sell_trades[q - 1].total_amount_base).add(Decimal(this.sell_trades[q].amount * this.sell_trades[q].price)).toFixed(8);
                    q++;
                }
            }
            if (this.buy_trades.length > 0) {
                this.buy_trades[0].total_amount = Decimal(this.buy_trades[0].amount).toFixed(8);
                this.buy_trades[0].total_amount_base = Decimal(this.buy_trades[0].amount).mul(Decimal(this.buy_trades[0].price)).toFixed(8);
                let q = 1;
                while (q < this.buy_trades.length) {
                    this.buy_trades[q].total_amount = Decimal(this.buy_trades[q - 1].total_amount).add(Decimal(this.buy_trades[q].amount)).toFixed(8);
                    this.buy_trades[q].total_amount_base = Decimal(this.buy_trades[q - 1].total_amount_base).add(Decimal(this.buy_trades[q].amount * this.buy_trades[q].price)).toFixed(8);
                    q++;
                }
            }

        },
        checkObject: function (e) {
            if (e == undefined || e == null) {
                return false;
            }
            else if (e.length != undefined) {
                return e.length > 0;
            }
            return true;
        },
        buy_trades_update() {
            if (this.buy_trades.length == 10 && !this.buy_updating) {
                this.buy_updating = true;
                axios({
                    method: 'post',
                    url: '/getTrades',
                    data: {
                        market_id: this.market_id,
                        type: "buy",
                        count: this.buy_trades.length
                    }
                }).then(function (response) {
                    if (response != -1) {
                        app.buy_trades = response.data.trades;
                    }
                });
                this.buy_updating = false;
            }
            this.calculate_totals();

        },
        sell_trades_update() {
            if (this.sell_trades.length == 10 && !this.sell_updating) {
                this.sell_updating = true;
                axios({
                    method: 'post',
                    url: '/getTrades',
                    data: {
                        market_id: this.market_id,
                        type: "sell",
                        count: this.sell_trades.length
                    }
                }).then(function (response) {
                    if (response != -1) {
                        app.sell_trades = response.data.trades;
                    }
                });
                this.sell_updating = false;
            }
            this.calculate_totals();

        },
    }
});


