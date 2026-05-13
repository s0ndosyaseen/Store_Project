<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="andalusia.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="bousala.js"></script>
    <title id="dynamic-title">البوصلة</title>
    <style id="dynamic-bg"></style>
    <style>
        /* =========================
   PRODUCTS HEADINGS
========================= */

.products-heading,
.bestsellers-header {
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
    padding: 24px 28px;
    border-radius: 24px;

    background:
        linear-gradient(135deg,
            rgba(26,26,46,.96),
            rgba(73,52,18,.94),
            rgba(135,103,35,.90));

    border: 1px solid rgba(255,255,255,.08);

    box-shadow:
        0 10px 30px rgba(0,0,0,.12),
        0 18px 45px rgba(135,103,35,.12);

    color: white;
}

.products-heading::before,
.bestsellers-header::before {
    content: "";
    position: absolute;
    inset: 0;

    background:
        linear-gradient(120deg,
            transparent 20%,
            rgba(255,255,255,.06) 50%,
            transparent 80%);

    transform: translateX(-100%);
    animation: shine 7s linear infinite;
}

@keyframes shine {
    to {
        transform: translateX(100%);
    }
}

.products-heading span,
.bestsellers-header span {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 7px 16px;
    margin-bottom: 12px;

    border-radius: 999px;

    background: rgba(255,255,255,.10);
    border: 1px solid rgba(255,255,255,.12);

    color: #f8df9a;

    font-size: 12px;
    font-weight: 700;

    backdrop-filter: blur(10px);
}

.products-heading h2,
.bestsellers-header h2 {
    margin: 0;
    font-size: 30px;
    line-height: 1.4;
    font-weight: 800;
    letter-spacing: .5px;
}



/* =========================
   BESTSELLERS SLIDER
========================= */

.bestsellers-section {
    position: relative;
    margin: 45px 0;
}

.bestsellers-slider {
    display: flex;
    gap: 18px;

    padding: 18px;

    overflow-x: auto;
    scroll-behavior: smooth;

    border-radius: 24px;

    background:
        linear-gradient(to bottom,
            #f7efe2,
            #f1e3ca);

    border: 1px solid rgba(135,103,35,.12);

    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.6),
        0 12px 30px rgba(0,0,0,.06);
}

.bestsellers-slider::-webkit-scrollbar {
    height: 6px;
}

.bestsellers-slider::-webkit-scrollbar-thumb {
    background: #c4a35a;
    border-radius: 999px;
      padding: 20px 60px;
}



/* =========================
   PRODUCT CARD
========================= */

.bestseller-card {
    position: relative;

      flex: 0 0 250px;


    padding: 14px;

    border-radius: 22px;

    background: rgba(255,255,255,.75);

    border: 1px solid rgba(255,255,255,.5);

    backdrop-filter: blur(10px);

    box-shadow:
        0 10px 25px rgba(0,0,0,.08),
        0 2px 6px rgba(0,0,0,.05);

    transition:
        transform .35s ease,
        box-shadow .35s ease;
}

.bestseller-card:hover {
    transform: translateY(-8px);

    box-shadow:
        0 18px 40px rgba(0,0,0,.12),
        0 6px 16px rgba(196,163,90,.18);
}

.bestseller-card img {
      width: 100%;
    height: 360px;

    object-fit: cover;

    border-radius: 18px;

    margin-bottom: 14px;

    transition: transform .4s ease;
}

.bestseller-card:hover img {
    transform: scale(1.03);
}

.bestseller-card h4 {
    margin: 10px 0 6px;

    color: #2d2d2d;

    font-size: 15px;
    font-weight: 700;
    line-height: 1.6;
}

.bestseller-card .price {
    display: inline-block;

    margin-top: 4px;

    color: #9b742e;

    font-size: 15px;
    font-weight: 800;
}

