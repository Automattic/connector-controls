<?php

namespace Automattic\ConnectorControls;

final class Admin {
	const OPTIONS_MENU_SLUG = 'connector-controls-settings';

	/** @var self|null */
	private static $instance;

	public static function get_instance(): self {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register the admin UI with WordPress.
	 */
	public static function register(): void {
		self::get_instance()->init();
	}

	public function init(): void {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'admin_init', [ AdminSettings::class, 'register' ] );
	}

	public function admin_init(): void {
		$plugin = plugin_basename( 'connector-controls/connector-controls.php' );
		add_filter( 'plugin_action_links_' . $plugin, [ $this, 'plugin_action_links' ] );
	}

	public function admin_menu(): void {
		add_options_page( __( 'Connector Controls Settings', 'connector-controls' ), __( 'Connector Controls Settings', 'connector-controls' ), 'manage_options', self::OPTIONS_MENU_SLUG, [ AdminSettings::class, 'settings_page' ] );
	}

	/**
	 * @param array<string,string> $links
	 * @return array<string,string>
	 */
	public function plugin_action_links( array $links ): array {
		$url               = esc_url( admin_url( 'options-general.php?page=' . self::OPTIONS_MENU_SLUG ) );
		$link              = '<a href="' . $url . '">' . __( 'Settings', 'connector-controls' ) . '</a>';
		$links['settings'] = $link;
		return $links;
	}
}
