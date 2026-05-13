

const _API_BASE    =  'backend/';

const API = {

        cart:       _API_BASE + 'cart.php',
    products:   _API_BASE + 'products.php',
    newsletter: _API_BASE + 'newsletter.php',
};

const Cart = {

    async updateBadge() {
        try {
            const res = await fetch(API.cart + '?action=count');
            const data = await res.json();
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = data.count || '';
                el.style.display = data.count > 0 ? 'inline-block' : 'none';
            });
        } catch (e) {  }
    },

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
            return { success: false, message: 'خطأ في الاتصال' };
        }
    },

    async remove(itemId) {
        const body = new FormData();
        body.append('action', 'remove');
        body.append('item_id', itemId);

        try {
            const res  = await fetch(API.cart, { method: 'POST', body });
            const data = await res.json();

            if (data.success) {
                Cart.showToast('تم حذف المنتج', 'success');
                Cart.renderCartPage(data.items, data.totals);
                Cart.updateBadge();
            } else {
                Cart.showToast(data.message || 'تعذر حذف المنتج', 'error');
            }
            return data;
        } catch(e) {
            Cart.showToast('تعذر الاتصال بالخادم', 'error');
            return { success: false, message: 'خطأ في الاتصال' };
        }
    },

    async update(itemId, quantity) {
        const body = new FormData();
        body.append('action',   'update');
        body.append('item_id',  itemId);
        body.append('quantity', quantity);

        try {
            const res  = await fetch(API.cart, { method: 'POST', body });
            const data = await res.json();
            if (!data.success) Cart.showToast(data.message, 'error');
            return data;
        } catch(e) {
            Cart.showToast('تعذر الاتصال بالخادم', 'error');
            return { success: false, message: 'خطأ في الاتصال' };
        }
    },

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
        
           toast._timer = setTimeout(() => { toast.style.opacity = '0'; }, 3200);
    },
};

function showFlashFromURL() {
    const params = new URLSearchParams(window.location.search);
    const type   = params.get('flash_type');
    const msg    = params.get('flash_msg');
    if (type && msg) {
        Cart.showToast(decodeURIComponent(msg), type);
        
        const url = new URL(window.location);
        url.searchParams.delete('flash_type');
        url.searchParams.delete('flash_msg');
        window.history.replaceState({}, '', url);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    Cart.updateBadge();
    showFlashFromURL();

    document.querySelectorAll('[data-add-to-cart]').forEach(btn => {
          btn.addEventListener('click', () => Cart.add(btn.dataset.addToCart));
    });
});

document.addEventListener('DOMContentLoaded', () => {
    if (document.cookie.includes("user_logged_in=true")) {
        const loginLink = document.querySelector('.login-link');
        if (loginLink) {
            loginLink.innerHTML = "تسجيل الخروج";
            loginLink.href = "backend/logout.php";
        }

        const emailDisplay = document.querySelector('.user-email-display');
        if (emailDisplay) {
            const email = document.cookie.split('user_email=')[1]?.split(';')[0];
            emailDisplay.textContent = decodeURIComponent(email);
        }
    }
});
