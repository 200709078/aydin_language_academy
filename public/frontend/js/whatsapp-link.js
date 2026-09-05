(function () {
    'use strict';

    var APP_OPEN_TIMEOUT = 1500;

    function isMobileViewport() {
        return window.matchMedia('(max-width: 767.98px)').matches;
    }

    document.addEventListener('click', function (event) {
        var link = event.target.closest('a[data-whatsapp-app-url]');

        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        // Desktop links use target="_blank" for WhatsApp Web. Mobile links
        // try the native app first and fall back to WhatsApp Web.
        if (!isMobileViewport()) {
            return;
        }

        event.preventDefault();

        var fallbackUrl = link.href;
        var fallbackTimer;
        var appOpened = false;

        function clearFallback() {
            window.clearTimeout(fallbackTimer);
            document.removeEventListener('visibilitychange', onVisibilityChange);
        }

        function onVisibilityChange() {
            if (document.visibilityState !== 'hidden') {
                return;
            }

            appOpened = true;
            clearFallback();
        }

        document.addEventListener('visibilitychange', onVisibilityChange);

        fallbackTimer = window.setTimeout(function () {
            clearFallback();

            if (!appOpened) {
                window.location.assign(fallbackUrl);
            }
        }, APP_OPEN_TIMEOUT);

        window.location.assign(link.dataset.whatsappAppUrl);
    });
})();
