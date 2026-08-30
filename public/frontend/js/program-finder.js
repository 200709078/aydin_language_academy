(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.querySelector('[data-program-finder]');

        if (!form) {
            return;
        }

        var allSteps = Array.prototype.slice.call(form.querySelectorAll('[data-program-finder-step]'));
        var detailsStep = form.querySelector('[data-step-key="details"]');
        var previousButton = form.querySelector('[data-program-finder-back]');
        var nextButton = form.querySelector('[data-program-finder-next]');
        var submitButton = form.querySelector('[data-program-finder-submit]');
        var progressBar = form.querySelector('[data-program-finder-progress-bar]');
        var progress = form.querySelector('.program-finder-progress');
        var stepLabel = form.querySelector('[data-program-finder-step-label]');
        var clientError = form.querySelector('[data-program-finder-client-error]');
        var currentIndex = 0;

        function selectedValue(name) {
            var selected = form.querySelector('input[name="' + name + '"]:checked');

            return selected ? selected.value : '';
        }

        function setInputsDisabled(container, disabled) {
            Array.prototype.slice.call(container.querySelectorAll('input, select, textarea')).forEach(function (input) {
                input.disabled = disabled;
            });
        }

        function updateChoiceStyles() {
            Array.prototype.slice.call(form.querySelectorAll('.program-finder-choice')).forEach(function (choice) {
                choice.classList.toggle('is-selected', Boolean(choice.querySelector('input:checked')));
            });
        }

        function syncConditionalFields() {
            var learnerType = selectedValue('learner_type');
            var goal = selectedValue('goal');

            Array.prototype.slice.call(form.querySelectorAll('[data-program-finder-goal]')).forEach(function (option) {
                var allowedLearnerTypes = (option.getAttribute('data-program-finder-for') || '').split(' ');
                var isVisible = learnerType !== '' && allowedLearnerTypes.indexOf(learnerType) !== -1;

                option.hidden = !isVisible;
                setInputsDisabled(option, !isVisible);

                if (!isVisible) {
                    Array.prototype.slice.call(option.querySelectorAll('input:checked')).forEach(function (input) {
                        input.checked = false;
                    });
                }
            });

            Array.prototype.slice.call(form.querySelectorAll('[data-program-finder-condition]')).forEach(function (section) {
                var isVisible = learnerType === section.getAttribute('data-program-finder-condition');

                section.hidden = !isVisible;
                setInputsDisabled(section, !isVisible);
            });

            Array.prototype.slice.call(form.querySelectorAll('[data-program-finder-condition-goal]')).forEach(function (section) {
                var isVisible = goal === section.getAttribute('data-program-finder-condition-goal');

                section.hidden = !isVisible;
                setInputsDisabled(section, !isVisible);
            });

            if (detailsStep) {
                var hasVisibleDetail = Array.prototype.slice.call(detailsStep.querySelectorAll('[data-program-finder-condition], [data-program-finder-condition-goal]'))
                    .some(function (section) {
                        return !section.hidden;
                    });

                detailsStep.classList.toggle('program-finder-step--skipped', !hasVisibleDetail);
            }

            updateChoiceStyles();
        }

        function visibleSteps() {
            return allSteps.filter(function (step) {
                return !step.classList.contains('program-finder-step--skipped');
            });
        }

        function clearClientError() {
            if (!clientError) {
                return;
            }

            clientError.textContent = '';
            clientError.hidden = true;
        }

        function showClientError() {
            if (!clientError) {
                return;
            }

            clientError.textContent = form.getAttribute('data-required-message') || '';
            clientError.hidden = false;
        }

        function validateStep(step, showError) {
            var names = [];

            Array.prototype.slice.call(step.querySelectorAll('input[type="radio"]')).forEach(function (input) {
                if (input.disabled || input.closest('[hidden]') || names.indexOf(input.name) !== -1) {
                    return;
                }

                names.push(input.name);
            });

            var hasMissingValue = names.some(function (name) {
                return !form.querySelector('input[name="' + name + '"]:checked:not(:disabled)');
            });

            if (hasMissingValue && showError) {
                showClientError();
            }

            if (!hasMissingValue) {
                clearClientError();
            }

            return !hasMissingValue;
        }

        function renderCurrentStep(focusHeading) {
            var steps = visibleSteps();

            if (steps.length === 0) {
                return;
            }

            currentIndex = Math.max(0, Math.min(currentIndex, steps.length - 1));
            var currentStep = steps[currentIndex];

            allSteps.forEach(function (step) {
                step.classList.toggle('program-finder-step--hidden', step !== currentStep);
            });

            if (previousButton) {
                previousButton.hidden = currentIndex === 0;
            }

            if (nextButton) {
                nextButton.hidden = currentIndex === steps.length - 1;
            }

            if (submitButton) {
                submitButton.hidden = currentIndex !== steps.length - 1;
            }

            var currentStepNumber = currentIndex + 1;
            var progressValue = (currentStepNumber / steps.length) * 100;

            if (progressBar) {
                progressBar.style.width = progressValue + '%';
            }

            if (progress) {
                progress.setAttribute('aria-valuemax', String(steps.length));
                progress.setAttribute('aria-valuenow', String(currentStepNumber));
            }

            if (stepLabel) {
                var template = form.getAttribute('data-step-template') || ':current / :total';
                stepLabel.textContent = template
                    .replace(':current', String(currentStepNumber))
                    .replace(':total', String(steps.length));
            }

            if (focusHeading) {
                var heading = currentStep.querySelector('legend');

                if (heading) {
                    heading.setAttribute('tabindex', '-1');
                    heading.focus({ preventScroll: true });
                }

                currentStep.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        form.addEventListener('change', function (event) {
            if (!event.target.matches('input[type="radio"]')) {
                return;
            }

            syncConditionalFields();
            renderCurrentStep(false);
        });

        if (nextButton) {
            nextButton.addEventListener('click', function () {
                syncConditionalFields();
                var steps = visibleSteps();

                if (!validateStep(steps[currentIndex], true)) {
                    return;
                }

                currentIndex += 1;
                renderCurrentStep(true);
            });
        }

        if (previousButton) {
            previousButton.addEventListener('click', function () {
                currentIndex -= 1;
                clearClientError();
                renderCurrentStep(true);
            });
        }

        form.addEventListener('submit', function (event) {
            syncConditionalFields();
            var steps = visibleSteps();
            var firstInvalidIndex = steps.findIndex(function (step) {
                return !validateStep(step, false);
            });

            if (firstInvalidIndex === -1) {
                return;
            }

            event.preventDefault();
            currentIndex = firstInvalidIndex;
            showClientError();
            renderCurrentStep(true);
        });

        syncConditionalFields();
        renderCurrentStep(false);
    });
})();
