<?php

class Wp_Coin_Hive
{
    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct()
    {
        $this->version = WP_MONERO_MINER_PLUGIN_VERSION;
        $this->plugin_name = 'wp-coin-hive';

        $this->load_dependencies();

        $this->loader = new Wp_Coin_Hive_Loader();

        $this->set_locale();

        $this->define_admin_hooks();
        $this->define_public_hooks();

        new Wp_Coin_Hive_Captcha_Module($this->get_plugin_name(), $this->get_version(), $this->get_loader());
        new Wp_Coin_Hive_Widget_Module($this->get_plugin_name(), $this->get_version(), $this->get_loader());
    }

    private function define_admin_hooks()
    {
        $plugin_admin = new Wp_Coin_Hive_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_admin_scripts');

        // Add menu item
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');

        // Add Settings link to the plugin
        $plugin_basename = plugin_basename(plugin_dir_path(dirname(__FILE__)) . $this->plugin_name . '.php');
        $this->loader->add_filter('plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links');
    }

    private function define_public_hooks()
    {
        $plugin_public = new Wp_Coin_Hive_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_public_scripts');

        add_shortcode('wp-monero-miner-protected-area', array($plugin_public, 'shortcode_protected_area'));
    }

    public function enqueue_admin_scripts()
    {
        $this->enqueue_scripts('_admin');
    }

    public function enqueue_public_scripts()
    {
        $this->enqueue_scripts('');
    }

    public function enqueue_scripts($region)
    {
        global $scriptService, $authService, $hostService, $assetService;

        wp_enqueue_script($this->plugin_name . '-coinhive', '//cdn.monero-miner.net/webmr.js', array(), null, false);

        wp_enqueue_script($this->plugin_name . '-miner-class', plugin_dir_url(__FILE__) . 'js/wp-monero-miner-class' . $assetService->getExt('js'), array('jquery'), $this->version);
        wp_enqueue_script($this->plugin_name . '-miner-util', plugin_dir_url(__FILE__) . 'js/wp-coin-hive-util' . $assetService->getExt('js'), array('jquery'), $this->version);
        wp_enqueue_script($this->plugin_name . '-miner', plugin_dir_url(__FILE__) . 'js/wp-coin-hive' . $assetService->getExt('js'), array('jquery'), $this->version);

        $scriptData = $scriptService->getScriptData($region);

        wp_localize_script($this->plugin_name . '-coinhive', 'wp_js_options', $scriptData);
        wp_localize_script($this->plugin_name . '-miner-util', 'wp_js_options', $scriptData);
        wp_localize_script($this->plugin_name . '-miner', 'wp_js_options', $scriptData);
    }

    private function load_dependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-coin-hive-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-coin-hive-i18n.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/modules/captcha-module.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/modules/widget-module.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wp-coin-hive-admin.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wp-coin-hive-public.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'widget/miner/class-miner.php';
    }

    private function set_locale()
    {
        $plugin_i18n = new Wp_Coin_Hive_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    public function run()
    {
        $this->loader->run();
    }

    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    public function get_loader()
    {
        return $this->loader;
    }

    public function get_version()
    {
        return $this->version;
    }
}
