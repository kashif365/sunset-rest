import * as bootstrap from 'bootstrap';
import Alpine from 'alpinejs';

window.bootstrap = bootstrap;
window.Alpine = Alpine;

/**
 * Menu page helper: live search + dietary / availability filters,
 * all operating on server-rendered cards.
 */
Alpine.data('menuFilter', () => ({
    search: '',
    diet: '',
    availableOnly: false,

    matches(el) {
        const haystack = (el.dataset.name + ' ' + (el.dataset.desc || '')).toLowerCase();
        if (this.search && !haystack.includes(this.search.toLowerCase())) return false;
        if (this.diet && !(el.dataset.diet || '').split(',').includes(this.diet)) return false;
        if (this.availableOnly && el.dataset.available !== '1') return false;
        return true;
    },

    apply() {
        document.querySelectorAll('[data-menu-item]').forEach((el) => {
            el.classList.toggle('d-none', !this.matches(el));
        });
        document.querySelectorAll('[data-menu-section]').forEach((section) => {
            const visible = section.querySelectorAll('[data-menu-item]:not(.d-none)').length;
            section.classList.toggle('d-none', visible === 0);
        });
    },
}));

/**
 * Item detail: price recalculation as variations/modifiers change.
 */
Alpine.data('itemConfigurator', (basePrice, variations) => ({
    qty: 1,
    variationId: variations.length ? String(variations[0].id) : '',
    basePrice,
    variations,

    get unitPrice() {
        let price = this.basePrice;
        const v = this.variations.find((x) => String(x.id) === this.variationId);
        if (v) price = parseFloat(v.price);
        document.querySelectorAll('.modifier-input:checked').forEach((el) => {
            price += parseFloat(el.dataset.price || 0);
        });
        return price;
    },

    get total() {
        return (this.unitPrice * this.qty).toFixed(2);
    },

    inc() { if (this.qty < 25) this.qty++; },
    dec() { if (this.qty > 1) this.qty--; },
}));

Alpine.start();

// Scroll-spy for the sticky menu category nav.
const catLinks = document.querySelectorAll('.menu-category-nav .cat-pill[href^="#"]');
if (catLinks.length) {
    const sections = [...catLinks]
        .map((a) => document.querySelector(a.hash))
        .filter(Boolean);

    const activate = (id) => {
        catLinks.forEach((a) => a.classList.toggle('active', a.hash === '#' + id));
        const active = document.querySelector('.menu-category-nav .cat-pill.active');
        if (active) active.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
    };

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) activate(e.target.id);
            });
        },
        { rootMargin: '-140px 0px -60% 0px' }
    );
    sections.forEach((s) => observer.observe(s));
}

// Auto-dismiss toasts / flash messages.
document.querySelectorAll('.toast.auto-show').forEach((el) => {
    new bootstrap.Toast(el, { delay: 4500 }).show();
});
