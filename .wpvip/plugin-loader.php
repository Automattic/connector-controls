<?php

defined( 'ABSPATH' ) || die();

if ( ! defined( 'WP_TESTS_DOMAIN' ) && function_exists( 'wpcom_vip_load_plugin' ) ) {
	if ( ! defined( 'VIP_CONNECTOR_CONTROLS_CONFIG' ) ) {
		// Mirror the VIP platform: runtime config is defined before the plugin loads.
		// A git-ignored fixtures/config-local.php overrides the committed fixture —
		// handy for local secrets and experiments (see fixtures/README.md).
		$vip_connector_controls_fixtures = WP_CONTENT_DIR . '/plugins/connector-controls/fixtures';
		define(
			'VIP_CONNECTOR_CONTROLS_CONFIG',
			file_exists( $vip_connector_controls_fixtures . '/config-local.php' )
				? require $vip_connector_controls_fixtures . '/config-local.php'
				: require $vip_connector_controls_fixtures . '/config-valid.php'
		);
		unset( $vip_connector_controls_fixtures );
	}

	wpcom_vip_load_plugin( 'connector-controls/connector-controls.php' );
}
