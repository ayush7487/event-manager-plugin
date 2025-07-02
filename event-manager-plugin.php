<?php
/**
 * Plugin Name: Event Manager Plugin
 * Description: Manage events with custom meta boxes, cities, and frontend forms.
 * Version: 1.0.0
 * Author: Ayush Jha
 * Text Domain: event-manager-plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main Plugin Class
 */
final class Event_Manager_Plugin {

    /**
     * Constructor - Initializes the plugin
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define plugin constants
     */
    private function define_constants() {
        define( 'EMP_VERSION', '1.0.0' );
        define( 'EMP_PATH', plugin_dir_path( __FILE__ ) );
        define( 'EMP_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include all core classes
     */
    private function includes() {
        require_once EMP_PATH . 'includes/class-event-post-type.php';
        require_once EMP_PATH . 'includes/class-city-taxonomy.php';
        require_once EMP_PATH . 'includes/class-event-meta-boxes.php';
        require_once EMP_PATH . 'includes/class-frontend-form.php';
        require_once EMP_PATH . 'includes/class-ajax-handler.php';
        require_once EMP_PATH . 'includes/class-event-admin.php';
        require_once EMP_PATH . 'includes/class-event-status.php';
        require_once EMP_PATH . 'includes/class-event-widget.php';
        require_once EMP_PATH . 'includes/class-event-roles.php';
        require_once EMP_PATH . 'includes/class-event-api.php';
    }

    /**
     * Add plugin initialization hooks
     */
    private function init_hooks() {
        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
    }

    /**
     * Initialize plugin components
     */
    public function init_plugin() {
        new EMP_Event_Post_Type();
        new EMP_City_Taxonomy();
        new EMP_Event_Meta_Boxes();
        new EMP_Frontend_Form();
        new EMP_AJAX_Handler();
        new EMP_Event_Admin();
        new EMP_Event_Status();
        new EMP_Event_Widget();
        new EMP_Event_Roles();
        new EMP_Event_API();
    }

    /**
     * Plugin Activation Hook
     */
    public static function activate() {
        require_once EMP_PATH . 'includes/class-event-roles.php';
        EMP_Event_Roles::add_roles_and_caps();
        flush_rewrite_rules();
    }

    /**
     * Plugin Deactivation Hook
     */
    public static function deactivate() {
        require_once EMP_PATH . 'includes/class-event-roles.php';
        EMP_Event_Roles::remove_roles_and_caps();
        flush_rewrite_rules();
    }
}

// Plugin activation/deactivation
register_activation_hook( __FILE__, [ 'Event_Manager_Plugin', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'Event_Manager_Plugin', 'deactivate' ] );

// Start the plugin
new Event_Manager_Plugin();
