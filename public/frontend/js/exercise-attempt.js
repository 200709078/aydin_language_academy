(() => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const setStatus = (element, message, type = 'muted') => {
        if (! element) {
            return;
        }

        element.replaceChildren();

        if (type === 'success' || type === 'error' || type === 'saving') {
            const icon = document.createElement('i');
            icon.className = `fa ${type === 'success'
                ? 'fa-check-circle text-success'
                : (type === 'error' ? 'fa-exclamation-circle text-danger' : 'fa-spinner fa-spin text-primary')}`;
            icon.setAttribute('aria-hidden', 'true');
            element.append(icon, document.createTextNode(` ${message}`));
        } else {
            element.textContent = message;
        }
    };

    document.querySelectorAll('[data-exercise-attempt-form]').forEach((form) => {
        const status = form.querySelector('[data-exercise-save-status]');
        const answeredCount = form.closest('.accordion-body')?.querySelector('[data-exercise-answered-count]');
        const totalQuestions = Number(form.dataset.totalQuestions || 0);
        const messages = {
            saving: form.dataset.savingMessage || '',
            saved: form.dataset.savedMessage || '',
            failed: form.dataset.failedMessage || '',
            retry: form.dataset.retryMessage || '',
        };
        let saveQueue = Promise.resolve();

        const updateAnsweredCount = (answered) => {
            if (! answeredCount || ! Number.isFinite(Number(answered))) {
                return;
            }

            const template = answeredCount.dataset.answeredCountTemplate || ':answered / :total';
            answeredCount.textContent = template
                .replace('__ANSWERED__', answered)
                .replace('__TOTAL__', totalQuestions);
        };

        const saveAnswer = (input) => {
            const question = input.closest('[data-exercise-question]');
            const answerUrl = question?.dataset.answerUrl;

            if (! answerUrl || ! csrfToken) {
                setStatus(status, `${messages.failed} ${messages.retry}`.trim(), 'error');

                return;
            }

            saveQueue = saveQueue
                .catch(() => undefined)
                .then(async () => {
                    setStatus(status, messages.saving, 'saving');

                    const formData = new FormData();
                    formData.append('selected_option', input.value);
                    formData.append('_method', 'PUT');

                    const response = await fetch(answerUrl, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: formData,
                    });

                    if (response.status === 401 || response.status === 419 || response.redirected) {
                        throw new Error(messages.failed);
                    }

                    const isJsonResponse = response.headers
                        .get('content-type')
                        ?.includes('application/json');
                    const payload = isJsonResponse ? await response.json() : {};

                    if (! response.ok) {
                        throw new Error(payload.message || messages.failed);
                    }

                    updateAnsweredCount(payload.answered_count);
                    setStatus(status, payload.message || messages.saved, 'success');
                });

            saveQueue.catch((error) => {
                setStatus(status, `${error.message || messages.failed} ${messages.retry}`.trim(), 'error');
            });
        };

        form.querySelectorAll('input[type="radio"][name^="answers["]').forEach((input) => {
            input.addEventListener('change', () => saveAnswer(input));
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const completeButton = form.querySelector('[data-exercise-complete-button]');

            if (completeButton) {
                completeButton.disabled = true;
            }

            try {
                await saveQueue;
                HTMLFormElement.prototype.submit.call(form);
            } catch (error) {
                setStatus(status, `${error.message || messages.failed} ${messages.retry}`.trim(), 'error');

                if (completeButton) {
                    completeButton.disabled = false;
                }
            }
        });
    });

    const hash = window.location.hash;

    if (! hash.startsWith('#exercise-')) {
        return;
    }

    const exerciseCollapse = document.getElementById(hash.slice(1));

    if (exerciseCollapse && window.bootstrap?.Collapse) {
        const Collapse = window.bootstrap.Collapse;
        const collapse = Collapse.getInstance(exerciseCollapse)
            || new Collapse(exerciseCollapse, { toggle: false });

        collapse.show();
    }
})();
