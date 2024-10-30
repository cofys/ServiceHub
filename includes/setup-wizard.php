<?php
/**
 * Setup Wizard Class
 *
 * This class handles the plugin setup wizard.
 *
 * @package ServiceHub
 */

class SetupWizard {

  /**
   * Instance of the class.
   *
   * @var SetupWizard
   */
  private static $instance = null;

  /**
   * Get the instance of the class.
   *
   * @return SetupWizard
   */
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new SetupWizard();
    }
    return self::$instance;
  }

  /**
   * Constructor.
   */
  private function __construct() {
    add_action( 'admin_menu', array( $this, 'add_setup_menu' ) );
    add_action( 'admin_init', array( $this, 'setup_wizard_actions' ) );
  }

  /**
   * Add the setup menu to the WordPress admin.
   */
  public function add_setup_menu() {
    add_dashboard_page(
      __( 'ServiceHub Setup', 'servicehub' ),
      __( 'ServiceHub Setup', 'servicehub' ),
      'manage_options',
      'servicehub-setup',
      array( $this, 'render_setup_page' )
    );
  }

  /**
   * Render the setup page.
   */
  public function render_setup_page() {
    $current_step = isset( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;
    ?>
    <div class="wrap">
      <h1><?php esc_html_e( 'ServiceHub Setup Wizard', 'servicehub' ); ?></h1>
      <p><?php esc_html_e( 'Welcome to ServiceHub! This wizard will guide you through the initial setup.', 'servicehub' ); ?></p>

      <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="servicehub_setup_wizard">
        <input type="hidden" name="step" value="<?php echo esc_attr( $current_step ); ?>">
        <?php wp_nonce_field( 'servicehub_setup_wizard' ); ?>

        <?php 
        switch ( $current_step ) {
          case 1:
            $this->render_step_1();
            break;
          case 2:
            $this->render_step_2();
            break;
          case 3:
            $this->render_step_3();
            break;
          default:
            $this->render_step_1();
            break;
        }
        ?>

        <?php if ( $current_step < 3 ) : ?>
          <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Next', 'servicehub' ); ?>">
        <?php else : ?>
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=servicehub-setup&step=1' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Back to Start', 'servicehub' ); ?></a>
        <?php endif; ?>
      </form>
    </div>
    <?php
  }

  /**
   * Render step 1 of the setup wizard.
   */
  private function render_step_1() {
    ?>
    <h2><?php esc_html_e( 'Step 1: Welcome', 'servicehub' ); ?></h2>
    <p><?php esc_html_e( 'Thank you for choosing ServiceHub! This plugin will help you manage your service business more efficiently.', 'servicehub' ); ?></p>
    <?php
  }

  /**
   * Render step 2 of the setup wizard.
   */
  private function render_step_2() {
    ?>
    <h2><?php esc_html_e( 'Step 2: Create Default Job Statuses', 'servicehub' ); ?></h2>
    <p><?php esc_html_e( 'ServiceHub uses custom statuses to track the progress of your jobs. We will create some default statuses for you.', 'servicehub' ); ?></p>
    <?php
  }

  /**
   * Render step 3 of the setup wizard.
   */
  private function render_step_3() {
    ?>
    <h2><?php esc_html_e( 'Step 3: Setup Complete', 'servicehub' ); ?></h2>
    <p><?php esc_html_e( 'Congratulations! ServiceHub is now set up and ready to use.', 'servicehub' ); ?></p>
    <p><?php esc_html_e( 'You can now start adding jobs, customers, and invoices.', 'servicehub' ); ?></p>
    <?php
  }

  /**
   * Handle setup wizard actions.
   */
  public function setup_wizard_actions() {
    if ( isset( $_POST['action'] ) && 'servicehub_setup_wizard' === $_POST['action'] ) {
      check_admin_referer( 'servicehub_setup_wizard' );

      $step = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : 1;

      switch ( $step ) {
        case 1:
          // Redirect to step 2
          wp_redirect( admin_url( 'admin.php?page=servicehub-setup&step=2' ) );
          exit;
        case 2:
          // Create default job statuses
          $this->create_default_job_statuses();

          // Redirect to step 3
          wp_redirect( admin_url( 'admin.php?page=servicehub-setup&step=3' ) );
          exit;
        default:
          // Redirect to step 1
          wp_redirect( admin_url( 'admin.php?page=servicehub-setup&step=1' ) );
          exit;
      }
    }
  }

  /**
   * Create default job statuses.
   */
  private function create_default_job_statuses() {
    // This is a placeholder for now.
    // You'll need to implement the logic to create job statuses here.
    // You can use WordPress options or any other suitable method.
    // For example:
    // update_option( 'servicehub_job_statuses', array( 'pending', 'in_progress', 'completed' ) );
  }
}
?>