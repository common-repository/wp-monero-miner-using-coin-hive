<?php

class AuthService
{
    private $basic = array('wp_monero_miner_minify', 'wp_monero_miner_link_filter');
    private $pro = array('wp_monero_miner_protected_area', 'wp_monero_miner_templates'); // wp_monero_miner_host

    public function can($feature) {

        $variant = get_option('wp_monero_miner_variant');

        if ((in_array($feature, $this->pro)) && ($variant == 'pro')) {
            return true;
        }

        if ((in_array($feature, $this->basic)) && (($variant == 'basic') || ($variant == 'pro'))) {
            return true;
        }

        return false;
    }

    public function version($feature)
    {
        if (in_array($feature, $this->pro)) {
            return 'pro';
        }

        if (in_array($feature, $this->basic)) {
            return 'basic';
        }

        return 'standard';
    }
}

?>