<?php
session_start();
include 'db.php';

$order = $_SESSION['order'] ?? null;
$username = $_SESSION['username'] ?? null;

if (!$order || !$username) {
  echo "<div class='container mt-5 text-center'>
    <div class='alert alert-warning p-4 shadow-sm'>
      ⚠️ لا يوجد طلب مؤكد أو لم يتم تسجيل الدخول.<br><br>
      <a href='index.php' class='btn btn-primary'>العودة للتسوق</a>
    </div>
  </div>";
  exit;
}

// جلب المنتجات
$order_id = $order['order_id'];
$stmt = $conn->prepare("SELECT name, price, price_before, quantity, image1 FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// حساب عدد المنتجات والمجموع قبل الخصم
$totalQty = 0;
$subtotalBefore = 0;
foreach ($items as $item) {
  $totalQty += $item['quantity'];
  $subtotalBefore += $item['price_before'] * $item['quantity'];
}

// جلب بيانات الطلب والتحقق من ملكيته
$infoStmt = $conn->prepare("SELECT created_at, username, fullname, phone, email, city, ZIPcode, address, payment_method, subtotal_before, discount, tax, grand_total FROM orders WHERE id = ? AND username = ?");
$infoStmt->bind_param("is", $order_id, $username);
$infoStmt->execute();
$orderData = $infoStmt->get_result()->fetch_assoc();
$infoStmt->close();

if (!$orderData) {
  echo "<div class='container mt-5 text-center'>
    <div class='alert alert-danger p-4 shadow-sm'>
      ⚠️ لا يمكنك عرض هذا الطلب لأنه لا يخص حسابك.<br><br>
      <a href='index.php' class='btn btn-primary'>العودة للتسوق</a>
    </div>
  </div>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>إيصال الطلب</title>
  <link rel="icon" href="images/logoo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 30px 200px 50px;
      direction: rtl;
      background-color: whitesmoke;
      font-family: "Gill Sans", "Gill Sans MT", Calibri, "Trebuchet MS", sans-serif;
    }
    @media print {
      .no-print { display: none !important; }
      body { margin: 0; background-color: white; }
      /*.card, .container { box-shadow: none !important; border: none !important; } */
    }
  </style>
</head>
<body>

<div class="container mt-5 text-center">
  <div class="p-4 shadow-sm border rounded" style="background-color:rgba(255, 127, 80, 0.48);">
    <h2 class="text-success"><svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="currentColor" class="bi bi-emoji-sunglasses" viewBox="0 0 16 16">
  <path d="M4.968 9.75a.5.5 0 1 0-.866.5A4.5 4.5 0 0 0 8 12.5a4.5 4.5 0 0 0 3.898-2.25.5.5 0 1 0-.866-.5A3.5 3.5 0 0 1 8 11.5a3.5 3.5 0 0 1-3.032-1.75M7 5.116V5a1 1 0 0 0-1-1H3.28a1 1 0 0 0-.97 1.243l.311 1.242A2 2 0 0 0 4.561 8H5a2 2 0 0 0 1.994-1.839A3 3 0 0 1 8 6c.393 0 .74.064 1.006.161A2 2 0 0 0 11 8h.438a2 2 0 0 0 1.94-1.515l.311-1.242A1 1 0 0 0 12.72 4H10a1 1 0 0 0-1 1v.116A4.2 4.2 0 0 0 8 5c-.35 0-.69.04-1 .116"/>
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-1 0A7 7 0 1 0 1 8a7 7 0 0 0 14 0"/>
</svg></h2>
    <!-- <h2 class="text-success">✅</h2> -->
    <h2 class="text-success fw-bold">تم الدفع بنجاح!</h2>
    <p>شكرا لثقتك بنا. سيتم توصيل طلبك في أقرب وقت ممكن.</p>
  </div>

  <div class="card p-4 shadow-sm">
    <h4 class="border-bottom pb-3 mb-3 text-end fw-bold"> إيصال الطلب <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-receipt-cutoff" viewBox="0 0 16 16"style="margin-right: 5px;">
  <path d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5M11.5 4a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"/>
  <path d="M2.354.646a.5.5 0 0 0-.801.13l-.5 1A.5.5 0 0 0 1 2v13H.5a.5.5 0 0 0 0 1h15a.5.5 0 0 0 0-1H15V2a.5.5 0 0 0-.053-.224l-.5-1a.5.5 0 0 0-.8-.13L13 1.293l-.646-.647a.5.5 0 0 0-.708 0L11 1.293l-.646-.647a.5.5 0 0 0-.708 0L9 1.293 8.354.646a.5.5 0 0 0-.708 0L7 1.293 6.354.646a.5.5 0 0 0-.708 0L5 1.293 4.354.646a.5.5 0 0 0-.708 0L3 1.293zm-.217 1.198.51.51a.5.5 0 0 0 .707 0L4 1.707l.646.647a.5.5 0 0 0 .708 0L6 1.707l.646.647a.5.5 0 0 0 .708 0L8 1.707l.646.647a.5.5 0 0 0 .708 0L10 1.707l.646.647a.5.5 0 0 0 .708 0L12 1.707l.646.647a.5.5 0 0 0 .708 0l.509-.51.137.274V15H2V2.118z"/>
</svg></h4>
    <div class="p-3 mb-3 text-end border rounded">
      <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><strong>رمز الطلب:</strong> <?= htmlspecialchars($order['tracking_code']) ?></div>
      <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><strong>تاريخ الطلب:</strong> <?= date("Y-m-d H:i", strtotime($orderData['created_at'])) ?></div>
      <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><strong>اسم المستخدم:</strong> <?= htmlspecialchars($orderData['username']) ?></div>
      <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><strong>الاسم الكامل:</strong> <?= htmlspecialchars($orderData['fullname']) ?></div>
      <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><strong>رقم الجوال:</strong> <?= htmlspecialchars($orderData['phone']) ?></div>
      <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><strong>البريد الإلكتروني:</strong> <?= htmlspecialchars($orderData['email']) ?></div>
      <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><strong>عملية الدفع:</strong> <?= htmlspecialchars($orderData['payment_method']) ?></div>
      <div class="d-flex justify-content-between border-bottom pb-2 mb-2"><strong>المدينة:</strong> <?= htmlspecialchars($orderData['city']) ?></div>
    </div>

    <h5 class="mb-3 text-end fw-bold"> تفاصيل المنتجات<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-box-seam-fill" viewBox="0 0 16 16" style="margin-right: 10px;">
  <path fill-rule="evenodd" d="M15.528 2.973a.75.75 0 0 1 .472.696v8.662a.75.75 0 0 1-.472.696l-7.25 2.9a.75.75 0 0 1-.557 0l-7.25-2.9A.75.75 0 0 1 0 12.331V3.669a.75.75 0 0 1 .471-.696L7.443.184l.01-.003.268-.108a.75.75 0 0 1 .558 0l.269.108.01.003zM10.404 2 4.25 4.461 1.846 3.5 1 3.839v.4l6.5 2.6v7.922l.5.2.5-.2V6.84l6.5-2.6v-.4l-.846-.339L8 5.961 5.596 5l6.154-2.461z"/>
</svg></h5>
    <ul class="list-group mb-3 p-3 text-end">
      <?php foreach ($items as $item): ?>
        <li class="list-group-item border text-end">
          <div class="d-flex align-items-center">
            <img src="<?= $item['image1'] ?>" width="60" class="rounded ms-3">
            <div class="text-end">
              <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
              <small>الكمية: <?= $item['quantity'] ?></small><br>
              <?php if ($item['price_before'] > $item['price']): 
                $discountPercent = ceil((($item['price_before'] - $item['price']) / $item['price_before']) * 100);
              ?>
                <small class="text-muted"><del><?= number_format($item['price_before'], 2) ?> ريال</del></small>
                <span class="badge bg-danger"><?= $discountPercent ?>%-</span>
              <?php endif; ?>
              <strong class="me-2" style="color: coral;"><?= number_format($item['price'], 2) ?> ريال</strong>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="border-top pt-3 text-end">
      <div class="d-flex justify-content-between mb-2"><span>المجموع الفرعي (<?= $totalQty ?> منتجات):</span><strong><?= number_format($subtotalBefore, 2) ?> ريال</strong></div>
      <div class="d-flex justify-content-between text-success mb-2"><span>الخصم:</span><strong>-<?= number_format($orderData['discount'], 2) ?> ريال</strong></div>
      <div class="d-flex justify-content-between text-muted mb-2"><span>رسوم التوصيل:</span><strong><del>20.00 ريال</del> <span class="text-success">مجانا</span></strong></div>
      <div class="d-flex justify-content-between fs-5 mb-2"><strong>المجموع الكلي (شامل الضريبة القيمة المضافة):</strong><strong><?= number_format($orderData['grand_total'], 2) ?> ريال</strong></div>
      <div class="d-flex justify-content-between text-danger small"><span>ضريبة القيمة المضافة المقدّرة:</span><span><?= number_format($orderData['tax'], 2) ?> ريال</span></div>
    </div>
  </div>

  <div class="text-center mt-4 no-print">
    <button onclick="window.print()" class="btn btn-dark fs-5"> طباعة الإيصال<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16" style="margin-right: 10px;">
  <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
  <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
</svg></button>
    <a href="index.php" class="btn btn-outline-dark me-2 fs-5">العودة للصفحة الرئيسية</a>
  </div>
</div>
</body>
</html>