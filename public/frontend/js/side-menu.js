(function () {
    'use strict';

    var STORAGE_KEY = 'frontendSideMenuOpen';
    var root = document.getElementById('fsmRoot');
    var strip = document.getElementById('fsmStrip');

    if (!root || !strip) {
        return;
    }

    var stored = null;
    try {
        stored = localStorage.getItem(STORAGE_KEY);
    } catch (error) {
        stored = null;
    }

    var isOpen = stored === null ? true : stored === 'true';

    function apply() {
        root.classList.toggle('is-closed', !isOpen);
        strip.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    function persist() {
        try {
            localStorage.setItem(STORAGE_KEY, isOpen ? 'true' : 'false');
        } catch (error) {
            /* Depolama kullanılamıyorsa yalnızca oturum içi durum değişir. */
        }
    }

    strip.addEventListener('click', function () {
        isOpen = !isOpen;
        persist();
        apply();
    });

    /* Menü dışına tıklanırsa menüyü kapat */
    document.addEventListener('click', function (event) {
        if (!isOpen || root.contains(event.target)) {
            return;
        }
        isOpen = false;
        persist();
        apply();
    });

    /* Level+alt seviye linki seçilince menüyü kapat */
    root.querySelectorAll('a.fsm-subitem').forEach(function (link) {
        link.addEventListener('click', function () {
            isOpen = false;
            persist();
            apply();
        });
    });

    apply();
})();
