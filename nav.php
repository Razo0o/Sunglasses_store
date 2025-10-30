<?php
include 'db.php';
session_start();

// حفظ القيم المهمة قبل المسح
$username     = $_SESSION['username']     ?? null;
$email        = $_SESSION['email']        ?? null;
$alerts       = $_SESSION['alerts']       ?? [];
$active_form  = $_SESSION['active_form']  ?? '';

// مسح الجلسة
session_unset();

// إعادة تعيين القيم بعد المسح
if ($username !== null)     $_SESSION['username']     = $username;
if ($email !== null)        $_SESSION['email']        = $email;
?>

<div class="alert-container"></div>


<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top "
  style="width: 100%;box-shadow: 0px 2px 5px black; z-index: 5;">
  <div class="container-fluid">
    <div class="d-flex justify-content-center align-items-center gap-3">
      <!-- Total price -->
      <div class="text-light  d-flex " style="width: auto">
        <h6 style="margin-right: 5px;">ريال</h6>
        <h6 class="nav-total">0</h6>

      </div>

      <button type="button" class=" btn btn-light text-dark position-relative shopping  btn-outline-secondary"
        style=" padding: 10px; border-radius: 100%; border: 1px solid gray; width: 50px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
          class="bi bi-bag-fill" viewBox="0 0 16 16">
          <path
            d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4z" />
        </svg>

        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger quantity"
          style="margin-top: 10px; ">
          0
          <span class="visually-hidden">non add product</span>
        </span>
      </button>

      <button type="button" class=" btn btn-light text-dark position-relative btn-outline-secondary heart"
        style="padding: 10px; border-radius: 100%; border: 1px solid gray; width: 50px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
          class="bi bi-heart" viewBox="0 0 16 16">
          <path
            d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15" />
        </svg>

        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger favorite-count quantityh "
          style="margin-top: 10px; ">
          0
          <span class="visually-hidden">non add favorate</span>
        </span>
      </button>
      <div class="user-auth">
        <?php if (!empty($username)): ?>
          <div class="profile-box">
            <div class="avatar-circlee">
              <?php echo strtoupper($username[0]); ?>
            </div>
            <div class="dropdown">
              <a href="#">حسابي</a>
              <a href="logout.php" style="color: red;">تسجيل خروج <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                  <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                </svg></a>
            </div>
          </div>
        <?php else: ?>
          <button type="button"
            class="btn btn-light text-dark position-relative btn-outline-secondary login-btn-modal"
            style="padding: 10px; border-radius: 100%; border: 1px solid gray; width: 50px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30"
              fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
              <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
            </svg>
          </button>
        <?php endif; ?>
      </div>
    </div>

    <!-- قسم البحث -->
    <div class="d-flex justify-content-center align-items-center gap-3">
      <form action="" method="GET" class="d-flex" role="search" dir="rtl" onsubmit="return false;">
        <input id="search-input" name="search" class="form-control me-2" type="search" placeholder="ابحث هنا ...." aria-label="Search" value="">
        <button class="btn btn-outline-warning me-2 ms-4" type="button" id="search-btn">بحث</button>
      </form>
    </div>



    <!-- زر التبديل للموضع المتجاوب -->
    <button class="navbar-toggler " type="button" data-bs-toggle="collapse" data-bs-target="#body">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- الروابط -->
    <div class="collapse navbar-collapse hstack gap-3" id="navbarNav">

      <ul class="navbar-nav ms-auto justify-content-center flex-grow-l">
        <li class="nav-item">
          <a class="nav-link fw-bold" aria-current="page" href="#social">تواصل معنا</a>

        </li>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-bold " aria-current="page" href="#bodyy">المنتجات</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-bold active " aria-current="page" href="#hero">الرئيسية</a>
        </li>

      </ul>

      <!-- العلامة التجارية -->
      <a class="navbar-brand" href="index.php"><img src="images/logoo.png" alt="" style="width: 70px;"></a>
    </div>
  </div>
</nav>


<!-- خلفية شفاف للبطاقة قسم السلة و المفضلة -->
<div class="overlay"><div id="alerts" class="mt-3 d-flex justify-content-center "></div>
</div>

<!-- قسم السلة -->
<div class="containerr">
  <div class="list"></div>
</div>

