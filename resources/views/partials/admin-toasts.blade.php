@php
    $adminToasts = [];

    if (session('modalSuccessTitle') && session('modalSuccessContent')) {
        $adminToasts[] = [
            'id' => 'toast-' . uniqid(),
            'type' => 'success',
            'title' => (string) session('modalSuccessTitle'),
            'message' => (string) session('modalSuccessContent'),
        ];
    }

    if (session('error')) {
        $adminToasts[] = [
            'id' => 'toast-' . uniqid(),
            'type' => 'error',
            'title' => '',
            'message' => (string) session('error'),
        ];
    }

    if (isset($errors) && $errors->any()) {
        $adminToasts[] = [
            'id' => 'toast-' . uniqid(),
            'type' => 'error',
            'title' => (string) __('dictt.errors'),
            'message' => '<ul class="mb-0 ps-3">' . collect($errors->all())
                ->map(fn ($error) => '<li>' . e($error) . '</li>')
                ->implode('') . '</ul>',
        ];
    }
@endphp

<div class="admin-toasts" x-data="{
        toasts: @js($adminToasts),
        addToast(detail) {
            const toast = {
                id: 'toast-' + Date.now() + '-' + Math.floor(Math.random() * 100000),
                type: detail.type || 'success',
                title: detail.title || '',
                message: detail.message || '',
            };
            this.toasts.push(toast);
            setTimeout(() => this.dismiss(toast.id), 6000);
        },
        dismiss(id) {
            this.toasts = this.toasts.filter((toast) => toast.id !== id);
        },
    }" x-init="toasts.forEach((toast) => setTimeout(() => dismiss(toast.id), 6000))"
    x-on:admin-toast.window="addToast($event.detail)" aria-live="polite">
    <template x-for="toast in toasts" :key="toast.id">
        <div :class="'admin-toast admin-toast--' + toast.type" role="status">
            <i :class="toast.type === 'success' ? 'fas fa-check-circle' : 'fas fa-times-circle'"
                class="admin-toast__icon" aria-hidden="true"></i>
            <div class="admin-toast__body">
                <div class="admin-toast__title" x-show="toast.title" x-html="toast.title"></div>
                <div class="admin-toast__message" x-html="toast.message"></div>
            </div>
            <button type="button" class="admin-toast__close" @click="dismiss(toast.id)"
                aria-label="{{ __('dictt.close') }}" title="{{ __('dictt.close') }}">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
    </template>
</div>
