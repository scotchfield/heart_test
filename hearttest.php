<?php
/**
 * Plugin Name: ❤️️Test
 * Plugin URI: http://scootah.com/
 * Description: WordPress Unit Testing for Plugins
 * Version: 1.0
 * Author: Scott Grant
 * Author URI: http://scootah.com/
 */
class WP_HeartTest {

	/**
	 * Store reference to singleton object.
	 */
	private static $instance = null;

	/**
	 * The domain for localization.
	 */
	const DOMAIN = 'wp-hearttest';

	/**
	 * Instantiate, if necessary, and add hooks.
	 */
	public function __construct() {
		global $wpdb;

		if ( isset( self::$instance ) ) {
			wp_die( esc_html__(
				'WP_HeartTest is already instantiated!',
				self::DOMAIN ) );
		}

		self::$instance = $this;

		$this->config_filename = 'hearttest.json';

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public static function get_instance() {
		return self::$instance;
	}

	/**
	 * Add a link to a settings page.
	 */
	public function admin_menu() {
		add_menu_page(
			'❤️️Test',
			'❤️️Test',
			'manage_options',
			self::DOMAIN . 'admin',
			array( $this, 'admin_page' )
		);
	}

	public function admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', self::DOMAIN ) );
		}
?>
<h1>❤️️Test</h1>
<?php
		$plugins = get_plugins();

		foreach ( $plugins as $plugin_key => $plugin_value ) {
			$path_obj = explode( DIRECTORY_SEPARATOR, $plugin_key );

			if ( count( $path_obj ) == 0 ) {
				continue;
			}

			$plugin_dir = dirname( __FILE__ );
			$plugin_dir = substr( $plugin_dir, 0, strrpos( $plugin_dir, DIRECTORY_SEPARATOR ) + 1 );
			$heart_filename = $plugin_dir . $path_obj[ 0 ] . DIRECTORY_SEPARATOR . $this->config_filename;

			if ( ! file_exists( $heart_filename ) ) {
				continue;
			}

			echo( '<p>' . $path_obj[ 0 ] . '</p>' );
		}
	}

}

new WP_HeartTest();
