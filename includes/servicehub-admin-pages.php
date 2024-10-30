<?php
/**
 * ServiceHub Admin Pages Class
 *
 * This class handles the creation of custom admin pages.
 *
 * @package ServiceHub
 */

class ServiceHub_Admin_Pages {

  /**
   * Instance of the class.
   *
   * @var ServiceHub_Admin_Pages
   */
  private static $instance = null;

  /**
   * Get the instance of the class.
   *
   * @return ServiceHub_Admin_Pages
   */
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new ServiceHub_Admin_Pages();
    }
    return self::$instance;
  }

  /**
   * Constructor.
   */
  private function __construct() {
    add_action( 'admin_menu', array( $this, 'create_admin_pages' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
  }

  /**
   * Create the admin pages.
   */
  public function create_admin_pages() {
    add_menu_page(
      __( 'ServiceHub', 'servicehub' ),
      __( 'ServiceHub', 'servicehub' ),
      'manage_options',
      'servicehub',
      array( $this, 'render_dashboard_page' ),
      'dashicons-hammer', 
      20
    );

    add_submenu_page(
      'servicehub',
      __( 'Dashboard', 'servicehub' ),
      __( 'Dashboard', 'servicehub' ),
      'manage_options',
      'servicehub',
      array( $this, 'render_dashboard_page' )
    );

    add_submenu_page(
      'servicehub',
      __( 'Jobs', 'servicehub' ),
      __( 'Jobs', 'servicehub' ),
      'manage_options',
      'servicehub-jobs',
      array( $this, 'render_jobs_page' )
    );

    add_submenu_page(
      'servicehub',
      __( 'Customers', 'servicehub' ),
      __( 'Customers', 'servicehub' ),
      'manage_options',
      'servicehub-customers',
      array( $this, 'render_customers_page' )
    );

    add_submenu_page(
      'servicehub',
      __( 'Invoices', 'servicehub' ),
      __( 'Invoices', 'servicehub' ),
      'manage_options',
      'servicehub-invoices',
      array( $this, 'render_invoices_page' )
    );
  }

  /**
   * Render the dashboard page.
   */
  public function render_dashboard_page() {
    global $wpdb;

    $active_jobs_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}servicehub_jobs WHERE status = 'in_progress'" );

    $upcoming_jobs = $wpdb->get_results( "SELECT title FROM {$wpdb->prefix}servicehub_jobs WHERE scheduled_date >= CURDATE() ORDER BY scheduled_date ASC LIMIT 3" );

    $recent_customers = $wpdb->get_results( "SELECT name FROM {$wpdb->prefix}servicehub_customers ORDER BY created_at DESC LIMIT 3" );
    ?>
    <div class="wrap servicehub-page">
      <h1 class="wp-heading-inline"><?php esc_html_e( 'ServiceHub Dashboard', 'servicehub' ); ?></h1>

      <div class="servicehub-dashboard">
        <div class="servicehub-card">
          <h3><?php esc_html_e( 'Active Jobs', 'servicehub' ); ?></h3>
          <p>
            <?php
            printf(
              __( '%s active jobs', 'servicehub' ),
              '<strong>' . esc_html( $active_jobs_count ) . '</strong>'
            );
            ?>
          </p>
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=servicehub-jobs' ) ); ?>" class="button button-primary"><?php esc_html_e( 'View Jobs', 'servicehub' ); ?></a>
        </div>

        <div class="servicehub-card">
          <h3><?php esc_html_e( 'Upcoming Jobs', 'servicehub' ); ?></h3>
          <ul>
            <?php
            if ( $upcoming_jobs ) {
              foreach ( $upcoming_jobs as $job ) {
                echo '<li>' . esc_html( $job->title ) . '</li>';
              }
            } else {
              echo '<li>' . esc_html__( 'No upcoming jobs found.', 'servicehub' ) . '</li>';
            }
            ?>
          </ul>
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=servicehub-jobs' ) ); ?>" class="button button-primary"><?php esc_html_e( 'View Jobs', 'servicehub' ); ?></a>
        </div>

        <div class="servicehub-card">
          <h3><?php esc_html_e( 'Recent Customers', 'servicehub' ); ?></h3>
          <ul>
            <?php
            if ( $recent_customers ) {
              foreach ( $recent_customers as $customer ) {
                echo '<li>' . esc_html( $customer->name ) . '</li>';
              }
            } else {
              echo '<li>' . esc_html__( 'No recent customers found.', 'servicehub' ) . '</li>';
            }
            ?>
          </ul>
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=servicehub-customers' ) ); ?>" class="button button-primary"><?php esc_html_e( 'View Customers', 'servicehub' ); ?></a>
        </div>
      </div>
    </div>
    <?php
  }

  public function render_jobs_page() {
    global $wpdb;

    // Get all jobs from the database
    $jobs = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}servicehub_jobs" );
    ?>
    <div class="wrap servicehub-page">
      <h1 class="wp-heading-inline"><?php esc_html_e( 'Jobs', 'servicehub' ); ?></h1>
      <a href="#" class="page-title-action" id="servicehub-add-job-button"><?php esc_html_e( 'Add New Job', 'servicehub' ); ?></a>

      <table class="wp-list-table widefat fixed striped">
        <thead>
          <tr>
            <th><?php esc_html_e( 'ID', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Title', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Customer', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Status', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Scheduled Date', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Actions', 'servicehub' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ( $jobs ) {
            foreach ( $jobs as $job ) {
              echo '<tr>';
              echo '<td>' . esc_html( $job->id ) . '</td>';
              echo '<td>' . esc_html( $job->title ) . '</td>';
              echo '<td>' . esc_html( $this->get_customer_name( $job->customer_id ) ) . '</td>';
              echo '<td>' . esc_html( $job->status ) . '</td>';
              echo '<td>' . esc_html( $job->scheduled_date ) . '</td>';
              echo '<td>';
              echo '<a href="#" class="servicehub-edit-job" data-job-id="' . esc_attr( $job->id ) . '">' . esc_html__( 'Edit', 'servicehub' ) . '</a> | ';
              echo '<a href="#" class="servicehub-delete-job" data-job-id="' . esc_attr( $job->id ) . '">' . esc_html__( 'Delete', 'servicehub' ) . '</a>';
              echo '</td>';
              echo '</tr>';
            }
          } else {
            echo '<tr><td colspan="6">' . esc_html__( 'No jobs found.', 'servicehub' ) . '</td></tr>';
          }
          ?>
        </tbody>
      </table>

      <div id="servicehub-add-job-form" style="display: none;">
        <h2><?php esc_html_e( 'Add New Job', 'servicehub' ); ?></h2>
        <form id="add-job-form"> 
          <label for="job_title"><?php esc_html_e( 'Title', 'servicehub' ); ?></label>
          <input type="text" name="job_title" id="job_title" required>

          <label for="job_description"><?php esc_html_e( 'Description', 'servicehub' ); ?></label>
          <textarea name="job_description" id="job_description"></textarea>

          <label for="job_customer"><?php esc_html_e( 'Customer', 'servicehub' ); ?></label>
          <select name="job_customer" id="job_customer">
            <option value=""><?php esc_html_e( 'Select Customer', 'servicehub' ); ?></option>
            <?php
            // Fetch customers from database and populate options
            $customers = $wpdb->get_results( "SELECT id, name FROM {$wpdb->prefix}servicehub_customers" );
            foreach ( $customers as $customer ) {
              echo '<option value="' . esc_attr( $customer->id ) . '">' . esc_html( $customer->name ) . '</option>';
            }
            ?>
          </select>

          <label for="job_status"><?php esc_html_e( 'Status', 'servicehub' ); ?></label>
          <select name="job_status" id="job_status">
            <option value="pending"><?php esc_html_e( 'Pending', 'servicehub' ); ?></option>
            <option value="in_progress"><?php esc_html_e( 'In Progress', 'servicehub' ); ?></option>
            <option value="completed"><?php esc_html_e( 'Completed', 'servicehub' ); ?></option>
          </select>

          <label for="job_technician"><?php esc_html_e( 'Technician', 'servicehub' ); ?></label>
          <select name="job_technician" id="job_technician">
            <option value=""><?php esc_html_e( 'Select Technician', 'servicehub' ); ?></option>
            <?php
            // Fetch technicians from database (you'll need to implement this)
            // For now, let's add some placeholder technicians
            $technicians = array(
              'Technician 1',
              'Technician 2',
            );
            foreach ( $technicians as $technician ) {
              echo '<option value="' . esc_attr( $technician ) . '">' . esc_html( $technician ) . '</option>';
            }
            ?>
          </select>

          <label for="job_scheduled_date"><?php esc_html_e( 'Scheduled Date', 'servicehub' ); ?></label>
          <input type="date" name="job_scheduled_date" id="job_scheduled_date">

          <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Add Job', 'servicehub' ); ?>">
        </form>
      </div>

      <div id="servicehub-edit-job-form" style="display: none;">
        <h2><?php esc_html_e( 'Edit Job', 'servicehub' ); ?></h2>
        <form id="edit-job-form">
          <input type="hidden" name="job_id" id="edit_job_id"> 
          <label for="edit_job_title"><?php esc_html_e( 'Title', 'servicehub' ); ?></label>
          <input type="text" name="job_title" id="edit_job_title" required>

          <label for="edit_job_description"><?php esc_html_e( 'Description', 'servicehub' ); ?></label>
          <textarea name="job_description" id="edit_job_description"></textarea>

          <label for="edit_job_customer"><?php esc_html_e( 'Customer', 'servicehub' ); ?></label>
          <select name="job_customer" id="edit_job_customer">
            <option value=""><?php esc_html_e( 'Select Customer', 'servicehub' ); ?></option>
            <?php
            foreach ( $customers as $customer ) {
              echo '<option value="' . esc_attr( $customer->id ) . '">' . esc_html( $customer->name ) . '</option>';
            }
            ?>
          </select>

          <label for="edit_job_status"><?php esc_html_e( 'Status', 'servicehub' ); ?></label>
          <select name="job_status" id="edit_job_status">
            <option value="pending"><?php esc_html_e( 'Pending', 'servicehub' ); ?></option>
            <option value="in_progress"><?php esc_html_e( 'In Progress', 'servicehub' ); ?></option>
            <option value="completed"><?php esc_html_e( 'Completed', 'servicehub' ); ?></option>
          </select>

          <label for="edit_job_technician"><?php esc_html_e( 'Technician', 'servicehub' ); ?></label>
          <select name="job_technician" id="edit_job_technician">
            <option value=""><?php esc_html_e( 'Select Technician', 'servicehub' ); ?></option>
            <?php
            foreach ( $technicians as $technician ) {
              echo '<option value="' . esc_attr( $technician ) . '">' . esc_html( $technician ) . '</option>';
            }
            ?>
          </select>

          <label for="edit_job_scheduled_date"><?php esc_html_e( 'Scheduled Date', 'servicehub' ); ?></label>
          <input type="date" name="job_scheduled_date" id="edit_job_scheduled_date">

          <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Update Job', 'servicehub' ); ?>">
        </form>
      </div>
    </div>
    <?php
  }
          

  /**
   * Render the customers page.
   **/
 public function render_customers_page() {
    global $wpdb;

    // Get all customers from the database
    $customers = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}servicehub_customers" );
    ?>
    <div class="wrap servicehub-page">
      <h1 class="wp-heading-inline"><?php esc_html_e( 'Customers', 'servicehub' ); ?></h1>
      <a href="#" class="page-title-action" id="servicehub-add-customer-button"><?php esc_html_e( 'Add New Customer', 'servicehub' ); ?></a>

      <table class="wp-list-table widefat fixed striped">
        <thead>
          <tr>
            <th><?php esc_html_e( 'ID', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Name', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Email', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Phone', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Actions', 'servicehub' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ( $customers ) {
            foreach ( $customers as $customer ) {
              echo '<tr>';
              echo '<td>' . esc_html( $customer->id ) . '</td>';
              echo '<td>' . esc_html( $customer->name ) . '</td>';
              echo '<td>' . esc_html( $customer->email ) . '</td>';
              echo '<td>' . esc_html( $customer->phone ) . '</td>';
              echo '<td><a href="#">' . esc_html__( 'Edit', 'servicehub' ) . '</a> | <a href="#">' . esc_html__( 'Delete', 'servicehub' ) . '</a></td>';
              echo '</tr>';
            }
          } else {
            echo '<tr><td colspan="5">' . esc_html__( 'No customers found.', 'servicehub' ) . '</td></tr>';
          }
          ?>
        </tbody>
      </table>

      <div id="servicehub-add-customer-form" style="display: none;">
        <h2><?php esc_html_e( 'Add New Customer', 'servicehub' ); ?></h2>
        <form id="add-customer-form">
          <label for="customer_name"><?php esc_html_e( 'Name', 'servicehub' ); ?></label>
          <input type="text" name="customer_name" id="customer_name" required>

          <label for="customer_email"><?php esc_html_e( 'Email', 'servicehub' ); ?></label>
          <input type="email" name="customer_email" id="customer_email">

          <label for="customer_phone"><?php esc_html_e( 'Phone', 'servicehub' ); ?></label>
          <input type="tel" name="customer_phone" id="customer_phone">

          <label for="customer_address"><?php esc_html_e( 'Address', 'servicehub' ); ?></label>
          <textarea name="customer_address" id="customer_address"></textarea>

          <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Add Customer', 'servicehub' ); ?>">
        </form>
      </div>
    </div>
    <?php
  }

  /**
   * Render the invoices page.
   */
  public function render_invoices_page() {
    global $wpdb;

    // Get all invoices from the database
    $invoices = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}servicehub_invoices" );
    ?>
    <div class="wrap servicehub-page">
      <h1 class="wp-heading-inline"><?php esc_html_e( 'Invoices', 'servicehub' ); ?></h1>
      <a href="#" class="page-title-action" id="servicehub-add-invoice-button"><?php esc_html_e( 'Add New Invoice', 'servicehub' ); ?></a>

      <table class="wp-list-table widefat fixed striped">
        <thead>
          <tr>
            <th><?php esc_html_e( 'ID', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Job', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Customer', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Amount', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Status', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Due Date', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Actions', 'servicehub' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ( $invoices ) {
            foreach ( $invoices as $invoice ) {
              echo '<tr>';
              echo '<td>' . esc_html( $invoice->id ) . '</td>';
              echo '<td>' . esc_html( $this->get_job_title( $invoice->job_id ) ) . '</td>';
              echo '<td>' . esc_html( $this->get_customer_name( $invoice->customer_id ) ) . '</td>';
              echo '<td>' . esc_html( $invoice->amount ) . '</td>';
              echo '<td>' . esc_html( $invoice->status ) . '</td>';
              echo '<td>' . esc_html( $invoice->due_date ) . '</td>';
              echo '<td>';
              echo '<a href="#" class="servicehub-edit-invoice" data-invoice-id="' . esc_attr( $invoice->id ) . '">' . esc_html__( 'Edit', 'servicehub' ) . '</a> | ';
              echo '<a href="#" class="servicehub-delete-invoice" data-invoice-id="' . esc_attr( $invoice->id ) . '">' . esc_html__( 'Delete', 'servicehub' ) . '</a>';
              echo '</td>';
              echo '</tr>';
            }
          } else {
            echo '<tr><td colspan="7">' . esc_html__( 'No invoices found.', 'servicehub' ) . '</td></tr>';
          }
          ?>
        </tbody>
      </table>

      <div id="servicehub-add-invoice-form" style="display: none;">
        <h2><?php esc_html_e( 'Add New Invoice', 'servicehub' ); ?></h2>
        <form id="add-invoice-form">
          <label for="invoice_job"><?php esc_html_e( 'Job', 'servicehub' ); ?></label>
          <select name="invoice_job" id="invoice_job">
            <option value=""><?php esc_html_e( 'Select Job', 'servicehub' ); ?></option>
            <?php
            // Fetch jobs from database and populate options
            $jobs = $wpdb->get_results( "SELECT id, title FROM {$wpdb->prefix}servicehub_jobs" );
            foreach ( $jobs as $job ) {
              echo '<option value="' . esc_attr( $job->id ) . '">' . esc_html( $job->title ) . '</option>';
            }
            ?>
          </select>

          <label for="invoice_customer"><?php esc_html_e( 'Customer', 'servicehub' ); ?></label>
          <select name="invoice_customer" id="invoice_customer">
            <option value=""><?php esc_html_e( 'Select Customer', 'servicehub' ); ?></option>
            <?php
            // Fetch customers from database and populate options
            $customers = $wpdb->get_results( "SELECT id, name FROM {$wpdb->prefix}servicehub_customers" );
            foreach ( $customers as $customer ) {
              echo '<option value="' . esc_attr( $customer->id ) . '">' . esc_html( $customer->name ) . '</option>';
            }
            ?>
          </select>

          <label for="invoice_amount"><?php esc_html_e( 'Amount', 'servicehub' ); ?></label>
          <input type="number" name="invoice_amount" id="invoice_amount" step="0.01" required>

          <label for="invoice_status"><?php esc_html_e( 'Status', 'servicehub' ); ?></label>
          <select name="invoice_status" id="invoice_status">
            <option value="pending"><?php esc_html_e( 'Pending', 'servicehub' ); ?></option>
            <option value="paid"><?php esc_html_e( 'Paid', 'servicehub' ); ?></option>
            <option value="overdue"><?php esc_html_e( 'Overdue', 'servicehub' ); ?></option>
          </select>

          <label for="invoice_due_date"><?php esc_html_e( 'Due Date', 'servicehub' ); ?></label>
          <input type="date" name="invoice_due_date" id="invoice_due_date">

          <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Add Invoice', 'servicehub' ); ?>">
        </form>
      </div>

      <div id="servicehub-edit-invoice-form" style="display: none;">
        <h2><?php esc_html_e( 'Edit Invoice', 'servicehub' ); ?></h2>
        <form id="edit-invoice-form">
          <input type="hidden" name="invoice_id" id="edit_invoice_id">

          <label for="edit_invoice_job"><?php esc_html_e( 'Job', 'servicehub' ); ?></label>
          <select name="invoice_job" id="edit_invoice_job">
            <option value=""><?php esc_html_e( 'Select Job', 'servicehub' ); ?></option>
            <?php
            foreach ( $jobs as $job ) {
              echo '<option value="' . esc_attr( $job->id ) . '">' . esc_html( $job->title ) . '</option>';
            }
            ?>
          </select>

          <label for="edit_invoice_customer"><?php esc_html_e( 'Customer', 'servicehub' ); ?></label>
          <select name="invoice_customer" id="edit_invoice_customer">
            <option value=""><?php esc_html_e( 'Select Customer', 'servicehub' ); ?></option>
            <?php
            foreach ( $customers as $customer ) {
              echo '<option value="' . esc_attr( $customer->id ) . '">' . esc_html( $customer->name ) . '</option>';
            }
            ?>
          </select>

          <label for="edit_invoice_amount"><?php esc_html_e( 'Amount', 'servicehub' ); ?></label>
          <input type="number" name="invoice_amount" id="edit_invoice_amount" step="0.01" required>

          <label for="edit_invoice_status"><?php esc_html_e( 'Status', 'servicehub' ); ?></label>
          <select name="invoice_status" id="edit_invoice_status">
            <option value="pending"><?php esc_html_e( 'Pending', 'servicehub' ); ?></option>
            <option value="paid"><?php esc_html_e( 'Paid', 'servicehub' ); ?></option>
            <option value="overdue"><?php esc_html_e( 'Overdue', 'servicehub' ); ?></option>
          </select>

          <label for="edit_invoice_due_date"><?php esc_html_e( 'Due Date', 'servicehub' ); ?></label>
          <input type="date" name="invoice_due_date" id="edit_invoice_due_date">

          <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Update Invoice','servicehub' ); ?>">
        </form>
      </div>
    </div>
    <?php
  }

  /**
   * Enqueue scripts and styles.
   */
  public function enqueue_scripts( $hook ) {
    // Only enqueue on ServiceHub admin pages
    if ( strpos( $hook, 'servicehub' ) !== false ) {
      wp_enqueue_style( 'servicehub-admin-styles', SERVICEHUB_URL . 'assets/css/admin.css', array(), SERVICEHUB_VERSION );

      // Enqueue admin scripts and localize data
      wp_enqueue_script( 'servicehub-admin-scripts', SERVICEHUB_URL . 'assets/js/admin.js', array( 'jquery' ), SERVICEHUB_VERSION, true );
      wp_localize_script( 
        'servicehub-admin-scripts', 
        'servicehub_vars', 
        array( 
          'nonce' => wp_create_nonce( 'servicehub_add_job_nonce' ), // Nonce for adding jobs
          'customer_nonce' => wp_create_nonce( 'servicehub_add_customer_nonce' ), // Nonce for adding customers
          'invoice_nonce' => wp_create_nonce( 'servicehub_add_invoice_nonce' ) // Nonce for adding invoices
        ) 
      );
    }
  }

  /**
   * Get customer name by ID.
   *
   * @param int $customer_id The customer ID.
   * @return string The customer name.
   */
  private function get_customer_name( $customer_id ) {
    global $wpdb;
    $customer = $wpdb->get_row( "SELECT name FROM {$wpdb->prefix}servicehub_customers WHERE id = $customer_id" );
    return $customer ? $customer->name : '';
  }

  /**
   * Get job title by ID.
   *
   * @param int $job_id The job ID.
   * @return string The job title.
   */
  private function get_job_title( $job_id ) {
    global $wpdb;
    $job = $wpdb->get_row( "SELECT title FROM {$wpdb->prefix}servicehub_jobs WHERE id = $job_id" );
    return $job ? $job->title : '';
  }
}

