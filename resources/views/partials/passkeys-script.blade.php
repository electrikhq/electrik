<script>
    if (! window.ElectrikPasskeys) {
        @php
            $electrikPasskeysJs = dirname((new ReflectionClass(\Electrik\ElectrikServiceProvider::class))->getFileName(), 2).'/resources/js/passkeys.js';
        @endphp
        {!! file_get_contents($electrikPasskeysJs) !!}
    }
</script>
