<?php

class Wp_Coin_Hive_i18n {

	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-monero-miner-using-coin-hive',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
