import './markdown-editor';

// Mobile sidebar: open and close the menu drawer.
const sidebar = document.querySelector('[data-sidebar]');
const backdrop = document.querySelector('[data-sidebar-backdrop]');

function setSidebar(open) {
    if (!sidebar) {
        return;
    }
    sidebar.dataset.open = open ? 'true' : 'false';
    backdrop?.classList.toggle('hidden', !open);
    document.body.classList.toggle('overflow-hidden', open);
    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.setAttribute('aria-expanded', String(open));
    });
}

document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
    button.addEventListener('click', () => setSidebar(sidebar?.dataset.open !== 'true'));
});
backdrop?.addEventListener('click', () => setSidebar(false));
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        setSidebar(false);
    }
});

// Show or hide the text in password fields.
document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    const input = document.getElementById(button.dataset.passwordToggle);
    button.addEventListener('click', () => {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(show));
        button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
});

// Toasts: show, then remove after a few seconds (errors stay a little longer).
document.querySelectorAll('[data-toast]').forEach((toast, index) => {
    const dismiss = () => {
        toast.removeAttribute('data-shown');
        setTimeout(() => toast.remove(), 300);
    };
    requestAnimationFrame(() => setTimeout(() => toast.setAttribute('data-shown', ''), 60 + index * 120));
    const delay = toast.getAttribute('role') === 'alert' ? 7000 : 4500;
    let timer = setTimeout(dismiss, delay);
    toast.addEventListener('mouseenter', () => clearTimeout(timer));
    toast.addEventListener('mouseleave', () => (timer = setTimeout(dismiss, 2000)));
    toast.querySelector('[data-toast-close]')?.addEventListener('click', dismiss);
});

// Confirm before destructive actions, e.g. deleting a post.
document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

// Post form: add and remove FAQ rows.
const faqList = document.querySelector('[data-faq-list]');
if (faqList) {
    const renumber = () => {
        faqList.querySelectorAll('[data-faq-row]').forEach((row, index) => {
            row.querySelectorAll('[name^="faq["]').forEach((field) => {
                field.name = field.name.replace(/faq\[\d+\]/, `faq[${index}]`);
            });
        });
    };
    document.querySelector('[data-faq-add]')?.addEventListener('click', () => {
        const row = faqList.querySelector('[data-faq-row]').cloneNode(true);
        row.querySelectorAll('input, textarea').forEach((field) => (field.value = ''));
        row.querySelectorAll('.text-error').forEach((error) => error.remove());
        faqList.append(row);
        renumber();
        row.querySelector('input')?.focus();
    });
    faqList.addEventListener('click', (event) => {
        const button = event.target.closest('[data-faq-remove]');
        if (!button) {
            return;
        }
        const rows = faqList.querySelectorAll('[data-faq-row]');
        const row = button.closest('[data-faq-row]');
        if (rows.length === 1) {
            row.querySelectorAll('input, textarea').forEach((field) => (field.value = ''));
        } else {
            row.remove();
            renumber();
        }
    });
}

// Post form: preview the chosen cover image.
document.querySelector('[data-cover-input]')?.addEventListener('change', (event) => {
    const file = event.target.files?.[0];
    const preview = document.querySelector('[data-cover-preview]');
    if (file && preview) {
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
    }
});

