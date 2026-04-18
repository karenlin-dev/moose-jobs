<!doctype html>
<html>
<head>
    <title>Categories</title>
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-50">

<div class="max-w-5xl mx-auto p-6">

    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Categories</h1>

        <a href="{{ route('admin.categories.create') }}"
           class="bg-black text-white px-4 py-2 rounded">
            + Add Category
        </a>
    </div>

    @if(session('success'))
        <div class="mt-4 bg-green-100 text-green-700 p-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6 bg-white shadow rounded">

        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Slug</th>
                    <th class="p-3 text-left">Color</th>
                    <th class="p-3 text-left">Icon</th>
                    <th class="p-3 text-right">Action</th>
                </tr>
            </thead>

            <tbody>
            @foreach($categories as $cat)
                <tr class="border-t">
                    <td class="p-3">{{ $cat->name }}</td>
                    <td class="p-3 text-gray-500">{{ $cat->slug }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded bg-{{ $cat->color }}-100">
                            {{ $cat->color }}
                        </span>
                    </td>
                    <td class="p-3">
                        <span class="text-xl">{{ $cat->icon_emoji }}</span>
                    </td>
                    <td class="p-3 text-right flex justify-end gap-2">

                        <a href="{{ route('admin.categories.edit', $cat) }}"
                           class="px-3 py-1 bg-blue-500 text-white rounded">
                            Edit
                        </a>

                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}">
                            @csrf
                            @method('DELETE')

                            <button class="px-3 py-1 bg-red-500 text-white rounded"
                                    onclick="return confirm('Delete?')">
                                Delete
                            </button>
                        </form>

                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>
    </div>

</div>

</body>
</html>