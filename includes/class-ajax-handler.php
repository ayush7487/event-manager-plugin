<?php

class EMP_AJAX_Handler {

    /**
     * Handles AJAX form submissions for the Event Manager plugin.
     */
    public function __construct() {
        add_action( 'wp_ajax_submit_event_form', [ $this, 'handle_submission' ] );
        add_action( 'wp_ajax_nopriv_submit_event_form', [ $this, 'handle_submission' ] );
    }

    /**
     * Handles the event form submission via AJAX.
     *
     * Validates and sanitizes input, creates a new event post,
     * saves meta fields, uploads a featured image, and returns JSON response.
     */
    public function handle_submission() {
        check_ajax_referer( 'emp_event_form_nonce', 'nonce' );

        $title = sanitize_text_field( $_POST['event_title'] ?? '' );
        $type  = sanitize_text_field( $_POST['event_type'] ?? '' );
        $zoom  = esc_url_raw( $_POST['zoom_link'] ?? '' );
        $start = sanitize_text_field( $_POST['start_date'] ?? '' );
        $end   = sanitize_text_field( $_POST['end_date'] ?? '' );

        $organizer_name  = sanitize_text_field( $_POST['organizer_name'] ?? '' );
        $organizer_email = sanitize_email( $_POST['organizer_email'] ?? '' );
        $city = sanitize_text_field( $_POST['event_city'] ?? '' );

        $location        = sanitize_textarea_field( $_POST['event_location'] ?? '' );

        // Validate required fields
        if ( empty( $title ) || empty( $type ) || empty( $start ) || empty( $end ) ) {
            wp_send_json_error( 'Please fill in all required fields.' );
        }

        // Validate date logic
        $today = date( 'Y-m-d' );
        if ( $start < $today ) {
            wp_send_json_error( 'Start date cannot be in the past.' );
        }
        if ( $end < $start ) {
            wp_send_json_error( 'End date must be after start date.' );
        }

        // Create post with "pending_review" status
        $post_id = wp_insert_post([
            'post_type'   => 'event',
            'post_title'  => $title,
            'post_status' => 'pending_review',
        ]);

        update_post_meta( $post_id, '_emp_event_type', $type );

        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( 'Failed to submit event.' );
        }

        // Save meta fields
        update_post_meta( $post_id, '_emp_start_date', $start );
        update_post_meta( $post_id, '_emp_end_date', $end );
        update_post_meta( $post_id, '_emp_organizer_name', $organizer_name );
        update_post_meta( $post_id, '_emp_organizer_email', $organizer_email );
        update_post_meta( $post_id, '_emp_event_city', $city );
        update_post_meta( $post_id, '_emp_event_location', $location );
        if ( $type === 'webinar' ) {
            update_post_meta( $post_id, '_emp_zoom_link', $zoom );
        }

        // Handle file upload AFTER post is created
        if ( ! empty( $_FILES['event_photo']['name'] ) ) {
            $file     = $_FILES['event_photo'];
            $file_ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
            $allowed  = ['jpg', 'jpeg', 'png'];

            if ( ! in_array( strtolower( $file_ext ), $allowed ) ) {
                wp_send_json_error( 'Invalid image format. Only JPG and PNG allowed.' );
            }

            if ( $file['size'] > 2 * 1024 * 1024 ) {
                wp_send_json_error( 'Image file is too large. Max 2MB allowed.' );
            }

            require_once ABSPATH . 'wp-admin/includes/file.php';

            $upload = wp_handle_upload( $file, ['test_form' => false] );

            if ( isset( $upload['error'] ) ) {
                wp_send_json_error( 'Image upload failed: ' . $upload['error'] );
            }

            // Attach image to post
            $attachment = [
                'post_mime_type' => $upload['type'],
                'post_title'     => sanitize_file_name( $file['name'] ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            ];

            $attach_id = wp_insert_attachment( $attachment, $upload['file'], $post_id );
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            // Save as featured image
            set_post_thumbnail( $post_id, $attach_id );
        }

        wp_send_json_success( 'Event submitted successfully! Awaiting review.' );

        wp_die(); 
    }
}
