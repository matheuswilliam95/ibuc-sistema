import { all, one, initDialogs } from './ui';

initDialogs();
all('a[href^="#"]').forEach((link) => link.addEventListener('click', () => {
    link.closest('dialog')?.close();
}));

const form = one('[data-structure-form]');
const edition = one('[data-edition-selector]', form);
const offering = one('[data-offering-selector]', form);
function filterEditionOptions() {
    const selected = edition?.value ?? offering?.selectedOptions[0]?.dataset.edition ?? '';
    all('[data-edition-options]', form).forEach((select) => {
        [...select.options].forEach((option) => {
            if (!option.value) return;
            const compatible = option.dataset.edition === selected;
            option.hidden = !compatible;
            option.disabled = !compatible;
            if (!compatible && option.selected) select.value = '';
        });
    });
}
edition?.addEventListener('change', filterEditionOptions);
offering?.addEventListener('change', filterEditionOptions);
filterEditionOptions();
one('[data-error-summary]')?.focus();