// Initialize the admin pages class
ServiceHub_Admin_Pages::get_instance();


/**
 * AJAX handler for adding a new job.
 */
function servicehub_add_job_ajax_handler() {
  // Check nonce for security
  check_ajax_referer( 'servicehub_add_job_nonce', 'nonce' );

  // Get form data from AJAX request
  $formData = isset( $_POST['formData'] ) ? $_POST['formData'] : '';

  // Parse form data (you might need to adjust this based on your form fields)
  parse_str( $formData, $jobData );

  // Validate and sanitize form data
  $title              = sanitize_text_field( $jobData['job_title'] );
  $description        = sanitize_textarea_field( $jobData['job_description'] );
  $customer_id        = absint( $jobData['job_customer'] );
  $status             = sanitize_text_field( $jobData['job_status'] );
  $assigned_technician = absint( $jobData['job_technician'] );
  $scheduled_date     = sanitize_text_field( $jobData['job_scheduled_date'] );

  // Insert job data into database
  global $wpdb;
  $wpdb->insert(
    $wpdb->prefix . 'servicehub_jobs',
    array(
      'title'              => $title,
      'description'        => $description,
      'customer_id'        => $customer_id,
      'status'             => $status,
      'assigned_technician' => $assigned_technician,
      'scheduled_date'     => $scheduled_date,
    )
  );

  // Send AJAX response
  wp_send_json_success( array( 'message' => __( 'Job added successfully!', 'servicehub' ) ) );
}
add_action( 'wp_ajax_servicehub_add_job', 'servicehub_add_job_ajax_handler' ); 


