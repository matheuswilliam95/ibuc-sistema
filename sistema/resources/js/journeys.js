import { one, all, notify, initDialogs } from './ui';
import { resolveJourneyHash, createState, confirmNotice, noticeReceipt, markAttendance, attendanceSummary, closeAttendance, remainingQuantity, prepareDelivery, recordDelivery } from './journey-state';

const fixture = JSON.parse(one('#journey-data').textContent);
let state = createState(fixture);
let noticeChild;
let attendanceSession;
let deliveryIntent;
let activeJourney = 'familia';
const { openDialog } = initDialogs();
const now = () => new Date().toISOString();
const time = (date) => new Intl.DateTimeFormat('pt-BR', { hour: '2-digit', minute: '2-digit', timeZone: 'America/Sao_Paulo' }).format(new Date(date));
const text = (selector, value) => { one(selector).textContent = value; };
const announce = (value) => text('#journey-announcement', value);
const focusResult = (selector) => { one(selector).setAttribute('tabindex', '-1'); one(selector).focus({ preventScroll: true }); };
const feedback = (selector, message = '') => { text(selector, message); one(selector).hidden = !message; };
const badge = (selector, label, tone) => { const node = one(selector); node.textContent = label; node.className = `badge badge--${tone}`; };

const journeys = {
    familia: ['Minha família', 'Olá, Bruno. Um lugar para acompanhar quem você ama.', fixture.family.actor, 'Responsável · demonstração'],
    professor: ['Minha turma', 'Olá, Ana. Vamos acompanhar os alunos da turma Sementes?', fixture.attendance.actor, 'Professora · Núcleo Jardim'],
    secretaria: ['Secretaria', 'Olá, Eva. Cada pedido acompanhado até a entrega.', fixture.order.actor, 'Secretaria · Núcleo Jardim'],
};

function showJourney(id, focus = false) {
    if (!Object.hasOwn(journeys, id)) id = 'familia';
    activeJourney = id;
    all('[data-journey-panel]').forEach((panel) => { panel.hidden = panel.id !== id; });
    all('[data-journey]').forEach((link) => {
        const active = link.dataset.journey === id;
        link.classList.toggle('is-active', active);
        if (active) link.setAttribute('aria-current', 'page');
        else link.removeAttribute('aria-current');
    });
    const [title, description, person, role] = journeys[id];
    text('#journey-heading', title);
    text('#journey-description', description);
    text('[data-preview-person]', person);
    text('[data-preview-role]', role);
    if (focus) one('#journey-heading').focus();
}
all('[data-journey]').forEach((link) => link.addEventListener('click', (event) => {
    event.preventDefault();
    link.closest('dialog')?.close();
    history.pushState(null, '', `#${link.dataset.journey}`);
    showJourney(link.dataset.journey, true);
}));
const syncLocation = () => showJourney(resolveJourneyHash(location.hash, activeJourney));
window.addEventListener('popstate', syncLocation);
window.addEventListener('hashchange', syncLocation);

function renderFamily() {
    const child = fixture.family.children.find((item) => item.id === one('#family-child').value);
    const receipt = noticeReceipt(state, child.id);
    text('#child-name', child.name);
    text('#child-classroom', child.classroom);
    text('#child-avatar', child.initial);
    badge('#child-level', `Nível ${child.level}`, `level-${child.level}`);
    text('#notice-child', child.shortName);
    badge('#family-notice-status', receipt ? 'Ciência confirmada' : 'Ciência pendente', receipt ? 'success' : 'warning');
    text('#notice-action-label', receipt ? `Confirmado para ${child.shortName}` : `Estou ciente por ${child.shortName}`);
    one('#review-notice').disabled = Boolean(receipt);
    text('#family-receipt', receipt ? `${receipt.actor} confirmou por ${child.shortName} às ${time(receipt.date)}, nesta demonstração.` : 'Confirme para sabermos que esta informação chegou até você.');
    let count = 0;
    fixture.family.children.forEach((item) => {
        const done = Boolean(noticeReceipt(state, item.id));
        if (done) count++;
        badge(`[data-child-receipt="${item.id}"]`, done ? 'Confirmado' : 'Pendente', done ? 'success' : 'warning');
    });
    text('#family-progress', `${count} de ${fixture.family.children.length}`);
}
one('#family-child').addEventListener('change', renderFamily);
one('#review-notice').addEventListener('click', (event) => {
    noticeChild = one('#family-child').value;
    const child = fixture.family.children.find((item) => item.id === noticeChild);
    text('#notice-confirm-summary', `Você, ${fixture.family.actor}, confirma ciência do cancelamento de 17 de novembro por ${child.name}, referente à versão ${fixture.notice.version} deste aviso.`);
    one('#confirm-notice').disabled = false;
    openDialog(one('#notice-confirm-dialog'), event.currentTarget);
});
one('#confirm-notice').addEventListener('click', (event) => {
    event.currentTarget.disabled = true;
    confirmNotice(state, noticeChild, now());
    one('#notice-confirm-dialog').close();
    renderFamily();
    focusResult('#family-receipt');
    notify('Ciência registrada para o filho indicado.');
});

