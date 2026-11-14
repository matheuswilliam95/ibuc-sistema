import test from 'node:test';
import assert from 'node:assert/strict';
import { resolveJourneyHash, createState, confirmNotice, noticeReceipt, markAttendance, attendanceSummary, closeAttendance, remainingQuantity, prepareDelivery, recordDelivery } from '../../resources/js/journey-state.js';

const fixture = {
    family: { actor: 'Responsável Exemplo', children: [{ id: 'lucas' }, { id: 'lia' }] },
    notice: { id: 'aviso', version: 1 },
    attendance: { actor: 'Professora Exemplo', sessions: [{ id: 'realizada', cancelled: false }, { id: 'cancelada', cancelled: true }], students: [{ id: 'lia' }, { id: 'sofia' }] },
    order: { quantity: 2, actor: 'Secretária Exemplo' },
};
const date = '2026-11-14T15:00:00Z';

test('pular para o conteúdo preserva o perfil e as âncoras de jornada selecionam o contexto', () => {
    assert.equal(resolveJourneyHash('#conteudo', 'professor'), 'professor');
    assert.equal(resolveJourneyHash('#secretaria', 'familia'), 'secretaria');
    assert.equal(resolveJourneyHash('', 'professor'), 'familia');
});

test('ciência é individual por filho e repetição preserva o recibo original', () => {
    const state = createState(fixture);
    const receipt = confirmNotice(state, 'lucas', date);
    assert.equal(noticeReceipt(state, 'lia'), null);
    assert.equal(confirmNotice(state, 'lucas', '2026-11-14T16:00:00Z'), receipt);
    assert.equal(Object.keys(state.acknowledgements).length, 1);
    assert.equal(receipt.date, date);
    assert.throws(() => confirmNotice(state, 'outro-filho', date));
});

test('nova versão do aviso não usa a ciência da versão anterior', () => {
    const state = createState(structuredClone(fixture));
    confirmNotice(state, 'lucas', date);
    state.fixture.notice.version = 2;
    assert.equal(noticeReceipt(state, 'lucas'), null);
    confirmNotice(state, 'lucas', date);
    assert.equal(Object.keys(state.acknowledgements).length, 2);
});

test('chamada incompleta não fecha nem presume faltas', () => {
    const state = createState(fixture);
    markAttendance(state, 'realizada', 'lia', 'present');
    assert.throws(() => closeAttendance(state, 'realizada', date), /todos os alunos/);
    assert.equal(state.closed, null);
    assert.deepEqual(attendanceSummary(state), { present: 1, absent: 0, justified: 0, pending: 1 });
});

test('cancelamento impede lançamento e fechamento, inclusive com marcações de outra ocorrência', () => {
    const state = createState(fixture);
    assert.throws(() => markAttendance(state, 'cancelada', 'lia', 'absent'), /cancelado/);
    markAttendance(state, 'realizada', 'lia', 'present');
    markAttendance(state, 'realizada', 'sofia', 'justified');
    assert.throws(() => closeAttendance(state, 'cancelada', date), /cancelado/);
    assert.equal(state.closed, null);
});

test('chamada fecha com justificadas separadas e conserva o fechamento em repetição', () => {
    const state = createState(fixture);
    markAttendance(state, 'realizada', 'lia', 'present');
    markAttendance(state, 'realizada', 'sofia', 'justified');
    const receipt = closeAttendance(state, 'realizada', date);
    assert.equal(closeAttendance(state, 'realizada', date), receipt);
    assert.throws(() => markAttendance(state, 'realizada', 'sofia', 'present'), /fechada/);
    assert.equal(receipt.marks.sofia, 'justified');
    assert.deepEqual(attendanceSummary(state), { present: 1, absent: 0, justified: 1, pending: 0 });
});

test('marcação não aceita aluno fora da lista nem estado desconhecido', () => {
    const state = createState(fixture);
    assert.throws(() => markAttendance(state, 'realizada', 'outro-aluno', 'present'));
    assert.throws(() => markAttendance(state, 'realizada', 'lia', 'desconhecido'));
    assert.equal(attendanceSummary(state).pending, 2);
});

test('entrega parcial repetida não entrega a segunda unidade', () => {
    const state = createState(fixture);
    const intent = prepareDelivery(state, 1, 'Bruno Exemplo');
    const receipt = recordDelivery(state, intent, date);
    assert.equal(recordDelivery(state, intent, date), receipt);
    assert.equal(remainingQuantity(state), 1);
    assert.equal(state.deliveries.length, 1);
    recordDelivery(state, prepareDelivery(state, 1, 'Bruno Exemplo'), date);
    assert.equal(remainingQuantity(state), 0);
    assert.throws(() => prepareDelivery(state, 1, 'Bruno Exemplo'));
});

test('entrega não aceita quantidade inválida ou recebedor ausente', () => {
    const state = createState(fixture);
    for (const quantity of [0, -1, 1.5, 3, NaN, Infinity]) assert.throws(() => prepareDelivery(state, quantity, 'Bruno Exemplo'));
    assert.throws(() => prepareDelivery(state, 1, '  '));
    assert.equal(remainingQuantity(state), 2);
    assert.equal(state.deliveries.length, 0);
});

test('conferência antiga exige revisão após outra entrega alterar o saldo', () => {
    const state = createState(fixture);
    const first = prepareDelivery(state, 1, 'Bruno Exemplo');
    const stale = prepareDelivery(state, 2, 'Outro Exemplo');
    recordDelivery(state, first, date);
    assert.throws(() => recordDelivery(state, stale, date), /saldo mudou/);
    assert.equal(remainingQuantity(state), 1);
    assert.equal(state.deliveries.length, 1);
});
