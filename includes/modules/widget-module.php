<?php

class Wp_Coin_Hive_Widget_Module
{
    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct($plugin_name, $version, $loader)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->loader = $loader;

        $this->define_hooks();
    }

    private function define_hooks()
    {
        $this->loader->add_action('widgets_init', $this, 'register_widget_for_miner');
    }

    public function register_widget_for_miner()
    {
        register_widget('WP_Coin_Hive_Widget_Miner');
    }
}
