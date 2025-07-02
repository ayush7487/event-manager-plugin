<?php

/**
 * Class to create a widget that displays a random event.
 */
class EMP_Event_Widget extends WP_Widget {

    /**
     * Constructor to initialize the event widget.
     */
    public function __construct() {
        parent::__construct(
            'emp_random_event_widget',
            __('Random Event', 'textdomain'),
            ['description' => __('Displays a random published event', 'textdomain')]
        );

        add_action( 'widgets_init', function() {
            register_widget( 'EMP_Event_Widget' );
        });
    }

    /**
     * Outputs the content of the widget.
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        echo $args['before_title'] . esc_html( $instance['title'] ?? 'Featured Event' ) . $args['after_title'];

        $event = get_posts([
            'post_type'      => 'event',
            'posts_per_page' => 1,
            'orderby'        => 'rand',
            'post_status'    => 'publish'
        ]);

        if ( ! empty( $event ) ) {
            $event = $event[0];
            $start_date = get_post_meta( $event->ID, '_emp_start_date', true );
            $location   = get_post_meta( $event->ID, '_emp_event_location', true );

            echo '<div class="emp-random-event">';
            echo '<h4><a href="' . get_permalink( $event ) . '">' . esc_html( get_the_title( $event ) ) . '</a></h4>';
            if ( $start_date ) echo '<p><strong>Date:</strong> ' . esc_html( date( 'M d, Y', strtotime( $start_date ) ) ) . '</p>';
            if ( $location ) echo '<p><strong>Location:</strong> ' . esc_html( $location ) . '</p>';
            echo '</div>';
        } else {
            echo '<p>No events found.</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin.
     *
     * @param array $instance
     */
    public function form( $instance ) {
        $title = $instance['title'] ?? 'Featured Event';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
        <?php
    }

    /**
     * Processing widget options on save.
     *
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        return [
            'title' => sanitize_text_field( $new_instance['title'] ?? '' )
        ];
    }
}
