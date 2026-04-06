@props(['title'])

<div x-data="{ open: true }" x-show="open"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 px-4">
    <div class="bg-blue-50 rounded-2xl shadow-xl p-6 md:p-8 w-full max-w-sm text-center">
        <h2 class="text-lg font-semibold mb-4 text-red-600">{{ $title }}</h2>
        <div class="mb-6">{{ $slot }}</div>
        <button @click="open = false"
            class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg">
            OK
        </button>
    </div>
</div>
