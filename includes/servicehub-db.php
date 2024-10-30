<?php
/**
 * ServiceHub Database Class
 *
 * This class handles the creation and management of custom database tables.
 *
 * @package ServiceHub
 */

class ServiceHub_DB {

  /**
   * Instance of the class.
   *
   * @var ServiceHub_DB
   */
  private static $instance = null;

  /**
   * Get the instance of the class.
   *
   * @return ServiceHub_DB
   */
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new ServiceHub_DB();
    }
    return self::$instance;
  }

  /**
   * Constructor.
   */
  private function __construct() {
    global $wpdb;

    $this->wpdb = $wpdb;
    $this->charset_collate = $wpdb->get_charset_collate();

    $this->jobs_table = $wpdb->prefix . 'servicehub_jobs';
    $this->customers_table = $wpdb->prefix . 'servicehub_customers';
    $this->invoices_table = $wpdb->prefix . 'servicehub_invoices';

    add_action( 'init', array( $this, 'create_tables' ), 1 );
  }

  /**
   * Create the custom database tables.
   */
  public function create_tables() {
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Create Jobs table
    $sql = "CREATE TABLE IF NOT EXISTS $this->jobs_table (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      title varchar(255) NOT NULL,
      description text,
      customer_id mediumint(9) NOT NULL,
      status varchar(20) NOT NULL DEFAULT 'pending',
      assigned_technician mediumint(9), 
      scheduled_date datetime, 
      created_at datetime DEFAULT CURRENT_TIMESTAMP,
      updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (id)
    ) $this->charset_collate;";

    dbDelta( $sql );

    // Create Customers table
    $sql = "CREATE TABLE IF NOT EXISTS $this->customers_table (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      name varchar(255) NOT NULL,
      email varchar(100),
      phone varchar(20),
      address text,
      created_at datetime DEFAULT CURRENT_TIMESTAMP,
      updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (id)
    ) $this->charset_collate;";

    dbDelta( $sql );

    // Create Invoices table
    $sql = "CREATE TABLE IF NOT EXISTS $this->invoices_table (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      job_id mediumint(9) NOT NULL,
      customer_id mediumint(9) NOT NULL,
      amount decimal(10,2) NOT NULL,
      status varchar(20) NOT NULL DEFAULT 'pending',
      due_date date,
      created_at datetime DEFAULT CURRENT_TIMESTAMP,
      updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (id)
    ) $this->charset_collate;";

    dbDelta( $sql );
  }
}

// Initialize the database class
ServiceHub_DB::get_instance();
?>