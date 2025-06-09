@if ($files)
<div class="space-y-4">
    <div class="border rounded p-2">
        <iframe src="{{ Storage::url($files) }}" class="w-full h-96"></iframe>
        <div class="mt-2 mt-2 text-center">
            <a href="{{ Storage::url($files) }}" target="_blank" class="text-blue-500 underline">
                Download File
            </a>
        </div>
    </div>
</div>

@else
<p>Tidak ada bukti transfer.</p>
@endif