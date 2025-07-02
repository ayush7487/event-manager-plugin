<?php

/**
 * NOTE: This is the frontend event submission form template.
 * 
 * It is displayed using a shortcode and supports:
 * - Conditional logic (e.g., shows Zoom link field if "Webinar" is selected)
 * - Custom field inputs for event title, type, dates, organizer, city, location, and photo
 * - Basic HTML5 validation and client-side interaction
 *
 * The form submits via AJAX to avoid page reload and improve UX.
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<p>
    <label><strong>Start Date:</strong></label><br>
    <input type="date" name="emp_start_date" value="<?php echo esc_attr( $meta['start_date'] ); ?>" required />
</p>
<p>
    <label><strong>End Date:</strong></label><br>
    <input type="date" name="emp_end_date" value="<?php echo esc_attr( $meta['end_date'] ); ?>" required />
</p>
<p>
    <label><strong>Zoom Link (for webinars only):</strong></label><br>
    <input type="url" name="zoom_link" value="<?php echo esc_attr( $meta['zoom_link'] ); ?>" />
</p>
<p>
    <label><strong>Organizer Name:</strong></label><br>
    <input type="text" name="emp_organizer_name" value="<?php echo esc_attr( $meta['organizer_name'] ); ?>" />
</p>
<p>
    <label><strong>Organizer Email:</strong></label><br>
    <input type="email" name="emp_organizer_email" value="<?php echo esc_attr( $meta['organizer_email'] ); ?>" />
</p>
<p>
    <label><strong>City:</strong></label><br>
    <input type="text" name="emp_event_city" value="<?php echo esc_attr( $meta['city'] ); ?>" />
</p>
<p>
    <label><strong>Event Location:</strong></label><br>
    <textarea name="emp_event_location" rows="3"><?php echo esc_textarea( $meta['location'] ); ?></textarea>
</p>
