(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var mobileMenu = document.getElementById('frontendMobileNavbarCollapse');

        if (!mobileMenu || !window.bootstrap || !window.bootstrap.Collapse) {
            return;
        }

        mobileMenu.addEventListener('click', function (event) {
            var link = event.target.closest('a[href]');

            if (!link || !mobileMenu.contains(link) || link.getAttribute('href') === '#' || link.hasAttribute('data-bs-toggle')) {
                return;
            }

            var collapse = window.bootstrap.Collapse.getInstance(mobileMenu)
                || new window.bootstrap.Collapse(mobileMenu, { toggle: false });

            collapse.hide();
        });
    });
})();
