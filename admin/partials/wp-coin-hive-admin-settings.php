<div class="wrap" id="wp-coin-hive-admin-settings">

    <h2>
        <?php echo esc_html(get_admin_page_title()); ?>
        <span class="badge badge-<?php echo $variantBadge; ?>"><?php echo $variant; ?></span>
        <span style="font-size:0.7em">(<a target="_blank" href="http://www.wp-monero-miner.com">wp-monero-miner.com</a>)</span>
    </h2>

    <?php if ($notice != null) { ?>

        <?php if (isset($notice['dismissable'])) { ?>

            <div class="notice notice-<?php echo $notice['type']; ?> my-is-dismissable">
                <p><?php echo $notice['text']; ?></p>
                <form action="" method="post">
                    <button type="submit" name="dismiss-notice-<?php echo $notice['name']; ?>" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </form>
            </div>

        <?php } else { ?>

            <div class="notice notice-<?php echo $notice['type']; ?>">
                <p><?php echo $notice['text']; ?></p>
            </div>

        <?php } ?>

    <?php } ?>

    <form method="post">

        <?php
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
        $active_template = isset($_GET['template']) ? $_GET['template'] : 'widget-miner';
        ?>

        <h2 class="nav-tab-wrapper">
            <a href="?page=wp-coin-hive&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
            <a href="?page=wp-coin-hive&tab=website" class="nav-tab <?php echo $active_tab == 'website' ? 'nav-tab-active' : ''; ?>">Website</a>
<!--            <a href="?page=wp-coin-hive&tab=captcha" class="nav-tab --><?php //echo $active_tab == 'captcha' ? 'nav-tab-active' : ''; ?><!--">Captcha</a>-->
        </h2>

        <?php if ($noticeForm != null) { ?>

            <div class="notice-form notice-form-<?php echo $noticeForm['type']; ?>">
                <p><?php echo $noticeForm['text']; ?></p>
            </div>

        <?php } ?>

        <input type="hidden" name="options" value="wp-monero-miner"/>

        <?php if ($active_tab == 'general') { ?>

            <div style="max-width:700px">

                <h3>MoneroOcean Mining Pool</h3>

                <p>
                    Create a monero wallet at <a target="_blank" href="https://mymonero.com/">mymonero.com</a> and enter your public wallet address.
                </p>

                <table class="form-table" id="form-coin-hive-account">

                    <tr valign="top">
                        <th scope="row">Monero Wallet Address</th>
                        <td>
                                <textarea name="wp_monero_miner_site_key" style="width:430px"
                                          rows="2"><?php echo esc_attr(get_option('wp_monero_miner_site_key')); ?></textarea>
                            <p class="description" id="tagline-description">Your monero wallet address.<br/>Please note that many pools only allow payments to public
                                addresses.
                                They don't do payments to Integrated addresses (such as used by exchanges).
                            </p>

                            <p>
                                To see your stats go to <a target="_blank" href="https://moneroocean.stream">https://moneroocean.stream</a>. Enter your
                                wallet address in the field "Enter Payment Address" and then click "Track address".
                            </p>
                        </td>
                    </tr>

<!--                    <tr valign="top">-->
<!--                        <th scope="row">Enabled</th>-->
<!--                        <td>-->
<!--                            <input type="checkbox" name="wp_monero_miner_pool_disabled" value="1" --><?php //echo checked('', get_option('wp_monero_miner_pool_enabled'), false) ?><!-- />-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                    <tr valign="top">-->
<!--                        <th scope="row">Site Key (public)</th>-->
<!--                        <td>-->
<!--                            <input type="text" name="wp_monero_miner_site_key" value="--><?php //echo esc_attr(get_option('wp_monero_miner_site_key')); ?><!--" style="width:300px"/>-->
<!--                            <p class="description" id="tagline-description">Can be found at <a target="_blank" href="https://coinhive.com/settings/sites">https://coinhive.com/settings/sites</a>.-->
<!--                            </p>-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                    <tr valign="top">-->
<!--                        <th scope="row">Secret Key (private)</th>-->
<!--                        <td>-->
<!--                            <input type="password" name="wp_monero_miner_secret_key" value="--><?php //echo esc_attr(base64_decode(get_option('wp_monero_miner_secret_key'))); ?><!--"-->
<!--                                   style="width:300px"/>-->
<!--                            <p class="description" id="tagline-description">Can be found at <a target="_blank" href="https://coinhive.com/settings/sites">https://coinhive.com/settings/sites</a>.-->
<!--                            </p>-->
<!--                        </td>-->
<!--                    </tr>-->
                </table>
            </div>

        <?php } ?>

        <?php if ($active_tab == 'website') { ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable</th>
                    <td>
                        <input type="checkbox" name="wp_monero_miner_enabled" value="1" <?php echo checked(1, get_option('wp_monero_miner_enabled'), false) ?> />
                        <p class="description" id="tagline-description">You can enable/disable miner for your website here.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Throttle</th>
                    <td>
                        <input type="text" size="3" value="20" disabled/> %
                        <p class="description" id="tagline-description">Percentage of CPU power used for mining.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Throttle (Mobile)</th>
                    <td>
                        <input type="text" size="3" value="10" disabled/> %
                        <p class="description" id="tagline-description">Percentage of CPU power used for mining on mobile devices.</p>
                    </td>
                </tr>
            </table>
        <?php } ?>

        <?php /* if ($active_tab == 'captcha') { ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable for login</th>
                    <td>
                        <input type="checkbox" name="wp_monero_miner_captcha_login_enabled"
                               value="1" <?php echo checked(1, get_option('wp_monero_miner_captcha_login_enabled'), false) ?> />
                        <p class="description" id="tagline-description">Show coin hive proof of work captcha for login.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Hashes</th>
                    <td>
                        <input type="text" name="wp_monero_miner_captcha_hashes" size="6" value="<?php echo esc_attr(get_option('wp_monero_miner_captcha_hashes')); ?>"/>
                        <p class="description" id="tagline-description">The number of hashes that have to be accepted. Must be a multiple of 256.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Auto Start</th>
                    <td>
                        <input type="checkbox" name="wp_monero_miner_captcha_autostart"
                               value="1" <?php echo checked(1, get_option('wp_monero_miner_captcha_autostart'), false) ?> />
                        <p class="description" id="tagline-description">Starts solving captcha automatically when page loaded.</p>
                    </td>
                </tr>
            </table>
        <?php } */ ?>

        <?php submit_button('Save all changes', 'primary', 'submit', TRUE); ?>
    </form>

    <?php if ($active_tab == 'link') { ?>

        <form method="post">
            <p>If the link rewriting does not work, try to flush the rewrite rules.</p>
            <?php submit_button('Flush Rewrite Rules', 'secondary', 'flush_rules', TRUE); ?>
        </form>

    <?php } ?>

