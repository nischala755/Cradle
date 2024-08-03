$(document).ready(function() {
    $('#signupForm').submit(function(e) {
        e.preventDefault();
        const username = $('#username').val();
        const password = $('#password').val();
        $.post('php/auth.php', { action: 'signup', username: username, password: password }, function(response) {
            alert(response.message);
            if (response.message === 'Signup successful') {
                window.location.href = 'login.html';
            }
        }, 'json');
    });

    $('#loginForm').submit(function(e) {
        e.preventDefault();
        const username = $('#username').val();
        const password = $('#password').val();
        $.post('php/auth.php', { action: 'login', username: username, password: password }, function(response) {
            if (response.message === 'Login successful') {
                window.location.href = 'dashboard.html';
            } else {
                alert(response.message);
            }
        }, 'json');
    });
});
