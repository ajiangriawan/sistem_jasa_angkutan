<div class="space-y-4">
    @forelse ($tindakLanjutList as $item)
    <div class="border p-3 rounded bg-gray-100 dark:bg-gray-700 dark:border-gray-600">
        <p class="text-gray-900 dark:text-gray-100"><strong>Oleh:</strong> {{ $item->user->name }}</p>
        <p class="text-gray-900 dark:text-gray-100"><strong>Catatan:</strong> {{ $item->catatan }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->created_at->format('d M Y H:i') }}</p>
    </div>
    @empty
    <p class="text-gray-500 dark:text-gray-400">Belum ada catatan tindak lanjut.</p>
    @endforelse
</div>