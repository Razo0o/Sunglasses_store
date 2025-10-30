<?php
include "db.php";
session_start();

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->num_rows > 0 ? $result->fetch_assoc() : null;

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['preserved_email'] = $user['email'];
            $_SESSION['alerts'][] = [
                'type' => 'success',
                'message' => 'تم تسجيل الدخول بنجاح'
            ];
        } else {
            $_SESSION['alerts'][] = [
                'type' => 'error',
                'message' => 'كلمة المرور غير صحيحة'
            ];
         $_SESSION['active_form'] = 'login';
        }
    } else {
        $_SESSION['alerts'][] = [
            'type' => 'error',
            'message' => 'البريد الإلكتروني غير مسجل لدينا'
        ];
        $_SESSION['active_form'] = 'login';
    }

    header('Location: index.php');
    exit();
}