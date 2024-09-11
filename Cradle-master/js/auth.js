$(document).ready(function() {
    // Signup Form Submission
    $('#signupForm').submit(function(e) {
        e.preventDefault();
        const username = $('#username').val();
        const password = $('#password').val();

        // Perform signup action
        $.post('php/auth.php', { action: 'signup', username: username, password: password }, function(response) {
            if (response.success) {
                alert('Signup successful');
                window.location.href = 'login.html'; // Redirect to login page
            } else {
                alert(response.message); // Show error message
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            alert('Signup request failed: ' + textStatus);
        });
    });

    // Login Form Submission
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        const username = $('#username').val();
        const password = $('#password').val();

        // Perform login action
        $.post('php/auth.php', { action: 'login', username: username, password: password }, function(response) {
            if (response.success) {
                alert('Login successful');
                window.location.href = 'dashboard.html'; // Redirect to dashboard
            } else {
                alert(response.message); // Show error message
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            alert('Login request failed: ' + textStatus);
        });
    });
});
