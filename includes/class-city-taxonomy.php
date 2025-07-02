<?php

class EMP_City_Taxonomy {

    /**
     * Register the city taxonomy on the "event" post type.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'register_taxonomy' ] );
    }

    /**
     * Register the "city" taxonomy.
     */
    public function register_taxonomy() {
        register_taxonomy( 'city', 'event', [
            'label'             => 'Cities',
            'hierarchical'      => true,
            'public'            => true,
            'rewrite'           => ['slug' => 'city'],
            'show_admin_column' => true,
            'show_in_rest'      => true,
        ] );
    }
}
