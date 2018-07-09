<?php
/**
 * A Base WordPress Database Table class
 *
 * @author JJJ
 * @link https://jjj.blog
 * @version 1.4.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package WP_Chimp/Core
 * @since 0.1.0
 */

namespace WP_Chimp\Core;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No script kiddies please!' );
}

/**
 * A base WordPress database table class, which facilitates the creation of
 * and schema changes to individual database tables.
 *
 * This class is intended to be extended for each unique database table,
 * including global multisite tables and users tables.
 *
 * It exists to make managing database tables in WordPress as easy as possible.
 *
 * Extending this class comes with several automatic benefits:
 * - Activation hook makes it great for plugins
 * - Tables store their versions in the database independently
 * - Tables upgrade via independent upgrade abstract methods
 * - Multisite friendly - site tables switch on "switch_blog" action
 *
 * @since 1.1.0
 */
abstract class Database {

	/**
	 * Table name, without the global table prefix
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Optional description.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Database version
	 *
	 * @var int
	 */
	protected $version = 0;

	/**
	 * Is this table for a site, or global
	 *
	 * @var boolean
	 */
	protected $global = false;

	/**
	 * Passed directly into register_activation_hook()
	 *
	 * @var string
	 */
	protected $file = __FILE__;

	/**
	 * Database version key (saved in _options or _sitemeta)
	 *
	 * @var string
	 */
	protected $db_version_key = '';

	/**
	 * Current database version
	 *
	 * @var string
	 */
	protected $db_version = 0;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $table_name = '';

	/**
	 * Table schema
	 *
	 * @var string
	 */
	protected $schema = '';

	/**
	 * Database character-set & collation for table
	 *
	 * @var string
	 */
	protected $charset_collation = '';

	/**
	 * Database object (usually $GLOBALS['wpdb'])
	 *
	 * @var WPDB
	 */
	protected $db = false;

	/**
	 * Hook into queries, admin screens, and more!
	 *
	 * @since 1.1.0
	 */
	public function __construct() {

		$this->setup(); // Setup the database.

		if ( empty( $this->name ) || empty( $this->db_version_key ) ) { // Bail if setup failed.
			return;
		}

		$this->get_db_version(); // Get the version of he table currently in the database.
		$this->set_wpdb_tables(); // Add the table to the object.
		$this->set_schema(); // Setup the database schema.

		if ( $this->is_testing() ) { // Maybe force upgrade if testing.
			$this->maybe_upgrade();
		}
	}

	/**
	 * Setup this database table
	 *
	 * @since 1.1.0
	 */
	abstract protected function set_schema();

	/**
	 * Upgrade this database table
	 *
	 * @since 1.1.0
	 */
	abstract protected function upgrade();

	/**
	 * Update table version & references.
	 *
	 * Hooked to the "switch_blog" action.
	 *
	 * @since 1.1.0
	 *
	 * @param int $site_id The site being switched to.
	 */
	public function switch_blog( $site_id = 0 ) {

		// Update DB version based on the current site.
		if ( ! $this->is_global() ) {
			$this->db_version = get_blog_option( $site_id, $this->db_version_key, false );
		}

		// Update table references based on th current site.
		$this->set_wpdb_tables();
	}

