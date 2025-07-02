<?php

class EMP_Event_API {

    /**
     * Register REST route and shortcode on init.
     */
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
        add_shortcode( 'emp_event_list', [ $this, 'render_event_list_shortcode' ] );
    }

    /**
     * Register a custom REST API endpoint for fetching events.
     */
    public function register_routes() {
        register_rest_route( 'emp/v1', '/events', [
            'methods'  => 'GET',
            'callback' => [ $this, 'get_events' ],
            'permission_callback' => '__return_true'
        ]);
    }

    /**
     * REST API callback to retrieve all published events.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_events( $request ) {
        $events = get_posts([
            'post_type'      => 'event',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'meta_value',
            'meta_key'       => '_emp_start_date',
            'order'          => 'ASC',
        ]);

        $data = [];

        foreach ( $events as $event ) {
        $data[] = [
            'id'               => $event->ID,
            'title'            => get_the_title( $event ),
            'link'             => get_permalink( $event ),
            'start_date'       => get_post_meta( $event->ID, '_emp_start_date', true ),
            'end_date'         => get_post_meta( $event->ID, '_emp_end_date', true ),
            'event_type'       => get_post_meta( $event->ID, '_emp_event_type', true ),
            'location'         => get_post_meta( $event->ID, '_emp_event_location', true ),
            'city'             => get_post_meta( $event->ID, '_emp_event_city', true ),
            'organizer_name'   => get_post_meta( $event->ID, '_emp_organizer_name', true ),
            'organizer_email'  => get_post_meta( $event->ID, '_emp_organizer_email', true ),
            'zoom_link'        => get_post_meta( $event->ID, '_emp_zoom_link', true ),
        ];
    }

        return rest_ensure_response( $data );
    }

    /**
     * Shortcode to render a container that loads events via REST API using JS.
     *
     * @return string HTML output
     */
    public function render_event_list_shortcode() {
        wp_enqueue_script(
            'emp-event-fetch',
            plugins_url( '../assets/js/emp-event-fetch.js', __FILE__ ),
            [],
            '1.0',
            true
        );

        // Add REST URL as data attribute
        $api_url = esc_url( rest_url( 'emp/v1/events' ) );

        return '<div id="emp-event-list" data-api="' . $api_url . '">Loading events...</div>';
    }

}
