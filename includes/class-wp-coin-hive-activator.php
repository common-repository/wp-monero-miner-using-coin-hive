<?php

class Wp_Coin_Hive_Activator
{
    public static function activate()
    {
        // Initialize options
        add_option('wp_monero_miner_site_key', '', '', 'yes');
        add_option('wp_monero_miner_secret_key', '', '', 'yes');
        add_option('wp_monero_miner_log', true, '', 'yes');

        add_option('wp_monero_miner_enabled', true, '', 'yes');

        add_option('wp_monero_miner_captcha_login_enabled', false, '', 'yes');
        add_option('wp_monero_miner_captcha_registration_enabled', false, '', 'yes');
        add_option('wp_monero_miner_captcha_hashes', 256, '', 'yes');
        add_option('wp_monero_miner_captcha_autostart', true, '', 'yes');
    }
}