.bestseller-card .add-to-cart-btn {
    width: 100%;

    margin-top: 14px;
    padding: 11px;

    border: none;
    border-radius: 14px;

    background:
        linear-gradient(135deg,
            #c4a35a,
            #9f7d36);

    color: white;

    font-size: 13px;
    font-weight: 700;

    cursor: pointer;

    transition: .3s ease;
}

.bestseller-card .add-to-cart-btn:hover {
    transform: translateY(-2px);

    background:
        linear-gradient(135deg,
            #d5b46a,
            #ae883b);
}



/* =========================
   SLIDER BUTTONS
========================= */

.slider-controls {
      position: absolute;

    inset: 50% 0 auto 0;

    transform: translateY(-50%);

    pointer-events: none;

    z-index: 10;
}

.slider-nav {
   position: absolute;

    width: 52px;
    height: 52px;

    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;

    background:
        linear-gradient(135deg,
            rgba(196,163,90,.96),
            rgba(145,113,45,.96));

    color: white;

    font-size: 18px;

    cursor: pointer;

    pointer-events: auto;

    box-shadow:
        0 10px 25px rgba(0,0,0,.16);

    transition: .3s ease;
}

.slider-nav:hover {
    transform: scale(1.08);

    box-shadow:
        0 14px 32px rgba(0,0,0,.22);
}
.slider-nav.left {
    right: 12px;
}

.slider-nav.right {
    left: 12px;
}


/* =========================
   LAYOUT
========================= */

.contain {
    display: grid;
    grid-template-columns: 280px 1fr;
    
    gap: 14px;

    align-items: start;

    position: relative;
}

/* =========================
   SIDEBAR
========================= */

.sidebar {
    position: sticky;
    top: 20px;

    max-height: calc(100vh - 100px);
    overflow-y: auto;
    overflow-x: hidden;

    padding: 18px;

    border-radius: 20px;

    background:
        linear-gradient(
            180deg,
            rgba(255,255,255,.88),
            rgba(248,241,228,.94)
        );

    border: 1px solid rgba(196,163,90,.18);

    box-shadow:
        0 8px 24px rgba(0,0,0,.05);

    backdrop-filter: blur(10px);
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(196,163,90,.08);
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(196,163,90,.4);
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(196,163,90,.6);
}

.sidebar h3 {
    margin-top: 0;
    margin-bottom: 22px;

    color: #1a1a2e;

    font-size: 24px;
    font-weight: 800;
}

.filter {
    margin-bottom: 26px;
}

.filter h4 {
    margin-bottom: 12px;

    color: #876723;

    font-size: 15px;
    font-weight: 800;
}

.filter label {
    display: block;

    margin-bottom: 10px;

    color: #444;

    cursor: pointer;

    transition: .2s ease;
}

.filter label:hover {
    color: #a67c52;
    transform: translateX(-3px);
}

/* =========================
   PRODUCTS AREA
========================= */

.products {
    min-width: 0;
}

/* =========================
   PRODUCTS GRID
========================= */

.grid {
    display: grid;

    grid-template-columns:
        repeat(auto-fit, minmax(300px, 1fr));

    gap: 20px;
}

/* =========================
   FIX CARD SIZE
========================= */

.bestseller-card {
    flex: 0 0 210px;
}

.bestseller-card img {
    height: 210px;
}

/* =========================
   RESPONSIVE
========================= */

@media (max-width: 1100px) {

    .contain {
        grid-template-columns: 220px 1fr;
        gap: 12px;
    }

    .bestseller-card {
        flex: 0 0 190px;
    }

    .bestseller-card img {
        height: 190px;
    }
}

@media (max-width: 900px) {

    .contain {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .sidebar {
        position: relative;
        top: unset;
        max-height: unset;
        overflow-y: visible;
    }

    .grid {
        grid-template-columns:
            repeat(auto-fit, minmax(180px, 1fr));
    }

    .bestseller-card {
        flex: 0 0 180px;
    }

    .bestseller-card img {
        height: 180px;
    }
}


/* =========================
   PROMO CAROUSEL
========================= */

.promo-carousel {
    position: relative;

    margin: 34px 0;

    height: 320px;

    overflow: hidden;

    border-radius: 28px;

    background: #1a1a2e;

    border: 1px solid rgba(255,255,255,.08);

    box-shadow:
        0 18px 45px rgba(0,0,0,.16);
}

.promo-slide {
    position: absolute;
    inset: 0;

    opacity: 0;

    transform: scale(1.04);

    transition:
        opacity .7s ease,
        transform .7s ease;
}

.promo-slide.active {
    opacity: 1;
    transform: scale(1);
}

.promo-slide img {
    width: 100%;
    height: 100%;

    object-fit: cover;
}

.promo-slide::after {
    content: "";

    position: absolute;
    inset: 0;

    background:
        linear-gradient(to top,
            rgba(0,0,0,.45),
            rgba(0,0,0,.05));
}



/* =========================
   DOTS
========================= */

.promo-dots {
    position: absolute;

    left: 22px;
    bottom: 20px;

    display: flex;
    gap: 8px;

    z-index: 5;
}

.promo-dots button {
    width: 10px;
    height: 10px;

    border: none;
    border-radius: 999px;

    background: rgba(255,255,255,.5);

    cursor: pointer;

    transition: .3s ease;
}

.promo-dots button.active {
    width: 34px;

    background: #c4a35a;
}



/* =========================
   RESPONSIVE
========================= */

@media (max-width: 768px) {

    .products-heading,
    .bestsellers-header {
        padding: 20px;
        border-radius: 20px;
    }

    .products-heading h2,
    .bestsellers-header h2 {
        font-size: 23px;
    }

    .promo-carousel {
        height: 220px;
        border-radius: 22px;
    }

    .bestseller-card {
        flex: 0 0 190px;
    }

    .bestseller-card img {
        height: 190px;
    }

    .slider-nav {
        padding: 11px;
        font-size: 14px;
    }
}
    </style>
</head>
<body>
<div class="header">
    <div class="container">
        <div class="logo">
            <img src="images/logo.png" alt="logo about busalah">
        </div>
        <div>
            <ul class="navbar">
                <li><a href="home.html">الرئيسية</a></li>
                <li><a href="home.html#story">قصتـنا</a></li>
                <li> <a href="home.html#categories">الـحضـارات</a></li>
                <li><a href="my_orders.php" class="login-btn">تتبع الطلب</a></li>
                <!--
                <li><a href="#">ديـكـور</a></li>
                <li><a href="#">اكسـسوارات</a></li>
                <li><a href="#">مـلابـس</a></li>
                 -->
                <li><a href="cart-full.html" class="login-btn">سلة المشتريات</a></li>
                <li><a href="login.html" class="login-btn login-link">تسجيل الدخول</a></li>
            </ul>

        </div>
    </div>
    <!-- start home  -->
    <div class="home" >
        <div class="container">
            <div class="home-pic">
             <h1 id="hero-title"></h1>
                <p id="hero-desc"></p>
                <a href="#categories">تسوق الآن !</a>
            </div>

        </div>

    </div>
    <!--start category  -->
    <div class="category" id="categories">

        <div class="above">
            <h2>استكشف الحضارات</h2>
            <h3>بوابتك لثقافات العالم من خلال قطع فنية مختارة تجسد التاريخ والروح الإبداعية لكل عصر</h3>
        </div>

        <div class="slider">

            <div class="hadara and">
                <a href="category.php?type=andalus">
                    <h3>الأنـدلــس</h3>
                    <hr class="line">
                </a>
            </div>

            <div class="hadara ps">
                <a href="category.php?type=sham">
                    <h3>بلاد الشـام</h3>
                    <hr class="line">
                </a>
            </div>

            <div class="hadara fk">
                <a href="category.php?type=victory">
                    <h3> الفكتورية </h3>
                    <hr class="line">
                </a>
            </div>
 
            <div class="hadara egp">
                <a href="category.php?type=egypt">
                    <h3>الفرعـونية</h3>
                    <hr class="line">
                </a>
            </div>

        </div>
    </div>

</div>


<!-- start content -->

<div class="contain">

    <!-- Sidebar Filters -->
    <aside class="sidebar">
        <h3>تصفية النتائج</h3>

        <div class="filter">
            <h4>بحث سريع</h4>
            <input type="text" id="search-products" placeholder="ابحث عن منتج..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;font-family:inherit">
        </div>

        <div class="filter">
            <h4>الفئات</h4>
            <label><input type="checkbox" class="subcategory-filter" value="ديكور"> ديكور منزلي</label>
            <label><input type="checkbox" class="subcategory-filter" value="اكسسوارات"> اكسسوارات نسائية</label>
            <label><input type="checkbox" class="subcategory-filter" value="ملابس"> ملابس نسائية</label>
        </div>

        <div class="filter">
            <h4>نطاق السعر</h4>

            <div class="price-values">
                <span id="minPrice">100</span> ر.س
                -
                <span id="maxPrice">5000</span> ر.س
            </div>

            <input type="range" id="rangeMin" min="100" max="5000" value="100">
            <input type="range" id="rangeMax" min="100" max="5000" value="1500">
        </div>
    </aside>

    <!-- Products -->
    <main class="products">
        
        <!-- Bestsellers Slider Section -->
        <section class="bestsellers-section">
            <div class="bestsellers-header">
                <span>أفضل الاختيارات</span>
                <h2>المنتجات الأكثر مبيعاً</h2>
            </div>
            <div class="bestsellers-slider" id="bestsellers-slider">
                <div style="text-align:center;padding:40px;color:#888">
                    <i class="fas fa-spinner fa-spin" style="font-size:28px;color:#c4a35a"></i>
                    <p style="margin-top:12px">جار تحميل المنتجات الأكثر مبيعاً...</p>
                </div>
            </div>
            <div class="slider-controls">
                <button class="slider-nav left" id="bestsellers-prev"><i class="fas fa-chevron-right"></i></button>
                <button class="slider-nav right" id="bestsellers-next"><i class="fas fa-chevron-left"></i></button>
            </div>
        </section>

        <!-- Promo Carousel -->
        <section class="promo-carousel" aria-label="عروض البوصلة">
            <div class="promo-track" id="promo-track"></div>
            <div class="promo-dots" id="promo-dots"></div>
        </section>

        <!-- Regular Products Section -->
        <section class="products-heading">
            <span>مختارات العملاء</span>
            <h2>جميع المنتجات</h2>
        </section>

        <div class="grid" id="products-grid">
            <div style="text-align:center;padding:40px;color:#888;grid-column:1/-1">
                <i class="fas fa-spinner fa-spin" style="font-size:28px;color:#c4a35a"></i>
                <p style="margin-top:12px">جار تحميل المنتجات...</p>
            </div>
        </div>

        <button class="load-more" id="load-more-btn" style="display:none">عرض المزيد</button>

    </main>

</div>

<?php include 'footer.php'; ?>

<script>

async function loadDynamicCategories() {
    const slider = document.querySelector('#categories .slider');
    if (!slider) return;

    try {
        const res = await fetch('backend/categories.php');
        const data = await res.json();
        if (!data.success || !Array.isArray(data.categories) || data.categories.length === 0) return;

        slider.innerHTML = '';
        data.categories.forEach(cat => {
            const image = cat.bg_image || 'images/logo.png';
            const card = document.createElement('div');
            card.className = 'hadara';
            card.style.backgroundImage = `linear-gradient(rgba(0,0,0,.10), rgba(0,0,0,.35)), url("${image}")`;

            const link = document.createElement('a');
            link.href = `category.php?type=${encodeURIComponent(cat.slug)}`;

            const title = document.createElement('h3');
            title.textContent = cat.title || cat.slug;

            const line = document.createElement('hr');
            line.className = 'line';

            link.appendChild(title);
            link.appendChild(line);
            card.appendChild(link);
            slider.appendChild(card);
        });
    } catch (e) {
        console.error('تعذر تحميل الحضارات:', e);
    }
}

document.addEventListener('DOMContentLoaded', loadDynamicCategories);

const urlParams = new URLSearchParams(window.location.search);
const type = urlParams.get('type') || 'egypt'; // الافتراضي فرعوني إذا ما في نوع

// 3. تحديث الصفحة بناءً على الحضارة
async function loadCategoryInfo() {
    try {
        const res = await fetch(`backend/get_category.php?type=${type}`);
        const result = await res.json();

        if (result.success) {
            const data = result.data;
            
            // تحديث العناوين بناءً على أعمدة جدول categories
            document.title = data.title;
            document.getElementById('hero-title').innerText = data.hero_title;
            document.getElementById('hero-desc').innerText = data.hero_desc;
            
            // تحديث الخلفية بالصورة المخزنة في الداتا بيس
            document.getElementById('dynamic-bg').innerHTML = `
                .home { 
                    background-image: url(${data.bg_image}); 
                    background-size: cover; 
                    height: 700px; 
                    background-attachment: fixed; 
                }
            `;
        }
    } catch(e) {
        console.error("خطأ في جلب بيانات الحضارة:", e);
    }
}

// استدعاء الدالة فوراً
loadCategoryInfo();


    const VISIBLE = 7;
    let allProducts = [];
    let bestsellers = [];

    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const res = await fetch(`backend/products.php?action=list&category=${type}&sort=bestselling`);
            const data = await res.json();
            if (!data.success || !data.products.length) {
                document.getElementById('products-grid').innerHTML = '<p style="text-align:center;padding:40px;color:#999;grid-column:1/-1">لا توجد منتجات حالياً</p>';
                document.getElementById('bestsellers-slider').innerHTML = '<p style="text-align:center;padding:40px;color:#999">لا توجد منتجات للعرض</p>';
                return;
            }
            allProducts = data.products;
            bestsellers = allProducts.slice(0, 5); // أفضل 5 منتجات مبيعاً
            
            // عرض الأكثر مبيعاً في الشريط اليدوي
            renderBestsellers(bestsellers);
            
            // عرض جميع المنتجات في الشبكة العادية
            renderProducts(allProducts.slice(0, VISIBLE));
            if (allProducts.length > VISIBLE) {
                const btn = document.getElementById('load-more-btn');
                btn.style.display = '';
                btn.addEventListener('click', () => { renderProducts(allProducts); btn.style.display = 'none'; });
            }
        } catch(e) {
            document.getElementById('products-grid').innerHTML = '<p style="text-align:center;padding:40px;color:red;grid-column:1/-1">تعذر تحميل المنتجات</p>';
        }
        // دالة تطبيق الفلترة الكاملة
        function applyFilters() {
            const minPrice = parseFloat(document.getElementById('rangeMin').value);
            const maxPrice = parseFloat(document.getElementById('rangeMax').value);
            const searchText = document.getElementById('search-products')?.value.trim().toLowerCase() || '';

            // جمع الفئات المختارة
            const selectedCategories = Array.from(document.querySelectorAll('.subcategory-filter:checked'))
                .map(cb => cb.value);

            const filtered = allProducts.filter(p => {
                const productPrice = parseFloat(p.price);
                const priceInRange = productPrice >= minPrice && productPrice <= maxPrice;
                const matchesSearch = !searchText || p.name.toLowerCase().includes(searchText);


                const matchesCategory = selectedCategories.length === 0 ||
                    selectedCategories.includes(p.subcategory);

                return priceInRange && matchesSearch && matchesCategory;
            });

            renderProducts(filtered);
            const btn = document.getElementById('load-more-btn');
            if(btn) btn.style.display = 'none';
        }

        //  تغيير الحد الأدنى
        document.getElementById('rangeMin').addEventListener('input', function() {
            const minPrice = parseFloat(this.value);
            const maxPrice = parseFloat(document.getElementById('rangeMax').value);
            if (minPrice > maxPrice) {
                this.value = maxPrice;
            }
            document.getElementById('minPrice').textContent = parseFloat(this.value);
            applyFilters();
        });

        //  تغيير الحد الأقصى
        document.getElementById('rangeMax').addEventListener('input', function() {
            const maxPrice = parseFloat(this.value);
            const minPrice = parseFloat(document.getElementById('rangeMin').value);
            if (maxPrice < minPrice) {
                this.value = minPrice;
            }
            document.getElementById('maxPrice').textContent = parseFloat(this.value);
            applyFilters();
        });

        //  البحث 
        const searchInput = document.getElementById('search-products');
        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }

        document.querySelectorAll('.subcategory-filter').forEach(checkbox => {
            checkbox.addEventListener('change', applyFilters);
        });
    });

    function renderProducts(products) {
        document.getElementById('products-grid').innerHTML = products.map(p => `
        <div class="card">
            <a class="card-link" href="product.html?id=${p.id}">
                <img src="${p.image || 'images/logo.png'}" alt="${p.name}">
                <h4>${p.name}</h4>
                <p class="price">${Number(p.price)} ر.س</p>
            </a>
            ${p.stock > 0
                ? `<button class="add-to-cart-btn" onclick="Cart.add(${p.id})">
                       <i class="fas fa-shopping-cart"></i> أضف للسلة
                   </button>`
                : `<button class="add-to-cart-btn" disabled>نفذت الكمية</button>`
            }
        </div>`
        ).join('');
    }

    function renderBestsellers(products) {
        const slider = document.getElementById('bestsellers-slider');
        if (!slider) return;

        slider.innerHTML = products.map(p => `
        <div class="bestseller-card">
            <a class="card-link" href="product.html?id=${p.id}">
                <img src="${p.image || 'images/logo.png'}" alt="${p.name}">
                <h4>${p.name}</h4>
                <p class="price">${Number(p.price)} ر.س</p>
            </a>
            ${p.stock > 0
                ? `<button class="add-to-cart-btn" onclick="Cart.add(${p.id})">
                       <i class="fas fa-shopping-cart"></i> أضف للسلة
                   </button>`
                : `<button class="add-to-cart-btn" disabled>نفذت الكمية</button>`
            }
        </div>`
        ).join('');

        // تفعيل التحكم بالأسهم
        setupBestsellersSliderControls();
    }

    function setupBestsellersSliderControls() {
        const slider = document.getElementById('bestsellers-slider');
        const prevBtn = document.getElementById('bestsellers-prev');
        const nextBtn = document.getElementById('bestsellers-next');
        
        if (!slider || !prevBtn || !nextBtn) return;

        const cardWidth = 280; // عرض الكارت + الفراغ
        let position = 0;

        prevBtn.addEventListener('click', () => {
            position = Math.min(position + cardWidth, 0);
            slider.style.transform = `translateX(${position}px)`;
        });

        nextBtn.addEventListener('click', () => {
            const maxScroll = -(slider.scrollWidth - slider.parentElement.clientWidth);
            position = Math.max(position - cardWidth, maxScroll);
            slider.style.transform = `translateX(${position}px)`;
        });
    }

    const PROMO_IMAGES = [
        'images/role.png',
        'images/role3.png',
        'images/role2.png'
        
    ];

    function initPromoCarousel() {
        const track = document.getElementById('promo-track');
        const dots = document.getElementById('promo-dots');
        if (!track || !dots || PROMO_IMAGES.length === 0) return;

        track.innerHTML = PROMO_IMAGES.map((src, index) => `
            <div class="promo-slide ${index === 0 ? 'active' : ''}">
                <img src="${src}" alt="عرض البوصلة ${index + 1}">
            </div>
        `).join('');

        dots.innerHTML = PROMO_IMAGES.map((_, index) => `
            <button type="button" class="${index === 0 ? 'active' : ''}" aria-label="الصورة ${index + 1}"></button>
        `).join('');

        let current = 0;
        const slides = Array.from(track.querySelectorAll('.promo-slide'));
        const dotButtons = Array.from(dots.querySelectorAll('button'));

        function showSlide(index) {
            slides[current].classList.remove('active');
            dotButtons[current].classList.remove('active');
            current = index;
            slides[current].classList.add('active');
            dotButtons[current].classList.add('active');
        }

        dotButtons.forEach((dot, index) => {
            dot.addEventListener('click', () => showSlide(index));
        });

        setInterval(() => {
            showSlide((current + 1) % slides.length);
        }, 3500);
    }

    document.addEventListener('DOMContentLoaded', initPromoCarousel);
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const emailParam = params.get('email');

        if (emailParam) {
            const emailInput = document.querySelector('input[type="email"]');
            if (emailInput) {
                emailInput.value = decodeURIComponent(emailParam);
            }
        }
    });
    function goToLogin() {
        const email = document.getElementById('quick-email').value.trim();
        if (email) {
            // الانتقال لصفحة login.html مع إرسال الإيميل في الرابط
            window.location.href = `login.html?email=${encodeURIComponent(email)}`;
        } else {
            window.location.href = `login.html`;
        }
    }

</script>
</body>
</html>
