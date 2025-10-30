<?php
include "db.php";
session_start();

if (isset($_POST['register_btn'])) {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // التحقق من تطابق كلمات المرور
    if ($password !== $cpassword) {
        $_SESSION['alerts'][] = [
            'type' => 'error',
            'message' => 'كلمات المرور غير متطابقة'
        ];
        $_SESSION['active_form'] = 'register';
        header('Location: index.php');
        exit();
    }

    // التحقق من وجود البريد
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['alerts'][] = [
            'type' => 'error',
            'message' => 'البريد الإلكتروني مسجل بالفعل'
        ];
        $_SESSION['active_form'] = 'register';
        header('Location: index.php');
        exit();
    }
    $stmt->close();

    // تشفير كلمة المرور وإدخال المستخدم
    $hashPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashPassword);

    if ($stmt->execute()) {
        $_SESSION['alerts'][] = [
            'type' => 'success',
            'message' => 'تم التسجيل بنجاح'
        ];
        $_SESSION['active_form'] = 'login';
    } else {
        $_SESSION['alerts'][] = [
            'type' => 'error',
            'message' => 'حدث خطأ أثناء التسجيل'
        ];
        $_SESSION['active_form'] = 'register';
    }

    $stmt->close();
    header('Location: index.php');
    exit();
}
?>