function renderAttendance() {
    const cancelled = fixture.attendance.sessions.find((session) => session.id === one('#attendance-session').value).cancelled;
    one('#cancelled-attendance').hidden = !cancelled;
    one('#attendance-list').hidden = cancelled;
    text('#attendance-list-instruction', state.closed ? 'Marcações registradas nesta chamada.' : 'Marque cada aluno antes de fechar.');
    const summary = cancelled ? { present: 0, absent: 0, justified: 0, pending: 0 } : attendanceSummary(state);
    Object.entries(summary).forEach(([status, count]) => text(`#count-${status}`, count));
    all('[data-student]').forEach((row) => {
        row.disabled = cancelled || Boolean(state.closed);
        row.classList.toggle('attendance-row--pending', !cancelled && !state.marks[row.dataset.student]);
        all('input', row).forEach((input) => { input.checked = state.marks[row.dataset.student] === input.value; });
    });
    one('#mark-all-present').disabled = cancelled || Boolean(state.closed);
    one('#review-attendance').disabled = cancelled || Boolean(state.closed);
    text('#attendance-action-label', cancelled ? 'Sem chamada' : state.closed ? 'Chamada fechada' : 'Conferir chamada');
    badge('#attendance-status', cancelled ? 'Cancelado' : state.closed ? 'Fechada' : 'Em preenchimento', cancelled ? 'info' : state.closed ? 'success' : 'warning');
    text('#attendance-hint', cancelled ? 'Cancelamento não gera faltas.' : state.closed ? `Fechada por ${state.closed.actor} às ${time(state.closed.date)}, nesta demonstração.` : `${fixture.attendance.students.length - summary.pending} de ${fixture.attendance.students.length} alunos marcados.`);
}
one('#attendance-session').addEventListener('change', () => { feedback('#attendance-feedback'); renderAttendance(); });
all('[data-student] input').forEach((input) => input.addEventListener('change', () => {
    markAttendance(state, one('#attendance-session').value, input.closest('[data-student]').dataset.student, input.value);
    feedback('#attendance-feedback');
    renderAttendance();
}));
one('#mark-all-present').addEventListener('click', () => {
    fixture.attendance.students.forEach((student) => markAttendance(state, one('#attendance-session').value, student.id, 'present'));
    feedback('#attendance-feedback'); renderAttendance(); announce('Todos os alunos foram marcados como presentes. Você pode ajustar cada marcação.');
});
one('#review-attendance').addEventListener('click', (event) => {
    const summary = attendanceSummary(state);
    if (summary.pending) {
        feedback('#attendance-feedback', `Falta marcar ${summary.pending} ${summary.pending === 1 ? 'aluno' : 'alunos'}. A chamada ainda não foi fechada.`);
        one('[data-student].attendance-row--pending input').focus();
        return;
    }
    attendanceSession = one('#attendance-session').value;
    text('#attendance-confirm-summary', `Presentes: ${summary.present} · Faltas: ${summary.absent} · Justificadas: ${summary.justified} · Nenhum aluno pendente.`);
    one('#confirm-attendance').disabled = false;
    openDialog(one('#attendance-confirm-dialog'), event.currentTarget);
});
one('#confirm-attendance').addEventListener('click', (event) => {
    event.currentTarget.disabled = true;
    closeAttendance(state, attendanceSession, now());
    one('#attendance-confirm-dialog').close(); renderAttendance();
    focusResult('#attendance-hint'); notify('Chamada fechada. As marcações foram preservadas nesta prévia.');
});

