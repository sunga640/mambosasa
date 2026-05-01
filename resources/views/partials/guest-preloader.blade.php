<div id="guest-preloader" class="guest-preloader" aria-hidden="true">
    <div class="guest-preloader__panel">
        <div class="guest-preloader__spinner"></div>
        <div class="guest-preloader__label">{{ $siteSettings->hotelDisplayName() }}</div>
    </div>
</div>
<style>
    .guest-preloader {
        position: fixed;
        inset: 0;
        z-index: 2147483000;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 253, 250, 0.94);
        backdrop-filter: blur(6px);
        opacity: 1;
        visibility: visible;
        transition: opacity 0.28s ease, visibility 0.28s ease;
    }
    .guest-preloader.is-hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }
    .guest-preloader__panel {
        display: grid;
        gap: 0.9rem;
        justify-items: center;
        text-align: center;
    }
    .guest-preloader__spinner {
        width: 3rem;
        height: 3rem;
        border-radius: 999px;
        border: 2px solid rgba(23, 53, 47, 0.14);
        border-top-color: #17352f;
        animation: guest-preloader-spin 0.9s linear infinite;
    }
    .guest-preloader__label {
        color: #17352f;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.2em;
        text-transform: uppercase;
    }
    @keyframes guest-preloader-spin {
        to { transform: rotate(360deg); }
    }
</style>
<script>
(function () {
    var preloader = document.getElementById('guest-preloader');
    if (!preloader) return;

    function hidePreloader() {
        preloader.classList.add('is-hidden');
    }

    function showPreloader() {
        preloader.classList.remove('is-hidden');
    }

    window.addEventListener('load', function () {
        setTimeout(hidePreloader, 180);
    });

    document.addEventListener('click', function (e) {
        var link = e.target.closest('a[href]');
        if (!link) return;
        var href = link.getAttribute('href') || '';
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        if (link.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
        var url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin) return;
        showPreloader();
    }, true);

    document.addEventListener('submit', function () {
        showPreloader();
    }, true);
})();
</script>
