<div class="max-w-xl mx-auto p-6">

    <h1 class="text-xl font-bold mb-4">Create Category</h1>

    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
        @csrf

        <input name="name"
               placeholder="Name"
               class="w-full border p-2 mb-3">
        <select name="color" class="w-full border p-2 mb-3">
            <option value="indigo">Indigo</option>
            <option value="red">Red</option>
            <option value="green">Green</option>
            <option value="yellow">Yellow</option>
            <option value="blue">Blue</option>
            <option value="teal">Teal</option>
            <option value="purple">Purple</option>
            <option value="pink">Pink</option>
        </select>
        <div>
            <label class="block mb-2 text-sm font-medium">Choose Icon</label>

        <div class="grid grid-cols-6 gap-3">
            @php
                $icons = \App\Models\Category::iconMap();
            @endphp

             @foreach($icons as $key => $emoji)
                <label class="cursor-pointer border rounded-xl p-3 text-center hover:bg-gray-100 transition relative">
                    <div class="text-2xl">
                        <input type="radio" name="icon" value="{{ $key }}" class="peer hidden">{{ $emoji }}{{ $key }}
                    </div>
                    <div class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-green-500 pointer-events-none"></div>

                </label>
            @endforeach
        </div>
        <button class="bg-black text-white px-4 py-2 w-full">
            Save
        </button>

    </form>

</div>