// Interações da prévia. Nenhuma informação de família é enviada ou persistida.
import { one, all, notify, initDialogs } from './ui';
initDialogs();
all('a[href^="#"]').forEach((link) => {
    link.addEventListener('click', () => {
        link.closest('dialog')?.close();
        if (link.classList.contains('nav-link')) {
            all('.nav-link').forEach((item) => {
                const current = item.hash === link.hash;
                item.classList.toggle('is-active', current);
                if (current) item.setAttribute('aria-current', 'location');
                else item.removeAttribute('aria-current');
            });
        }
    });
});
all('[data-demo-action]').forEach((button) => {
    button.addEventListener('click', () => {
        if (button.dataset.demoScroll) document.getElementById(button.dataset.demoScroll).scrollIntoView({ behavior: 'smooth' });
        notify(button.dataset.demoAction);
    });
});
all('[data-copy]').forEach((button) => {
    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(button.dataset.copy);
            notify('Cor ' + button.dataset.copy + ' copiada.');
        } catch {
            notify('Selecione o código ' + button.dataset.copy + ' para copiar.');
        }
    });
});
all('[data-button-size]').forEach((button) => {
    button.addEventListener('click', () => {
        all('[data-button-size]').forEach((option) => {
            const selected = option === button;
            option.classList.toggle('is-active', selected);
            option.setAttribute('aria-pressed', String(selected));
        });
        all('.button', one('#button-specimens')).forEach((sample) => {
            sample.classList.toggle('button--sm', button.dataset.buttonSize === 'sm');
            sample.classList.toggle('button--md', button.dataset.buttonSize === 'md');
        });
    });
});
one('#component-search').addEventListener('input', (event) => {
    const normalize = (value) => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    const text = normalize(event.target.value);
    const results = all('[data-search-item]');
    results.forEach((item) => { item.hidden = !normalize(item.dataset.searchItem).includes(text); });
    one('.search-empty').hidden = results.some((item) => !item.hidden);
});
one('[data-confirm-cancel]').addEventListener('click', (event) => {
    event.currentTarget.closest('dialog').close();
    notify('Cancelamento simulado. Nenhum encontro real foi alterado.');
});
one('#acknowledgement-button').addEventListener('click', (event) => {
    const button = event.currentTarget;
    if (button.disabled) return;
    button.disabled = true;
    one('#notice-status').textContent = 'Ciência confirmada';
    one('#notice-status').classList.replace('badge--warning', 'badge--success');
    one('#acknowledgement-hint').textContent = 'Você confirmou este aviso de exemplo.';
    button.lastChild.textContent = 'Confirmado';
    notify('Pronto! Assim será a confirmação de ciência.');
});
one('#preview-form').addEventListener('submit', (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const name = form.elements.namedItem('name');
    const email = form.elements.namedItem('email');
    const status = one('#form-feedback');
    let message = '';
    let invalidField = null;
    [name, email].forEach((field) => field.removeAttribute('aria-invalid'));
    name.setAttribute('aria-describedby', 'name-message');
    email.removeAttribute('aria-describedby');
    if (name.value.trim().length < 2) {
        message = 'Para experimentar, preencha um nome fictício com pelo menos 2 letras.';
        invalidField = name;
    } else if (email.value && !email.validity.valid) {
        message = 'Confira o formato do e-mail. Por exemplo: familia@ibuc.local.';
        invalidField = email;
    }
    status.hidden = false;
    status.classList.toggle('is-error', Boolean(invalidField));
    if (invalidField) {
        status.textContent = message;
        invalidField.setAttribute('aria-invalid', 'true');
        invalidField.setAttribute('aria-describedby', invalidField === name ? 'name-message form-feedback' : 'form-feedback');
        invalidField.focus();
        return;
    }
    status.textContent = 'Tudo certo! Você experimentou o formulário. Os dados ficam apenas nesta tela.';
    const button = one('button[type="submit"]', form);
    button.disabled = true;
    const previousLabel = button.lastChild.textContent;
    button.lastChild.textContent = 'Conferindo…';
    button.setAttribute('aria-busy', 'true');
    setTimeout(() => {
        button.disabled = false;
        button.removeAttribute('aria-busy');
        button.lastChild.textContent = previousLabel;
        notify('Formulário de exemplo conferido. Nenhum dado foi enviado.');
    }, 650);
});
const tabs = all('.section-tab');
if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        const entry = entries.filter((item) => item.isIntersecting).sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
        if (!entry) return;
        tabs.forEach((tab) => {
            const active = tab.hash === '#' + entry.target.id;
            tab.classList.toggle('is-active', active);
            if (active) tab.setAttribute('aria-current', 'location');
            else tab.removeAttribute('aria-current');
        });
    }, { rootMargin: '-85px 0px -45% 0px', threshold: [0, 0.1, 0.4] });
    ['fundamentos', 'componentes', 'em-uso'].forEach((id) => observer.observe(document.getElementById(id)));
}