<div class="cardShop">
  <div class="con">
    <ul>
      <li>
        <div class="closeShopping">
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
          </svg>
        </div>
      </li>
      <li>
        <h2>سلة التسوق</h2>
      </li>
    </ul>
  </div>

  <ul class="listCard"></ul>
  <div class="empty-cart-message">
    <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="currentColor" class="bi bi-bag-fill" viewBox="0 0 16 16">
      <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4z" />
    </svg>
    <p> لاتوجد منتجات في السلة</p>
  </div>


  <!-- ملخص السلة -->
  <div class="checkout p-3" dir="rtl" style="display: none;">
    <div class="d-flex justify-content-between subtotal-row">
      <span>
        المجموع الفرعي
        <span class="text-muted small">(${count} منتجات)</span>
      </span>
      <span class="subtotal">0 ريال</span>
    </div>
    <div class="d-flex justify-content-between discount-row text-success">
      <span>الخصم:</span>
      <span class="discount">0 ريال</span>
    </div>
    <hr>
    <div class="d-flex justify-content-between">
      <strong>المجموع الكلي <span style="font-size:15px;">(شامل ضريبة القيمة المضافة)</span>:</strong>
      <strong class="total">0 ريال</strong>
    </div>
    <div class="d-flex justify-content-between text-danger small">
      <span>ضريبة القيمة المضافة المقدّرة:</span>
      <span class="tax">0 ريال</span>
    </div>

    <div class="d-flex justify-content-center align-items-center mt-4 ">
      <a href="#" onclick="sendCartToSession()" class="btn-checkout">
       إتمام الطلب
      </a>
    </div>
  </div>
</div>
<!-- قسم المفضلة -->

<div class="containerrr">
  <div class="listHeart"></div>
</div>
<div class="cardHeart">
  <div class="conH">
    <ul>
      <li>
        <div class="closeHeart"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
          </svg>
        </div>
      </li>
      <li>
        <h2> المفضلة</h2>
      </li>
    </ul>
  </div>

  <ul class="listCardHeart"></ul>
  <div class="empty-heart-message">
    <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="currentColor" class="bi bi-bag-heart-fill" viewBox="0 0 16 16">
      <path d="M11.5 4v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m0 6.993c1.664-1.711 5.825 1.283 0 5.132-5.825-3.85-1.664-6.843 0-5.132" />
    </svg>
    <p> لاتوجد منتجات في المفضلة</p>
  </div>

</div>
</div>
<?php if (!empty($alerts)): ?>
  <div class="alert-box">
    <?php foreach ($alerts as $alert): ?>
      <div class="alert <?php echo $alert['type']; ?>">
        <span><?php echo $alert['message']; ?></span>

        <?php if ($alert['type'] === 'success'): ?>
          <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
          </svg>
        <?php else: ?>
          <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
          </svg>
        <?php endif; ?>

      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Overlay Container -->
<div class="containerPerson <?php echo !empty($activ_form) ? 'active' : ''; ?>">
  <!-- Auth Modal -->
  <div class=" auth-modal <?php echo $activ_form === 'register' ? 'show slide' : ($activ_form === 'login' ? 'show' : ''); ?>">
    <!-- <i class="uil uil-times form_close"></i> -->
    <button type="button" class="close-btn-modal"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-lg form_close" viewBox="0 0 16 16">
        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
      </svg></button>
    <!-- Login Form -->
    <div class="form-box login">
      <h2>تسجيل الدخول</h2>
      <h4>!مرحبا بعودتك</h4>
      <form action="login.php" method="POST">
        <div class="input-box">
          <label for="email">البريد الإلكتروني</label>
          <input type="email" name="email" required>
          <!-- <i class=" uil uil-envelope-alt email"></i> -->
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope email" viewBox="0 0 16 16">
            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
          </svg>
        </div>
        <div class="input-box">
          <label for="password">كلمة المرور</label>
          <input type="password" id="password" name="password" required>

          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
            class="bi bi-eye-slash-fill pw_hide" id="togglePassword"
            viewBox="0 0 16 16" style="cursor:pointer;">
            <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z" />
            <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829" />
            <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z" />
          </svg>
        </div>

        <button type="submit" class="btnn" name="login_btn">تسجيل الدخول</button>

        <p>ليس لديك حساب؟ <a href="#" id="register-link"> سجل الأن</a></p>
      </form>
    </div>


    <!-- Signup Form -->
    <div class="form-box register">
      <h2> إنشاء حساب جديد</h2>
      <form action="signup.php" method="POST">
        <div class="input-box">
          <label for="username"> إسم المستخدم</label>
          <input type="text" name="username" required>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-square" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
            <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1v-1c0-1-1-4-6-4s-6 3-6 4v1a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z" />
          </svg>
        </div>
        <div class="input-box">
          <label for="email">البريد الإلكتروني</label>
          <input type="email" name="email" required>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope email" viewBox="0 0 16 16">
            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
          </svg>
        </div>
        <div class="input-box">
          <label for="password">كلمة المرور</label>
          <input type="password" name="password" class="password" required>
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
            class="bi bi-eye-slash-fill pw_hide" id="togglePassword"
            viewBox="0 0 16 16" style="cursor:pointer;">
            <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z" />
            <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829" />
            <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z" />
          </svg>
        </div>
        <div class="input-box">
          <label for="password">تأكيد كلمة المرور </label>
          <input type="password" name="cpassword" class="password" required>
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
            class="bi bi-eye-slash-fill pw_hide" id="togglePassword"
            viewBox="0 0 16 16" style="cursor:pointer;">
            <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z" />
            <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829" />
            <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z" />
          </svg>         
        </div>

        <button type="submit" class="btnn" name="register_btn">تسجيل حساب </button>

        <p> لديك حساب؟ <a href="#" id="login-link">تسجيل الدخول</a></p>
      </form>
    </div>
  </div>
