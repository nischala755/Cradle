$(document).ready(function() {
    // Signup Form Submission
    $('#signupForm').submit(function(e) {
        e.preventDefault();
        const username = $('#username').val();
        const password = $('#password').val();

        // Perform signup action
        $.post('php/auth.php', { action: 'signup', username: username, password: password }, function(response) {
            alert(response.message); // Display message

            if (response.message === 'Signup successful') {
                // Redirect to login page upon successful signup
                window.location.href = 'login.html';
            }
        }, 'json');
    });

    // Login Form Submission
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        const username = $('#username').val();
        const password = $('#password').val();

        // Perform login action
        $.post('php/auth.php', { action: 'login', username: username, password: password }, function(response) {
            if (response.message === 'Login successful') {
                // Redirect to dashboard upon successful login
                window.location.href = 'dashboard.html';
            } else {
                alert(response.message); // Display error message
            }
        }, 'json');
    });
});
