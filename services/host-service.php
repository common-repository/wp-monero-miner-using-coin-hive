<?php

class HostService
{
    public function getHash($name) {
        return get_option('wp_monero_miner_host_hash_' . $name);
    }

    public function setHash($name, $hash) {
        update_option('wp_monero_miner_host_hash_' . $name, $hash);
    }
}

?>