/**
 * AJAX handler for adding a new customer.
 */
function servicehub_add_customer_ajax_handler() {
  // Check nonce for security
  check_ajax_referer( 'servicehub_add_customer_nonce', 'nonce' );

  // Get form data from AJAX request
  $formData = isset( $_POST['formData'] ) ? $_POST['formData'] : '';

  // Parse form data
  parse_str( $formData, $customerData );

  // Validate and sanitize form data
  $name    = sanitize_text_field( $customerData['customer_name'] );
  $email   = sanitize_email( $customerData['customer_email'] );
  $phone   = sanitize_text_field( $customerData['customer_phone'] );
  $address = sanitize_textarea_field( $customerData['customer_address'] );

  // Insert customer data into database
  global $wpdb;
  $wpdb->insert(
    $wpdb->prefix . 'servicehub_customers',
    array(
      'name'    => $name,
      'email'   => $email,
      'phone'   => $phone,
      'address' => $address,
    )
  );

  // Send AJAX response
  wp_send_json_success( array( 'message' => __( 'Customer added successfully!', 'servicehub' ) ) );
}
add_action( 'wp_ajax_servicehub_add_customer', 'servicehub_add_customer_ajax_handler' );

/**
 * AJAX handler for adding a new invoice.
 */
function servicehub_add_invoice_ajax_handler() {
  // Check nonce for security
  check_ajax_referer( 'servicehub_add_invoice_nonce', 'nonce' );

  // Get form data from AJAX request
  $formData = isset( $_POST['formData'] ) ? $_POST['formData'] : '';

  // Parse form data
  parse_str( $formData, $invoiceData );

  // Validate and sanitize form data
  $job_id      = absint( $invoiceData['invoice_job'] );
  $customer_id = absint( $invoiceData['invoice_customer'] );
  $amount      = floatval( $invoiceData['invoice_amount'] ); // Use floatval for amount
  $status      = sanitize_text_field( $invoiceData['invoice_status'] );
  $due_date    = sanitize_text_field( $invoiceData['invoice_due_date'] );

  // Insert invoice data into database
  global $wpdb;
  $wpdb->insert(
    $wpdb->prefix . 'servicehub_invoices',
    array(
      'job_id'      => $job_id,
      'customer_id' => $customer_id,
      'amount'      => $amount,
      'status'      => $status,
      'due_date'    => $due_date,
    )
  );

  // Send AJAX response
  wp_send_json_success( array( 'message' => __( 'Invoice added successfully!', 'servicehub' ) ) );
}
add_action( 'wp_ajax_servicehub_add_invoice', 'servicehub_add_invoice_ajax_handler' );

