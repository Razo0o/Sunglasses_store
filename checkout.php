<?php
session_start();

$cart = $_SESSION['cart'] ?? null;

if (!$cart || empty($cart['items'])) {
  echo "<div class='alert alert-warning text-center'>السلة فارغة</div>";
  exit;
}

$items = $cart['items'];
$subtotal = $cart['subtotal'];
$subtotalBefore = $cart['subtotalBefore'];
$discount = $cart['discount'];
$tax = $cart['tax'];
$net = $cart['net'];
$grandTotal = $cart['grandTotal'];
$totalQty = $cart['totalQty'] ?? count($items);
?>

<!DOCTYPE html>
<html lang="ar">

<head>
  <meta charset="UTF-8">
  <title>إتمام الطلب</title>
  <link rel="icon" href="images/logoo.png">
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body dir="rtl">

  <div class="container mt-5">
    <h1 class="text-center mb-4" style="color: coral; font-weight:bold;">إتمام الطلب</h1>

    <div class="row" style="margin-bottom: 80px;">
      <!-- يسار: نموذج العميل والدفع -->
      <div class="col-md-7">
        <form method="POST" action="place_order.php" onsubmit="return validateCheckout();">
          <!-- 🏠 معلومات العنوان -->
          <div class="p-4 border rounded bg-light shadow-sm mb-4" dir="rtl">
            <h4 class="mb-3">
              معلومات العنوان
              <i class="fa-solid fa-location-dot me-2"></i>
            </h4>

            <!-- الاسم الكامل + رقم الجوال -->
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">الاسم الكامل <span style="color:crimson">*</span></label>
                <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">رقم الجوال <span style="color:crimson">*</span></label>
                <input type="tel" name="phone" id="phoneInput" class="form-control" required
                  placeholder="05xxxxxxxx" style="color: gray;"
                  pattern="^05\d{8}$"
                  maxlength="10" inputmode="numeric">
                <div id="phoneError" class="text-danger mt-1" style="display: none;">رقم الجوال غير صالح، يجب أن يبدأ بـ 05 ويتكون من 10 أرقام.</div>
                <div id="notNumberError" class="text-danger mt-1" style="display: none;"> يجب إدخال أرقام فقط بدون حروف أو رموز.</div>
              </div>
            </div>

            <!-- البريد الإلكتروني -->
            <div class="mb-3">
              <label class="form-label">البريد الإلكتروني <span style="color:crimson">*</span></label>
              <input type="email" name="email" class="form-control"
                value="<?= isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '' ?>" required>
            </div>

            <!-- المدينة + الرمز البريدي -->
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">المدينة <span style="color:crimson">*</span></label>
                <input type="text" name="city" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">الرمز البريدي <span style="color:crimson">*</span></label>
                <input type="text" name="ZIPcode" class="form-control" required>
              </div>
            </div>

            <!-- عنوان التوصيل -->
            <div class="mb-3">
              <label class="form-label">عنوان التوصيل <span style="color:crimson">*</span></label>
              <textarea name="address" class="form-control" rows="2" required></textarea>
            </div>
          </div>

          <!--  معلومات الدفع -->
          <div class="p-4 border rounded bg-light shadow-sm">
            <h4 class="mb-3 fw-bold">
              عملية الدفع <i class="fa-solid fa-money-check-alt me-2"></i>
            </h4>

            <div class="mb-3">
              <!-- خيار: البطاقات الإئتمانية -->
              <div class="form-check m-4 d-flex align-items-center justify-content-between ">
                <input class="form-check-input" type="radio" name="payment_method" id="mada" value="البطاقة الإئتمانية" required>
                <label class="form-check-label d-flex justify-content-between" for="mada" style="gap: 335px;">
                  <img src="images/pays/mada.png" alt="mada" style="height: 35px; width: 120px;">
                  <h5>البطاقات الإئتمانية</h5>
                </label>
              </div>

              <!-- خيار: أبل باي -->
              <div class="form-check m-4 d-flex align-items-center justify-content-between ">
                <input class="form-check-input" type="radio" name="payment_method" id="apple" value="أبل باي">
                <label class="form-check-label d-flex justify-content-between" for="apple" style="gap: 440px;">
                  <img src="images/pays/Apple-Pay.png" alt="Apple Pay" style="height: 35px; width: 90px; border-radius: 10px;">
                  <h5>أبل باي</h5>
                </label>
              </div>

              <!-- خيار: تمارا -->
              <div class="form-check m-4 d-flex align-items-center justify-content-between ">
                <input class="form-check-input" type="radio" name="payment_method" id="tamara" value="تمارا">
                <label class="form-check-label d-flex justify-content-between" for="tamara" style="gap: 460px;">
                  <img src="images/pays/tamara.png" alt="tamara" style="height: 30px; width: 90px;">
                  <h5>تمارا</h5>
                </label>
              </div>

              <!-- خيار: تابّي -->
              <div class="form-check m-4 d-flex align-items-center justify-content-between ">
                <input class="form-check-input" type="radio" name="payment_method" id="tabby" value="تابي">
                <label class="form-check-label d-flex justify-content-between" for="tabby" style="gap: 470px;">
                  <img src="images/pays/tabby.webp" alt="tabby" style="height: 30px; width: 90px; border-radius: 10px;">
                  <h5>تابّي</h5>
                </label>
              </div>

              <!-- خيار: الدفع عند الاستلام -->
              <div class="form-check m-4 d-flex align-items-center justify-content-between ">
                <input class="form-check-input" type="radio" name="payment_method" id="cash" value="الدفع عند الاستلام">

                <label class="form-check-label d-flex justify-content-between" for="cash" style="gap: 415px;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor"
                    class="bi bi-cash-stack" viewBox="0 0 16 16">
                    <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4" />
                    <path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2z" />
                  </svg>
                  <h5>الدفع عند الاستلام</h5>

                </label>
              </div>

            </div>

            <div id="paymentInfo" class="text-info mb-3"></div>

            <button type="submit" id="payButton" class="btn w-100 mt-3 fs-5 text-light" style="background-color: coral;">تأكيد الطلب</button>
            <a href="index.php" class="btn btn-outline-dark w-100 mt-3 fs-5">العودة للصفحة الرئيسية</a>
          </div>
      </div>


      <!-- ملخص الطلب -->
      <div class="col-md-5">
        <div class="p-4 border rounded bg-white shadow-sm">
          <h4 class="mb-3 bold"> ملخص الطلب<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-cart4 " style="margin-right: 10px; margin-top:0;" viewBox="0 0 16 16">
              <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5M3.14 5l.5 2H5V5zM6 5v2h2V5zm3 0v2h2V5zm3 0v2h1.36l.5-2zm1.11 3H12v2h.61zM11 8H9v2h2zM8 8H6v2h2zM5 8H3.89l.5 2H5zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0" />
            </svg></h4>

          <ul class="list-group mb-3">
            <?php foreach ($items as $item):
              $name = htmlspecialchars($item['name'] ?? 'منتج غير معروف');
              $price = (float)($item['price'] ?? 0);
              $priceBefore = (float)($item['priceBefore'] ?? $price);
              $qty = (int)($item['quantity'] ?? 0);
            ?>
              <li class="list-group-item">
                <div class="d-flex align-items-center">
                  <div class="">
                    <img src="<?= $item['image1'] ?? 'images/default.png' ?>" width="70" class="rounded">
                  </div>
                  <div class="flex-grow-1">
                    <h5 class="mb-1 me-2"><?= $name ?></h5>
                    <div>
                      <?php if ($priceBefore > $price):
                        $discountPercent = ceil((($priceBefore - $price) / $priceBefore) * 100);
                      ?>
                        <small class="text-muted me-2"><del><?= $priceBefore ?> ريال</del></small>
                        <span class="badge bg-danger ms-2 "> <?= $discountPercent ?>%-</span>
                        <div><strong class="me-2" style="color: coral;"><?= $price ?> ريال</strong></div>
                      <?php else: ?>
                        <div><strong class="me-2" style="color: coral;"><?= $price ?> ريال</strong></div>
                      <?php endif; ?>
                    </div>
                    <small class="text-muted me-2">الكمية: <?= $qty ?></small>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>

          <div class="d-flex justify-content-between mb-2">
            <span>المجموع الفرعي (<?= $totalQty ?> منتجات):</span>
            <strong><?= $subtotalBefore ?> ريال</strong>
          </div>


          <div class="d-flex justify-content-between text-success mb-2">
            <span>الخصم:</span>
            <strong>-<?= $discount ?> ريال</strong>
          </div>

          <div class="d-flex justify-content-between text-muted mb-2">
            <span>رسوم التوصيل:</span>
            <strong><del>20.00 ريال</del> <span class="text-success">مجانا</span></strong>
          </div>

          <hr>

          <div class="d-flex justify-content-between fs-5 mb-2">
            <strong>المجموع الكلي (شامل الضريبة القيمة المضافة):</strong>
            <strong class=""><?= $grandTotal ?> ريال</strong>
          </div>

          <div class="d-flex justify-content-between text-danger small">
            <span>ضريبة القيمة المضافةالمقّدرة:</span>
            <span><?= $tax ?> ريال</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const radios = document.querySelectorAll('input[name="payment_method"]');
      const info = document.getElementById('paymentInfo');
      const payButton = document.getElementById("payButton");

      radios.forEach(radio => {
        radio.addEventListener("change", () => {
          let buttonHTML = "تأكيد الطلب";
          let message = "";

          switch (radio.id) {
            case "mada":
              buttonHTML = 'الدفع بواسطة البطاقة الإئتمانية';
              message = "سيتم تحويلك لإدخال بيانات البطاقة البنكية.";
              break;
            case "apple":
              buttonHTML = `
    <div style="
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: center;
      padding: 5px;
      text-align: center;
    ">      
    <p style="margin: 0;"> الدفع بواسطة</p>
      <img src="images/pays/Apple-Pay.png" alt="Apple Pay" style="height: 40px; border-radius: 10px;margin-right:10px;">
    </div>
  `;
              message = "تأكد من تفعيل Apple Pay على جهازك.";
              break;
            case "cash":
              buttonHTML = "الدفع عند الاستلام";
              message = "سيتم الدفع نقدًا عند استلام الطلب.";
              break;
            case "tamara":
              buttonHTML = `
    <div style="
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: center;
      padding: 5px;
      text-align: center;
    ">      
    <p style="margin: 0;"> الدفع بواسطة</p>
      <img src="images/pays/tamara.png" alt="tamara" style="height: 40px; border-radius: 10px;margin-right:10px;">
    </div>
  `;
              message = "ادفع على 4 دفعات بدون فوائد عبر تمارا.";
              break;
            case "tabby":
              buttonHTML = `
    <div style="
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: center;
      padding: 5px;
      text-align: center;
    ">     
     <p style="margin: 0;"> الدفع بواسطة</p>
      <img src="images/pays/tabby.webp" alt="tabby" style="height: 40px; border-radius: 10px;margin-right:10px;">
    </div>
  `;
              message = "ادفع على 3 دفعات بدون فوائد عبر تابّي.";
              break;
          }

          if (payButton) payButton.innerHTML = buttonHTML;
          if (info) info.textContent = message;
        });
      });
    });

    const phoneInput = document.getElementById("phoneInput");
    const phoneError = document.getElementById("phoneError");
const notNumberError = document.getElementById("notNumberError");

    phoneInput.onkeyup = ("input", () => {
const value = phoneInput.value;

  const isValidFormat = /^05\d{8}$/.test(value);      // يبدأ بـ 05 ويتكون من 10 أرقام
  const isNumericOnly = /^\d*$/.test(value);          // يحتوي على أرقام فقط

  // تحقق من أن المدخل يحتوي على حروف أو رموز
  if (!isNumericOnly) {
    notNumberError.style.display = "block";
    phoneInput.classList.add("is-invalid");
  } else {
    notNumberError.style.display = "none";
  }

  // تحقق من الصيغة العامة
  if (value.length === 10 && !isValidFormat) {
    phoneError.style.display = "block";
    phoneInput.classList.add("is-invalid");
  } else {
    phoneError.style.display = "none";
    if (isNumericOnly) {
      phoneInput.classList.remove("is-invalid");
    }
  }
});

  </script>
</body>

</html>