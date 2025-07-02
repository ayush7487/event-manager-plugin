<?php

class EMP_Event_Admin {

    /**
     * Hooks into WordPress admin actions and filters.
     */
    public function __construct() {
        add_action( 'restrict_manage_posts', [ $this, 'add_filters' ] );
        add_filter( 'parse_query', [ $this, 'filter_events' ] );
        add_filter( 'manage_edit-event_columns', [ $this, 'add_columns' ] );
        add_action( 'manage_event_posts_custom_column', [ $this, 'render_columns' ], 10, 2 );
        add_filter( 'bulk_actions-edit-event', [ $this, 'register_bulk_publish_action' ] );
        add_filter( 'handle_bulk_actions-edit-event', [ $this, 'handle_bulk_publish_action' ], 10, 3 );
        add_action( 'admin_notices', [ $this, 'bulk_publish_notice' ] );
    }

    /**
     * Add custom dropdown filters (City and Event Type) to the Event admin list screen.
     */
    public function add_filters() {
        global $typenow, $wpdb;
        if ( $typenow !== 'event' ) return;

        // City filter
        $selected_city = $_GET['event_city_filter'] ?? '';
        $cities = $wpdb->get_col( "
            SELECT DISTINCT meta_value FROM {$wpdb->postmeta}
            WHERE meta_key = '_emp_event_city' AND meta_value != ''
            ORDER BY meta_value ASC
        " );
        echo '<select name="event_city_filter">';
        echo '<option value="">All Cities</option>';
        foreach ( $cities as $city ) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($city),
                selected($selected_city, $city, false),
                esc_html($city)
            );
        }
        echo '</select>';

        // Event type filter
        $selected_type = $_GET['event_type_filter'] ?? '';
        ?>
        <select name="event_type_filter">
            <option value="">All Event Types</option>
            <option value="conference" <?php selected($selected_type, 'conference'); ?>>Conference</option>
            <option value="workshop" <?php selected($selected_type, 'workshop'); ?>>Workshop</option>
            <option value="webinar" <?php selected($selected_type, 'webinar'); ?>>Webinar</option>
        </select>
        <?php
    }

    /**
     * Filter the admin event list based on selected dropdown filters (type and city).
     *
     * @param WP_Query $query
     */
    public function filter_events($query) {
        global $pagenow;
        $post_type = $_GET['post_type'] ?? '';

        if ( $pagenow === 'edit.php' && $post_type === 'event' && is_admin() ) {
            $meta_query = [];

            if ( ! empty( $_GET['event_type_filter'] ) ) {
                $meta_query[] = [
                    'key'   => '_emp_event_type',
                    'value' => sanitize_text_field( $_GET['event_type_filter'] ),
                ];
            }

            if ( ! empty( $_GET['event_city_filter'] ) ) {
                $meta_query[] = [
                    'key'   => '_emp_event_city',
                    'value' => sanitize_text_field( $_GET['event_city_filter'] ),
                ];
            }

            if ( $meta_query ) {
                $query->set( 'meta_query', $meta_query );
            }
        }
    }

    /**
     * Add custom columns (Start Date, Event Type) to the Event admin list table.
     *
     * @param array $columns
     * @return array
     */
    public function add_columns($columns) {
        $columns['start_date'] = 'Start Date';
        $columns['event_type'] = 'Event Type';
        return $columns;
    }

    /**
     * Render content for custom columns in the Event admin list.
     *
     * @param string $column
     * @param int $post_id
     */
    public function render_columns($column, $post_id) {
        if ( $column === 'start_date' ) {
            $date = get_post_meta( $post_id, '_emp_start_date', true );
            echo $date ? esc_html( $date ) : '—';
        }

        if ( $column === 'event_type' ) {
            $type = get_post_meta( $post_id, '_emp_event_type', true );
            echo esc_html( ucfirst($type) );
        }
    }

    /**
     * Register custom bulk action "Publish Events" for event post type.
     *
     * @param array $bulk_actions
     * @return array
     */
    public function register_bulk_publish_action( $bulk_actions ) {
        $bulk_actions['bulk_publish_events'] = 'Publish Events';
        return $bulk_actions;
    }

    /**
     * Handle the custom bulk publish action for events.
     *
     * @param string $redirect_to
     * @param string $action
     * @param array $post_ids
     * @return string
     */
    public function handle_bulk_publish_action( $redirect_to, $action, $post_ids ) {
        if ( $action !== 'bulk_publish_events' ) return $redirect_to;

        $count = 0;

        foreach ( $post_ids as $post_id ) {
            $post = get_post( $post_id );
            if ( $post && $post->post_status !== 'publish' ) {
                wp_update_post([
                    'ID' => $post_id,
                    'post_status' => 'publish',
                ]);
                $count++;
            }
        }

        return add_query_arg( 'bulk_published_events', $count, $redirect_to );
    }

    /**
     * Show an admin notice after bulk publishing events.
     */
    public function bulk_publish_notice() {
        if ( isset($_REQUEST['bulk_published_events']) ) {
            $count = intval($_REQUEST['bulk_published_events']);
            printf(
                '<div class="notice notice-success is-dismissible"><p>%d event(s) published.</p></div>',
                $count
            );
        }
    }
}
