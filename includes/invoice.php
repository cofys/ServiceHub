<?php
/**
 * Invoice Class
 *
 * This class handles the creation and management of invoices.
 *
 * @package ServiceHub
 */

class Invoice {

  /**
   * Instance of the class.
   *
   * @var Invoice
   */
  private static $instance = null;

  /**
   * Get the instance of the class.
   *
   * @return Invoice
   */
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new Invoice();
    }
    return self::$instance;
  }

  /**
   * Constructor.
   */
  private function __construct() {
    add_action( 'init', array( $this, 'register_post_type' ) );
    add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
    add_action( 'save_post_invoice', array( $this, 'save_post_meta' ) );
  }

  /**
   * Register the Invoice custom post type.
   */
  public function register_post_type() {
    $labels = array(
      'name'                  => __( 'Invoices', 'servicehub' ),
      'singular_name'         => __( 'Invoice', 'servicehub' ),
      'menu_name'             => __( 'Invoices', 'servicehub' ),
      'name_admin_bar'        => __( 'Invoice', 'servicehub' ),
      'add_new'               => __( 'Add New', 'servicehub' ),
      'add_new_item'          => __( 'Add New Invoice', 'servicehub' ),
      'new_item'              => __( 'New Invoice', 'servicehub' ),
      'edit_item'             => __( 'Edit Invoice', 'servicehub' ),
      'view_item'             => __( 'View Invoice', 'servicehub' ),
      'all_items'             => __( 'All Invoices', 'servicehub' ),
      'search_items'          => __( 'Search Invoices', 'servicehub' ),
      'parent_item_colon'     => __( 'Parent Invoices:', 'servicehub' ),
      'not_found'             => __( 'No invoices found.', 'servicehub' ),
      'not_found_in_trash'    => __( 'No invoices found in Trash.', 'servicehub' ),
      'featured_image'        => __( 'Featured Image', 'servicehub' ),
      'set_featured_image'    => __( 'Set featured image', 'servicehub' ),
      'remove_featured_image' => __( 'Remove featured image', 'servicehub' ),
      'use_featured_image'    => __( 'Use as featured image', 'servicehub' ),
      'archives'              => __( 'Invoice Archives', 'servicehub' ),
      'insert_into_item'      => __( 'Insert into invoice', 'servicehub' ),
      'uploaded_to_this_item' => __( 'Uploaded to this invoice', 'servicehub' ),
      'filter_items_list'     => __( 'Filter invoices list', 'servicehub' ),
      'items_list_navigation' => __( 'Invoices list navigation', 'servicehub' ),
      'items_list'            => __( 'Invoices list', 'servicehub' ),
    );

    $args = array(
      'labels'              => $labels,
      'public'              => true,
      'has_archive'         => true,
      'publicly_queryable'  => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'query_var'           => true,
      'rewrite'             => array( 'slug' => 'invoice' ),
      'capability_type'     => 'post',
      'has_archive'         => true,
      'hierarchical'        => false,
      'menu_position'       => null,
      'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
      'show_in_rest'       => true, // Add support for the REST API
    );

    register_post_type( 'invoice', $args );
  }

  /**
   * Add meta boxes to the Invoice post type.
   */
  public function add_meta_boxes() {
    add_meta_box(
      'invoice_status_meta_box',
      __( 'Invoice Status', 'servicehub' ),
      array( $this, 'render_status_meta_box' ),
      'invoice',
      'side',
      'high'
    );

    add_meta_box(
      'invoice_job_meta_box',
      __( 'Job', 'servicehub' ),
      array( $this, 'render_job_meta_box' ),
      'invoice',
      'side',
      'high'
    );

    add_meta_box(
      'invoice_customer_meta_box',
      __( 'Customer', 'servicehub' ),
      array( $this, 'render_customer_meta_box' ),
      'invoice',
      'side',
      'high'
    );
  }

  /**
   * Render the Invoice Status meta box.
   *
   * @param WP_Post $post The current post object.
   */
  public function render_status_meta_box( $post ) {
    $current_status = get_post_meta( $post->ID, '_invoice_status', true );
    $statuses       = array(
      'pending'  => __( 'Pending', 'servicehub' ),
      'paid'     => __( 'Paid', 'servicehub' ),
      'overdue'  => __( 'Overdue', 'servicehub' ),
    );

    ?>
    <select name="invoice_status" id="invoice_status">
      <?php foreach ( $statuses as $value => $label ) : ?>
        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_status, $value ); ?>><?php echo esc_html( $label ); ?></option>
      <?php endforeach; ?>
    </select>
    <?php
  }

  /**
   * Render the Job meta box.
   *
   * @param WP_Post $post The current post object.
   */
  public function render_job_meta_box( $post ) {
    $current_job = get_post_meta( $post->ID, '_invoice_job', true );
    $jobs        = get_posts(
      array(
        'post_type'      => 'job',
        'posts_per_page' => -1,
      )
    );
    ?>
    <select name="invoice_job" id="invoice_job">
      <option value=""><?php esc_html_e( 'Select a Job', 'servicehub' ); ?></option>
      <?php foreach ( $jobs as $job ) : ?>
        <option value="<?php echo esc_attr( $job->ID ); ?>" <?php selected( $current_job, $job->ID ); ?>><?php echo esc_html( $job->post_title ); ?></option>
      <?php endforeach; ?>
    </select>
    <?php
  }

  /**
   * Render the Customer meta box.
   *
   * @param WP_Post $post The current post object.
   */
  public function render_customer_meta_box( $post ) {
    $current_customer = get_post_meta( $post->ID, '_invoice_customer', true );
    $customers       = get_posts(
      array(
        'post_type'      => 'customer',
        'posts_per_page' => -1,
      )
    );
    ?>
    <select name="invoice_customer" id="invoice_customer">
      <option value=""><?php esc_html_e( 'Select a Customer', 'servicehub' ); ?></option>
      <?php foreach ( $customers as $customer ) : ?>
        <option value="<?php echo esc_attr( $customer->ID ); ?>" <?php selected( $current_customer, $customer->ID ); ?>><?php echo esc_html( $customer->post_title ); ?></option>
      <?php endforeach; ?>
    </select>
    <?php
  }

  /**
   * Save the Invoice meta data.
   *
   * @param int $post_id The ID of the post being saved.
   */
  public function save_post_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
      return;
    }

    if ( isset( $_POST['invoice_status'] ) ) {
        update_post_meta( $post_id, '_invoice_status', sanitize_text_field( $_POST['invoice_status'] ) );
      }
  
      if ( isset( $_POST['invoice_job'] ) ) {
        update_post_meta( $post_id, '_invoice_job', sanitize_text_field( $_POST['invoice_job'] ) );
      }
  
      if ( isset( $_POST['invoice_customer'] ) ) {
        update_post_meta( $post_id, '_invoice_customer', sanitize_text_field( $_POST['invoice_customer'] ) );
      }
    }
  }
  ?>