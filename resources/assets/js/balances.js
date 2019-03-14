require('./bootstrap');
window.Vue = require('vue');
import Notifications from 'vue-notification'
var Decimal = require('decimal.js');
Vue.use(Notifications);
let fee_multiplier=0.998;
const app = new Vue({
    el: '#app',
    data: {
        withdrawals: withdrawals,
        deposits: deposits,
        wallets: wallets,
        trades: trades,
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
                console.log(e.data);
                let withdrawal = this.withdrawals.find(withdrawal => withdrawal.id === e.data.withdrawal.id);
                if (withdrawal === undefined) {
                    e.data.withdrawal.value = Decimal(e.data.withdrawal).toFixed(8).toString();
                    this.withdrawals.unshift(e.data.withdrawal);
                }
                else if (withdrawal.status != "sent") {
                    withdrawal.tx = e.data.withdrawal.tx;
                    withdrawal.status = "sent";
                }


            })
            .listen('.WithdrawalApproved', (e) => {
                console.log("Approved");
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
                console.log(e.data);

            })
            .listen('.WithdrawalRequested', (e) => {
                console.log("Requested");
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
                else if (withdrawal.status != "approved" && withdrawal.status != 'sent') {
                    withdrawal.status = "requested";
                }
                console.log(e.data);

            })
            .listen('.TxConfirmation', (e) => {
                console.log("Confirmation");
                this.$notify({
                    group: 'notifications',
                    title: e.data.deposit.name + " deposit confirmation",
                    text: "Deposit received " + e.data.deposit.confirmations + "/" + e.data.deposit.needed_confirmations + " confirmations",
                    duration: 3000
                });

                let deposit = this.deposits.find(deposit => deposit.id === e.data.deposit.id);
                if (deposit === undefined) {
                    e.data.deposit.value = Decimal(e.data.deposit.value).toFixed(8).toString();
                    this.deposits.unshift(e.data.deposit);
                }
                else if (!deposit.confirmed) {
                    deposit.confirmations = e.data.deposit.confirmations;
                    if (deposit.confirmations >= deposit.needed_confirmations) {
                        deposit.confirmed = true;
                    }
                }
                console.log(e.data);


            })
            .listen('.TxConfirmed', (e) => {
                console.log("Confirmed");
                this.$notify({
                    group: 'notifications',
                    title: e.data.deposit.name + " deposit confirmed",
                    text: "Deposit for " + Decimal(e.data.deposit.value).toPrecision(8).toString() + e.data.deposit.name + " confirmed",
                    duration: 3000
                });
                let deposit = this.deposits.find(deposit => deposit.id === e.data.deposit.id);
                if (deposit === undefined) {
                    e.data.deposit.value = Decimal(e.data.deposit.value).toFixed(8).toString();
                    this.deposits.unshift(e.data.deposit);
                }
                else if (!deposit.confirmed) {
                    deposit.confirmed = true;
                }
                let wallet = this.wallets.find(wallet => wallet.name === e.data.deposit.name);
                if (wallet !== undefined) {
                    wallet.balance = (Decimal(wallet.balance).plus(Decimal(e.data.deposit.value))).toFixed(8).toString();
                }
                console.log(e.data);

            })
            .listen('.TxReceived', (e) => {
                console.log("Received");
                this.$notify({
                    group: 'notifications',
                    title: e.data.deposit.name + " deposit received",
                    text: "Deposit for " + Decimal(e.data.deposit.value).toFixed(8).toString() + " " + e.data.deposit.name + " received",
                    duration: 3000
                });
                let deposit = this.deposits.find(deposit => deposit.id === e.data.deposit.id);
                if (deposit === undefined) {
                    e.data.deposit.value = Decimal(e.data.deposit.value).toFixed(8).toString();
                    this.deposits.unshift(e.data.deposit);
                }
                else if (!deposit.confirmed) {
                    deposit.confirmations = e.data.deposit.confirmations;
                    if (deposit.confirmations >= deposit.needed_confirmations) {
                        deposit.confirmed = true;
                    }
                }
                console.log(e.data);

            });
        Echo.private('trades.' + this.user_id)
            .listen('.NewTrade', (e) => {
                console.log('new trade');
                let trade = this.trades.find(trade => trade.id === e.data.trade.id);
                if (trade === undefined) {
                    e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8).toString();
                    e.data.trade.price = Decimal(e.data.trade.price).toFixed(8).toString();
                    this.trades.unshift(e.data.trade);
                }
                else {
                    e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8).toString();
                    e.data.trade.price = Decimal(e.data.trade.price).toFixed(8).toString();
                    let index = this.trades.indexOf(trade);
                    this.trades.splice(index, 1, e.data.trade);
                }
                let name = e.data.trade.market.split('/');
                if (e.data.trade.type == 'sell') {
                    let trade_coin = name[1];
                    let wallet = this.wallets.find(wallet=>wallet.name===trade_coin);
                    if (wallet!=undefined)
                    {
                        wallet.balance=Decimal(wallet.balance).sub(Decimal(e.data.trade.amount)).toFixed(8).toString();
                    }
                }
                else if (e.data.trade.type == 'buy') {
                    let base_coin= name[0];
                    let wallet = this.wallets.find(wallet=>wallet.name===base_coin);
                    if (wallet!=undefined)
                    {
                        wallet.balance=Decimal(wallet.balance).sub(Decimal(e.data.trade.amount).mul(Decimal(e.data.trade.price))).toFixed(8).toString();
                    }
                }
            })
            .listen('.TradeSuccessful', (e) => {
                console.log('TradeSuccessful');
                let trade = this.trades.find(trade => trade.id === e.data.trade.id);
                if (trade === undefined) {
                    e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8).toString();
                    e.data.trade.price = Decimal(e.data.trade.price).toFixed(8).toString();
                    this.trades.unshift(e.data.trade);
                }
                else {
                    e.data.trade.amount = Decimal(e.data.trade.amount).toFixed(8).toString();
                    e.data.trade.price = Decimal(e.data.trade.price).toFixed(8).toString();
                    let index = this.trades.indexOf(trade);
                    this.trades.splice(index, 1, e.data.trade);
                }
                let name = e.data.trade.market.split('/');
                if (e.data.trade.type == 'sell') {
                    let base_coin = name[0];
                    let wallet = this.wallets.find(wallet=>wallet.name===base_coin);
                    if (wallet!=undefined)
                    {
                        wallet.balance=Decimal(wallet.balance).add(Decimal(e.data.trade.amount).mul(Decimal(e.data.trade.price)).mul(fee_multiplier)).toFixed(8).toString();
                    }
                }
                else if (e.data.trade.type == 'buy') {
                    let trade_coin= name[1];
                    let wallet = this.wallets.find(wallet=>wallet.name===trade_coin);
                    if (wallet!=undefined)
                    {
                        wallet.balance=Decimal(wallet.balance).add(Decimal(e.data.trade.amount).mul(fee_multiplier)).toFixed(8).toString();
                    }
                }
            })
            .listen('.TradeDeleted', (e) => {
                console.log('TradeDeleted');
                let trade = this.trades.find(trade => trade.id === e.data.trade.id);
                console.log(trade);
                if (trade !== undefined) {
                    let index = this.trades.indexOf(trade);
                    console.log(index);
                    this.trades.splice(index, 1);
                }
                let name = e.data.trade.market.split('/');
                if (e.data.trade.type == 'sell') {
                    let trade_coin = name[1];
                    let wallet = this.wallets.find(wallet=>wallet.name===trade_coin);
                    if (wallet!=undefined)
                    {
                        wallet.balance=Decimal(wallet.balance).add(Decimal(e.data.trade.amount)).toFixed(8).toString();
                    }
                }
                else if (e.data.trade.type == 'buy') {
                    let base_coin = name[0];
                    let wallet = this.wallets.find(wallet=>wallet.name===base_coin);
                    if (wallet!=undefined)
                    {
                        wallet.balance=Decimal(wallet.balance).add(Decimal(e.data.trade.amount).mul(Decimal(e.data.trade.price))).toFixed(8).toString();
                    }
                }
            })
    },
    methods: {
        deleteTrade: function (id) {
            axios.post('/deleteTrade', {
                trade: id
            });
        },
    }
});


