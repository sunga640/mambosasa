<script>
(function () {
    var blockedCombos = new Set([
        'CTRL+U',
        'CTRL+S',
        'CTRL+P',
        'CTRL+SHIFT+I',
        'CTRL+SHIFT+J',
        'CTRL+SHIFT+C',
        'F12'
    ]);

    var normalizeKey = function (event) {
        var parts = [];
        if (event.ctrlKey || event.metaKey) parts.push('CTRL');
        if (event.shiftKey) parts.push('SHIFT');
        var key = String(event.key || '').toUpperCase();
        if (key === 'CONTROL' || key === 'SHIFT' || key === 'META') return '';
        parts.push(key);
        return parts.join('+');
    };

    document.addEventListener('contextmenu', function (event) {
        event.preventDefault();
    });

    document.addEventListener('keydown', function (event) {
        var combo = normalizeKey(event);
        if (blockedCombos.has(combo)) {
            event.preventDefault();
            event.stopPropagation();
            return false;
        }
    }, true);
})();
</script>
