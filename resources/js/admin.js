import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// Mobile sidebar toggle.
const sidebar = document.querySelector('.admin-sidebar');
document.querySelectorAll('[data-sidebar-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => sidebar?.classList.toggle('show'));
});

// Slug auto-fill: <input data-slug-source> feeds <input data-slug-target>.
const slugify = (s) =>
    s.toLowerCase().trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');

document.querySelectorAll('[data-slug-source]').forEach((src) => {
    const target = document.querySelector(src.dataset.slugSource);
    if (!target) return;
    src.addEventListener('input', () => {
        if (!target.dataset.touched) target.value = slugify(src.value);
    });
    target.addEventListener('input', () => (target.dataset.touched = '1'));
});

// Image inputs: live preview.
document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
    const img = document.querySelector(input.dataset.preview);
    if (!img) return;
    input.addEventListener('change', () => {
        const file = input.files?.[0];
        if (file) img.src = URL.createObjectURL(file);
    });
});

// Simple row reordering with up/down buttons: posts new order.
document.querySelectorAll('[data-reorder-table]').forEach((table) => {
    const save = async () => {
        const ids = [...table.querySelectorAll('tr[data-id]')].map((tr) => tr.dataset.id);
        await fetch(table.dataset.reorderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ ids }),
        });
    };

    table.addEventListener('click', (e) => {
        const up = e.target.closest('[data-move-up]');
        const down = e.target.closest('[data-move-down]');
        if (!up && !down) return;
        e.preventDefault();
        const row = e.target.closest('tr[data-id]');
        if (up && row.previousElementSibling) row.parentNode.insertBefore(row, row.previousElementSibling);
        if (down && row.nextElementSibling) row.parentNode.insertBefore(row.nextElementSibling, row);
        save();
    });
});

// Confirm-before-delete forms.
document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (e) => {
        if (!window.confirm(form.dataset.confirm)) e.preventDefault();
    });
});

// Auto-dismiss flash toasts.
document.querySelectorAll('.toast.auto-show').forEach((el) => {
    new bootstrap.Toast(el, { delay: 4000 }).show();
});
