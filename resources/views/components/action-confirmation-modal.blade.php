<div
    x-data="{
        confirmationOpen: false,
        confirmationFormId: null,
        confirmationTitle: '',
        confirmationContent: '',
        confirmationActionLabel: '',
        confirmationIcon: 'fa-archive',
        confirmationTone: 'neutral',
        confirmationTriggerHandler: null,
        init() {
            this.confirmationTriggerHandler = (event) => {
                const trigger = event.target.closest('[data-action-confirmation]');

                if (!trigger) {
                    return;
                }

                event.preventDefault();
                this.openConfirmation({
                    formId: trigger.dataset.confirmForm,
                    title: trigger.dataset.confirmTitle,
                    content: trigger.dataset.confirmContent,
                    actionLabel: trigger.dataset.confirmAction,
                    icon: trigger.dataset.confirmIcon,
                    tone: trigger.dataset.confirmTone
                });
            };

            window.addEventListener('click', this.confirmationTriggerHandler);
        },
        destroy() {
            window.removeEventListener('click', this.confirmationTriggerHandler);
        },
        openConfirmation(detail) {
            this.confirmationFormId = detail.formId || null;
            this.confirmationTitle = detail.title || '';
            this.confirmationContent = detail.content || '';
            this.confirmationActionLabel = detail.actionLabel || '';
            this.confirmationIcon = detail.icon || 'fa-archive';
            this.confirmationTone = detail.tone || 'neutral';
            this.confirmationOpen = true;
        },
        submitConfirmation() {
            const form = document.getElementById(this.confirmationFormId);

            if (!form) {
                return;
            }

            this.confirmationOpen = false;
            form.submit();
        }
    }"
    x-on:ala-action-confirmation.window="openConfirmation($event.detail)"
>
    <div
        x-show="confirmationOpen"
        x-on:keydown.escape.window="confirmationOpen = false"
        class="jetstream-modal fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
        style="display: none;"
        role="dialog"
        aria-modal="true"
        x-bind:aria-label="confirmationTitle"
    >
        <div x-show="confirmationOpen" class="fixed inset-0 transform transition-all"
            x-on:click="confirmationOpen = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div x-show="confirmationOpen"
            class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto"
            x-on:click.stop
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95">
            <div class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8 w-full relative">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-orange-500 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span x-text="confirmationTitle"></span>
                    </h2>
                    <button type="button" x-on:click="confirmationOpen = false"
                        class="text-gray-400 hover:text-red-500 transition" aria-label="{{ __('dictt.close') }}">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div class="text-gray-700" x-text="confirmationContent"></div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" x-on:click="confirmationOpen = false"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">
                        <i class="fa fa-ban mr-1"></i> {{ __('dictt.cancel') }}
                    </button>
                    <button type="button" x-on:click="submitConfirmation()"
                        class="px-4 py-2 text-white rounded-md transition"
                        x-bind:class="confirmationTone === 'danger'
                            ? 'bg-red-600 hover:bg-red-700'
                            : confirmationTone === 'success'
                                ? 'bg-green-600 hover:bg-green-700'
                                : 'bg-secondary hover:bg-gray-700'">
                        <i class="fa mr-1" x-bind:class="confirmationIcon"></i>
                        <span x-text="confirmationActionLabel"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