/**
 * AJAX handler for fetching job data for editing.
 */
function servicehub_get_job_data_ajax_handler() {
    // Check nonce for security (you might need to add a nonce for this)
    // ...
  
    // Get job ID from AJAX request
    $job_id = isset( $_POST['job_id'] ) ? absint( $_POST['job_id'] ) : 0;
  
    if ( $job_id ) {
      global $wpdb;
      $job = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}servicehub_jobs WHERE id = $job_id" );
  
      if ( $job ) {
        // Send AJAX response with job data
        wp_send_json_success( $job );
      } else {
        wp_send_json_error( array( 'message' => __( 'Job not found.', 'servicehub' ) ) );
      }
    } else {
      wp_send_json_error( array( 'message' => __( 'Invalid job ID.', 'servicehub' ) ) );
    }
  }
  add_action( 'wp_ajax_servicehub_get_job_data', 'servicehub_get_job_data_ajax_handler' );
  
  /**
   * AJAX handler for updating a job.
   */
  function servicehub_update_job_ajax_handler() {
    // Check nonce for security (you might need to add a nonce for this)
    // ...
  
    // Get form data from AJAX request
    $formData = isset( $_POST['formData'] ) ? $_POST['formData'] : '';
  
    // Parse form data
    parse_str( $formData, $jobData );
  
    // Validate and sanitize form data
    $job_id              = absint( $jobData['job_id'] );
    $title              = sanitize_text_field( $jobData['job_title'] );
    $description        = sanitize_textarea_field( $jobData['job_description'] );
    $customer_id        = absint( $jobData['job_customer'] );
    $status             = sanitize_text_field( $jobData['job_status'] );
    $assigned_technician = absint( $jobData['job_technician'] );
    $scheduled_date     = sanitize_text_field( $jobData['job_scheduled_date'] );
  
    // Update job data in database
    global $wpdb;
    $wpdb->update(
      $wpdb->prefix . 'servicehub_jobs',
      array(
        'title'              => $title,
        'description'        => $description,
        'customer_id'        => $customer_id,
        'status'             => $status,
        'assigned_technician' => $assigned_technician,
        'scheduled_date'     => $scheduled_date,
      ),
      array( 'id' => $job_id )
    );

      /**
   * Render the customers page.
   */
function render_customers_page() {
    global $wpdb;

    // Get all customers from the database
    $customers = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}servicehub_customers" );
    ?>
    <div class="wrap servicehub-page">
      <h1 class="wp-heading-inline"><?php esc_html_e( 'Customers', 'servicehub' ); ?></h1>
      <a href="#" class="page-title-action" id="servicehub-add-customer-button"><?php esc_html_e( 'Add New Customer', 'servicehub' ); ?></a>

      <table class="wp-list-table widefat fixed striped">
        <thead>
          <tr>
            <th><?php esc_html_e( 'ID', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Name', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Email', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Phone', 'servicehub' ); ?></th>
            <th><?php esc_html_e( 'Actions', 'servicehub' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ( $customers ) {
            foreach ( $customers as $customer ) {
              echo '<tr>';
              echo '<td>' . esc_html( $customer->id ) . '</td>';
              echo '<td>' . esc_html( $customer->name ) . '</td>';
              echo '<td>' . esc_html( $customer->email ) . '</td>';
              echo '<td>' . esc_html( $customer->phone ) . '</td>';
              echo '<td>';
              echo '<a href="#" class="servicehub-edit-customer" data-customer-id="' . esc_attr( $customer->id ) . '">' . esc_html__( 'Edit', 'servicehub' ) . '</a> | ';
              echo '<a href="#" class="servicehub-delete-customer" data-customer-id="' . esc_attr( $customer->id ) . '">' . esc_html__( 'Delete', 'servicehub' ) . '</a>';
              echo '</td>';
              echo '</tr>';
            }
          } else {
            echo '<tr><td colspan="5">' . esc_html__( 'No customers found.', 'servicehub' ) . '</td></tr>';
          }
          ?>
        </tbody>
      </table>

      <div id="servicehub-add-customer-form" style="display: none;">
        <h2><?php esc_html_e( 'Add New Customer', 'servicehub' ); ?></h2>
        <form id="add-customer-form">
          <label for="customer_name"><?php esc_html_e( 'Name', 'servicehub' ); ?></label>
          <input type="text" name="customer_name" id="customer_name" required>

          <label for="customer_email"><?php esc_html_e( 'Email', 'servicehub' ); ?></label>
          <input type="email" name="customer_email" id="customer_email">

          <label for="customer_phone"><?php esc_html_e( 'Phone', 'servicehub' ); ?></label>
          <input type="tel" name="customer_phone" id="customer_phone">

          <label for="customer_address"><?php esc_html_e( 'Address', 'servicehub' ); ?></label>
          <textarea name="customer_address" id="customer_address"></textarea>

          <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Add Customer', 'servicehub' ); ?>">
        </form>
      </div>

      <div id="servicehub-edit-customer-form" style="display: none;">
        <h2><?php esc_html_e( 'Edit Customer', 'servicehub' ); ?></h2>
        <form id="edit-customer-form">
          <input type="hidden" name="customer_id" id="edit_customer_id">
          <label for="edit_customer_name"><?php esc_html_e( 'Name', 'servicehub' ); ?></label>
          <input type="text" name="customer_name" id="edit_customer_name" required>

          <label for="edit_customer_email"><?php esc_html_e( 'Email', 'servicehub' ); ?></label>
          <input type="email" name="customer_email" id="edit_customer_email">

          <label for="edit_customer_phone"><?php esc_html_e( 'Phone', 'servicehub' ); ?></label>
          <input type="tel" name="customer_phone" id="edit_customer_phone">

          <label for="edit_customer_address"><?php esc_html_e( 'Address', 'servicehub' ); ?></label>
          <textarea name="customer_address" id="edit_customer_address"></textarea>

          <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Update Customer', 'servicehub' ); ?>">
        </form>
      </div>
    </div>
    <?php
  }
  
    // Send AJAX response
    wp_send_json_success( array( 'message' => __( 'Job updated successfully!', 'servicehub' ) ) );
  }
  add_action( 'wp_ajax_servicehub_update_job', 'servicehub_update_job_ajax_handler' );
  
  /**
   * AJAX handler for deleting a job.
   */
  function servicehub_delete_job_ajax_handler() {
    // Check nonce for security (you might need to add a nonce for this)
    // ...
  
    // Get job ID from AJAX request
    $job_id = isset( $_POST['job_id'] ) ? absint( $_POST['job_id'] ) : 0;
  
    if ( $job_id ) {
      global $wpdb;
      $wpdb->delete( $wpdb->prefix . 'servicehub_jobs', array( 'id' => $job_id ) );
  
      // Send AJAX response
      wp_send_json_success( array( 'message' => __( 'Job deleted successfully!', 'servicehub' ) ) );
    } else {
      wp_send_json_error( array( 'message' => __( 'Invalid job ID.', 'servicehub' ) ) );
    }
  }
  add_action( 'wp_ajax_servicehub_delete_job', 'servicehub_delete_job_ajax_handler' );
  
/**
 * AJAX handler for fetching customer data for editing.
 */
function servicehub_get_customer_data_ajax_handler() {
    // Check nonce for security (you might need to add a nonce for this)
    // ...
  
    // Get customer ID from AJAX request
    $customer_id = isset( $_POST['customer_id'] ) ? absint( $_POST['customer_id'] ) : 0;
  
    if ( $customer_id ) {
      global $wpdb;
      $customer = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}servicehub_customers WHERE id = $customer_id" );
  
      if ( $customer ) {
        // Send AJAX response with customer data
        wp_send_json_success( $customer );
      } else {
        wp_send_json_error( array( 'message' => __( 'Customer not found.', 'servicehub' ) ) );
      }
    } else {
      wp_send_json_error( array( 'message' => __( 'Invalid customer ID.', 'servicehub' ) ) );
    }
  }
  add_action( 'wp_ajax_servicehub_get_customer_data', 'servicehub_get_customer_data_ajax_handler' );
  
  /**
   * AJAX handler for updating a customer.
   */
  function servicehub_update_customer_ajax_handler() {
    // Check nonce for security (you might need to add a nonce for this)
    // ...
  
    // Get form data from AJAX request
    $formData = isset( $_POST['formData'] ) ? $_POST['formData'] : '';
  
    // Parse form data
    parse_str( $formData, $customerData );
  
    // Validate and sanitize form data
    $customer_id = absint( $customerData['customer_id'] );
    $name        = sanitize_text_field( $customerData['customer_name'] );
    $email       = sanitize_email( $customerData['customer_email'] );
    $phone       = sanitize_text_field( $customerData['customer_phone'] );
    $address     = sanitize_textarea_field( $customerData['customer_address'] );
  
    // Update customer data in database
    global $wpdb;
    $wpdb->update(
      $wpdb->prefix . 'servicehub_customers',
      array(
        'name'    => $name,
        'email'   => $email,
        'phone'   => $phone,
        'address' => $address,
      ),
      array( 'id' => $customer_id )
    );
  
    // Send AJAX response
    wp_send_json_success( array( 'message'=> __( 'Customer updated successfully!', 'servicehub' ) ) );
}
add_action( 'wp_ajax_servicehub_update_customer', 'servicehub_update_customer_ajax_handler' );

