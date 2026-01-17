<h1 class="text-2xl font-bold">Service Providers</h1>

@foreach($workers as $w)
    <div>
        {{ $w->name }} – {{ $w->city ?? 'Moose Jaw' }}
        <a href="{{ route('workers.show', $w->id) }}">View</a>
    </div>
@endforeach

{{ $workers->links() }}
