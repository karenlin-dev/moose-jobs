@extends('layouts.app')

@section('title', 'My Assignments')

@section('content')
<div class="container">
    <h1>My Assignments</h1>

    @if($assignments->isEmpty())
        <p>You have no assignments yet.</p>
    @else
        @foreach($assignments as $assignment)
            <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px">
                <p>
                    <strong>Job:</strong>
                    <a href="{{ route('jobs.show', $assignment->job) }}">{{ $assignment->job->title }}</a>
                </p>

                <p>
                    <strong>Employer:</strong> {{ $assignment->employer->name }}
                </p>

                <p>
                    <strong>Agreed Price:</strong> ${{ $assignment->agreed_price }}
                </p>

                <p>
                    <strong>Started At:</strong> {{ $assignment->started_at->format('Y-m-d H:i') }}
                </p>

                <p>
                    <strong>Status:</strong>
                    {{ ucfirst(str_replace('_', ' ', $assignment->job->status)) }}
                </p>

                @if($assignment->job->status === 'in_progress')
                    <form method="POST" action="{{ route('assignments.complete', $assignment) }}">
                        @csrf
                        <button type="submit">Mark as Completed</button>
                    </form>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection
