(function () {
    if (window.__premiumGridAsyncBound) {
        return;
    }

    window.__premiumGridAsyncBound = true;

    const pageSelector = '.premium-grid-page';
    const paginationLinkSelector = '.pagination a';
    const toolbarFormSelector = 'form.premium-toolbar';

    const canUseAsync = typeof window.fetch === 'function' && typeof DOMParser === 'function';

    if (!canUseAsync) {
        return;
    }

    const normalizeToCurrentOrigin = function (urlValue) {
        const parsed = new URL(urlValue, window.location.href);

        return window.location.origin + parsed.pathname + parsed.search + parsed.hash;
    };

    const buildUrlFromForm = function (form) {
        const action = form.getAttribute('action') || window.location.href;
        const url = new URL(action, window.location.origin);
        const formData = new FormData(form);

        formData.forEach(function (value, key) {
            const stringValue = String(value ?? '').trim();
            if (stringValue !== '') {
                url.searchParams.set(key, stringValue);
            }
        });

        return url.toString();
    };

    const extractGridFromHtml = function (html) {
        let nextGrid = null;

        try {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            nextGrid = doc.querySelector(pageSelector);
        } catch (error) {
            nextGrid = null;
        }

        if (!nextGrid) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            nextGrid = tempDiv.querySelector(pageSelector);
        }

        return nextGrid;
    };

    const replaceGrid = async function (url, currentPage) {
        if (!currentPage || currentPage.classList.contains('is-grid-loading')) {
            return;
        }

        currentPage.classList.add('is-grid-loading');

        try {
            const response = await fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-Premium-Grid': '1',
                },
            });

            if (!response.ok) {
                throw new Error('Grid request failed with status ' + response.status);
            }

            const html = await response.text();
            const nextGrid = extractGridFromHtml(html);

            if (!nextGrid) {
                return;
            }

            currentPage.innerHTML = nextGrid.innerHTML;
            window.history.replaceState({}, '', url);
        } catch (error) {
            // Keep the current grid view if async fetch fails.
        } finally {
            currentPage.classList.remove('is-grid-loading');
        }
    };

    document.addEventListener(
        'click',
        function (event) {
            const link = event.target.closest('.grid-sort-link, ' + paginationLinkSelector);
            if (!link) {
                return;
            }

            const href = link.getAttribute('href') || '';
            if (href === '' || href === '#') {
                return;
            }

            const currentPage = link.closest(pageSelector);
            if (!currentPage) {
                return;
            }

            if (
                event.button !== 0 ||
                event.metaKey ||
                event.ctrlKey ||
                event.shiftKey ||
                event.altKey
            ) {
                return;
            }

            event.preventDefault();
            replaceGrid(normalizeToCurrentOrigin(link.href), currentPage);
        },
        false
    );

    document.addEventListener(
        'submit',
        function (event) {
            const form = event.target.closest(toolbarFormSelector);
            if (!form) {
                return;
            }

            const currentPage = form.closest(pageSelector);
            if (!currentPage) {
                return;
            }

            event.preventDefault();
            replaceGrid(normalizeToCurrentOrigin(buildUrlFromForm(form)), currentPage);
        },
        false
    );
})();
