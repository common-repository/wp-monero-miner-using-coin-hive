<?php

class Wp_Coin_Hive_Public
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function shortcode_protected_area($atts, $content = null, $tag)
    {
        global $apiService, $formatService, $authService, $templateService;

        ob_start();

        $tokens = isset($atts['tokens']) ? $atts['tokens'] : 10;

        $context = array(
            'tokens' => $tokens,
        );

        echo 'Please enable pro version to use this feature.';
        return ob_get_clean();
    }

    public function enqueue_styles()
    {
        global $assetService;
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-coin-hive-public'.$assetService->getExt('css'), array(), $this->version, 'all');
    }

    public function enqueue_scripts()
    {
    }
}
