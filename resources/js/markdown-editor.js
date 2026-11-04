// Markdown editor for blog articles: toolbar, keyboard shortcuts, live
// preview (rendered by the server exactly as the website shows it),
// full screen, and image upload by button, drag and drop or paste.

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

function initEditor(editor) {
    const input = editor.querySelector('[data-md-input]');
    const preview = editor.querySelector('[data-md-preview]');
    const status = editor.querySelector('[data-md-status]');
    const count = editor.querySelector('[data-md-count]');
    const fileInput = editor.querySelector('[data-md-file]');
    const defaultStatus = status.textContent;

    // Replaces the selection while keeping the browser's undo history.
    function replaceSelection(text, selectStart = text.length, selectEnd = selectStart) {
        const start = input.selectionStart;
        input.focus();
        if (!document.execCommand('insertText', false, text)) {
            input.setRangeText(text, start, input.selectionEnd, 'end');
        }
        input.setSelectionRange(start + selectStart, start + selectEnd);
        input.dispatchEvent(new Event('input', { bubbles: true }));
    }

    const selected = () => input.value.slice(input.selectionStart, input.selectionEnd);

    // Wraps the selection, e.g. **bold**. Clicking again on wrapped text removes it.
    function wrap(before, after = before, placeholder = 'text') {
        const { selectionStart: start, selectionEnd: end, value } = input;
        if (value.slice(start - before.length, start) === before && value.slice(end, end + after.length) === after) {
            input.setSelectionRange(start - before.length, end + after.length);
            replaceSelection(value.slice(start, end), 0, end - start);
            return;
        }
        const text = selected() || placeholder;
        replaceSelection(before + text + after, before.length, before.length + text.length);
    }

    // Selects whole lines around the cursor so prefixes apply to each line.
    function selectLines() {
        const { value } = input;
        const start = value.lastIndexOf('\n', input.selectionStart - 1) + 1;
        let end = value.indexOf('\n', input.selectionEnd);
        if (end === -1) {
            end = value.length;
        }
        input.setSelectionRange(start, end);
        return value.slice(start, end).split('\n');
    }

    function prefixLines(prefixFor, pattern) {
        const lines = selectLines();
        const allPrefixed = lines.every((line) => pattern.test(line));
        const text = lines
            .map((line, index) => (allPrefixed ? line.replace(pattern, '') : prefixFor(index) + line.replace(pattern, '')))
            .join('\n');
        // One line: put the cursor at the end so typing continues the item.
        // Several lines: keep them selected so the button can be pressed again.
        if (lines.length === 1) {
            replaceSelection(text);
        } else {
            replaceSelection(text, 0, text.length);
        }
    }

    function heading(level) {
        const lines = selectLines();
        const hashes = '#'.repeat(level) + ' ';
        const isSame = lines[0].startsWith(hashes);
        const text = lines.map((line) => (isSame ? '' : hashes) + line.replace(/^#{1,6}\s+/, '')).join('\n');
        replaceSelection(text, text.length);
    }

    // Inserts a block on its own lines, with blank lines around it.
    function insertBlock(block, selectFrom = block.length, selectTo = selectFrom) {
        const { value, selectionStart: start } = input;
        const before = start === 0 || value.slice(0, start).endsWith('\n\n') ? '' : value.slice(0, start).endsWith('\n') ? '\n' : '\n\n';
        const after = value.slice(input.selectionEnd).startsWith('\n') ? '\n' : '\n\n';
        replaceSelection(before + block + after, before.length + selectFrom, before.length + selectTo);
    }

    const actions = {
        h1: () => heading(1),
        h2: () => heading(2),
        h3: () => heading(3),
        h4: () => heading(4),
        bold: () => wrap('**'),
        italic: () => wrap('_'),
        strike: () => wrap('~~'),
        code: () => wrap('`', '`', 'code'),
        link: () => {
            const text = selected() || 'link text';
            const url = window.prompt('Link address (a full URL, or a page like /services/website-care-plans):', 'https://');
            if (url) {
                replaceSelection(`[${text}](${url})`, 1, 1 + text.length);
            }
        },
        image: () => fileInput.click(),
        codeblock: () => {
            const code = selected() || 'your code here';
            insertBlock('```js\n' + code + '\n```', 6, 6 + code.length);
        },
        quote: () => prefixLines(() => '> ', /^>\s?/),
        tip: () => insertBlock('> **Tip title**\n> Tip text', 4, 13),
        ul: () => prefixLines(() => '- ', /^[-*]\s+/),
        ol: () => prefixLines((index) => `${index + 1}. `, /^\d+[.)]\s+/),
        table: () => insertBlock('| Column | Column |\n| --- | --- |\n| Text | Text |', 2, 8),
        hr: () => insertBlock('---'),
    };

    editor.querySelectorAll('[data-md-action]').forEach((button) => {
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => actions[button.dataset.mdAction]());
    });

    input.addEventListener('keydown', (event) => {
        if (!(event.ctrlKey || event.metaKey)) {
            return;
        }
        const key = event.key.toLowerCase();
        const shortcut = event.shiftKey ? { x: 'strike' }[key] : { b: 'bold', i: 'italic', k: 'link', e: 'code' }[key];
        if (shortcut) {
            event.preventDefault();
            actions[shortcut]();
        }
    });

    // Continue lists when pressing Enter at the end of a list item.
    input.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' || event.shiftKey || input.selectionStart !== input.selectionEnd) {
            return;
        }
        const lineStart = input.value.lastIndexOf('\n', input.selectionStart - 1) + 1;
        const line = input.value.slice(lineStart, input.selectionStart);
        const match = line.match(/^(\s*)([-*]|\d+[.)])\s+(.*)$/);
        if (!match) {
            return;
        }
        event.preventDefault();
        if (match[3] === '') {
            input.setSelectionRange(lineStart, input.selectionStart);
            replaceSelection('');
            return;
        }
        const marker = /\d/.test(match[2]) ? `${parseInt(match[2], 10) + 1}${match[2].slice(-1)}` : match[2];
        replaceSelection(`\n${match[1]}${marker} `);
    });

    // Image upload by toolbar button, drag and drop, or paste.
    async function uploadImage(file) {
        if (!file || !file.type.startsWith('image/')) {
            return;
        }
        status.textContent = `Uploading ${file.name}…`;
        const placeholder = `![Uploading ${file.name}…]()`;
        replaceSelection(placeholder);
        const body = new FormData();
        body.append('image', file);
        try {
            const response = await fetch(editor.dataset.uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
                body,
            });
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.errors?.image?.[0] ?? data.message ?? 'Upload failed.');
            }
            const alt = file.name.replace(/\.[^.]+$/, '').replace(/[-_]+/g, ' ');
            input.value = input.value.replace(placeholder, `![${alt}](${data.url})`);
            status.textContent = 'Image uploaded. Edit the text in [ ] to describe it.';
        } catch (error) {
            input.value = input.value.replace(placeholder, '');
            status.textContent = error.message;
        }
        input.dispatchEvent(new Event('input', { bubbles: true }));
        setTimeout(() => (status.textContent = defaultStatus), 5000);
    }

    fileInput.addEventListener('change', () => {
        uploadImage(fileInput.files[0]);
        fileInput.value = '';
    });
    input.addEventListener('dragover', (event) => event.preventDefault());
    input.addEventListener('drop', (event) => {
        const file = event.dataTransfer?.files?.[0];
        if (file?.type.startsWith('image/')) {
            event.preventDefault();
            uploadImage(file);
        }
    });
    input.addEventListener('paste', (event) => {
        const file = [...(event.clipboardData?.files ?? [])].find((item) => item.type.startsWith('image/'));
        if (file) {
            event.preventDefault();
            uploadImage(file);
        }
    });

    // Live preview, rendered by the server so it matches the website.
    let previewTimer;
    let previewRequest = 0;
    async function renderPreview() {
        if (editor.dataset.mode === 'write') {
            return;
        }
        const request = ++previewRequest;
        const response = await fetch(editor.dataset.previewUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ body: input.value }),
        }).catch(() => null);
        if (request !== previewRequest || !response?.ok) {
            return;
        }
        const { html } = await response.json();
        preview.innerHTML = html || '<p class="text-muted">Nothing to preview yet.</p>';
    }

    function updateCount() {
        const words = input.value.trim().split(/\s+/).filter(Boolean).length;
        count.textContent = `${words} words · ${Math.max(1, Math.round(words / 200))} min read`;
    }

    input.addEventListener('input', () => {
        updateCount();
        clearTimeout(previewTimer);
        previewTimer = setTimeout(renderPreview, 350);
    });
    updateCount();

    editor.querySelectorAll('[data-md-mode]').forEach((button) => {
        button.addEventListener('click', () => {
            editor.dataset.mode = button.dataset.mdMode;
            renderPreview();
            if (editor.dataset.mode !== 'preview') {
                input.focus();
            }
        });
    });

    editor.querySelector('[data-md-fullscreen]')?.addEventListener('click', () => {
        const on = !editor.hasAttribute('data-fullscreen');
        editor.toggleAttribute('data-fullscreen', on);
        document.body.classList.toggle('overflow-hidden', on);
        if (on && window.matchMedia('(min-width: 64rem)').matches && editor.dataset.mode === 'write') {
            editor.dataset.mode = 'split';
            renderPreview();
        }
        input.focus();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && editor.hasAttribute('data-fullscreen')) {
            editor.removeAttribute('data-fullscreen');
            document.body.classList.remove('overflow-hidden');
        }
    });
}

document.querySelectorAll('[data-md-editor]').forEach(initEditor);
