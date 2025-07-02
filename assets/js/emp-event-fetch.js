document.addEventListener("DOMContentLoaded", function () {
    const wrapper = document.getElementById('emp-event-list');
    if (!wrapper) return;

    const apiUrl = wrapper.getAttribute('data-api');

    fetch(apiUrl)
        .then(res => res.json())
        .then(events => {
            if (!Array.isArray(events) || events.length === 0) {
                wrapper.innerHTML = "<p>No upcoming events.</p>";
                return;
            }

            const list = document.createElement("ul");

            events.forEach(ev => {
                const li = document.createElement("li");
                li.innerHTML = `
                    <strong><a href="${ev.link}">${ev.title}</a></strong><br>
                    <small>
                        <strong>Date:</strong> ${ev.start_date} to ${ev.end_date}<br>
                        <strong>Type:</strong> ${ev.event_type}<br>
                        <strong>City:</strong> ${ev.city}<br>
                        <strong>Location:</strong> ${ev.location}<br>
                        <strong>Organizer:</strong> ${ev.organizer_name} (${ev.organizer_email})<br>
                        ${ev.zoom_link ? `<strong>Zoom:</strong> <a href="${ev.zoom_link}" target="_blank">${ev.zoom_link}</a><br>` : ''}
                    </small>
                `;
                list.appendChild(li);
            });

            wrapper.innerHTML = '';
            wrapper.appendChild(list);
        })
        .catch(err => {
            wrapper.innerHTML = "<p>Error loading events.</p>";
            console.error("REST fetch error:", err);
        });
});
