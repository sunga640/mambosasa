<div id="site-page-progress" aria-hidden="true"></div>
<style>
#site-page-progress {
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    width: 0;
    z-index: 2147483000;
    pointer-events: none;
    background: linear-gradient(90deg, #1e4d6b 0%, #0099cc 45%, #c41e3a 100%);
    box-shadow: 0 0 12px rgba(0, 153, 204, 0.45);
    transition: width 0.35s ease, opacity 0.4s ease;
    opacity: 1;
}
#site-page-progress.is-done {
    width: 100% !important;
    opacity: 0;
}
</style>
<script>
(function () {
    var bar = document.getElementById('site-page-progress');
    if (!bar) return;
    var active = false;
    function kick() {
        active = true;
        bar.classList.remove('is-done');
        bar.style.opacity = '1';
        bar.style.width = '0';
        requestAnimationFrame(function () {
            bar.style.width = '38%';
        });
        setTimeout(function () { bar.style.width = '72%'; }, 120);
        setTimeout(function () { bar.style.width = '92%'; }, 420);
    }
    function done() {
        bar.style.width = '100%';
        bar.classList.add('is-done');
        setTimeout(function () {
            bar.style.width = '0';
            bar.classList.remove('is-done');
            active = false;
        }, 500);
    }
    if (document.readyState === 'complete') {
        kick();
    } else {
        document.addEventListener('readystatechange', function () {
            if (document.readyState === 'interactive') kick();
        });
    }
    document.addEventListener('click', function (e) {
        var a = e.target.closest('a[href]');
        if (!a) return;
        var href = a.getAttribute('href') || '';
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        if (a.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
        var url = new URL(a.href, window.location.href);
        if (url.origin !== window.location.origin) return;
        if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return;
        kick();
    }, true);
    document.addEventListener('submit', function () {
        kick();
    }, true);
    window.addEventListener('beforeunload', function () {
        if (!active) kick();
    });
    window.addEventListener('load', function () {
        done();
    });
})();
</script>
