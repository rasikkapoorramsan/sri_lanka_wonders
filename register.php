<?php
session_start();
include 'config.php';

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle AJAX validation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'validate') {
    $response = ['success' => true, 'message' => ''];
    if (isset($_POST['username'])) {
        $username = mysqli_real_escape_string($conn, trim($_POST['username']));
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        if ($result === false) {
            $response['success'] = false;
            $response['message'] = "Database error: " . $conn->error;
        } else {
            $row = $result->fetch_assoc();
            if ($row['count'] > 0) {
                $response['success'] = false;
                $response['message'] = 'This username is already taken.';
            }
        }
    }

    if (isset($_POST['email'])) {
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = '$email'";
        $result = $conn->query($sql);
        if ($result === false) {
            $response['success'] = false;
            $response['message'] = "Database error: " . $conn->error;
        } else {
            $row = $result->fetch_assoc();
            if ($row['count'] > 0) {
                $response['success'] = false;
                $response['message'] = 'This email is already registered.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['success'] = false;
                $response['message'] = 'Please enter a valid email address.';
            }
        }
    }

    if (isset($_POST['password'])) {
        $password = trim($_POST['password']);
        if (strlen($password) < 8 || !preg_match("/[a-zA-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
            $response['success'] = false;
            $response['message'] = 'Password must be at least 8 characters long and include letters and numbers.';
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    // Double-check uniqueness (optional, as AJAX should handle this)
    $check_username_sql = "SELECT COUNT(*) as count FROM users WHERE username = '$username'";
    $check_username_result = $conn->query($check_username_sql);
    $check_email_sql = "SELECT COUNT(*) as count FROM users WHERE email = '$email'";
    $check_email_result = $conn->query($check_email_sql);

    if ($check_username_result->fetch_assoc()['count'] > 0) {
        $error = "Username '$username' is already taken. Please choose a different username.";
    } elseif ($check_email_result->fetch_assoc()['count'] > 0) {
        $error = "Email '$email' is already registered. Please use a different email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen(trim($_POST['password'])) < 8 || !preg_match("/[a-zA-Z]/", trim($_POST['password'])) || !preg_match("/[0-9]/", trim($_POST['password']))) {
        $error = "Password must be at least 8 characters long and include letters and numbers.";
    } else {
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "Registration successful! You can now log in.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Error registering user: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Discover the Wonders of Sri Lanka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .error { color: red; font-size: 0.9em; display: none; }
        .valid { color: green; font-size: 0.9em; display: none; }
        .validation-loading { display: none; color: gray; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Sign Up</h2>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <?php if (isset($_SESSION['success'])) { echo "<div class='alert alert-success'>{$_SESSION['success']}</div>"; unset($_SESSION['success']); } ?>
        <form method="POST" id="registerForm" class="mt-4">
            <div class="mb-3 position-relative">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <span id="usernameError" class="error"></span>
                <span id="usernameValid" class="valid">Username is available!</span>
                <span id="usernameLoading" class="validation-loading">Validating...</span>
            </div>
            <div class="mb-3 position-relative">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <span id="emailError" class="error"></span>
                <span id="emailValid" class="valid">Email is available!</span>
                <span id="emailLoading" class="validation-loading">Validating...</span>
            </div>
            <div class="mb-3 position-relative">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <span id="passwordError" class="error"></span>
                <span id="passwordValid" class="valid">Password is valid!</span>
                <span id="passwordLoading" class="validation-loading">Validating...</span>
            </div>
            <button type="submit" class="btn btn-primary">Sign Up</button>
        </form>
        <p class="mt-3">Already have an account? <a href="login.php">Log in</a></p>
    </div>
    <script>
        $(document).ready(function() {
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded');
                alert('Error: JavaScript library failed to load. Please check your internet connection.');
                return;
            }

            function validateField($field, errorId, validId, loadingId, type) {
                $field.on('input', function() {
                    var value = $(this).val().trim();
                    if (value.length > 0) {
                        $(loadingId).show();
                        $(errorId).hide();
                        $(validId).hide();
                        $.ajax({
                            url: 'register.php',
                            type: 'POST',
                            data: { action: 'validate', [type]: value },
                            dataType: 'json',
                            success: function(response) {
                                $(loadingId).hide();
                                if (response && !response.success) {
                                    $(errorId).text(response.message).show();
                                    $(validId).hide();
                                } else if (response && response.success) {
                                    $(errorId).hide();
                                    $(validId).show();
                                } else {
                                    $(errorId).text('Error validating ' + type + '.').show();
                                    $(validId).hide();
                                }
                            },
                            error: function(xhr, status, error) {
                                $(loadingId).hide();
                                $(errorId).text('AJAX error validating ' + type + ': ' + error).show();
                                $(validId).hide();
                                console.error('AJAX Error:', status, error);
                            }
                        });
                    } else {
                        $(errorId).hide();
                        $(validId).hide();
                        $(loadingId).hide();
                    }
                });
            }

            validateField($('#username'), '#usernameError', '#usernameValid', '#usernameLoading', 'username');
            validateField($('#email'), '#emailError', '#emailValid', '#emailLoading', 'email');
            validateField($('#password'), '#passwordError', '#passwordValid', '#passwordLoading', 'password');

            $('#registerForm').on('submit', function(e) {
                var usernameValid = $('#usernameValid').is(':visible');
                var emailValid = $('#emailValid').is(':visible');
                var passwordValid = $('#passwordValid').is(':visible');
                if (!usernameValid || !emailValid || !passwordValid) {
                    e.preventDefault();
                    alert('Please fix the validation errors before submitting.');
                } else {
                    var usernameValue = $('#username').val().trim();
                    var emailValue = $('#email').val().trim();
                    var passwordValue = $('#password').val().trim();
                    if (usernameValue.length === 0 || emailValue.length === 0 || passwordValue.length === 0) {
                        e.preventDefault();
                        alert('Please fill all required fields.');
                    }
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>