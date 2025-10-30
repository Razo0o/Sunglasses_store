let products = [];
let currentPage = 1;
let productsPerPage = 8;
let filteredProducts = [];

const cardsContainer = document.getElementById("glasses");
const totalProductsEl = document.getElementById("total-products");
const searchTitle = document.getElementById("search-title");
const heroSection = document.querySelector("#hero");
const paginationEl = document.getElementById("pagination");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");

// Fetch products from backend
fetch("http://localhost/HashPlus_final/getProduct.php")
  .then((res) => res.json())
  .then((data) => {
    products = data;
    filteredProducts = [...products];
    renderPageNumbers();
    showPage(currentPage); 
  })
  .catch((err) => console.error("Error fetching products:", err));

// ---------------- Pagination ----------------
function showPage(page) {
  const start = (page - 1) * productsPerPage;
  const end = start + productsPerPage;
  const productsToShow = filteredProducts.slice(start, end);

  renderProducts(productsToShow);
  document
    .querySelectorAll(".page-number")
    .forEach((el) => el.classList.remove("active"));
  const activeNum = document.querySelector(`.page-number[data-page='${page}']`);
  if (activeNum) activeNum.classList.add("active");

  prevBtn.parentElement.classList.toggle("disabled", page === 1);
  nextBtn.parentElement.classList.toggle(
    "disabled",
    page === Math.ceil(filteredProducts.length / productsPerPage)
  );
}

function renderPageNumbers() {
  const totalPages = Math.ceil(filteredProducts.length / productsPerPage);

  // إزالة أي أرقام سابقة
  document.querySelectorAll(".page-number").forEach((el) => el.remove());

  for (let i = 1; i <= totalPages; i++) {
    const li = document.createElement("li");
    li.className = "page-item page-number";
    li.dataset.page = i;
    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
    paginationEl.insertBefore(li, nextBtn.parentElement);

    li.addEventListener("click", (e) => {
      e.preventDefault();
      currentPage = i;
      showPage(currentPage);
    });
  }

  //paginationEl.style.display = totalPages > 1 ? "flex" : "none";
}

prevBtn.addEventListener("click", (e) => {
  e.preventDefault();
  if (currentPage > 1) {
    currentPage--;
    showPage(currentPage);
  }
});

nextBtn.addEventListener("click", (e) => {
  e.preventDefault();
  const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
  if (currentPage < totalPages) {
    currentPage++;
    showPage(currentPage);
  }
});

// ---------------- Render Products ----------------
function renderProducts(productsToShow) {
  cardsContainer.innerHTML = "";

  productsToShow.forEach((p, key) => {
    const premium = p.premium || ""; // إذا موجود بريميوم
    const discount = p.discount || 0; // إذا موجود خصم
    const priceBefore = p.priceBefore || 0; // إذا موجود السعر السابق

    cardsContainer.innerHTML += `
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">     
      <div class="card small-card position-relative" style="height:520px">
        <div 
          class="btn btn-light icon heart-icon" onclick="addToCardHeartById(${
            p.id
          },this)" data-id="${p.id}" 
          style="border-radius:50%; background-color:white; height:60px; width:60px; margin-left:5px; border:1px solid rgba(128,128,128,0.4);">
          <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor"
              class="bi bi-heart" viewBox="0 0 20 10">
            <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
          </svg>
        </div>

        ${premium ? `<div class="pri"><p>${premium}</p></div>` : ""}

        <div class="product-img">
          <img src="${p.image1}" class="product1" alt="...">
          <img src="${p.image2}" class="product2" alt="...">
        </div>

        <div class="card-body text-center">
          <h3 class="card-title">${p.name}</h3>
          <p class="card-text" style="margin-bottom:10px;">${
            p.nameDisc || ""
          }</p>

          <div class="m-f">
            <h4 style="padding-right: 5px;">ريال</h4>
            <h4>${p.price}</h4>
          </div>

          ${
            discount > 0 && priceBefore > 0
              ? `
            <ul class="money-dis">
              <li><div class="m-bb"><h5>-${discount}%</h5></div></li>
              <li><div class="m-b"><h5>ريال</h5><h5>${priceBefore}</h5></div></li>
            </ul>`
              : `<ul class="money-dis" style="padding:15px"></ul>`
          }

          <button onclick="addToCardById(${p.id})" data-id="${p.id}" 
            type="button" class="btn btn-dark btn-outline-secondary add-to-cart-btn" 
            style="width:100%; font-size:20px; color:white;">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                class="bi bi-bag" viewBox="0 0 16 16"
                style="margin-right:5px; margin-bottom:5px;">
              <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
            </svg>
            أضف للسلة
          </button>
        </div>
      </div>
    </div>
  `;
  });
}

