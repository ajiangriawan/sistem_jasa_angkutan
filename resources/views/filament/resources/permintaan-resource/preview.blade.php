<div class="space-y-4">
    @foreach ($files as $file)
        <div class="border rounded p-2">
            <iframe src="{{ asset('storage/' . $file) }}" class="w-full h-96"></iframe>
            <div class="mt-2 mt-2 text-center">
                <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-blue-500 underline">
                    Download File
                </a>
            </div>
        </div>
    @endforeach
</div>
