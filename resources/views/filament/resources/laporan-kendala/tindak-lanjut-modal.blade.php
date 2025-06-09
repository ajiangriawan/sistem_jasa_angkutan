<div class="space-y-4">
    @forelse ($tindakLanjutList as $item)
    <div class="border p-3 rounded bg-gray-100">
        <p><strong>Oleh:</strong> {{ $item->user->name }}</p>
        <p><strong>Catatan:</strong> {{ $item->catatan }}</p>
        <p class="text-sm text-gray-500">{{ $item->created_at->format('d M Y H:i') }}</p>
    </div>
    @empty
    <p class="text-gray-500">Belum ada catatan tindak lanjut.</p>
    @endforelse
</div>