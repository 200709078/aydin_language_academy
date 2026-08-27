(function () {
    'use strict';

    var root = document.getElementById('fsmRoot');
    var strip = document.getElementById('fsmStrip');

    if (!root || !strip) {
        return;
    }

    // Her yeni sayfada menü kapalı başlar; kullanıcı o sayfada isterse açabilir.
    var isOpen = false;

    function apply() {
        root.classList.toggle('is-closed', !isOpen);
        strip.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    strip.addEventListener('click', function () {
        isOpen = !isOpen;
        apply();
    });

    /* Menü dışına tıklanırsa menüyü kapat */
    document.addEventListener('click', function (event) {
        if (!isOpen || root.contains(event.target)) {
            return;
        }
        isOpen = false;
        apply();
    });

    /* Level+alt seviye linki seçilince menüyü kapat */
    root.querySelectorAll('a.fsm-subitem').forEach(function (link) {
        link.addEventListener('click', function () {
            isOpen = false;
            apply();
        });
    });

    apply();
})();
