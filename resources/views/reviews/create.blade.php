<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Write a Review for {{ $book->title }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 md:p-12">
                    <h2 class="text-3xl font-black text-gray-900 mb-2">Share your thoughts</h2>
                    <p class="text-gray-500 mb-8">Reviewing <span class="font-bold text-gray-900">{{ $book->title }}</span> by {{ $book->author }}</p>

                    @if ($errors->any())
                         <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                            <ul class="list-disc list-inside text-sm">
                                 @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                 @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('reviews.store', $book->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Your Rating</label>
                            <div class="flex gap-4">
                                @for ($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="rating" value="{{ $i }}" class="hidden peer" required>
                                        <div class="w-12 h-12 flex items-center justify-center rounded-lg border-2 border-gray-100 peer-checked:border-gray-900 peer-checked:bg-gray-900 peer-checked:text-white hover:bg-gray-50 transition-all">
                                            {{ $i }}
                                        </div>
                                    </label>
                                @endfor
                            </div>
                        </div>

                        <div class="mb-8">
                            <label for="comment" class="block text-sm font-bold text-gray-700 mb-3">Your Experience</label>
                            <textarea name="comment" id="comment" rows="5" 
                                class="w-full rounded-xl border-gray-200 focus:border-gray-900 focus:ring-gray-900 transition-all"
                                placeholder="What did you think of the story?"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('books.show', $book->id) }}" class="text-sm font-bold text-gray-400 hover:text-gray-600">Cancel</a>
                            <button type="submit" class="bg-gray-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-gray-800 transition-all shadow-lg">
                                Post Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>