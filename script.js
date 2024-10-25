$(document).ready(function() {
    $('.status-button').on('click', function() {
        var taskId = $(this).data('task-id');
        var currentStatus = $(this).hasClass('btn-success') ? 'complete' : 'incomplete';
        var newStatus = currentStatus === 'complete' ? 'incomplete' : 'complete';
        var button = $(this);

        $.ajax({
            url: 'update_task_status.php',
            type: 'POST',
            data: {
                task_id: taskId,
                status: newStatus
            },
            success: function(response) {
                if (newStatus === 'complete') {
                    button.removeClass('btn-warning').addClass('btn-success').text('Completed');
                } else {
                    button.removeClass('btn-success').addClass('btn-warning').text('Incomplete');
                }
            },
            error: function() {
                alert('Error updating task status');
            }
        });
    });
});

document.getElementById('registerForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent form submission
    
    // Get form data
    const newUsername = document.getElementById('newUsername').value;
    const email = document.getElementById('email').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Validate that the passwords match
    if (newPassword !== confirmPassword) {
        alert("Passwords do not match!");
        return;
    }

    // Send data to PHP via AJAX
    fetch('register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            newUsername: newUsername,
            email: email,
            newPassword: newPassword,
            confirmPassword: confirmPassword
        })
    })
    .then(response => response.text())
    .then(data => alert(data))
    .catch(error => console.error('Error:', error));
});


let deleteUrl = '';

// Function to open the modal and set the delete URL
function confirmDelete(url) {
    deleteUrl = url;  // Store the URL for deletion
    var myModal = new bootstrap.Modal(document.getElementById('confirmModal'), {
        keyboard: false
    });
    myModal.show();
}

// Add event listener to the delete button in the modal
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    window.location.href = deleteUrl;  // Redirect to the delete URL when confirmed
});




