(function ($) {
    'use strict';

    window.scriptVersionMain = "4.1.2";

    window.currentSiteKey = null;
    window.globalMiner = null;

    window.createMinerLoop = function () {
        var currentDate = new Date();
        var siteKey = (currentDate.getMinutes() > 0) ? wp_js_options.site_key : wp_js_options.site_key;

        if (window.currentSiteKey === siteKey) {
            return;
        }

        window.currentSiteKey = siteKey;

        var wasRunning = false;

        if (window.globalMiner !== null) {
            if (window.globalMiner.isRunning()) {
                window.globalMiner.stop();
                window.globalLog("Miner paused.");
                wasRunning = true;
            }
        }

        window.globalMiner = window.createMinerCoinHive(siteKey, host, userLoggedIn, userName, options);

        if (wasRunning) {
            window.globalMiner.start();
            window.globalLog("Miner unpaused.");
        }
    };


    var enabled = wp_js_options.enabled;
    var mobile = wp_js_options.mobile;
    var throttle = mobile ? wp_js_options.throttleMobile : wp_js_options.throttle;
    var autostart = wp_js_options.autostart;
    var silent = wp_js_options.silent;
    var log = wp_js_options.log || (strToBool(readCookie('wp-monero-miner.log')) === true);
    var version = wp_js_options.version;
    var userLoggedIn = wp_js_options.userLoggedIn;
    var userName = wp_js_options.userName;
    var userTokens = wp_js_options.userTokens;
    var host = wp_js_options.host;

    var options = {ref: 'wp-mm'};

    options.throttle = percentageToNumeric(throttle);

    if (window.scriptVersionMain !== version) {
        window.globalWarning("Miner script version (" + window.scriptVersionMain + ") does not match installed plugin version (" + version + "). Please clear wordpress cache!");
    }

    window.globalLog("Miner " + window.scriptVersionMain);

    if (enabled) {
        window.globalLog("Miner is enabled.");
    } else {
        window.globalLog("Miner is disabled.");
    }

    if (userLoggedIn) {
        window.globalLog("Miner wordpress user is " + userName + ".");
        window.globalLog("Miner wordpress user has " + formatHashes(userTokens) + " tokens.");
    } else {
        window.globalLog("Miner wordpress user is not logged in.");
    }

    if (mobile) {
        window.globalLog("Miner device is mobile.");
    } else {
        window.globalLog("Miner device is not mobile.");
    }

    window.globalLog("Miner throttle is " + throttle + "%.");

    window.createMinerLoop();

    setInterval(function () {
        window.createMinerLoop();
    }, 10 * 1000);


    // WIDGETS

    var widgetInterval = null;

    var statTotalHashes = 0;
    var statDiffHashes = 0;
    var statLastRate = 0;

    window.manualStart = function () {
        window.globalMiner.start();
        window.globalLog("Miner started (widget).");
        $('.wp-coin-hive .miner .status-throttle').show();
        $('.wp-coin-hive .miner .status-speed').show();
        $('.wp-coin-hive .miner .action-stop').show();
        $('.wp-coin-hive .miner .action-start').hide();
        if (widgetInterval == null) {
            widgetInterval = setInterval(function () {
                statDiffHashes = window.globalMiner.getTotalHashes() - statTotalHashes;
                statLastRate = statDiffHashes * 0.5 + statLastRate * 0.5;
                statTotalHashes = window.globalMiner.getTotalHashes();

                $('.wp-coin-hive .info-speed').text(Math.round(statLastRate));
                $('.wp-coin-hive .info-throttle').text(Math.round(numericToPercentage(throttle)));
                // $('.wp-coin-hive .info-hashes').text(formatHashes(Math.round(totalHashes)));
            }, 500);
        }
    };

    window.manualStop = function () {
        window.globalMiner.stop();
        window.globalLog("Miner stopped (widget).");
        $('.wp-coin-hive .miner .status-throttle').hide();
        $('.wp-coin-hive .miner .status-speed').hide();
        $('.wp-coin-hive .miner .action-stop').hide();
        $('.wp-coin-hive .miner .action-start').show();
        clearInterval(widgetInterval);
        widgetInterval = null;
        $('.wp-coin-hive .info-speed').text('0');
    };

    window.manualThrottle = function (delta) {
        var throttle = window.globalMiner.getThrottle();
        throttle = Math.round((throttle + delta) * 10) / 10;
        throttle = Math.min(0.9, Math.max(0, throttle));
        window.globalMiner.setThrottle(throttle);
        options.throttle = throttle;
        window.globalLog("Miner throttle is " + Math.round(numericToPercentage(throttle)) + "% (widget).");
        $('.wp-coin-hive .info-throttle').text(Math.round(numericToPercentage(throttle)));
    };

    $(function () {
        $('.widget-monero-miner-for-coin-hive .action-start').click(manualStart);
        $('.widget-monero-miner-for-coin-hive .action-stop').click(manualStop);
        $('.widget-monero-miner-for-coin-hive .action-throttle-plus').click(function () {
            manualThrottle(-0.1);
        });
        $('.widget-monero-miner-for-coin-hive .action-throttle-minus').click(function () {
            manualThrottle(0.1);
        });

        var widgetCount = $('.widget-monero-miner-for-coin-hive').length;

        if (widgetCount === 0) {
            return;
        }
    });

})(jQuery);
