<div class="space-y-4">
    @foreach($dokumenList as $index => $path)
        <div class="p-4 border rounded shadow-sm bg-white">
            <h3 class="font-semibold text-gray-700">{{ $documentName }} #{{ $index + 1 }}</h3>
            <iframe src="{{ Storage::url($path) }}" class="w-full h-96 mt-2 rounded" frameborder="0"></iframe>
        </div>
    @endforeach
</div>
