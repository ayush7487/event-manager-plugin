<?php

class EMP_Event_Meta_Boxes {

    /**
     * Hook into meta box and post save actions.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
        add_action( 'save_post_event', [ $this, 'save_event_meta' ] );
    }

    /**
     * Register the meta box on the "event" post type.
     */
    public function register_meta_boxes() {
        add_meta_box(
            'emp_event_meta_box',
            'Event Details',
            [ $this, 'render_meta_box' ],
            'event',
            'normal',
            'default'
        );
    }

    /**
     * Render the meta box UI using a template.
     *
     * @param WP_Post $post The current post object.
     */
    public function render_meta_box( $post ) {
        // Get existing meta values
        $meta = [
            'start_date'       => get_post_meta( $post->ID, '_emp_start_date', true ),
            'end_date'         => get_post_meta( $post->ID, '_emp_end_date', true ),
            'zoom_link'        => get_post_meta( $post->ID, '_emp_zoom_link', true ),
            'organizer_name'   => get_post_meta( $post->ID, '_emp_organizer_name', true ),
            'organizer_email'  => get_post_meta( $post->ID, '_emp_organizer_email', true ),
            'location'         => get_post_meta( $post->ID, '_emp_event_location', true ),
            'city'             => get_post_meta( $post->ID, '_emp_event_city', true ),
        ];

        wp_nonce_field( 'emp_save_event_meta', 'emp_event_meta_nonce' );

        // Load template
        include EMP_PATH . 'templates/admin-meta-box-event.php';
    }

    /**
     * Save custom meta fields on post save.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save_event_meta( $post_id ) {
        if ( ! isset( $_POST['emp_event_meta_nonce'] ) ||
             ! wp_verify_nonce( $_POST['emp_event_meta_nonce'], 'emp_save_event_meta' ) ) {
            return;
        }

        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

        if ( isset($_POST['emp_start_date']) ) {
            update_post_meta( $post_id, '_emp_start_date', sanitize_text_field($_POST['emp_start_date']) );
        }

        if ( isset($_POST['emp_end_date']) ) {
            update_post_meta( $post_id, '_emp_end_date', sanitize_text_field($_POST['emp_end_date']) );
        }

        if ( isset($_POST['zoom_link']) ) {
            update_post_meta( $post_id, '_emp_zoom_link', esc_url_raw($_POST['zoom_link']) );
        }

        if ( isset($_POST['emp_organizer_name']) ) {
            update_post_meta( $post_id, '_emp_organizer_name', sanitize_text_field($_POST['emp_organizer_name']) );
        }

        if ( isset($_POST['emp_organizer_email']) ) {
            update_post_meta( $post_id, '_emp_organizer_email', sanitize_email($_POST['emp_organizer_email']) );
        }

        if ( isset($_POST['emp_event_city']) ) {
            update_post_meta( $post_id, '_emp_event_city', sanitize_text_field($_POST['emp_event_city']) );
        }

        if ( isset($_POST['emp_organizer_phone']) ) {
            update_post_meta( $post_id, '_emp_organizer_phone', sanitize_text_field($_POST['emp_organizer_phone']) );
        }

        if ( isset($_POST['emp_event_location']) ) {
            update_post_meta( $post_id, '_emp_event_location', sanitize_textarea_field($_POST['emp_event_location']) );
        }
    }
}
