(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var mobileMenu = document.getElementById('frontendMobileNavbarCollapse');

        if (!mobileMenu || !window.bootstrap || !window.bootstrap.Collapse) {
            return;
        }

        function hideMobileMenu() {
            var collapse = window.bootstrap.Collapse.getInstance(mobileMenu)
                || new window.bootstrap.Collapse(mobileMenu, { toggle: false });

            collapse.hide();
        }

        mobileMenu.addEventListener('click', function (event) {
            var link = event.target.closest('a[href]');

            if (!link || !mobileMenu.contains(link) || link.getAttribute('href') === '#' || link.hasAttribute('data-bs-toggle')) {
                return;
            }

            hideMobileMenu();
        });

        document.addEventListener('click', function (event) {
            if (!mobileMenu.classList.contains('show')
                || mobileMenu.contains(event.target)
                || event.target.closest('[data-bs-target="#frontendMobileNavbarCollapse"]')) {
                return;
            }

            hideMobileMenu();
        });
    });
})();