/**
 * AJAX handler for deleting a customer.
 */
function servicehub_delete_customer_ajax_handler() {
  // Check nonce for security (you might need to add a nonce for this)
  // ...

  // Get customer ID from AJAX request
  $customer_id = isset( $_POST['customer_id'] ) ? absint( $_POST['customer_id'] ) : 0;

  if ( $customer_id ) {
    global $wpdb;
    $wpdb->delete( $wpdb->prefix . 'servicehub_customers', array( 'id' => $customer_id ) );

    // Send AJAX response
    wp_send_json_success( array( 'message' => __( 'Customer deleted successfully!', 'servicehub' ) ) );
  } else {
    wp_send_json_error( array( 'message' => __( 'Invalid customer ID.', 'servicehub' ) ) );
  }
}
add_action( 'wp_ajax_servicehub_delete_customer', 'servicehub_delete_customer_ajax_handler' );

/**
 * AJAX handler for fetching invoice data for editing.
 */
function servicehub_get_invoice_data_ajax_handler() {
    // Check nonce for security (you might need to add a nonce for this)
    // ...
  
    // Get invoice ID from AJAX request
    $invoice_id = isset( $_POST['invoice_id'] ) ? absint( $_POST['invoice_id'] ) : 0;
  
    if ( $invoice_id ) {
      global $wpdb;
      $invoice = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}servicehub_invoices WHERE id = $invoice_id" );
  
      if ( $invoice ) {
        // Send AJAX response with invoice data
        wp_send_json_success( $invoice );
      } else {
        wp_send_json_error( array( 'message' => __( 'Invoice not found.', 'servicehub' ) ) );
      }
    } else {
      wp_send_json_error( array( 'message' => __( 'Invalid invoice ID.', 'servicehub' ) ) );
    }
  }
  add_action( 'wp_ajax_servicehub_get_invoice_data', 'servicehub_get_invoice_data_ajax_handler' );
  
  /**
   * AJAX handler for updating an invoice.
   */
  function servicehub_update_invoice_ajax_handler() {
    // Check nonce for security (you might need to add a nonce for this)
    // ...
  
    // Get form data from AJAX request
    $formData = isset( $_POST['formData'] ) ? $_POST['formData'] : '';
  
    // Parse form data
    parse_str( $formData, $invoiceData );
  
    // Validate and sanitize form data
    $invoice_id = absint( $invoiceData['invoice_id'] );
    $job_id      = absint( $invoiceData['invoice_job'] );
    $customer_id = absint( $invoiceData['invoice_customer'] );
    $amount      = floatval( $invoiceData['invoice_amount'] );
    $status      = sanitize_text_field( $invoiceData['invoice_status'] );
    $due_date    = sanitize_text_field( $invoiceData['invoice_due_date'] );
  
    // Update invoice data in database
    global $wpdb;
    $wpdb->update(
      $wpdb->prefix . 'servicehub_invoices',
      array(
        'job_id'      => $job_id,
        'customer_id' => $customer_id,
        'amount'      => $amount,
        'status'      => $status,
        'due_date'    => $due_date,
      ),
      array( 'id' => $invoice_id )
    );
  
    // Send AJAX response
    wp_send_json_success( array( 'message' => __( 'Invoice updated successfully!', 'servicehub' ) ) );
  }
  add_action( 'wp_ajax_servicehub_update_invoice', 'servicehub_update_invoice_ajax_handler' );
  
  /**
   * AJAX handler for deleting an invoice.
   */
  function servicehub_delete_invoice_ajax_handler() {
    // Check nonce for security (you might need to add a nonce for this)
    // ...
  
    // Get invoice ID from AJAX request
    $invoice_id = isset( $_POST['invoice_id'] ) ? absint( $_POST['invoice_id'] ) : 0;
  
    if ( $invoice_id ) {
      global $wpdb;
      $wpdb->delete( $wpdb->prefix . 'servicehub_invoices', array( 'id' => $invoice_id ) );
  
      // Send AJAX response
      wp_send_json_success( array( 'message' => __( 'Invoice deleted successfully!', 'servicehub' ) ) );
    } else {
      wp_send_json_error( array( 'message' => __( 'Invalid invoice ID.', 'servicehub' ) ) );
    }
  }
  add_action( 'wp_ajax_servicehub_delete_invoice', 'servicehub_delete_invoice_ajax_handler' );


?>