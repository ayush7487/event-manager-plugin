<?php

class EMP_Event_Roles {

    /**
     * Constructor to initialize the custom roles and capabilities.
     */
    public static function add_roles_and_caps() {
        // Add custom role
        add_role( 'event_manager', 'Event Manager', [
            'read'                   => true,
            'edit_events'           => true,
            'edit_others_events'    => true,
            'publish_events'        => true,
            'delete_events'         => true,
            'delete_others_events'  => true,
            'read_private_events'   => true,
        ] );

        // Register capabilities for event post type
        $role = get_role( 'event_manager' );
        if ( $role ) {
            $caps = [
                'edit_event',
                'read_event',
                'delete_event',
                'edit_events',
                'edit_others_events',
                'publish_events',
                'read_private_events',
                'delete_events',
                'delete_others_events',
            ];

            foreach ( $caps as $cap ) {
                $role->add_cap( $cap );
            }
        }
    }

    /**
     * Remove custom roles and capabilities.
     */
    public static function remove_roles_and_caps() {
        remove_role( 'event_manager' );
    }
}
