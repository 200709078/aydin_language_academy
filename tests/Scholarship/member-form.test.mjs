import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { test } from 'node:test';
import { runInNewContext } from 'node:vm';

// Only the native select operations used by the form; no browser or demo data.
function select(options, initial = '') {
    const control = {
        options: [{ value: '', dataset: {} }, ...options],
        selectedIndex: 0,
        listeners: {},
        get value() { return this.options[this.selectedIndex].value; },
        set value(value) { this.selectedIndex = Math.max(0, this.options.findIndex((option) => option.value === value)); },
        get selectedOptions() { return [this.options[this.selectedIndex]]; },
        addEventListener(event, listener) { this.listeners[event] = listener; },
        choose(value) {
            this.selectedIndex = this.options.findIndex((option) => option.value === value && !option.disabled);
            assert.ok(this.selectedIndex >= 0, 'A user can only select an enabled option');
            this.listeners.change();
        },
    };
    control.value = initial;
    return control;
}

test('changing branch clears dependent choices and never enables full or suspended sessions', () => {
    const option = (value, dataset = {}) => ({ value, dataset });
    const branch = select([option('1'), option('2')], '1');
    const group = select([option('9', { branchId: '1' }), option('9', { branchId: '2' })], '9');
    const exam = select([
        option('Exam', { branchId: '1', groupId: '9' }),
        option('Exam', { branchId: '2', groupId: '9' }),
    ], 'Exam');
    const sessionOption = (id, branchId, state, remaining = '2') => option(id, {
        branchId, groupId: '9', examTitle: 'Exam', state,
        remaining, capacity: '5', examDate: '25.01.2035', startsAt: '08:00', endsAt: '10:00',
    });
    const session = select([
        sessionOption('10', '1', 'available'),
        sessionOption('11', '1', 'full', '0'),
        sessionOption('12', '1', 'suspended'),
        sessionOption('13', '2', 'available', '4'),
        sessionOption('14', '2', 'suspended'),
    ], '10');
    const remaining = { dataset: { sessionDetail: 'remaining' } };
    const warning = {};
    const elements = {
        '[name="branch_id"]': branch,
        '[name="exam_group_id"]': group,
        '[name="exam_title"]': exam,
        '[name="session_id"]': session,
        '[data-no-sessions]': {},
        '[data-suspended-sessions]': warning,
    };
    const form = { querySelector: (selector) => elements[selector], querySelectorAll: () => [remaining] };
    runInNewContext(readFileSync(new URL('../../public/frontend/js/scholarship-application-form.js', import.meta.url), 'utf8'), {
        document: { querySelectorAll: () => [form] },
    });

    assert.equal(remaining.value, '2');
    branch.choose('2');
    assert.deepEqual([group.value, exam.value, session.value], ['', '', '']);
    assert.equal(remaining.value, '—');
    assert.equal(session.disabled, true);
    group.choose('9');
    exam.choose('Exam');
    assert.deepEqual(session.options.filter((item) => item.value && !item.disabled).map((item) => item.value), ['13']);
    session.choose('13');
    assert.equal(remaining.value, '4');
    assert.equal(warning.hidden, false);
    branch.choose('1');
    group.choose('9');
    exam.choose('Exam');
    assert.deepEqual(session.options.filter((item) => item.value && !item.disabled).map((item) => item.value), ['10']);
    assert.equal(session.value, '');
});
