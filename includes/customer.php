<?php
/**
 * Customer Class
 *
 * This class handles the creation and management of customers.
 *
 * @package ServiceHub
 */

class Customer {

  /**
   * Instance of the class.
   *
   * @var Customer
   */
  private static $instance = null;

  /**
   * Get the instance of the class.
   *
   * @return Customer
   */
  public static function get_instance() {
    if ( self::$instance == null ) {
      self::$instance = new Customer();
    }
    return self::$instance;
  }

  /**
   * Constructor.
   */
  private function __construct() {
    add_action( 'init', array( $this, 'register_post_type' ) );
    // Add meta boxes and other functionality for customer details later
  }

  /**
   * Register the Customer custom post type.
   */
  public function register_post_type() {
    $labels = array(
      'name'                  => __( 'Customers', 'servicehub' ),
      'singular_name'         => __( 'Customer', 'servicehub' ),
      'menu_name'             => __( 'Customers', 'servicehub' ),
      'name_admin_bar'        => __( 'Customer', 'servicehub' ),
      'add_new'               => __( 'Add New', 'servicehub' ),
      'add_new_item'          => __( 'Add New Customer', 'servicehub' ),
      'new_item'              => __( 'New Customer', 'servicehub' ),
      'edit_item'             => __( 'Edit Customer', 'servicehub' ),
      'view_item'             => __( 'View Customer', 'servicehub' ),
      'all_items'             => __( 'All Customers', 'servicehub' ),
      'search_items'          => __( 'Search Customers', 'servicehub' ),
      'parent_item_colon'     => __( 'Parent Customers:', 'servicehub' ),
      'not_found'             => __( 'No customers found.', 'servicehub' ),
      'not_found_in_trash'    => __( 'No customers found in Trash.', 'servicehub' ),
      'featured_image'        => __( 'Featured Image', 'servicehub' ),
      'set_featured_image'    => __( 'Set featured image', 'servicehub' ),
      'remove_featured_image' => __( 'Remove featured image', 'servicehub' ),
      'use_featured_image'    => __( 'Use as featured image', 'servicehub' ),
      'archives'              => __( 'Customer Archives', 'servicehub' ),
      'insert_into_item'      => __( 'Insert into customer', 'servicehub' ),
      'uploaded_to_this_item' => __( 'Uploaded to this customer', 'servicehub' ),
      'filter_items_list'     => __( 'Filter customers list', 'servicehub' ),
      'items_list_navigation' => __( 'Customers list navigation', 'servicehub' ),
      'items_list'            => __( 'Customers list', 'servicehub' ),
    );

    $args = array(
      'labels'              => $labels,
      'public'              => true,
      'has_archive'         => true,
      'publicly_queryable'  => true,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'query_var'           => true,
      'rewrite'             => array( 'slug' => 'customer' ),
      'capability_type'     => 'post',
      'has_archive'         => true,
      'hierarchical'        => false,
      'menu_position'       => null,
      'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
      'show_in_rest'       => true, // Add support for the REST API
    );

    register_post_type( 'customer', $args );
  }
}
?>