function renderOrder() {
    const remaining = remainingQuantity(state);
    const delivered = fixture.order.quantity - remaining;
    text('#order-delivered', delivered); text('#order-remaining', remaining);
    badge('#order-status', remaining === 0 ? 'Entregue' : delivered ? 'Entrega parcial' : 'Pronto para retirada', remaining === 0 ? 'success' : delivered ? 'warning' : 'info');
    one('#delivery-form').hidden = remaining === 0;
    one('#delivery-complete').hidden = remaining !== 0;
    one('#delivery-quantity').max = remaining;
    if (remaining) one('#delivery-quantity').value = Math.min(Number(one('#delivery-quantity').value) || 1, remaining);
    one('#delivery-empty').hidden = Boolean(delivered);
    one('#delivery-history').replaceChildren(...state.deliveries.map((receipt) => {
        const item = document.createElement('li');
        const title = document.createElement('strong');
        title.textContent = `${receipt.quantity} ${receipt.quantity === 1 ? 'unidade entregue' : 'unidades entregues'}`;
        const detail = document.createElement('p'); detail.textContent = `Recebeu: ${receipt.receiver}`;
        const meta = document.createElement('small'); meta.textContent = `${receipt.actor} · ${time(receipt.date)} · nesta demonstração`;
        item.append(title, detail, meta); return item;
    }));
}
one('#delivery-form').addEventListener('submit', (event) => {
    event.preventDefault();
    const quantity = one('#delivery-quantity'); const receiver = one('#delivery-receiver');
    [quantity, receiver].forEach((field) => { field.removeAttribute('aria-invalid'); field.setAttribute('aria-describedby', `${field.id}-message`); });
    feedback('#delivery-feedback'); feedback('#delivery-confirm-error');
    try {
        deliveryIntent = prepareDelivery(state, Number(quantity.value), receiver.value);
    } catch (error) {
        feedback('#delivery-feedback', error.message);
        const invalid = !Number.isInteger(Number(quantity.value)) || Number(quantity.value) < 1 || Number(quantity.value) > remainingQuantity(state) ? quantity : receiver;
        invalid.setAttribute('aria-invalid', 'true'); invalid.setAttribute('aria-describedby', `${invalid.id}-message delivery-feedback`); invalid.focus(); return;
    }
    text('#delivery-confirm-summary', `Registrar ${deliveryIntent.quantity} ${deliveryIntent.quantity === 1 ? 'unidade' : 'unidades'} de ${fixture.order.product} para ${fixture.order.beneficiary}. Recebedor: ${deliveryIntent.receiver}.`);
    const balance = remainingQuantity(state) - deliveryIntent.quantity;
    text('#delivery-confirm-balance', balance ? `Após esta entrega, ${balance} unidade permanece pendente.` : 'Esta entrega conclui o pedido.');
    one('#confirm-delivery').disabled = false;
    openDialog(one('#delivery-confirm-dialog'), one('button[type="submit"]', event.currentTarget));
});
one('#confirm-delivery').addEventListener('click', (event) => {
    if (event.currentTarget.disabled) return;
    event.currentTarget.disabled = true;
    try {
        recordDelivery(state, deliveryIntent, now());
    } catch (error) {
        feedback('#delivery-confirm-error', error.message); return;
    }
    one('#delivery-confirm-dialog').close(); renderOrder();
    focusResult('#order-status'); notify('Entrega registrada. O saldo do pedido foi atualizado.');
});

one('#reset-journeys').addEventListener('click', () => {
    state = createState(fixture); noticeChild = null; attendanceSession = null; deliveryIntent = null;
    one('#family-child').value = fixture.family.children[0].id;
    one('#attendance-session').value = fixture.attendance.sessions[0].id;
    one('#delivery-form').reset();
    feedback('#attendance-feedback'); feedback('#delivery-feedback'); feedback('#delivery-confirm-error');
    all('#delivery-form input').forEach((field) => { field.removeAttribute('aria-invalid'); field.setAttribute('aria-describedby', `${field.id}-message`); });
    one('#reset-dialog').close(); renderFamily(); renderAttendance(); renderOrder();
    announce('Os três exemplos voltaram ao início.');
    notify('Os três exemplos voltaram ao início.');
});

all('[data-prototype-controls]').forEach((fieldset) => { fieldset.disabled = false; });
syncLocation(); renderFamily(); renderAttendance(); renderOrder();
