function updateStatus(bookingId, newStatus) {
    if (confirm(`Are you sure you want to ${newStatus} this booking?`)) {
        // Simple AJAX request
        fetch('/../app/model/update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `booking_id=${bookingId}&action=${newStatus}`
        })
        .then(response => response.text())
        .then(result => {
            if (result === 'success') {
                // Update the status display without page reload
                document.querySelector(`.status-${bookingId}`).textContent = newStatus;
                alert('Status updated successfully!');
            } else {
                alert('Error updating status');
            }
        });
    }
}