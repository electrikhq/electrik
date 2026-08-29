@props([
    'team' => null,
])

@php
    $primary = $team?->brandPrimary() ?: config('electrik.branding.primary');
    $foreground = null;

    if (filled($primary) && preg_match('/^#([0-9A-Fa-f]{6})$/', $primary, $m)) {
        $hex = $m[1];
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $luma = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;
        $foreground = $luma > 0.55 ? '#0a0a0a' : '#fafafa';
    }
@endphp
<style id="electrik-brand-styles">
    [x-cloak] { display: none !important; }
    @if (filled($primary))
    :root,
    html,
    html.dark {
        --slate-primary: {{ $primary }} !important;
        --color-primary: {{ $primary }} !important;
        @if ($foreground)
        --slate-primary-foreground: {{ $foreground }} !important;
        --color-primary-foreground: {{ $foreground }} !important;
        @endif
    }
    @endif
</style>
<script>
    (function () {
        function applyBrand(primary) {
            var root = document.documentElement;
            if (! primary) {
                root.style.removeProperty('--slate-primary');
                root.style.removeProperty('--color-primary');
                root.style.removeProperty('--slate-primary-foreground');
                root.style.removeProperty('--color-primary-foreground');
                return;
            }
            root.style.setProperty('--slate-primary', primary);
            root.style.setProperty('--color-primary', primary);
            var hex = String(primary).replace('#', '');
            if (hex.length === 6) {
                var r = parseInt(hex.slice(0, 2), 16);
                var g = parseInt(hex.slice(2, 4), 16);
                var b = parseInt(hex.slice(4, 6), 16);
                var luma = (0.2126 * r + 0.7152 * g + 0.0722 * b) / 255;
                var fg = luma > 0.55 ? '#0a0a0a' : '#fafafa';
                root.style.setProperty('--slate-primary-foreground', fg);
                root.style.setProperty('--color-primary-foreground', fg);
            }
        }
        @if (filled($primary))
        applyBrand(@js($primary));
        @endif
        window.addEventListener('electrik:brand-updated', function (event) {
            applyBrand(event.detail && event.detail.primary ? event.detail.primary : null);
        });
    })();
</script>