// ---------------- Search Functionality ----------------
const searchBtn = document.getElementById("search-btn");
const searchInput = document.getElementById("search-input");

searchBtn.addEventListener("click", () => {
  const query = searchInput.value.trim();

  fetch(`search.php?search=${encodeURIComponent(query)}`)
    .then((res) => res.json())
    .then((data) => {
      products = data;
      filteredProducts = [...products];

      totalProductsEl.style.display = products.length > 0 ? "block" : "none";
      totalProductsEl.innerText = query
        ? `عدد المنتجات: ${products.length}`
        : "";

      searchTitle.innerText = query
        ? `نتائج البحث عن "${query}"`
        : "جميع نظارات الشمسية";

      searchTitle.style.color = "coral";
      searchTitle.style.marginTop = query ? "150px" : "0";

      // إضافة خط <hr> فقط عند البحث
      let hr = document.querySelector(".search-divider");
      if (query) {
        if (!hr) {
          hr = document.createElement("hr");
          hr.classList.add("search-divider");
          totalProductsEl.insertAdjacentElement("afterend", hr);
        }
      } else {
        if (hr) hr.remove();
      }

      // إخفاء heroSection إذا وُجد نص في البحث
      if (heroSection) heroSection.style.display = query ? "none" : "block";

      // رسالة إذا لم يتم العثور على منتجات
      const noProductsEl = document.getElementById("no-products");
      if (products.length === 0 && query) {
        noProductsEl.style.display = "block";
      } else {
        noProductsEl.style.display = "none";
      }

      // --- تمييز الكلمات المطابقة في الاسم والوصف ---
      // if (query) {
      //     const regex = new RegExp(`(${query})`, "gi"); // g = global, i = ignore case
      //     filteredProducts.forEach(p => {
      //         if (p.name) p.name = p.name.replace(regex, `<span class="highlight" style=" color: crimson; font-weight: bold;">$1</span>`);
      //         if (p.nameDisc) p.nameDisc = p.nameDisc.replace(regex, `<span class="highlight"style=" color: crimson; font-weight: bold;">$1</span>`);
      //     });
      // }

      currentPage = 1;
      renderPageNumbers();
      showPage(currentPage);
    })
    .catch((err) => console.error("Error fetching search results:", err));
});

// ---------------- Cart & Favorites ----------------

// تفاعل رمز المفضلة و اضافة في قسم المفضلة

let openHeart = document.querySelector(".heart");
let closeHeart = document.querySelector(".closeHeart");
let listHeart = document.querySelector(".listHeart");
let favoriteCount = document.querySelector(".favorite-count");

// تفاعل زر سلة التسوق  و اضافة في قسم السلة

let openShopping = document.querySelector(".shopping");
let closeShopping = document.querySelector(".closeShopping");
let listCard = document.querySelector(".listCard");
let total = document.querySelector(".total");
let quantity = document.querySelector(".quantity");

openShopping.addEventListener("click", () => {
  document.body.classList.add("active");
});
closeShopping.addEventListener("click", () => {
  document.body.classList.remove("active");
});

