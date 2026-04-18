<div class="max-w-xl mx-auto p-6">

    <h1 class="text-xl font-bold mb-4">Edit Category</h1>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @csrf
        @method('PUT')

        <input name="name"
               value="{{ $category->name }}"
               class="w-full border p-2 mb-3">

        <select name="color" class="w-full border p-2 mb-3">
            <option value="indigo" @selected($category->color=='indigo')>Indigo</option>
            <option value="red" @selected($category->color=='red')>Red</option>
            <option value="green" @selected($category->color=='green')>Green</option>
            <option value="yellow" @selected($category->color=='yellow')>Yellow</option>
             <option value="blue" @selected($category->color=='blue')>Blue</option>
            <option value="teal" @selected($category->color=='teal')>Teal</option>
            <option value="purple" @selected($category->color=='purple')>Purple</option>
            <option value="pink" @selected($category->color=='pink')>Pink</option>
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
        </div>
        <button class="bg-black text-white px-4 py-2 w-full">
            Update
        </button>

    </form>

</div>