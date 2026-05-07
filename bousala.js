/**
 * =============================================
 * Al-Bousala Store - Frontend JS Integration
 * يربط صفحات HTML بالـ PHP Backend
 * =============================================
 */

// const _isSubfolder = window.location.pathname.includes('/fixed_html/');
const _API_BASE    =  'backend/';


const API = {
    // cart:     'backend/cart.php',
    // products: 'backend/products.php',
        cart:       _API_BASE + 'cart.php',
    products:   _API_BASE + 'products.php',
    newsletter: _API_BASE + 'newsletter.php',
};

// ====================================================
// إدارة السلة
// ====================================================

const Cart = {

    /** جلب عدد المنتجات في السلة وتحديث الشارة */
    async updateBadge() {
        try {
            const res = await fetch(API.cart + '?action=count');
            const data = await res.json();
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = data.count || '';
                el.style.display = data.count > 0 ? 'inline-block' : 'none';
            });
        } catch (e) { /* تجاهل أخطاء الشبكة */ }
    },

    /** إضافة منتج إلى السلة */
    async add(productId, quantity = 1) {
        const body = new FormData();
        body.append('action', 'add');
        body.append('product_id', productId);
        body.append('quantity', quantity);

          try {
            const res  = await fetch(API.cart, { method: 'POST', body });
            const data = await res.json();
            if (data.success) {
                Cart.showToast(data.message || 'تمت الإضافة إلى السلة ✓', 'success');
                Cart.updateBadge();
            } else {
                Cart.showToast(data.message || 'تعذرت الإضافة', 'error');
            }
            return data;
        } catch(e) {
            Cart.showToast('تعذر الاتصال بالخادم', 'error');
        }
        return data;
    },

    /** حذف عنصر من السلة */
    async remove(itemId) {
        const body = new FormData();
        body.append('action', 'remove');
        body.append('item_id', itemId);

        const res  = await fetch(API.cart, { method: 'POST', body });
        const data = await res.json();

        if (data.success) {
            Cart.showToast('تم حذف المنتج', 'success');
            Cart.renderCartPage(data.items, data.totals);
            Cart.updateBadge();
        }
        return data;
    },

    /** تحديث الكمية */
    async update(itemId, quantity) {
        const body = new FormData();
         body.append('action',   'update');
        body.append('item_id',  itemId);
        body.append('quantity', quantity);

        const res  = await fetch(API.cart, { method: 'POST', body });
        const data = await res.json();
        if (!data.success) Cart.showToast(data.message, 'error');
        return data;
   
    },

    /** رسالة Toast مؤقتة */
    showToast(message, type = 'success') {
        let toast = document.getElementById('bousala-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'bousala-toast';
            toast.style.cssText = `
                        position:fixed; bottom:24px; 
                        left:50%; 
                        transform:translateX(-50%);
                padding:12px 28px;
                 border-radius:8px; 
                 font-size:14px;
                  z-index:9999;
                color:#fff; 
                font-family:'Segoe UI',sans-serif; direction:rtl;
                box-shadow:0 4px 20px rgba(0,0,0,.25); 
                transition:opacity .35s;
                max-width:90vw; 
                text-align:center; 
                pointer-events:none;`;
            document.body.appendChild(toast);
        }
        toast.style.background = type === 'success' ? '#27ae60' : '#e74c3c';
        toast.textContent = message;
        toast.style.opacity = '1';
        clearTimeout(toast._timer);
        // toast._timer = setTimeout(() => { toast.style.opacity = '0'; }, 3000);
           toast._timer = setTimeout(() => { toast.style.opacity = '0'; }, 3200);
    },
};

// ====================================================
// عرض المنتجات ديناميكياً
// ====================================================

// const Products = {

//     async load(category, containerId) {
//         const container = document.getElementById(containerId);
//         if (!container) return;

//         container.innerHTML = '<p style="text-align:center;padding:20px">جار التحميل...</p>';

//         const res  = await fetch(`${API.products}?action=list&category=${category}`);
//         const data = await res.json();

//         if (!data.success || !data.products.length) {
//             container.innerHTML = '<p style="text-align:center;padding:20px;color:#999">لا توجد منتجات حالياً</p>';
//             return;
//         }

//         container.innerHTML = data.products.map(p => `
//             <div class="product-card">
//                 <div class="product-img">
//                     <img src="${p.image || 'images/logo.png'}" alt="${p.name}">
//                 </div>
//                 <div class="product-info">
//                     <h3>${p.name}</h3>
//                     <p>${p.description || ''}</p>
//                     <div class="product-footer">
//                         <span class="price">${Number(p.price).toFixed(2)} ر.س</span>
//                         ${p.stock > 0
//                             ? `<button class="add-to-cart-btn" onclick="Cart.add(${p.id})">أضف للسلة</button>`
//                             : `<span class="out-of-stock">نفذت الكمية</span>`
//                         }
//                     </div>
//                 </div>
//             </div>`
//         ).join('');
//     },
// };

// ====================================================
// رسائل Flash من PHP
// ====================================================

function showFlashFromURL() {
    const params = new URLSearchParams(window.location.search);
    const type   = params.get('flash_type');
    const msg    = params.get('flash_msg');
    if (type && msg) {
        Cart.showToast(decodeURIComponent(msg), type);
        // إزالة الباراميترات من الرابط
        const url = new URL(window.location);
        url.searchParams.delete('flash_type');
        url.searchParams.delete('flash_msg');
        window.history.replaceState({}, '', url);
    }
}

// ====================================================
// تهيئة عند تحميل الصفحة
// ====================================================

document.addEventListener('DOMContentLoaded', () => {
    Cart.updateBadge();
    showFlashFromURL();

    // // تحميل صفحة السلة ديناميكياً إذا كانت مفتوحة
    // if (document.querySelector('.cart-items-section')) {
    //     Cart.loadCartPage();
    // }

    // // أزرار الإضافة للسلة
    document.querySelectorAll('[data-add-to-cart]').forEach(btn => {
          btn.addEventListener('click', () => Cart.add(btn.dataset.addToCart));
    });
});
