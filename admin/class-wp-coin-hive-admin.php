<?php

class Wp_Coin_Hive_Admin
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function add_plugin_admin_menu()
    {
        add_options_page('WP Monero Miner', 'WP Monero Miner', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
    }

    public function add_action_links($links)
    {
        $settings_link = array('<a href="' . admin_url('options-general.php?page=' . $this->plugin_name) . '">Settings</a>',);
        return array_merge($settings_link, $links);
    }

    private function sanitize_list_field($list)
    {
        $list = preg_replace('/\r\n|[\r\n]/', "\n", $list);
        $list = preg_replace('/\n+/', "\n", $list);
        $list = implode("\n", array_map('sanitize_text_field', explode("\n", $list)));
        return trim($list);
    }

    private function updateFromPost($type, $option)
    {
        if ($type == 'bool') {
            update_option($option, isset($_POST[$option]));
            return;
        }
        if (isset($_POST[$option])) {
            $value = strip_tags(trim($_POST[$option]));
            switch ($type) {
                case 'list':
                    update_option($option, $this->sanitize_list_field($value));
                    break;
                case 'secret':
                    update_option($option, sanitize_text_field(base64_encode($value)));
                    break;
                case 'string':
                    update_option($option, sanitize_text_field($value));
                    break;
                default:
                    update_option($option, sanitize_text_field($value));
            }
        }
    }

    public $notice = null;
    public $noticeForm = null;

    public function display_plugin_setup_page()
    {
        global $formatService, $hostService, $templateService, $authService, $siteService;

        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';

        if (isset($_POST['options']) && $_POST['options'] == 'wp-monero-miner' && isset($_POST['submit'])) {

            switch ($active_tab) {
                case 'general':
                    $this->updateFromPost('string', 'wp_monero_miner_site_key');
                    $this->updateFromPost('secret', 'wp_monero_miner_secret_key');
                    break;
                case 'website':
                    $this->updateFromPost('bool', 'wp_monero_miner_enabled');
                    break;
                case 'captcha':
                    $this->updateFromPost('bool', 'wp_monero_miner_captcha_login_enabled');
                    $this->updateFromPost('number', 'wp_monero_miner_captcha_hashes');
                    $this->updateFromPost('bool', 'wp_monero_miner_captcha_autostart');
                    break;
            }
        }

        if(isset($_POST['dismiss-notice-pro'])) {
            update_option('wp_monero_miner_notice_pro', true);
        }

        $this->setDefaultNotice();
        $this->setDefaultNoticeForm();

        $variant = 'Free';
        $variantBadge = 'secondary';

        $notice = $this->notice;
        $noticeForm = $this->noticeForm;

        $scheme = $siteService->get_current_scheme();

        $controller = $this;

        include_once('partials/wp-coin-hive-admin-settings.php');
    }

    function renderNotice($type, $text)
    {
        echo '<div class="notice-form notice-form-' . $type . '">';
        echo '    <p>' . $text . '</p>';
        echo '</div>';
    }

    function setNotice($notice)
    {
        $this->notice = $notice;
    }

    function setNoticeForm($noticeForm)
    {
        $this->noticeForm = $noticeForm;
    }

    function setDefaultNotice()
    {
        $settingSiteKey = get_option('wp_monero_miner_site_key');

        if (($settingSiteKey == false || $settingSiteKey == '')) {
            $this->setNotice(array(
                'type' => 'info',
                'text' => 'Welcome to WP Monero Miner plugin. To get started please fill in your <a href="https://coinhive.com" target="_blank">coinhive</a> site key <a href="?page=wp-coin-hive&tab=general">here</a> or fill in your monero wallet address for pool mining <a href="?page=wp-coin-hive&tab=general">here</a>.'
            ));
            return;
        }

        if (!get_option('wp_monero_miner_notice_pro')) {
            $this->setNotice(array(
                'dismissable' => true,
                'name' => 'pro',
                'type' => 'warning',
                'text' => 'NOTE: This plugin has been adapted to the wordpress plugin guidelines resulting in limited functionality. For additional features, please <a href="http://wp-monero-miner.com" target="_blank">download</a> WP Monero Miner <span class="badge badge-success">Pro</span> from <a href="http://wp-monero-miner.com" target="_blank">wp-monero-miner.com</a>.'
            ));
            return;
        }
    }

    function setDefaultNoticeForm()
    {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
        $settingSecretKey = get_option('wp_monero_miner_secret_key');

        if ($active_tab == 'captcha') {
            if (($settingSecretKey == false) || ($settingSecretKey == '')) {
                $this->setNoticeForm(array(
                    'type' => 'warning',
                    'text' => 'To use this feature fill in your <a href="https://coinhive.com">coinhive</a> secret site key in <a href="?page=wp-coin-hive&tab=general">general settings</a>.'
                ));
                return;
            }
        }
    }

    function notice_flush_success()
    {
        ?>
        <div class="updated notice">
            <p>Rewrite rules for links have been flushed.</p>
        </div>
        <?php
    }

    public function enqueue_styles()
    {
        global $assetService;
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-coin-hive-admin' . $assetService->getExt('css'), array(), $this->version);
    }

    public function enqueue_scripts()
    {
    }
}