	/**
	 * Maybe upgrade the database table. Handles creation & schema changes.
	 *
	 * Hooked to the "admin_init" action.
	 *
	 * @since 1.1.0
	 */
	public function maybe_upgrade() {

		// Bail if no upgrade needed.
		if ( true === version_compare( (int) $this->db_version, (int) $this->version, '>=' ) ) {
			return;
		}

		// Include file with dbDelta() for create/upgrade usages.
		if ( ! function_exists( 'dbDelta' ) || ! function_exists( 'wp_should_upgrade_global_tables' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		// Bail if global and upgrading global tables is not allowed.
		if ( $this->is_global() && ! wp_should_upgrade_global_tables() ) {
			return;
		}

		// Create or upgrade?
		$this->exists()
			? $this->upgrade()
			: $this->create();

		// Only set database version if table exists.
		if ( $this->exists() ) {
			$this->set_db_version();
		}
	}

	/** Private ***************************************************************/

	/**
	 * Setup the necessary table variables
	 *
	 * @since 1.1.0
	 */
	private function setup() {

		// Setup database.
		$this->db = isset( $GLOBALS['wpdb'] ) ? $GLOBALS['wpdb'] : false;

		if ( false === $this->db ) { // Bail if no WordPress database interface is available.
			return;
		}

		$this->name = $this->sanitize_table_name( $this->name ); // Sanitize the database table name.

		if ( false === $this->name ) { // Bail if database table name was garbage.
			return;
		}

		if ( empty( $this->db_version_key ) ) { // Maybe create database key.
			$this->db_version_key = "wp_{$this->name}_db_version";
		}
	}

	/**
	 * Modify the database object and add the table to it
	 *
	 * This must be done directly because WordPress does not have a mechanism
	 * for manipulating them safely
	 *
	 * @since 1.1.0
	 */
	private function set_wpdb_tables() {

		if ( $this->is_global() ) { // Is installed globally (multisite)?.
			$prefix = $this->db->get_blog_prefix( 0 );
			$this->db->{$this->name} = "{$prefix}{$this->name}";
			$this->db->ms_global_tables[] = $this->name;
		} else { // Site.
			$prefix = $this->db->get_blog_prefix( null );
			$this->db->{$this->name} = "{$prefix}{$this->name}";
			$this->db->tables[] = $this->name;
		}

		$this->table_name = $this->db->{$this->name}; // Set the table name locally.

		if ( ! empty( $this->db->charset ) ) { // Charset.
			$this->charset_collation = "DEFAULT CHARACTER SET {$this->db->charset}";
		}

		if ( ! empty( $this->db->collate ) ) { // Collation.
			$this->charset_collation .= " COLLATE {$this->db->collate}";
		}
	}

	/**
	 * Set the database version for the table
	 *
	 * Global table version in "_sitemeta" on the main network
	 *
	 * @since 1.1.0
	 */
	private function set_db_version() {

		// Set the class version.
		$this->db_version = $this->version;

		// Update the DB version.
		$this->is_global()
			? update_network_option( null, $this->db_version_key, $this->version )
			: update_option( $this->db_version_key, $this->version );
	}

	/**
	 * Get the table version from the database
	 *
	 * Global table version from "_sitemeta" on the main network
	 *
	 * @since 1.1.0
	 */
	private function get_db_version() {

		$this->db_version = $this->is_global()
			? get_network_option( null, $this->db_version_key, false )
			: get_option( $this->db_version_key, false );
	}

	/**
	 * Check if the current request is from some kind of test.
	 *
	 * This is primarily used to skip 'admin_init' and force-install tables.
	 *
	 * @since 1.4.0
	 *
	 * @return bool
	 */
	private function is_testing() {

		/**
		 * Tests constant is being used.
		 * Or, Scaffolded (https://make.wordpress.org/cli/handbook/plugin-unit-tests/).
		 */
		return (bool) ( defined( 'WP_TESTS_DIR' ) && WP_TESTS_DIR ) || function_exists( '_manually_load_plugin' );
	}

	/**
	 * Create the table
	 *
	 * @since 1.1.0
	 */
	private function create() {

		// Bail if dbDelta() moved in WordPress core.
		if ( ! function_exists( 'dbDelta' ) ) {
			return false;
		}

		// Run CREATE TABLE query.
		$query = "CREATE TABLE {$this->table_name} (
			{$this->schema}
		) {$this->charset_collation};";

		$created = dbDelta( $query );

		// Was the table created?
		return ! empty( $created );
	}

	/**
	 * Check if table already exists
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	private function exists() {
		// phpcs:ignore Squiz.Strings.DoubleQuoteUsage
		$query       = "SHOW TABLES LIKE %s";
		$like        = $this->db->esc_like( $this->table_name );
		$prepared    = $this->db->prepare( $query, $like );
		$table_exist = $this->db->get_var( $prepared );

		// Does the table exist?
		return ! empty( $table_exist );
	}

	/**
	 * Check if table is global
	 *
	 * @since 1.2.0
	 *
	 * @return bool
	 */
	private function is_global() {

		// Is the table global?
		return ( true === $this->global );
	}

	/**
	 * Sanitize a table name string
	 *
	 * Applies the following formatting to a string:
	 * - No accents
	 * - No special characters
	 * - No hyphens
	 * - No double underscores
	 * - No trailing underscores
	 *
	 * @since 1.3.0
	 *
	 * @param string $name The name of the database table.
	 * @return string Sanitized database table name
	 */
	private function sanitize_table_name( $name = '' ) {

		// Only non-accented table names (avoid truncation).
		$accents = remove_accents( $name );

		// Only lowercase characters, hyphens, and dashes (avoid index corruption).
		$lower = sanitize_key( $accents );

		// Replace hyphens with single underscores.
		$under = str_replace( '-', '_', $lower );

		// Single underscores only.
		$single = str_replace( '__', '_', $under );

		// Remove trailing underscores.
		$clean = trim( $single, '_' );

		// Bail if table name was garbaged.
		if ( empty( $clean ) ) {
			return false;
		}

		// Return the cleaned table name.
		return $clean;
	}
}
