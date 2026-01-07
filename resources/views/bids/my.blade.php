@extends('layouts.app')

@section('title', 'My Bids')

@section('content')
<div class="container">
    <h1>My Bids</h1>

    @if($bids->isEmpty())
        <p>You have not placed any bids yet.</p>
    @else
        @foreach($bids as $bid)
            <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px">
                <p>
                    <strong>Job:</strong>
                    <a href="{{ route('jobs.show', $bid->job) }}">{{ $bid->job->title }}</a>
                </p>

                <p>
                    <strong>Price:</strong> ${{ $bid->price }}
                </p>

                <p>
                    <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $bid->status)) }}
                </p>

                <p>
                    <strong>Employer:</strong> {{ $bid->job->user->name }}
                </p>

                @if($bid->status === 'accepted')
                    <p><strong>Job started at:</strong> {{ optional($bid->job->assignment)->started_at ?? 'Pending' }}</p>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection
