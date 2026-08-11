<?php
/**
 * Plugin Name: Connector Controls
 * Description: Reference implementation of a WordPress VIP partner integration, built from the VIP Integrations Starter Kit.
 * Version: 1.0.0
 * Requires at least: 6.9
 * Requires PHP: 8.2
 * Author: Automattic
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: connector-controls
 */

use Automattic\ConnectorControls\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'VIP_CONNECTOR_CONTROLS_LOADED' ) ) {
	return;
}

define( 'VIP_CONNECTOR_CONTROLS_LOADED', true );
define( 'VIP_CONNECTOR_CONTROLS_VERSION', '1.0.0' );
define( 'VIP_CONNECTOR_CONTROLS_FILE', __FILE__ );

require_once __DIR__ . '/vendor/autoload.php';

Plugin::get_instance();
