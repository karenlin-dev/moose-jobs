<x-app-layout>
    <x-slot name="header">
        <h2>{{ $bid->task->title }}</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8 space-y-4">
        <p><strong>Price:</strong> ${{ $bid->price }}</p>
        <p><strong>Message:</strong> {{ $bid->message }}</p>
        <p><strong>Status:</strong> {{ $bid->status }}</p>
        <p><strong>Submitted:</strong> {{ $bid->created_at->diffForHumans() }}</p>
    </div>
</x-app-layout>
