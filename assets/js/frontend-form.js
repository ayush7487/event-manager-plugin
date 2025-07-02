document.getElementById("emp-event-form").addEventListener("submit", function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const photo = form.querySelector('input[name="event_photo"]').files[0];

    // Validation
    const today = new Date().toISOString().split("T")[0];
    const startDate = form.querySelector('input[name="start_date"]').value;
    const endDate = form.querySelector('input[name="end_date"]').value;

    if (startDate < today) {
        alert("Start date cannot be in the past.");
        return;
    }

    if (endDate < startDate) {
        alert("End date must be after start date.");
        return;
    }

    if (photo) {
        const allowedTypes = ['image/jpeg', 'image/png'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (!allowedTypes.includes(photo.type)) {
            alert("Only JPEG and PNG images are allowed.");
            return;
        }

        if (photo.size > maxSize) {
            alert("File size must be less than 2MB.");
            return;
        }
    }

    formData.append('action', 'submit_event_form');
    formData.append('nonce', emp_ajax_object.nonce);

    fetch(emp_ajax_object.ajax_url, {
        method: 'POST',
        body: formData,
    })
    .then(res => res.json())
    .then(data => {
        const result = document.getElementById("emp-form-response");
        result.innerHTML = data.success
            ? `<p style="color:green;">${data.data}</p>`
            : `<p style="color:red;">${data.data}</p>`;
        if (data.success) form.reset();
    })
    .catch(error => console.error('AJAX Error:', error));
});


document.addEventListener('DOMContentLoaded', function() {
    const eventType = document.getElementById('event-type');
    const conditionalFields = document.getElementById('conditional-fields');

    eventType.addEventListener('change', function() {
        if (eventType.value === 'webinar') {
            conditionalFields.style.display = 'block';
        } else {
            conditionalFields.style.display = 'none';
        }
    });
});