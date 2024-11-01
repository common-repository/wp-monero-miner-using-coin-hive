<?php

class AssetService
{
    public function getHash($name) {
        return get_option('wp_monero_miner_host_hash_' . $name);
    }

    public function setHash($name, $hash) {
        update_option('wp_monero_miner_host_hash_' . $name, $hash);
    }

    public function getExt($type)
    {
        global $authService;

        if (get_option('wp_monero_miner_minify_js_enabled') && $authService->can('wp_monero_miner_minify')) {
            return '.min.'.$type;
        }
        return '.'.$type;
    }
}

?>