// Post form: build the web address from the title while typing, until the
// address is edited by hand. Existing posts keep their address.
const slugSource = document.querySelector('[data-slug-source]');
const slugTarget = document.querySelector('[data-slug-target]');
if (slugSource && slugTarget) {
    const slugify = (text) =>
        text
            .normalize('NFKD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/&/g, ' and ')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .slice(0, 160);
    slugSource.addEventListener('input', () => {
        if (slugTarget.dataset.auto === 'true') {
            slugTarget.value = slugify(slugSource.value);
        }
    });
    slugTarget.addEventListener('input', () => {
        slugTarget.dataset.auto = slugTarget.value === '' ? 'true' : 'false';
    });
    slugTarget.addEventListener('blur', () => {
        slugTarget.value = slugify(slugTarget.value);
    });
}

// Post form: live Google preview and character counters for the SEO fields.
const seo = document.querySelector('[data-seo]');
if (seo) {
    const titleInput = document.querySelector('[data-seo-fallback="title"]');
    const summary = document.querySelector('[data-seo-fallback="description"]');
    const slug = document.querySelector('[data-slug-target]');
    const field = (name) => seo.querySelector(`[data-seo-input="${name}"]`);

    const update = () => {
        ['title', 'description'].forEach((name) => {
            const input = field(name);
            const length = input.value.trim().length;
            const counter = seo.querySelector(`[data-seo-count="${name}"]`);
            counter.textContent = length;
            counter.classList.toggle('text-error', length > Number(input.dataset.seoLimit));
        });
        seo.querySelector('[data-seo-preview-title]').textContent = field('title').value.trim() || titleInput?.value.trim() || 'Your page title';
        seo.querySelector('[data-seo-preview-description]').textContent =
            field('description').value.trim() || summary?.value.trim() || 'Your summary will appear here.';
        seo.querySelector('[data-seo-preview-slug]').textContent = slug?.value || 'your-page';
    };

    [titleInput, summary, slug, field('title'), field('description')].forEach((input) => input?.addEventListener('input', update));
    update();
}

// Project form: add, preview and remove screenshots.
const shotList = document.querySelector('[data-shot-list]');
if (shotList) {
    const template = document.querySelector('[data-shot-template]');
    let nextIndex = shotList.querySelectorAll('[data-shot-row]').length + 100;

    document.querySelector('[data-shot-add]')?.addEventListener('click', () => {
        const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
        shotList.insertAdjacentHTML('beforeend', html);
        shotList.lastElementChild.querySelector('[data-shot-file]')?.click();
    });

    shotList.addEventListener('change', (event) => {
        const input = event.target.closest('[data-shot-file]');
        const file = input?.files?.[0];
        if (!file) {
            return;
        }
        const row = input.closest('[data-shot-row]');
        const preview = row.querySelector('[data-shot-preview]');
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
        row.querySelector('[data-shot-empty]')?.remove();
    });

    shotList.addEventListener('click', (event) => {
        const button = event.target.closest('[data-shot-remove]');
        if (!button) {
            return;
        }
        const row = button.closest('[data-shot-row]');
        if (row.hasAttribute('data-new')) {
            row.remove();
            return;
        }
        // Existing screenshots are only marked for removal, so it can be undone.
        const flag = row.querySelector('[data-shot-remove-input]');
        const removing = flag.value !== '1';
        flag.value = removing ? '1' : '0';
        row.classList.toggle('opacity-40', removing);
        button.textContent = removing ? 'Undo remove' : 'Remove';
    });
}

// Experience form: "I work here now" disables the end date.
const currentToggle = document.querySelector('[data-current-toggle]');
const endInput = document.querySelector('[data-end-input]');
currentToggle?.addEventListener('change', () => {
    endInput.disabled = currentToggle.checked;
    if (!currentToggle.checked) {
        endInput.focus();
    }
});

// Generic image previews: <input type="file" data-preview-target="selector">.
document.querySelectorAll('[data-preview-target]').forEach((input) => {
    input.addEventListener('change', () => {
        const file = input.files?.[0];
        const preview = document.querySelector(input.dataset.previewTarget);
        if (file && preview) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        }
    });
});

// "Add more" lists: add, remove and renumber single-line rows.
document.querySelectorAll('[data-list]').forEach((list) => {
    const rows = list.querySelector('[data-list-rows]');
    const addButton = list.querySelector('[data-list-add]');
    const max = Number(list.dataset.listMax || 10);

    const refresh = () => {
        const all = rows.querySelectorAll('[data-list-row]');
        all.forEach((row, index) => {
            row.querySelector('[data-list-number]').textContent = index + 1;
        });
        addButton.hidden = all.length >= max;
    };

    addButton.addEventListener('click', () => {
        const row = rows.querySelector('[data-list-row]').cloneNode(true);
        row.querySelector('input').value = '';
        rows.append(row);
        refresh();
        row.querySelector('input').focus();
    });

    rows.addEventListener('click', (event) => {
        const button = event.target.closest('[data-list-remove]');
        if (!button) {
            return;
        }
        const row = button.closest('[data-list-row]');
        if (rows.querySelectorAll('[data-list-row]').length === 1) {
            row.querySelector('input').value = '';
        } else {
            row.remove();
        }
        refresh();
    });

    // Enter in a row adds the next one instead of submitting the form.
    rows.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && event.target.matches('input')) {
            event.preventDefault();
            addButton.click();
        }
    });

    refresh();
});
