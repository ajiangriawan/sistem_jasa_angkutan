<x-filament::page>
    {{ $this->form }}

    <script>
        window.addEventListener('open-export-tab', event => {
            window.open(event.detail.url, '_blank');
        });
    </script>
</x-filament::page>
