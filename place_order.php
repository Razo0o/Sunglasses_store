<?php
session_start();
include 'db.php'; // الاتصال بقاعدة البيانات

$username = $_SESSION['username'] ?? null;
$cart = $_SESSION['cart'] ?? null;

if (!$username || !$cart || empty($cart['items'])) {
  echo "<div class='container mt-5 text-center'>
    <div class='alert alert-warning p-4 shadow-sm'>
      ⚠️ يجب تسجيل الدخول وإضافة منتجات للسلة قبل إتمام الطلب.<br><br>
      <a href='index.php' class='btn btn-primary'>العودة للتسوق</a>
    </div>
  </div>";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // استقبال بيانات النموذج
  $fullname = $_POST['fullname'] ?? '';
  $phone = $_POST['phone'] ?? '';
  $email = $_POST['email'] ?? '';
  $city = $_POST['city'] ?? '';
  $ZIPcode = $_POST['ZIPcode'] ?? '';
  $address = $_POST['address'] ?? '';
  $payment_method = $_POST['payment_method'] ?? '';

  // التحقق من الحقول المطلوبة
  if (!$fullname || !$phone || !$email || !$city || !$ZIPcode || !$address || !$payment_method) {
    echo "<div class='container mt-5 text-center'>
      <div class='alert alert-danger p-4 shadow-sm'>
        ⚠️ يرجى تعبئة جميع الحقول المطلوبة.<br><br>
        <a href='checkout.php' class='btn btn-warning'>العودة لصفحة الدفع</a>
      </div>
    </div>";
    exit;
  }

  // حساب القيم المالية
  $subtotalBefore = 0;
  foreach ($cart['items'] as $item) {
    $subtotalBefore += floatval($item['priceBefore']) * intval($item['quantity']);
  }

  $discount = floatval($cart['discount']);
  $tax = floatval(str_replace(',', '', $cart['tax']));
  $grandTotal = floatval(str_replace(',', '', $cart['grandTotal']));

  // حفظ الطلب في جدول orders وربطه بـ username
  $stmt = $conn->prepare("INSERT INTO orders (username, fullname, phone, email, city, ZIPcode, address, payment_method, subtotal_before, discount, tax, grand_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param(
    "ssssssssdddd",
    $username,
    $fullname,
    $phone,
    $email,
    $city,
    $ZIPcode,
    $address,
    $payment_method,
    $subtotalBefore,
    $discount,
    $tax,
    $grandTotal
  );
  $stmt->execute();
  $order_id = $stmt->insert_id;
  $stmt->close();

  // حفظ المنتجات في جدول order_items
  $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, name, price, price_before, quantity, image1) VALUES (?, ?, ?, ?, ?, ?, ?)");
  foreach ($cart['items'] as $item) {
    $product_id = intval($item['id']);
    $name = $item['name'];
    $price = floatval($item['price']);
    $price_before = floatval($item['priceBefore']);
    $quantity = intval($item['quantity']);
    $image1 = $item['image1'];

    $itemStmt->bind_param("iissdis", $order_id, $product_id, $name, $price, $price_before, $quantity, $image1);
    $itemStmt->execute();
  }
  $itemStmt->close();

  // توليد رمز تتبع فني
  $trackingCode = "RSG" . date("Ymd") . rand(1000, 9999);

  // حفظ رمز التتبع في الطلب
  $trackStmt = $conn->prepare("UPDATE orders SET tracking_code = ? WHERE id = ?");
  $trackStmt->bind_param("si", $trackingCode, $order_id);
  $trackStmt->execute();
  $trackStmt->close();

  // حفظ بيانات الطلب في الجلسة لعرضها في صفحة الإيصال
  $_SESSION['order'] = [
    'order_id' => $order_id,
    'tracking_code' => $trackingCode,
    'fullname' => $fullname,
    'phone' => $phone,
    'email' => $email,
    'payment_method' => $payment_method,
    'grandTotal' => number_format($grandTotal, 2)
  ];

  // حذف السلة من الجلسة
  unset($_SESSION['cart']);

  // التوجيه إلى صفحة الإيصال
  header("Location: order_success.php");
  exit;
} else {
  header("Location: checkout.php");
  exit;
}