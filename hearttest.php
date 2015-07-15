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

	private function checkPrefixSuffix( $st, $testsuite ) {
		if ( strpos( $st, $testsuite[ 'prefix' ] ) !== 0 ) {
			return false;
		}

		if ( strrpos( $st, $testsuite[ 'suffix' ] ) !== strlen( $st ) - strlen( $testsuite[ 'suffix' ] ) ) {
			return false;
		}

		return true;
	}

	private function do_tests( $❤️️_filename ) {
?>
<h2>Running tests from <?php echo( $❤️️_filename ); ?></h2>
<?php
		$config_handle = fopen( $❤️️_filename, 'r' );
		$config_content = fread( $config_handle, filesize( $❤️️_filename ) );
		$config = json_decode( $config_content, $assoc = true );

		$dir = substr( $❤️️_filename, 0, strrpos( $❤️️_filename, '/' ) + 1 );

		if ( isset( $config[ 'bootstrap' ] ) ) {
			require( $dir . $config[ 'bootstrap' ] );
		}

		foreach ( $config[ 'testsuites' ] as $testsuite ) {
			$files = scandir( $dir . $testsuite[ 'directory' ] );

			foreach ( $files as $file ) {
				if ( $this->checkPrefixSuffix( $file, $testsuite ) ) {
					require( $dir . $testsuite[ 'directory' ] . '/' . $file );
				}
			}
		}
	}

	public function admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', self::DOMAIN ) );
		}
?>
<h1>❤️️Test</h1>
<?php

		if ( ! WP_DEBUG ) {
?>
<p>Sorry, ❤️️Test can only run when WP_DEBUG is set to true.</p>
<?php
			return;
		}

		if ( isset( $_GET[ 'test' ] ) && isset( $_GET[ 'nonce' ] ) && wp_verify_nonce( $_GET[ 'nonce' ], 'test_' . $_GET[ 'test' ] ) ) {
			$plugin = $_GET[ 'test' ];

			$plugin_dir = dirname( __FILE__ );
			$plugin_dir = substr( $plugin_dir, 0, strrpos( $plugin_dir, DIRECTORY_SEPARATOR ) + 1 );
			$❤️️_filename = $plugin_dir . $plugin . DIRECTORY_SEPARATOR . $this->config_filename;

			if ( file_exists( $❤️️_filename ) ) {
				$this->do_tests( $❤️️_filename );
			}
		}

		$plugins = get_plugins();

		foreach ( $plugins as $plugin_key => $plugin_value ) {
			$path_obj = explode( DIRECTORY_SEPARATOR, $plugin_key );

			if ( count( $path_obj ) == 0 ) {
				continue;
			}

			$plugin = $path_obj[ 0 ];

			$plugin_dir = dirname( __FILE__ );
			$plugin_dir = substr( $plugin_dir, 0, strrpos( $plugin_dir, DIRECTORY_SEPARATOR ) + 1 );
			$❤️️_filename = $plugin_dir . $plugin . DIRECTORY_SEPARATOR . $this->config_filename;

			if ( ! file_exists( $❤️️_filename ) ) {
				continue;
			}

			echo( '<p><a href="?page=' . $_GET[ 'page' ] . '&amp;test=' . $plugin . '&amp;nonce=' . wp_create_nonce( 'test_' . $plugin ) . '">' . $plugin . '</a></p>' );
		}
	}

}

new WP_HeartTest();
