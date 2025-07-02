<?php

class EMP_Event_Post_Type {
    
    /**
     * Constructor to initialize the custom post type functionality.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register_post_type' ] );
    }

    /**
     * Registers the 'event' custom post type with appropriate labels and arguments.
     */
    public function register_post_type() {
        $labels = [
            'name'               => 'Events',
            'singular_name'      => 'Event',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Event',
            'edit_item'          => 'Edit Event',
            'new_item'           => 'New Event',
            'all_items'          => 'All Events',
            'view_item'          => 'View Event',
            'search_items'       => 'Search Events',
            'not_found'          => 'No events found',
            'not_found_in_trash' => 'No events found in Trash',
            'menu_name'          => 'Events',
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true,
            'rewrite'            => ['slug' => 'events'],
            'show_in_rest'       => true,
            'supports'           => ['title', 'editor', 'thumbnail'],
            'menu_icon'          => 'dashicons-calendar',
        ];

        register_post_type( 'event', $args );
    }

    /**
     * Called during plugin activation to register the post type and flush rewrite rules.
     */
    public static function activate() {
        $self = new self();
        $self->register_post_type();
        flush_rewrite_rules();
    }
}
