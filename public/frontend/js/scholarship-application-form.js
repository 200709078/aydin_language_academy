document.querySelectorAll('[data-scholarship-form]').forEach((form) => {
    const branch = form.querySelector('[name="branch_id"]');
    const group = form.querySelector('[name="exam_group_id"]');
    const exam = form.querySelector('[name="exam_title"]');
    const session = form.querySelector('[name="session_id"]');

    const filterOptions = (select, matches, enabled) => {
        for (const option of select.options) {
            if (!option.value) continue;
            option.hidden = !matches(option.dataset);
            option.disabled = option.hidden || (option.dataset.state && option.dataset.state !== 'available');
        }
        if (select.selectedOptions[0]?.disabled) select.value = '';
        select.disabled = !enabled;
    };

    const showDetails = () => {
        const selected = session.selectedOptions[0];
        const data = selected && !selected.disabled && selected.dataset.state === 'available' ? selected.dataset : {};
        form.querySelectorAll('[data-session-detail]').forEach((field) => {
            field.value = data[field.dataset.sessionDetail] ?? '—';
        });
    };

    const refresh = () => {
        filterOptions(group, (data) => data.branchId === branch.value, Boolean(branch.value));
        filterOptions(exam, (data) => data.branchId === branch.value && data.groupId === group.value, Boolean(group.value));
        filterOptions(session, (data) => data.branchId === branch.value && data.groupId === group.value && data.examTitle === exam.value, Boolean(exam.value));
        const visible = Array.from(session.options).filter((option) => option.value && !option.hidden);
        form.querySelector('[data-no-sessions]').hidden = !exam.value || visible.some((option) => !option.disabled);
        form.querySelector('[data-suspended-sessions]').hidden = !visible.some((option) => option.dataset.state === 'suspended');
        showDetails();
    };

    branch.addEventListener('change', () => {
        group.value = exam.value = session.value = '';
        refresh();
    });
    group.addEventListener('change', () => {
        exam.value = session.value = '';
        refresh();
    });
    exam.addEventListener('change', () => {
        session.value = '';
        refresh();
    });
    session.addEventListener('change', showDetails);
    refresh();
});
