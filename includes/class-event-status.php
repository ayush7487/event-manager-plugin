<?php

class EMP_Event_Status {

    /**
     * Constructor to initialize the custom post status functionality.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register_status' ] );
        add_filter( 'display_post_states', [ $this, 'show_custom_state' ], 10, 2 );
        add_filter( 'post_status_list', [ $this, 'add_to_status_dropdown' ] );
        add_filter( 'views_edit-event', [ $this, 'modify_status_list' ] );
    }

    /**
     * Register the custom post status "Pending Review".
     */
    public function register_status() {
        register_post_status( 'pending_review', [
            'label'                     => _x( 'Pending Review', 'post' ),
            'public'                    => true,
            'internal'                  => false,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop(
                'Pending Review <span class="count">(%s)</span>',
                'Pending Review <span class="count">(%s)</span>'
            ),
        ] );
    }

    /**
     * Display the custom post status in the post list.
     *
     * @param array $states
     * @param WP_Post $post
     * @return array
     */
    public function show_custom_state( $states, $post ) {
        if ( get_post_status( $post->ID ) === 'pending_review' ) {
            $states[] = 'Pending Review';
        }
        return $states;
    }

    /**
     * Add "Pending Review" to the post status dropdown in the admin.
     *
     * @param array $statuses
     * @return array
     */
    public function add_to_status_dropdown( $statuses ) {
        global $post;
        if ( is_object( $post ) && $post->post_type === 'event' ) {
            $statuses['pending_review'] = __( 'Pending Review', 'textdomain' );
        }
        return $statuses;
    }

    /**
     * Modify the post status list in the admin to include "Pending Review".
     *
     * @param array $views
     * @return array
     */
    public function modify_status_list( $views ) {
        global $wpdb;

        $count = $wpdb->get_var( "
            SELECT COUNT(1)
            FROM $wpdb->posts
            WHERE post_type = 'event'
            AND post_status = 'pending_review'
        " );

        if ( $count > 0 ) {
            $class = ( $_GET['post_status'] ?? '' ) === 'pending_review' ? 'current' : '';
            $views['pending_review'] = "<a href='edit.php?post_status=pending_review&post_type=event' class='{$class}'>" .
                __('Pending Review') . " <span class='count'>($count)</span></a>";
        }

        return $views;
    }
}
