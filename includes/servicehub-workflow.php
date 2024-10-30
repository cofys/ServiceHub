<?php
/**
 * ServiceHub Workflow Automation Class
 *
 * This class handles the automation of workflows within ServiceHub.
 *
 * @package ServiceHub
 */

class ServiceHub_Workflow {

  /**
   * Instance of the class.
   *
   * @var ServiceHub_Workflow
   */
  private static $instance = null;

  /**
   * Get the instance of the class.
   *
   * @return ServiceHub_Workflow
   */
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new ServiceHub_Workflow();
    }
    return self::$instance;
  }

  /**
   * Constructor.
   */
  private function __construct() {
    add_action( 'save_post_invoice', array( $this, 'update_job_status_on_invoice_paid' ) );
    // Add more actions for other workflow automations here
  }

  /**
   * Update the job status to "completed" when an invoice is marked as "paid".
   *
   * @param int $post_id The ID of the invoice post being saved.
   */
  public function update_job_status_on_invoice_paid( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
      return;
    }

    if ( isset( $_POST['invoice_status'] ) && 'paid' === $_POST['invoice_status'] ) {
      $job_id = get_post_meta( $post_id, '_invoice_job', true );
      if ( ! empty( $job_id ) ) {
        update_post_meta( $job_id, '_job_status', 'completed' );
      }
    }
  }
}

// Initialize the workflow automation
ServiceHub_Workflow::get_instance();
?>