openHeart.addEventListener("click", () => {
  document.body.classList.add("active-heart");
});

closeHeart.addEventListener("click", () => {
  document.body.classList.remove("active-heart");
});

// قسم المفضلة

let listCardsHeart = [];
function addToCardHeartById(id, el) {
  const product = products.find((p) => p.id == id);
  if (!product) {
    console.warn("المنتج غير موجود في products");
    return;
  }

  const heartBtn = el;
  const heartSVG = heartBtn ? heartBtn.querySelector("svg") : null;

  if (!listCardsHeart[id]) {
    listCardsHeart[id] = { ...product, quantity: 1 };

    if (heartBtn) heartBtn.classList.add("active-heart");
    if (heartSVG)
      heartSVG.innerHTML = `
      <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>
    `;

    showAlertH(
      `<h5>تم إضافته إلى المفضلة <strong>(${product.name})</strong></h5>`
    );
  } else {
    delete listCardsHeart[id];

    if (heartBtn) heartBtn.classList.remove("active-heart");
    if (heartSVG)
      heartSVG.innerHTML = `
 class="bi bi-heart" viewBox="0 0 20 10">
            <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
            `;

    showAlertH(
      `<h5>تم إزالته من المفضلة <strong>(${product.name})</strong></h5>`
    );
  }

  reloadCardHeart();
}

// Show custom alert for favorites
function showAlertH(messageh) {
  const container = document.querySelector(".alert-container");
  if (!container) return;

  const alertDivH = document.createElement("div");
  alertDivH.className = "alert alert-primary alert-dismissible fade show";
  alertDivH.role = "alert";
  alertDivH.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
         class="bi bi-check-circle-fill me-2" viewBox="0 0 16 16">
      <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 
               5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 
               0 0 0-.01-1.05z"/>
    </svg>
    ${messageh}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;

  container.appendChild(alertDivH);

  setTimeout(() => {
    alertDivH.classList.remove("show");
    alertDivH.classList.add("hide");
    setTimeout(() => alertDivH.remove(), 1000);
  }, 6000);
}
function reloadCardHeart() {
  const listCardHeart = document.querySelector(".listCardHeart");
  const emptyHeartMsg = document.querySelector(".empty-heart-message");
  if (!listCardHeart || !emptyHeartMsg) return;

  listCardHeart.innerHTML = "";
  let countH = 0;

  Object.values(listCardsHeart).forEach((item) => {
    if (item) {
      countH += item.quantity;

      const newDivH = document.createElement("li");
      newDivH.classList.add("favorite-item");
      newDivH.innerHTML = `
        <div>
          <button onclick="deleteItemHeart(${
            item.id
          })" class="btn btn-danger btn-outline-danger text-light">
   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
  <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
</svg>
          </button>
        </div>
        <div><h4>ريال</h4>${item.price.toLocaleString()}</div>
        <div>${item.name}</div>
        <div><img src="${item.image1}" width="50"/></div>
      `;
      listCardHeart.appendChild(newDivH);
    }
  });

  const favoriteCount = document.querySelector(".favorite-count");
  if (favoriteCount) favoriteCount.innerText = countH;

  // إظهار أو إخفاء الرسالة حسب وجود منتجات
  emptyHeartMsg.style.display = countH === 0 ? "block" : "none";
}
// حذف المنتج من المفضلة
function deleteItemHeart(id) {
  delete listCardsHeart[id];
  reloadCardHeart();

  const heartBtn = document.querySelector(`.heart-icon[data-id='${id}']`);
  const heartSVG = heartBtn ? heartBtn.querySelector("svg") : null;

  if (heartBtn) heartBtn.classList.remove("active-heart");
  if (heartSVG) {
    heartSVG.innerHTML = `
      <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385..."/>
`;
  }
}

// قسم اضاقة في السلة

