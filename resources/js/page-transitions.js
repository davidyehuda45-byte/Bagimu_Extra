// Smooth Page Navigation Transitions & Top Progress Bar
(function () {
    if (typeof window === 'undefined') return;

    // Check prefers-reduced-motion
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    if (prefersReducedMotion.matches) return;

    let progressBar = null;
    let progressTimer = null;
    let progressVal = 0;

    function getOrCreateProgressBar() {
        if (!progressBar) {
            progressBar = document.getElementById('app-page-progress');
            if (!progressBar && document.body) {
                progressBar = document.createElement('div');
                progressBar.id = 'app-page-progress';
                progressBar.setAttribute('aria-hidden', 'true');
                document.body.appendChild(progressBar);
            }
        }
        return progressBar;
    }

    function setProgress(val) {
        progressVal = val;
        const bar = getOrCreateProgressBar();
        if (!bar) return;
        bar.style.width = `${val}%`;
        bar.style.opacity = '1';
    }

    function startProgress() {
        if (progressTimer) clearInterval(progressTimer);
        const bar = getOrCreateProgressBar();
        if (!bar) return;

        bar.style.transition = 'width 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease';
        setProgress(28);

        progressTimer = setInterval(() => {
            if (progressVal < 70) {
                setProgress(progressVal + Math.random() * 12 + 4);
            } else if (progressVal < 88) {
                setProgress(progressVal + Math.random() * 4 + 1);
            }
        }, 180);
    }

    function finishProgress() {
        if (progressTimer) {
            clearInterval(progressTimer);
            progressTimer = null;
        }
        const bar = getOrCreateProgressBar();
        if (!bar) return;

        bar.style.transition = 'width 0.2s ease-out, opacity 0.3s ease 0.15s';
        bar.style.width = '100%';
        bar.style.opacity = '0';

        setTimeout(() => {
            if (bar) {
                bar.style.transition = 'none';
                bar.style.width = '0%';
            }
        }, 500);
    }

    function getMainElement() {
        return document.getElementById('konten-utama') || document.getElementById('app-content') || document.querySelector('main') || document.body;
    }

    // Intercept internal link clicks
    document.addEventListener('click', function (e) {
        const link = e.target.closest('a');
        if (!link) return;

        // Ignore if event was prevented
        if (e.defaultPrevented) return;

        // Ignore modifier keys (Cmd, Ctrl, Shift, Alt, middle-click)
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) return;

        // Ignore non-self targets
        if (link.target && link.target !== '_self') return;

        // Ignore download links
        if (link.hasAttribute('download')) return;

        // Ignore links opting out
        if (link.hasAttribute('data-no-transition') || link.classList.contains('no-transition')) return;

        const href = link.getAttribute('href');
        if (!href) return;

        // Ignore hash links, protocols
        if (href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

        try {
            const url = new URL(link.href, window.location.href);
            // Must be same origin
            if (url.origin !== window.location.origin) return;

            // If same page with just hash change, ignore
            if (url.pathname === window.location.pathname && url.search === window.location.search) {
                if (url.hash) return;
                // Clicking current page again
                return;
            }

            // Start transition
            startProgress();

            const main = getMainElement();
            if (main) {
                main.classList.add('page-is-leaving');
            }
        } catch (_) {
            // Ignore parse errors
        }
    }, { capture: true });

    // Handle form submissions if standard navigation
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form || form.hasAttribute('data-no-transition') || form.target === '_blank') return;
        setTimeout(() => {
            if (!e.defaultPrevented) {
                startProgress();
            }
        }, 20);
    });

    // Reset and trigger entrance on pageshow (initial load and bfcache)
    window.addEventListener('pageshow', function () {
        finishProgress();
        const main = getMainElement();
        if (main) {
            main.classList.remove('page-is-leaving');
            main.classList.remove('animate-page-enter');
            void main.offsetWidth; // Force DOM reflow to restart CSS animation cleanly
            main.classList.add('animate-page-enter');
        }
    });

    // Cancel if pagehide without full unload
    window.addEventListener('pagehide', function () {
        finishProgress();
    });
})();
