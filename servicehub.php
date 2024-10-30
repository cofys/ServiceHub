<?php
/**
 * Plugin Name: ServiceHub
 * Description: All-in-one business management plugin for service businesses.
 * Version: 1.4.0
 * Author: Gemini Advanced AI
 */

// Define plugin constants
define( 'SERVICEHUB_VERSION', '1.4.0' );
define( 'SERVICEHUB_PATH', plugin_dir_path( __FILE__ ) );
define( 'SERVICEHUB_URL', plugin_dir_url( __FILE__ ) );

// Include core modules
require_once SERVICEHUB_PATH . 'includes/job.php';
require_once SERVICEHUB_PATH . 'includes/customer.php';
require_once SERVICEHUB_PATH . 'includes/invoice.php';
require_once SERVICEHUB_PATH . 'includes/setup-wizard.php';
require_once SERVICEHUB_PATH . 'includes/servicehub-admin.php';
require_once SERVICEHUB_PATH . 'includes/servicehub-workflow.php';
require_once SERVICEHUB_PATH . 'includes/servicehub-notices.php'; 
require_once SERVICEHUB_PATH . 'includes/servicehub-db.php';
require_once SERVICEHUB_PATH . 'includes/servicehub-admin-pages.php';

// Initialize the plugin
function servicehub_init() {
  // Initialize core classes 
  Job::get_instance();
  Customer::get_instance();
  Invoice::get_instance();
  SetupWizard::get_instance();
}
add_action( 'plugins_loaded', 'servicehub_init' );

?>