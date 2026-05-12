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

    document.addEventListener('DOMContentLoaded', async () => {
        const grid = document.getElementById('products-grid');
        try {
            const res  = await fetch(`backend/products.php?action=list&category=${type}`);
            const data = await res.json();
            if (!data.success || !data.products.length) {
                grid.innerHTML = '<p style="text-align:center;padding:40px;color:#999;grid-column:1/-1">لا توجد منتجات حالياً</p>';
                return;
            }
            allProducts = data.products;
            renderProducts(allProducts.slice(0, VISIBLE));
            if (allProducts.length > VISIBLE) {
                const btn = document.getElementById('load-more-btn');
                btn.style.display = '';
                btn.addEventListener('click', () => { renderProducts(allProducts); btn.style.display = 'none'; });
            }
        } catch(e) {
            grid.innerHTML = '<p style="text-align:center;padding:40px;color:red;grid-column:1/-1">تعذر تحميل المنتجات</p>';
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
