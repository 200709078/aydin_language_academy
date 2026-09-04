(function () {
    'use strict';

    var APP_OPEN_TIMEOUT = 1500;

    document.addEventListener('click', function (event) {
        var link = event.target.closest('a[data-whatsapp-app-url]');

        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        // Let links explicitly marked for a new tab follow their normal HTML
        // behaviour. Intercepting them would make window.location.assign()
        // replace the current page and ignore target="_blank".
        if (link.target === '_blank') {
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
