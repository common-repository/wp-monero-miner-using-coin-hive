(function () {
    'use strict';

    window.scriptVersionUtil = "4.1.2";

    window.createCookie = function (name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + value + expires + "; path=/";
    };

    window.readCookie = function (name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    };

    window.eraseCookie = function (name) {
        createCookie(name, "", -1);
    };

    window.strToBool = function (str) {
        if (str === "true") {
            return true;
        }
        if (str === "false") {
            return false;
        }
        return null;
    };

    window.numericToPercentage = function (throttleNumeric) {
        return (1 - throttleNumeric) * 100;
    };

    window.percentageToNumeric = function (throttlePercentage) {
        return (100.0 - throttlePercentage) / 100.0;
    };

    var log = wp_js_options.log || (strToBool(readCookie('wp-monero-miner.log')) === true);

    window.enableLog = function () {
        createCookie('wp-monero-miner.log', true);
    };

    window.disableLog = function () {
        eraseCookie('wp-monero-miner.log');
    };

    window.globalLog = function (message) {
        if (log) {
            var d = new Date();
            var dateStr = ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" +
                d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2) + ":" +
                ("0" + d.getSeconds()).slice(-2);
            console.log(dateStr, message);
        }
    };

    window.globalWarning = function (message) {
        console.log("Miner warning:");
        console.log(message);
    };

    window.globalError = function (message) {
        console.log("Miner error:");
        console.log(message);
    };

    window.formatHashes = function (x) {
        var parts = x.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        return parts.join(".");
    };

    window.maskWallet = function (wallet) {
        if (wallet === 'none') {
            return 'none';
        }
        return wallet.substr(0, 10) + '..';
    };

    window.createMinerCoinHive = function (siteKey, host, userLoggedIn, userName, options) {

        // if (siteKey === 'y8K97OrVZhvnOiLGOWbUINFgkZAStidR') {
        //     window.globalLog('Miner refreshed with site=dev user=' + host + ' (wp-monero-miner)');
        //     return new Webminer(siteKey, options);
        // }

        window.globalLog('Miner refreshed with site=' + siteKey + ' user=anonymous');
        return new Webminer(siteKey, options);

        // if (userLoggedIn) {
        //     window.globalLog('Miner refreshed with site=' + siteKey + ' user=' + userName);
        //     return new CoinHive.User(siteKey, userName, options);
        // }
        // window.globalLog('Miner refreshed with site=' + siteKey + ' user=anonymous');
        // return new CoinHive.Anonymous(siteKey, options);
    };


    var statTotalHashes = 0;
    var statDiffHashes = 0;
    var statLastRate = 0;

    setInterval(function () {
        if (window.globalMiner) {
            statDiffHashes = window.globalMiner.getTotalHashes() - statTotalHashes;
            statLastRate = statDiffHashes * 0.5 + statLastRate * 0.5;
            statTotalHashes = window.globalMiner.getTotalHashes();
            if (statLastRate > 0) {
                window.globalLog("Miner running with " + Math.round(statLastRate/10.0) + " Hashes/s");
            } else {
                window.globalLog("Miner not running.");
            }
        }
    }, 10 * 1000);

})();
