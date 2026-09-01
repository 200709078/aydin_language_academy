(() => {
    const selector = [
        'button',
        'input[type="button"]',
        'input[type="submit"]',
        'input[type="reset"]',
        'input[role="switch"]',
        'a.btn',
        '[role="button"]',
    ].join(', ');

    const normalize = (value) => (value || '').replace(/\s+/g, ' ').trim();

    const closestControl = (node) => {
        const element = node?.nodeType === Node.ELEMENT_NODE
            ? node
            : node?.parentElement;

        if (! element) {
            return null;
        }

        return element.matches(selector) ? element : element.closest(selector);
    };

    const setTitleIfMissing = (control) => {
        if (! control || control.hasAttribute('title')) {
            return;
        }

        const title = normalize(
            control.getAttribute('aria-label')
            || control.getAttribute('data-title')
            || control.value
            || control.textContent,
        );

        if (title) {
            control.setAttribute('title', title);
        }
    };

    const setTitlesWithin = (root = document) => {
        if (root.nodeType === Node.ELEMENT_NODE && root.matches(selector)) {
            setTitleIfMissing(root);
        }

        if (typeof root.querySelectorAll === 'function') {
            root.querySelectorAll(selector).forEach(setTitleIfMissing);
        }
    };

    const initialize = () => {
        setTitlesWithin();

        new MutationObserver((records) => {
            records.forEach((record) => {
                if (record.type === 'characterData') {
                    setTitleIfMissing(closestControl(record.target));
                    return;
                }

                if (record.type === 'attributes') {
                    setTitleIfMissing(closestControl(record.target));
                    return;
                }

                record.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        setTitleIfMissing(closestControl(node));
                        setTitlesWithin(node);
                    } else if (node.nodeType === Node.TEXT_NODE) {
                        setTitleIfMissing(closestControl(node));
                    }
                });
            });
        }).observe(document.body, {
            attributes: true,
            attributeFilter: ['aria-label'],
            characterData: true,
            childList: true,
            subtree: true,
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})();
