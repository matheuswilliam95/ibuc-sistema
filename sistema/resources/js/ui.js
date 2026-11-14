export const one = (selector, root = document) => root.querySelector(selector);
export const all = (selector, root = document) => [...root.querySelectorAll(selector)];
let toastTimeout;

export function notify(message) {
    const toast = one('#toast');
    if (!toast) return;
    one('span', toast).textContent = message;
    toast.hidden = false;
    clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => { toast.hidden = true; }, 5500);
}

export function initDialogs() {
    const triggers = new WeakMap();
    function openDialog(dialog, trigger) {
        triggers.set(dialog, trigger ?? document.activeElement);
        dialog.showModal();
        if (dialog.id === 'search-dialog') one('input', dialog)?.focus();
    }
    all('[data-open-dialog]').forEach((button) => button.addEventListener('click', () => {
        openDialog(document.getElementById(button.dataset.openDialog), button);
    }));
    all('dialog').forEach((dialog) => {
        all('[data-close-dialog]', dialog).forEach((button) => button.addEventListener('click', () => dialog.close()));
        dialog.addEventListener('click', (event) => {
            if (event.target !== dialog) return;
            const box = dialog.getBoundingClientRect();
            if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) dialog.close();
        });
        dialog.addEventListener('close', () => {
            const trigger = triggers.get(dialog);
            if (trigger?.isConnected && !trigger.disabled) trigger.focus({ preventScroll: true });
        });
    });
    one('[data-dismiss-toast]')?.addEventListener('click', () => { one('#toast').hidden = true; });
    return { openDialog };
}
