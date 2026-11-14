// Regras locais do protótipo T04. Sem persistência, autenticação ou efeito operacional.
export function resolveJourneyHash(hash, current = 'familia') {
    const id = hash.replace(/^#/, '');
    if (['familia', 'professor', 'secretaria'].includes(id)) return id;
    return hash ? current : 'familia';
}

export function createState(fixture) {
    return { fixture, acknowledgements: {}, marks: Object.fromEntries(fixture.attendance.students.map((student) => [student.id, null])), closed: null, deliveries: [], orderVersion: 0, sequence: 0 };
}

export function confirmNotice(state, childId, date) {
    if (!state.fixture.family.children.some((child) => child.id === childId)) throw new Error('Selecione um filho desta família.');
    const key = `${state.fixture.notice.id}:${state.fixture.notice.version}:${childId}`;
    state.acknowledgements[key] ??= { childId, version: state.fixture.notice.version, actor: state.fixture.family.actor, date };
    return state.acknowledgements[key];
}

export function noticeReceipt(state, childId) {
    return state.acknowledgements[`${state.fixture.notice.id}:${state.fixture.notice.version}:${childId}`] ?? null;
}

function sessionFor(state, id) {
    const session = state.fixture.attendance.sessions.find((item) => item.id === id);
    if (!session || session.cancelled) throw new Error('Encontro cancelado não permite lançar ou fechar chamada.');
    return session;
}

export function markAttendance(state, sessionId, studentId, status) {
    sessionFor(state, sessionId);
    if (state.closed) throw new Error('Esta chamada já foi fechada.');
    if (!Object.hasOwn(state.marks, studentId) || !['present', 'absent', 'justified'].includes(status)) throw new Error('Confira o aluno e a marcação.');
    state.marks[studentId] = status;
}

export function attendanceSummary(state) {
    const summary = { present: 0, absent: 0, justified: 0, pending: 0 };
    Object.values(state.marks).forEach((status) => { summary[status ?? 'pending']++; });
    return summary;
}

export function closeAttendance(state, sessionId, date) {
    sessionFor(state, sessionId);
    if (state.closed) return state.closed;
    if (attendanceSummary(state).pending) throw new Error('Marque a presença de todos os alunos antes de fechar a chamada.');
    state.closed = { sessionId, date, actor: state.fixture.attendance.actor, marks: { ...state.marks } };
    return state.closed;
}

export function remainingQuantity(state) {
    return state.fixture.order.quantity - state.deliveries.reduce((sum, item) => sum + item.quantity, 0);
}

function validateDelivery(state, quantity, receiver) {
    if (!Number.isInteger(quantity) || quantity < 1 || quantity > remainingQuantity(state)) throw new Error('Informe uma quantidade inteira entre 1 e o saldo disponível.');
    if (typeof receiver !== 'string' || receiver.trim().length < 3 || receiver.trim().length > 100) throw new Error('Informe um nome fictício de recebedor entre 3 e 100 caracteres.');
}

export function prepareDelivery(state, quantity, receiver) {
    validateDelivery(state, quantity, receiver);
    return { id: ++state.sequence, version: state.orderVersion, quantity, receiver: receiver.trim() };
}

export function recordDelivery(state, intent, date) {
    const previous = state.deliveries.find((item) => item.id === intent.id);
    if (previous) return previous;
    if (intent.version !== state.orderVersion) throw new Error('O saldo mudou. Confira a quantidade antes de registrar outra entrega.');
    validateDelivery(state, intent.quantity, intent.receiver);
    const receipt = { ...intent, date, actor: state.fixture.order.actor };
    state.deliveries.push(receipt);
    state.orderVersion++;
    return receipt;
}