</div>

<script>
  function sendCartToSession() {
    const items = listCards.filter(i => i);
    if (items.length === 0) {
      alert("السلة فارغة!");
      return;
    }

    let subtotalBefore = 0;
    let subtotal = 0;
    let totalQty = 0;

    const cleanedItems = items.map(p => {
      const qty = p.quantity || 1;
      const price = parseFloat(p.price || 0);
      const priceBefore = p.priceBefore && parseFloat(p.priceBefore) > 0 ?
        parseFloat(p.priceBefore) :
        price;

      subtotal += price * qty;
      subtotalBefore += priceBefore * qty;
      totalQty += qty;

      return {
        id: p.id,
        name: p.name,
        price,
        priceBefore,
        quantity: qty,
        image1: p.image1
      };
    });

    const discount = subtotalBefore - subtotal;
    const grandTotal = subtotal; // الأسعار بعد الخصم تشمل الضريبة
    const net = grandTotal / 1.15; // السعر قبل الضريبة
    const tax = grandTotal - net; // الضريبة المفصولة

    const payload = {
      items: cleanedItems,
      subtotal: subtotal.toLocaleString(),
      subtotalBefore: subtotalBefore.toLocaleString(),
      discount: discount.toFixed(2),
      tax: tax.toLocaleString('en-US', {
        maximumFractionDigits: 2
      }),
      net: net.toLocaleString('en-US', {
        maximumFractionDigits: 2
      }),
      grandTotal: grandTotal.toLocaleString(),
      totalQty
    };

fetch("save_cart.php", {
  method: "POST",
  headers: {
    "Content-Type": "application/json"
  },
  body: JSON.stringify(payload)
})
.then(res => res.json())
.then(data => {
  const alertsDiv = document.getElementById("alerts");
  let alertHTML = "";

  if (data.success) {
    window.location.href = "checkout.php";
  } else {
    alertHTML = `
      <div class="alert error alert-danger alert-dismissible fade show text-end" role="alert" id="autoAlert" style="font-size:20px">
        ! فشل اتمام الطلب، يجب تسجيل الدخول أولاً
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    `;
    alertsDiv.innerHTML = alertHTML;

    // إخفاء التنبيه تلقائيًا بعد 6 ثوانٍ
    setTimeout(() => {
      const alertBox = document.getElementById("autoAlert");
      if (alertBox) {
        alertBox.classList.remove("show");
        alertBox.classList.add("fade");
        setTimeout(() => alertBox.remove(), 1000); // إزالة العنصر بعد انتهاء التأثير
      }
    }, 6000);
  }
})
.catch(err => {
  console.error(err);
  const alertsDiv = document.getElementById("alerts");
  alertsDiv.innerHTML = `
    <div class="alert error alert-danger alert-dismissible fade show text-end" role="alert" id="autoAlert">
       حدث خطأ أثناء حفظ السلة
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  `;

  setTimeout(() => {
    const alertBox = document.getElementById("autoAlert");
    if (alertBox) {
      alertBox.classList.remove("show");
      alertBox.classList.add("fade");
      setTimeout(() => alertBox.remove(), 500);
    }
  }, 5000);
});
}
</script>