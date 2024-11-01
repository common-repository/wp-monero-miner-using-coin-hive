<?php

class Wp_Coin_Hive_Captcha_Module
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
        $this->loader->add_action('login_enqueue_scripts', $this, 'enqueue_login_scripts');

        $this->determine_current_page();

//        if ($this->user_can_bypass()) {
//            return;
//        }

//         this action needs to be added when a captcha is manually needed
//        add_action('bwp_recaptcha_add_markups', array($this, 'add_recaptcha'));

//            if ('yes' == $this->options['enable_comment'])
//                $this->init_comment_form_captcha();

        if (get_option('wp_monero_miner_captcha_login_enabled') && $this->is_login) {
            $this->init_login_form_captcha();
        }

//        if (get_option('wp_monero_miner_captcha_registration_enabled') && $this->is_reg) {
//            $this->init_registration_form_captcha();
//        }

//            if ('yes' == $this->options['enable_registration'] && $this->is_signup)
//                $this->init_multisite_registration_form_captcha();
    }

    public function enqueue_login_scripts()
    {
        global $authService, $hostService, $assetService;

        if (get_option('wp_monero_miner_captcha_login_enabled')) {
            if (get_option('wp_monero_miner_host_enabled') && $authService->can('wp_monero_miner_host')) {
                wp_enqueue_script($this->plugin_name . '-coinhive-captcha', plugin_dir_url(dirname(__FILE__)) . 'js-lib/captcha.min.js', array(), $hostService->getHash('captcha'));
            } else {
                wp_enqueue_script($this->plugin_name . '-coinhive-captcha', 'https://coinhive.com/lib/captcha.min.js');
            }
            wp_enqueue_style($this->plugin_name . '-login', plugin_dir_url(dirname(__FILE__)) . 'css/wp-coin-hive-login' . $assetService->getExt('css'), array(), $this->version, 'all');
        }
    }
    
    /**
     * User capabilities to bypass captcha
     */
    public $caps;

    /**
     * Is registering via wp-login.php
     */
    public $is_reg = false;

    /**
     * Captcha error message when registering via wp-login.php
     */
    public $reg_errors = false;

    /**
     * Is signing up (multi-site only)
     */
    public $is_signup = false;

    /**
     * Is logging in via wp-login.php
     */
    public $is_login = false;

    /**
     * Copied from wp-includes/general-template.php:wp_registration_url
     * because we still have to support 3.0
     */
    private function _wp_registration_url()
    {
        return apply_filters('register_url', site_url('wp-login.php?action=register', 'login'));
    }

    protected function determine_current_page()
    {
        // @since 2.0.3 only strip the host and scheme (including https), so
        // we can properly compare with REQUEST_URI later on.
        $login_path = preg_replace('#https?://[^/]+/#i', '', wp_login_url());
        $register_path = preg_replace('#https?://[^/]+/#i', '', $this->_wp_registration_url());

        global $pagenow;

        $request_uri = ltrim($_SERVER['REQUEST_URI'], '/');

        if (strpos($request_uri, $register_path) === 0) {
            // whether user is requesting regular user registration page
            $this->is_reg = true;
        } elseif (strpos($request_uri, $login_path) === 0) {
            // whether user is requesting the wp-login page
            $this->is_login = true;
        } elseif (!empty($pagenow) && $pagenow == 'wp-signup.php') {
            // whether user is requesting wp-signup page (multi-site page for
            // user/site registration)
            $this->is_signup = true;
        }
    }

    public function add_recaptcha($errors = '', $formId = null)
    {
        $errors = !is_wp_error($errors) ? new WP_Error() : $errors;
        $this->renderCaptcha($errors, $formId);
    }

    public function renderCaptcha(WP_Error $errors = null, $formId = null)
    {
        $site_key = get_option('wp_monero_miner_site_key');
        $hashes = get_option('wp_monero_miner_captcha_hashes', 256);
        $autostart = get_option('wp_monero_miner_captcha_autostart', true) ? 'true' : 'false';

        echo '<div class="coinhive-captcha" data-autostart="' . esc_attr($autostart) . '" data-hashes="' . esc_attr($hashes) . '" data-key="' . esc_attr($site_key) . '"></div>';

//        $output = array();
//        $formId = $this->_getUniqueFormId($formId);
//
//        $this->instances[] = $formId;
//
//        if (!empty($_GET['cerror'])) {
//            $captchaError = $this->getErrorMessageFromCode($_GET['cerror']);
//        } elseif (isset($errors)) {
//            $captchaError = $errors->get_error_message('recaptcha-error');
//        }
//
//        if (!empty($captchaError)) {
//            $output[] = '<p class="bwp-recaptcha-error error">' . $captchaError . '</p>';
//        }
//
//
//        $output[] = implode('', array(
//            '<input type="hidden" name="bwp-recaptcha-widget-id" value="' . esc_attr($this->_getWidgetId($formId)) . '" />',
//            '<div class="coinhive-captcha" data-hashes="256" data-key="'.esc_attr(get_option('wp_monero_miner_site_key')).'"></div>',
//            '<div id="' . $this->_getWidgetHtmlId($formId) . '" class="bwp-recaptcha g-recaptcha" ',
//            '>',
//            '</div>'
//        ));
//
//        echo implode("\n", $output);
    }