let listCards = [];
function addToCardById(id) {
  const product = products.find((p) => p.id == id);
  if (!product) {
    console.warn("المنتج غير موجود في products");
    return;
  }

  if (!listCards[product.id]) {
    listCards[product.id] = { ...product, quantity: 1 };
  } else {
    listCards[product.id].quantity += 1;
  }

  reloadCard();
  showAlert(
    `<h5>تم إضافته إلى السلة <span style="font-weight:bold;">(${product.name})</span></h5>`
  );
}

function showAlert(message) {
  const alertContainer = document.querySelector(".alert-container");

  const alertDiv = document.createElement("div");
  alertDiv.className = "alert alert-success alert-dismissible fade show";
  alertDiv.role = "alert";
  alertDiv.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-check-circle-fill me-2" viewBox="0 0 16 16">
      <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
    </svg>
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;

  alertContainer.appendChild(alertDiv);
  // Trigger transition
  setTimeout(() => {
    alertDiv.classList.add("show");
  }, 50); // slight delay to apply transition

  // Hide and remove after 3 seconds
  setTimeout(() => {
    alertDiv.classList.remove("show");
    alertDiv.classList.add("hide");
    setTimeout(() => alertDiv.remove(), 1000);
  }, 6000);
}
function reloadCard() {
  listCard.innerHTML = ``;

  let count = 0;
  let subtotalBefore = 0;
  let subtotalAfter = 0;
  let hasDiscount = false;

  listCards.forEach((item, key) => {
    if (!item) return;

    const qty = item.quantity || 1;
    const price = parseFloat(item.price) || 0;
    const priceBeforeRaw = parseFloat(item.priceBefore);
    const priceBefore = priceBeforeRaw > 0 ? priceBeforeRaw : price;
    const hasValidDiscount = priceBefore > price;

    subtotalBefore += priceBefore * qty;
    subtotalAfter += price * qty;
    count += qty;
    if (hasValidDiscount) hasDiscount = true;

    const li = document.createElement("li");
    li.innerHTML = `
      <div class="controls">
        <div class="controlsQty">
          <button onclick="changeQuantity(${key}, ${qty - 1})">-</button>
          <span class="count">${qty}</span>
          <button onclick="changeQuantity(${key}, ${qty + 1})">+</button>
        </div>
        <div>
          <button onclick="deleteItem(${key})" class="btn btn-danger btn-outline-danger text-light">
<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
  <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
</svg>
          </button>
        </div>
      </div>

      <div class="product-info">
        <div class="name">${item.name}</div>
        <div class="price-line" dir="rtl">
          ${
            hasValidDiscount
              ? `<del>${priceBefore.toLocaleString()} <span>ريال</span></del>`
              : ""
          }
          ${
            hasValidDiscount
              ? `<span class="badge">${Math.ceil(
                  (1 - price / priceBefore) * 100
                )}%-</span>`
              : ""
          }
        </div>
        <div class="totall" dir="rtl">
          <h4>${price.toLocaleString()} <span>ريال</span></h4>
        </div>
      </div>

      <div>
        <img src="${item.image1}" alt="${item.name}" />
      </div>
    `;
    listCard.appendChild(li);
  });

  const discount = subtotalBefore - subtotalAfter;
  const grandTotal = subtotalAfter;
  const net = grandTotal / 1.15;
  const tax = grandTotal - net;

  const subtotalEl = document.querySelector(".subtotal");
  const discountEl = document.querySelector(".discount");
  const totalEl = document.querySelector(".total");
  const taxEl = document.querySelector(".tax");

  const subtotalRow = document.querySelector(".subtotal-row");
  const discountRow = document.querySelector(".discount-row");

  if (subtotalRow) subtotalRow.style.display = "flex";
  if (subtotalEl)
    subtotalEl.textContent = subtotalBefore.toLocaleString() + " ريال";

  if (discountRow) {
    discountRow.style.display = hasDiscount && discount > 0 ? "flex" : "none";
    if (discountEl)
      discountEl.textContent = "-" + discount.toLocaleString() + " ريال";
  }

  if (totalEl) totalEl.textContent = grandTotal.toLocaleString() + " ريال";
  if (taxEl)
    taxEl.textContent =
      tax.toLocaleString("en-US", { maximumFractionDigits: 2 }) + " ريال";

  if (typeof quantity !== "undefined") {
    quantity.innerText = count;
  }

  const subtotalLabel = document.querySelector(".subtotal-row span");
  if (subtotalLabel) {
    subtotalLabel.innerHTML = `المجموع الفرعي <span class="text-muted small">(${count} منتجات)</span>`;
  }

  const navTotalEl = document.querySelector(".nav-total");
  if (navTotalEl) {
    navTotalEl.textContent = grandTotal.toLocaleString();
  }

  const checkoutEl = document.querySelector(".checkout");
  const emptyMessageEl = document.querySelector(".empty-cart-message");

  if (count === 0) {
    if (checkoutEl) checkoutEl.style.display = "none";
    if (emptyMessageEl) emptyMessageEl.style.display = "block";
  } else {
    if (checkoutEl) checkoutEl.style.display = "block";
    if (emptyMessageEl) emptyMessageEl.style.display = "none";
  }
}

