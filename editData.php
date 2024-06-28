<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: loginPage.php");
    exit();
}

require_once "database.php";
$user_id = $_SESSION['user'];
echo "User ID: " . $user_id;
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $sql);
$userData = mysqli_fetch_assoc($result);

if (!$userData) {
    echo "<div class='alert alert-danger'>User data not found.</div>";
    exit();
}

if (isset($_POST['update_data'])) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $errors = array();

    if (!password_verify($current_password, $userData['password'])) {
        array_push($errors, "Current password is incorrect.");
    }

    if (!empty($new_password)) {
        if (strlen($new_password) < 8) {
            array_push($errors, "New password must be at least 8 characters long.");
        }

        if ($new_password !== $confirm_password) {
            array_push($errors, "New passwords do not match.");
        }
    }

    if (count($errors) == 0) {
        $update_data = array(
            'name' => $name,
            'last_name' => $surname,
            'email' => $email
        );

        if (!empty($new_password)) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_data['password'] = $new_password_hash;
        }

        $update_fields = array();
        foreach ($update_data as $field => $value) {
            $update_fields[] = "`$field` = '$value'";
        }

        $update_sql = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id='$user_id'";

        if (mysqli_query($conn, $update_sql)) {
            echo "<div class='alert alert-success'>Data updated successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating data: " . mysqli_error($conn) . "</div>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Your Data</h1>
        <form action="editData.php" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name"  required>
            </div>
            <div class="form-group">
                <label for="surname">Surname:</label>
                <input type="text" id="surname" name="surname"  required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email"  required>
            </div>
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password (leave blank to keep current password):</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            <div class="form-group">
                <input type="submit" name="update_data" value="Update Data" class="btn btn-primary">
            </div>
        </form>
    </div>
</body>
</html>