//    private function _getWidgetId($formId)
//    {
//        return 'bwpRecaptchaWidget' . (array_search($formId, $this->instances, true) + 1);
//    }
//
//
//    private function _getWidgetHtmlId($formId)
//    {
//        return 'bwp-recaptcha-' . md5($formId);
//    }
//
//    protected $instances = array();
//
//    private function _getUniqueFormId($formId)
//    {
//        $formId = $formId ?: 'form';
//
//        if (!in_array($formId, $this->instances)) {
//            return $formId;
//        }
//
//        // non-unique form id, append the total number of instances plus one
//        return $formId . '-' . (count($this->instances) + 1);
//    }

    /**
     * @return WP_User if captcha is ok
     *         WP_Error if captcha is NOT ok
     */
    public function check_login_recaptcha($user)
    {
        // CoinHive Captcha does not exist anymore. Accept login.
        return $user;

        if (empty($_POST['log']) && empty($_POST['pwd']))
            return $user;

        // if the $user object itself is a WP_Error object, we simply append errors to it, otherwise we create a new one.
        $errors = is_wp_error($user) ? $user : new WP_Error();

        if ($this->verify()) {
            return $user;
        }

        $errors->add('recaptcha-error', 'Coin Hive Captcha Error');

        // invalid recaptcha detected, the returned $user object should be a WP_Error object
        $user = is_wp_error($user) ? $user : $errors;

        // do not allow WordPress to try authenticating the user, either
        // using cookie or username/password pair
        remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
        remove_filter('authenticate', 'wp_authenticate_cookie', 30, 3);

        return $user;
    }

    public function verify($userResponse = null)
    {
        $hashes = get_option('wp_monero_miner_captcha_hashes', 256);

        $post_data = array(
            'secret' => base64_decode(get_option('wp_monero_miner_secret_key')),
            'token' => $_POST['coinhive-captcha-token'],
            'hashes' => $hashes
        );

        $post_context = stream_context_create(array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($post_data)
            )
        ));

        $url = 'https://api.coinhive.com/token/verify';
        $response = json_decode(file_get_contents($url, false, $post_context));

        return ($response && $response->success);
    }

    public static function get_request_var($key, $empty_as_null = true)
    {
        if (!isset($_REQUEST[$key]))
            return null;

        $value = $_REQUEST[$key];

        if (is_array($value)) {
            $value = array_map('stripslashes', $value);
            $value = array_map('strip_tags', $value);
            $value = array_map('trim', $value);
        } else {
            $value = trim(strip_tags(stripslashes($value)));
        }

        if ($empty_as_null && empty($value))
            return null;

        return $value;
    }


    protected function init_login_form_captcha()
    {
        // @since 1.1.0 add captcha to login form
        add_action('login_form', array($this, 'add_recaptcha'));

        // the priority of 15 is to ensure that we run the filter before
        // WordPress authenticates the user.
        add_filter('authenticate', array($this, 'check_login_recaptcha'), 15);
    }

//    protected function init_registration_form_captcha()
//    {
//        // normal user registration page
//        add_action('register_form', array($this, 'add_recaptcha'));
//        add_filter('registration_errors', array($this, 'check_reg_recaptcha'));
//    }
//
//    protected function init_multisite_registration_form_captcha()
//    {
//        add_action('signup_extra_fields', array($this, 'add_multisite_user_reg_recaptcha'));
//        add_action('signup_blogform', array($this, 'add_multisite_blog_reg_recaptcha'));
//        add_filter('wpmu_validate_user_signup', array($this, 'check_multisite_user_reg_recaptcha'));
//        add_filter('wpmu_validate_blog_signup', array($this, 'check_multisite_blog_reg_recaptcha'));
//    }
}
