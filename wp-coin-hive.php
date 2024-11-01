<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link
 * @since             1.0.0
 * @package           Wp_Coin_Hive
 *
 * @wordpress-plugin
 * Plugin Name:       WP Monero Miner
 * Description:       Let your users mine for you (Monero mining using coinhive).
 * Version:           4.1.2
 * Author:            Dennis Keil
 * Author URI:        https://github.com/denniske
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-monero-miner-using-coin-hive
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Do not break wordpress when pro version is installed.
$proVersionInstalled = false;

$activePlugins = get_option('active_plugins');
foreach ($activePlugins as $index => $name) {
    if (strpos($name, '/wp-monero-miner.php') !== false) {
        $proVersionInstalled = true;
    }
}

if (is_multisite()) {
    $activePlugins = get_site_option('active_sitewide_plugins');
    foreach ($activePlugins as $name => $index) {
        if (strpos($name, '/wp-monero-miner.php') !== false) {
            $proVersionInstalled = true;
        }
    }
}

if (!$proVersionInstalled) {
    define('WP_MONERO_MINER_PLUGIN_VERSION', '4.1.2');

    require_once plugin_dir_path(__FILE__) . 'services/throttle-service.php';
    require_once plugin_dir_path(__FILE__) . 'services/api-service.php';
    require_once plugin_dir_path(__FILE__) . 'services/format-service.php';
    require_once plugin_dir_path(__FILE__) . 'services/auth-service.php';
    require_once plugin_dir_path(__FILE__) . 'services/host-service.php';
    require_once plugin_dir_path(__FILE__) . 'services/asset-service.php';
    require_once plugin_dir_path(__FILE__) . 'services/template-service.php';
    require_once plugin_dir_path(__FILE__) . 'services/script-service.php';
    require_once plugin_dir_path(__FILE__) . 'services/site-service.php';

    $throttleService = new ThrottleService();
    $apiService = new ApiService();
    $formatService = new FormatService();
    $authService = new AuthService();
    $hostService = new HostService();
    $assetService = new AssetService();
    $templateService = new TemplateService();
    $scriptService = new ScriptService();
    $siteService = new SiteService();

    if (!class_exists('Mustache_Autoloader')) {
        require 'vendor/mustache/mustache/src/Mustache/Autoloader.php';
        Mustache_Autoloader::register();
    }

    class MustacheHelper
    {
        static function translate($str, $helper)
        {
            $parts = preg_split("/\s*\|\|\s*/", $str);
            $translation = __($parts[0], 'wp-monero-miner-using-coin-hive');
            array_shift($parts);
            return vsprintf($translation, $parts);
        }

        static function formatHashes($hashes, $helper)
        {
            global $formatService;
            return $formatService->formatHashes($helper->render($hashes));
        }

        static function maskWallet($wallet, $helper)
        {
            $wallet = $helper->render($wallet);
            return strlen($wallet) <= 10 ? $wallet : substr($wallet, 0, 10) . '..';
        }

        static function escape($value)
        {
            return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
        }
    }

    $mustache = new Mustache_Engine(array(
        'template_class_prefix' => '__MyTemplates_',
        'cache' => dirname(__FILE__) . '/templates/cache',
        'cache_file_mode' => 0666,
        'cache_lambda_templates' => true,
        'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/templates'),
        'helpers' => array(
            'translate' => array('MustacheHelper', 'translate'),
            'formatHashes' => array('MustacheHelper', 'formatHashes'),
            'maskWallet' => array('MustacheHelper', 'maskWallet'),
        ),
        'escape' => 'MustacheHelper::escape',
        'charset' => 'ISO-8859-1',
        'logger' => new Mustache_Logger_StreamLogger('php://stderr'),
        'strict_callables' => false,
        'pragmas' => array(Mustache_Engine::PRAGMA_FILTERS),
    ));

    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-wp-coin-hive-activator.php
     */
    function activate_wp_coin_hive()
    {
        require_once plugin_dir_path(__FILE__) . 'includes/class-wp-coin-hive-activator.php';
        Wp_Coin_Hive_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-wp-coin-hive-deactivator.php
     */
    function deactivate_wp_coin_hive()
    {
        require_once plugin_dir_path(__FILE__) . 'includes/class-wp-coin-hive-deactivator.php';
        Wp_Coin_Hive_Deactivator::deactivate();
    }

    register_activation_hook(__FILE__, 'activate_wp_coin_hive');
    register_deactivation_hook(__FILE__, 'deactivate_wp_coin_hive');

    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path(__FILE__) . 'includes/class-wp-coin-hive.php';

    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function run_wp_coin_hive()
    {

        $plugin = new Wp_Coin_Hive();
        $plugin->run();

    }

    run_wp_coin_hive();
}
