<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            @if(auth()->user()->isEmployer())
                <a href="{{ route('tasks.create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Post Task
                </a>
            @endif
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8 space-y-6">
       @php
            $currentCatName = null;
            if (!empty($categoryId) && isset($categories)) {
                $currentCatName = optional($categories->firstWhere('id', (int)$categoryId))->name;
            }
        @endphp

        <h2 class="text-xl font-semibold text-gray-800">
            {{ auth()->user()->isEmployer() ? 'My Tasks' : 'Worker Dashboard' }}
            @if($currentCatName)
                <span class="text-sm text-gray-500">— {{ $currentCatName }}</span>
            @endif
        </h2>

        @php
            // 防止未定义
            $categories = $categories ?? collect();
            $categoryId = $categoryId ?? null;
        @endphp

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('dashboard') }}"
            class="px-4 py-2 rounded border text-sm
                    {{ empty($categoryId) ? 'bg-black text-white border-black' : 'bg-gray-100 hover:bg-gray-200' }}">
                All
            </a>

            @foreach($categories as $cat)
                <a href="{{ route('dashboard', ['category' => $cat->id]) }}"
                class="px-4 py-2 rounded border text-sm
                        {{ (string)$categoryId === (string)$cat->id ? 'bg-black text-white border-black' : 'bg-gray-100 hover:bg-gray-200' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        @forelse($tasks as $task)
        <div class="bg-white p-6 rounded shadow space-y-2">
            <h3 class="text-lg font-semibold">{{ $task->title }}</h3>
            <p class="text-gray-600">{{ $task->description }}</p>
            <p class="text-sm text-gray-500">
                Budget: ${{ $task->budget }} | Status: {{ $task->status }}
            </p>
            <div class="flex gap-2">
                @php
                    $taskUserId = (int) $task->user_id;
                    $authId = auth()->id();
                @endphp
                @if($authId === $taskUserId)
                    <a href="{{ route('tasks.edit', $task) }}"
                    class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 text-sm">
                        Edit
                    </a>
                @endif

                <a href="{{ route('tasks.show', $task) }}"
                class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 text-sm">
                    View
                </a>

            </div>  

            @if($task->bids->count() > 0)
                <div class="mt-2">
                    <h4 class="font-semibold">Bids:</h4>
                    <ul class="divide-y divide-gray-200">
                        @foreach($task->bids as $bid)
                            @php
                                $isAccepted = $bid->status === 'accepted';
                            @endphp

                            <li class="py-2 flex justify-between items-center {{ $isAccepted ? 'bg-green-100 rounded p-2' : '' }}">
                                <div>
                                    <span class="font-medium">{{ $bid->worker->name }}</span> -
                                    ${{ $bid->price }}
                                    <span class="text-gray-400 text-sm">({{ $bid->status }})</span>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('workers.show', $bid->worker) }}"
                                    class="text-indigo-600 hover:underline">View Profile</a>

                                    @if($bid->status === 'pending' && $task->status === 'open')
                                        <form class="accept-bid-form" method="POST" action="{{ route('bids.accept', $bid) }}">
                                            @csrf
                                            @method('PATCH')
                                            <x-primary-button>Accept</x-primary-button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-gray-500">No bids yet.</p>
            @endif
        </div>
    @empty
        <p class="text-gray-500">You have not posted any tasks yet.</p>
    @endforelse

    </div>

    <script>
    document.querySelectorAll('.accept-bid-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = form.action;
            const data = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(url, {
                method: 'PATCH',
                body: data,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(json => {
                // 找到这个 bid 的 li 元素
                const li = form.closest('li');
                li.classList.add('bg-green-100', 'rounded', 'p-2');

                // 更新状态文本
                const statusSpan = li.querySelector('span.text-gray-400');
                if(statusSpan) statusSpan.textContent = '(accepted)';

                // 移除 Accept 按钮
                form.remove();

                alert(json.message);
            })
            .catch(err => console.error(err));
        });
    });
    </script>
</x-app-layout>