function changeQuantity(key, newQuantity) {
  if (newQuantity <= 0) {
    delete listCards[key];
  } else {
    listCards[key].quantity = newQuantity;
  }
  reloadCard();
}

function deleteItem(key) {
  delete listCards[key];
  reloadCard();
}

//button login/signup
const authModal = document.querySelector(".auth-modal");
const loginLink = document.getElementById("login-link");
const registerLink = document.getElementById("register-link");
const loginBtnModal = document.querySelector(".login-btn-modal");
const closeBtnModal = document.querySelector(".close-btn-modal");
const containerPerson = document.querySelector(".containerPerson");
const profileBox = document.querySelector(".profile-box");
const avatarCirclee = document.querySelector(".avatar-circlee");
const alertBox = document.querySelector(".alert-box");

// فتح modal
if (loginBtnModal)
  loginBtnModal.addEventListener("click", () => {
    containerPerson.classList.add("active");
    authModal.classList.add("show");
  });

// اغلاق modal مع transition
if (closeBtnModal)
  closeBtnModal.addEventListener("click", () => {
    authModal.classList.remove("show", "slide"); // start closing animation
    containerPerson.classList.remove("active");
  });

// تبديل forms
registerLink.addEventListener("click", (e) => {
  e.preventDefault();
  authModal.classList.add("slide");
});

loginLink.addEventListener("click", (e) => {
  e.preventDefault();
  authModal.classList.remove("slide");
});

if (avatarCirclee)
  avatarCirclee.addEventListener("click", () =>
    profileBox.classList.toggle("show")
  );

if (alertBox) {
  setTimeout(() => alertBox.classList.add("show"), 50);

  setTimeout(() => {
    alertBox.classList.remove("show");
    setTimeout(alertBox.remove(), 1000);
  }, 6000);
}

// فلترة المنتجات
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("filter-btn")) {
    const category = e.target.dataset.category;

    // تفعيل الزر المحدد فقط
    document
      .querySelectorAll(".filter-btn")
      .forEach((btn) => btn.classList.remove("active"));
    e.target.classList.add("active");

    // فلترة المنتجات
    if (category === "all") {
      filteredProducts = [...products];
    } else {
      filteredProducts = products.filter((p) => p.category === category);
    }

    currentPage = 1;
    renderPageNumbers();
    showPage(currentPage);

    // تحديث العنوان وعدد المنتجات
    searchTitle.innerText =
      category === "all" ? "جميع نظارات الشمسية" : `نظارات الشمسية ${category}`;
    totalProductsEl.style.display = "block";
    totalProductsEl.innerText = `عدد المنتجات: ${filteredProducts.length}`;
  }
});
