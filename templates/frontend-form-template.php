<?php
/**
 * Frontend Event Submission Form
 *
 * NOTE:
 * This is the HTML template for submitting events from the frontend.
 * It supports:
 * - Required fields: title, event type, start/end dates, city
 * - Conditional display: shows Zoom link only when 'Webinar' is selected
 * - Optional fields: organizer name/email, location, event photo
 * - File input: allows JPEG/PNG image upload (max 2MB, validated in JS/PHP)
 *
 * This form should be:
 * - Enqueued with frontend-form.js to handle conditional logic + AJAX submission
 * - Processed via a registered AJAX action in your plugin
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<form id="emp-event-form" method="post">
    <p>
        <label>Event Title *</label><br>
        <input type="text" name="event_title" required>
    </p>

    <p>
        <label>Event Type *</label><br>
        <select name="event_type" id="event-type" required>
            <option value="">-- Select Type --</option>
            <option value="conference">Conference</option>
            <option value="workshop">Workshop</option>
            <option value="webinar">Webinar</option>
        </select>
    </p>
    
    <div id="conditional-fields" style="display:none;">
        <p>
            <label>Zoom Link (for webinars only)</label><br>
            <input type="url" name="zoom_link">
        </p>
    </div>
    
    <p>
        <label>Start Date *</label><br>
        <input type="date" name="start_date" required>
    </p>

    <p>
        <label>End Date *</label><br>
        <input type="date" name="end_date" required>
    </p>

    <p>
        <label>Organizer Name</label><br>
        <input type="text" name="organizer_name">
    </p>

        <p>
        <label>City *</label><br>
        <input type="text" name="event_city" required>
    </p>

    <p>
        <label>Organizer Email</label><br>
        <input type="email" name="organizer_email">
    </p>

    <p>
        <label>Event Location</label><br>
        <textarea name="event_location"></textarea>
    </p>

    <p>
        <label>Event Photo (JPEG/PNG, max 2MB)</label><br>
        <input type="file" name="event_photo" accept=".jpg,.jpeg,.png">
    </p>

    <p>
        <button type="submit">Submit Event</button>
    </p>
</form>

<div id="emp-form-response"></div>
