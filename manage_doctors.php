<?php
require_once "database2.php";

function getAllDoctors($conn) {
    $sql = "SELECT * FROM doctors";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function addDoctor($conn, $doctors_id, $password) {
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $sql = "INSERT INTO doctors (doctors_id, password) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $doctors_id, $passwordHash);
    return mysqli_stmt_execute($stmt);
}

function updateDoctorPassword($conn, $doctors_id, $new_password) {
    $passwordHash = password_hash($new_password, PASSWORD_BCRYPT);
    $sql = "UPDATE doctors SET password = ? WHERE doctors_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $passwordHash, $doctors_id);
    return mysqli_stmt_execute($stmt);
}

function deleteDoctor($conn, $doctors_id) {
    $sql = "DELETE FROM doctors WHERE doctors_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $doctors_id);
    return mysqli_stmt_execute($stmt);
}

function getUniqueDoctors($doctors) {
    $uniqueDoctors = [];
    $seenDoctorIds = [];
    
    foreach ($doctors as $doctor) {
        if (!in_array($doctor['doctors_id'], $seenDoctorIds)) {
            $uniqueDoctors[] = $doctor;
            $seenDoctorIds[] = $doctor['doctors_id'];
        }
    }
    
    return $uniqueDoctors;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_doctor'])) {
        $doctors_id = $_POST['doctors_id'];
        $password = $_POST['password'];
        if (addDoctor($conn, $doctors_id, $password)) {
            $success_message = "Doctor added successfully.";
        } else {
            $error_message = "Error adding doctor.";
        }
    } elseif (isset($_POST['update_password'])) {
        $doctors_id = $_POST['doctors_id'];
        $new_password = $_POST['new_password'];
        if (updateDoctorPassword($conn, $doctors_id, $new_password)) {
            $success_message = "Password updated successfully.";
        } else {
            $error_message = "Error updating password.";
        }
    } elseif (isset($_POST['delete_doctor'])) {
        $doctors_id = $_POST['doctors_id'];
        if (deleteDoctor($conn, $doctors_id)) {
            $success_message = "Doctor deleted successfully.";
        } else {
            $error_message = "Error deleting doctor.";
        }
    }
}

$doctors = getAllDoctors($conn);
$uniqueDoctors = getUniqueDoctors($doctors);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Manage Doctors</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <h2>Add New Doctor</h2>
        <form method="POST">
            <div class="form-group">
                <label for="doctors_id">Doctor ID:</label>
                <input type="text" class="form-control" id="doctors_id" name="doctors_id" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" name="add_doctor" class="btn btn-primary">Add Doctor</button>
        </form>

        <h2 class="mt-5">Existing Doctors</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Doctor ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($uniqueDoctors as $doctor): ?>
                <tr>
                    <td><?php echo htmlspecialchars($doctor['doctors_id']); ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="doctors_id" value="<?php echo $doctor['doctors_id']; ?>">
                            <input type="password" name="new_password" placeholder="New Password" required>
                            <button type="submit" name="update_password" class="btn btn-warning btn-sm">Update Password</button>
                        </form>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this doctor?');">
                            <input type="hidden" name="doctors_id" value="<?php echo $doctor['doctors_id']; ?>">
                            <button type="submit" name="delete_doctor" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
