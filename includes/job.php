<?php
/**
 * Job Class
 *
 * This class handles the creation and management of jobs.
 *
 * @package ServiceHub
 */

class Job {

  /**
   * Instance of the class.
   *
   * @var Job
   */
  private static $instance = null;

  /**
   * Get the instance of the class.
   *
   * @return Job
   */
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new Job();
    }
    return self::$instance;
  }

  /**
   * Constructor.
   */
  private function __construct() {
    add_action( 'init', array( $this, 'register_post_type' ) );
    add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
    add_action( 'save_post_job', array( $this, 'save_post_meta' ) );
  }

  /**
   * Register the Job custom post type.
   */
  public function register_post_type() {
    $labels = array(
      'name'                  => __( 'Jobs', 'servicehub' ),
      'singular_name'         => __( 'Job', 'servicehub' ),
      'menu_name'             => __( 'Jobs', 'servicehub' ),
      'name_admin_bar'        => __( 'Job', 'servicehub' ),
      'add_new'               => __( 'Add New', 'servicehub' ),
      'add_new_item'          => __( 'Add New Job', 'servicehub' ),
      'new_item'              => __( 'New Job', 'servicehub' ),
      'edit_item'             => __( 'Edit Job', 'servicehub' ),
      'view_item'             => __( 'View Job', 'servicehub' ),
      'all_items'             => __( 'All Jobs', 'servicehub' ),
      'search_items'          => __( 'Search Jobs', 'servicehub' ),
      'parent_item_colon'     => __( 'Parent Jobs:', 'servicehub' ),
      'not_found'             => __( 'No jobs found.', 'servicehub' ),
      'not_found_in_trash'    => __( 'No jobs found in Trash.', 'servicehub' ),
      'featured_image'        => __( 'Featured Image', 'servicehub' ),
      'set_featured_image'    => __( 'Set featured image', 'servicehub' ),
      'remove_featured_image' => __( 'Remove featured image', 'servicehub' ),
      'use_featured_image'    => __( 'Use as featured image', 'servicehub' ),
      'archives'              => __( 'Job Archives', 'servicehub' ),
      'insert_into_item'      => __( 'Insert into job', 'servicehub' ),
      'uploaded_to_this_item' => __( 'Uploaded to this job', 'servicehub' ),
      'filter_items_list'     => __( 'Filter jobs list', 'servicehub' ),
      'items_list_navigation' => __( 'Jobs list navigation', 'servicehub' ),
      'items_list'            => __( 'Jobs list', 'servicehub' ),
    );

    $args = array(
      'labels'              => $labels,
      'public'              => true,
      'has_archive'         => true,
      'publicly_queryable'  => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'query_var'           => true,
      'rewrite'             => array( 'slug' => 'job' ),
      'capability_type'     => 'post',
      'has_archive'         => true,
      'hierarchical'        => false,
      'menu_position'       => null,
      'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
      'show_in_rest'       => true, // Add support for the REST API
    );

    register_post_type( 'job', $args );
  }

  /**
   * Add meta boxes to the Job post type.
   */
  public function add_meta_boxes() {
    add_meta_box(
      'job_status_meta_box',
      __( 'Job Status', 'servicehub' ),
      array( $this, 'render_status_meta_box' ),
      'job',
      'side',
      'high'
    );

    add_meta_box(
      'job_customer_meta_box',
      __( 'Customer', 'servicehub' ),
      array( $this, 'render_customer_meta_box' ),
      'job',
      'side',
      'high'
    );
  }

  /**
   * Render the Job Status meta box.
   *
   * @param WP_Post $post The current post object.
   */
  public function render_status_meta_box( $post ) {
    $current_status = get_post_meta( $post->ID, '_job_status', true );
    $statuses       = array(
      'pending'    => __( 'Pending', 'servicehub' ),
      'in_progress' => __( 'In Progress', 'servicehub' ),
      'completed'   => __( 'Completed', 'servicehub' ),
    );

    ?>
    <select name="job_status" id="job_status">
      <?php foreach ( $statuses as $value => $label ) : ?>
        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_status, $value ); ?>><?php echo esc_html( $label ); ?></option>
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
    $current_customer = get_post_meta( $post->ID, '_job_customer', true );
    $customers       = get_posts(
      array(
        'post_type'      => 'customer',
        'posts_per_page' => -1,
      )
    );
    ?>
    <select name="job_customer" id="job_customer">
      <option value=""><?php esc_html_e( 'Select a Customer', 'servicehub' ); ?></option>
      <?php foreach ( $customers as $customer ) : ?>
        <option value="<?php echo esc_attr( $customer->ID ); ?>" <?php selected( $current_customer, $customer->ID ); ?>><?php echo esc_html( $customer->post_title ); ?></option>
      <?php endforeach; ?>
    </select>
    <?php
  }

  /**
   * Save the Job meta data.
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

    if ( isset( $_POST['job_status'] ) ) {
      update_post_meta( $post_id, '_job_status', sanitize_text_field( $_POST['job_status'] ) );
    }

    if ( isset( $_POST['job_customer'] ) ) {
      update_post_meta( $post_id, '_job_customer', sanitize_text_field( $_POST['job_customer'] ) );
    }
  }
}
?>