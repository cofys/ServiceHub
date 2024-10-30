<?php
/**
 * ServiceHub Error Handling and Notices Class
 *
 * This class handles error handling and admin notices for ServiceHub.
 *
 * @package ServiceHub
 */

class ServiceHub_Notices {

  /**
   * Instance of the class.
   *
   * @var ServiceHub_Notices
   */
  private static $instance = null;

  /**
   * Get the instance of the class.
   *
   * @return ServiceHub_Notices
   */
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new ServiceHub_Notices();
    }
    return self::$instance;
  }

  /**
   * Constructor.
   */
  private function __construct() {
    add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );
    // Add actions for specific error handling here
  }

  /**
   * Display admin notices.
   */
  public function display_admin_notices() {
    // This is a placeholder for now.
    // You'll need to implement the logic to display notices based on different events or errors.
    // For example:
    if ( isset( $_GET['message'] ) && 'job_created' === $_GET['message'] ) {
      $this->show_notice( __( 'Job created successfully!', 'servicehub' ), 'success' );
    }

    if ( isset( $_GET['error'] ) && 'invalid_invoice_amount' === $_GET['error'] ) {
      $this->show_notice( __( 'Invalid invoice amount.', 'servicehub' ), 'error' );
    }
  }

  /**
   * Show an admin notice.
   *
   * @param string $message The notice message.
   * @param string $type    The type of notice (success, error, warning, info).
   */
  private function show_notice( $message, $type = 'info' ) {
    ?>
    <div class="notice notice-<?php echo esc_attr( $type ); ?> is-dismissible">
      <p><?php echo esc_html( $message ); ?></p>
    </div>
    <?php
  }
}

// Initialize the error handling and notices
ServiceHub_Notices::get_instance();
?>