</div>

<div class="monero-ad">
    <div class="box-1">
        <div>
            <h3>WP Monero Miner <span class="badge badge-success">Pro</span></h3>
        </div>

        <div>
            <p style="padding:0 20px;">
                The Pro version has many additional features. It is financed by donating a percentage of your earnings to the developers.
            </p>
        </div>

        <div class="link">
            <a href="http://wp-monero-miner.com" target="_blank" class="button button-primary">Get Pro Version Now</a>
        </div>

        <div class="url">
            <span style="font-size:1em">(<a target="_blank" href="http://www.wp-monero-miner.com">wp-monero-miner.com</a>)</span>
        </div>
    </div>
    <div class="box-2">
        <h4>Main Features of Pro Version:</h4>

        <ul>
            <li><strong>Miner Widget</strong> enabling users on your site to start/stop mining and control mining speed.</li>
            <li><strong>Background Mining</strong> with configurable CPU usage for mobile and desktop clients</li>
            <li>PoW (“Proof of Work”) Verification for <strong>External links</strong></li>
            <li>All controls/widgets can be customized visually to fit your page through <strong>Templates</strong></li>
            <li>Prevention of <strong>AdBlock / Antivirus</strong>.</li>
        </ul>
    </div>
    <div class="clear"></div>
</div>



<script>

    (function ($) {
        'use strict';

        $('input[name=wp_monero_miner_pool_disabled]').change(function () {
            if (this.checked) {
                $('input[name=wp_monero_miner_pool_enabled]').prop("checked", false);
                $('#form-pool input[type=text]').prop("disabled", true);
                $('#form-pool textarea').prop("disabled", true);
                $('#form-coin-hive-account input[type=text]').prop("disabled", false);
                $('#form-coin-hive-account input[type=password]').prop("disabled", false);
            }
        }).change();

        $('input[name=wp_monero_miner_pool_enabled]').change(function () {
            if (this.checked) {
                $('input[name=wp_monero_miner_pool_disabled]').prop("checked", false);
                $('#form-pool input[type=text]').prop("disabled", false);
                $('#form-pool textarea').prop("disabled", false);
                $('#form-coin-hive-account input[type=text]').prop("disabled", true);
                $('#form-coin-hive-account input[type=password]').prop("disabled", true);
            }
        }).change();

        $(".form-elements-disabled :input").prop("disabled", true);
        $(".form-elements-disabled :button").prop("disabled", true);

    })(jQuery);

</script>