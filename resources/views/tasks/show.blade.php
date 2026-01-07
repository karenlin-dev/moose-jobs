@extends('layouts.app')

@section('title', $job->title)

@section('content')
<div class="container">

    <h1>{{ $job->title }}</h1>

    <p>
        <strong>Budget:</strong> ${{ $job->budget ?? 'Negotiable' }}
    </p>

    <p>
        <strong>Status:</strong>
        <span>{{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
    </p>

    <hr>

    <h3>Description</h3>
    <p>{{ $job->description }}</p>

    {{-- ===================== --}}
    {{-- 投标区（Worker） --}}
    {{-- ===================== --}}
    @auth
        @if(auth()->user()->role === 'worker' && $job->status === 'open')
            <hr>
            <h3>Place a Bid</h3>

            <form method="POST" action="{{ route('jobs.bids.store', $job) }}">
                @csrf

                <div>
                    <label>Price</label>
                    <input type="number" name="price" step="0.01" required>
                </div>

                <div>
                    <label>Message</label>
                    <textarea name="message"></textarea>
                </div>

                <button type="submit">Submit Bid</button>
            </form>
        @endif
    @endauth

    {{-- ===================== --}}
    {{-- 投标列表（Employer） --}}
    {{-- ===================== --}}
    @auth
        @if(auth()->user()->id === $job->user_id)
            <hr>
            <h3>Bids</h3>

            @forelse($job->bids as $bid)
                <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px">
                    <p>
                        <strong>Worker:</strong>
                        {{ $bid->user->name }}
                    </p>

                    <p>
                        <strong>Price:</strong> ${{ $bid->price }}
                    </p>

                    <p>
                        <strong>Status:</strong> {{ $bid->status }}
                    </p>

                    @if($bid->status === 'pending' && $job->status === 'open')
                        <form method="POST" action="{{ route('bids.accept', $bid) }}">
                            @csrf
                            <button type="submit">Accept</button>
                        </form>
                    @endif
                </div>
            @empty
                <p>No bids yet.</p>
            @endforelse
        @endif
    @endauth

</div>
@endsection
