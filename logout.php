<?php
session_start();

// حفظ رسالة تسجيل الخروج قبل مسح بيانات المستخدم
$_SESSION['alerts'][] = [
    'type' => 'success',
    'message' => 'تم تسجيل الخروج'
];

// حذف بيانات المستخدم فقط، دون مسح الرسائل
unset($_SESSION['username']); 
unset($_SESSION['email']); 

header('Location: index.php');
exit();
?>
