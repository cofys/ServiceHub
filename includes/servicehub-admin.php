<?php
/**
 * ServiceHub Admin Class
 *
 * This class handles the admin UI enhancements and workflow optimizations.
 *
 * @package ServiceHub
 */

class ServiceHub_Admin {

  /**
   * Instance of the class.
   *
   * @var ServiceHub_Admin
   */
  private static $instance = null;

  /**
   * Get the instance of the class.
   *
   * @return ServiceHub_Admin
   */
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new ServiceHub_Admin();
    }
    return self::$instance;
  }

  /**
   * Constructor.
   */
  private function __construct() {
    add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
    add_action( 'admin_init', array( $this, 'add_job_list_filters' ) );
    // Add more actions for other admin enhancements here
  }

  /**
   * Add dashboard widgets.
   */
  public function add_dashboard_widgets() {
    wp_add_dashboard_widget(
      'servicehub_active_jobs',
      __( 'Active Jobs', 'servicehub' ),
      array( $this, 'render_active_jobs_widget' )
    );

    wp_add_dashboard_widget(
      'servicehub_outstanding_invoices',
      __( 'Outstanding Invoices', 'servicehub' ),
      array( $this, 'render_outstanding_invoices_widget' )
    );
  }

  /**
   * Render the Active Jobs widget.
   */
  public function render_active_jobs_widget() {
    $active_jobs = wp_count_posts( 'job' );
    $count       = $active_jobs->publish; // Assuming 'publish' status for active jobs

    echo '<p>';
    printf(
      __( 'You have %s active jobs.', 'servicehub' ),
      '<strong>' . esc_html( $count ) . '</strong>'
    );
    echo '</p>';

    echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=job' ) ) . '" class="button button-primary">' . esc_html__( 'View Jobs', 'servicehub' ) . '</a>';
  }

  /**
   * Render the Outstanding Invoices widget.
   */
  public function render_outstanding_invoices_widget() {
    // This is a placeholder for now.
    // You'll need to implement the logic to calculate outstanding invoices here.
    // For example, you can query for invoices with 'pending' or 'overdue' status.

    $outstanding_invoices = 0; // Replace with your calculated value

    echo '<p>';
    printf(
      __( 'You have %s outstanding invoices.', 'servicehub' ),
      '<strong>' . esc_html( $outstanding_invoices ) . '</strong>'
    );
    echo '</p>';

    echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=invoice' ) ) . '" class="button button-primary">' . esc_html__( 'View Invoices', 'servicehub' ) . '</a>';
  }

  /**
   * Add filters to the Job list table.
   */
  public function add_job_list_filters() {
    $screen = get_current_screen();
    if ( 'edit-job' === $screen->id ) {
      $job_statuses = array(
        'pending'    => __( 'Pending', 'servicehub' ),
        'in_progress' => __( 'In Progress', 'servicehub' ),
        'completed'   => __( 'Completed', 'servicehub' ),
      );

      foreach ( $job_statuses as $value => $label ) {
        add_filter( 'parse_query', function( $query ) use ( $value, $label ) {
          if ( isset( $_GET['job_status'] ) && $_GET['job_status'] === $value ) {
            $query->query_vars['meta_key']   = '_job_status';
            $query->query_vars['meta_value'] = $value;
          }
          return $query;
        } );

        add_filter( 'views_' . $screen->id, function( $views ) use ( $screen, $value, $label ) {
          $count = $this->get_job_count_by_status( $value );
          $class = ( isset( $_GET['job_status'] ) && $_GET['job_status'] === $value ) ? 'current' : '';
          $views[ $value ] = '<a href="' . esc_url( add_query_arg( 'job_status', $value, $screen->parent_file ) ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $label ) . ' <span class="count">(' . $count . ')</span></a>';
          return $views;
        } );
      }
    }
  }

  /**
   * Get the number of jobs by status.
   *
   * @param string $status The job status.
   * @return int The number of jobs with the given status.
   */
  private function get_job_count_by_status( $status ) {
    $args = array(
      'post_type'      => 'job',
      'post_status'    => 'publish',
      'posts_per_page' => -1,
      'meta_key'       => '_job_status',
      'meta_value'     => $status,
    );
    $jobs = get_posts( $args );
    return count( $jobs );
  }
}

// Initialize the admin enhancements
ServiceHub_Admin::get_instance();
?>