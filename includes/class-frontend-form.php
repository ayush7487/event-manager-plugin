<?php

class EMP_Frontend_Form {

    /**
     * Constructor to initialize the frontend form functionality.
     */
    public function __construct() {
        add_shortcode( 'event_submission_form', [ $this, 'render_form_shortcode' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    /**
     * Enqueue frontend scripts and styles.
     */
     public function enqueue_assets() {
        wp_enqueue_script( 'emp-frontend-js', EMP_URL . 'assets/js/frontend-form.js', ['jquery'], '1.0', true );
        wp_localize_script( 'emp-frontend-js', 'emp_ajax_object', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'emp_event_form_nonce' )
        ]);
        wp_enqueue_style( 'emp-frontend-css', EMP_URL . 'assets/style.css', [], '1.0' );
    }

    /**
     * Render the event submission form shortcode.
     *
     * @return string
     */
    public function render_form_shortcode() {
        ob_start();
        include EMP_PATH . 'templates/frontend-form-template.php';
        return ob_get_clean();
    }
}

