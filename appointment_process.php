<?php
// appointment_process.php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_name = trim($_POST['patient_name']);
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $department = $_POST['department'];
    $symptoms = trim($_POST['symptoms']);
    
    $errors = [];
    if (empty($patient_name)) $errors[] = "Name required";
    if ($age < 0 || $age > 120) $errors[] = "Valid age required";
    if (!preg_match('/^[0-9]{10}$/', $phone)) $errors[] = "10-digit phone required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO appointments (patient_name, age, gender, phone, email, appointment_date, appointment_time, department, symptoms) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$patient_name, $age, $gender, $phone, $email, $appointment_date, $appointment_time, $department, $symptoms]);
            $_SESSION['message'] = "Appointment booked successfully!";
            $_SESSION['msg_type'] = "success";
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $_SESSION['message'] = "Slot already taken. Choose different date/time.";
                $_SESSION['msg_type'] = "error";
            } else {
                $_SESSION['message'] = "Booking failed: " . $e->getMessage();
                $_SESSION['msg_type'] = "error";
            }
        }
    } else {
        $_SESSION['message'] = implode("<br>", $errors);
        $_SESSION['msg_type'] = "error";
    }
    header("Location: index.php#appointment");
    exit();
}
?>