<x-app-layout>
    <div class="bg-gray-900 py-16 text-center">
        <h2 class="text-4xl font-bold text-white mb-4">Welcome to Bibliohaha!</h2>
        <p class="text-lg text-gray-300">Discover your next favorite book from our curated collection</p>
    </div>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="bg-white p-4 rounded-lg shadow-sm mb-8">
            <form action="{{ route('books.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                
                <div class="flex-1 relative">
                    <input type="text" name="txt_search" value="{{ request('txt_search') }}" placeholder="Search books or authors..." 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 pl-4 py-2" required>
                </div>
                
                <div class="md:w-48">
                    <select name="txt_genre" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 py-2">
                        <option value="">All Genres</option>
                        <option value="fiction" {{ request('txt_genre') == 'fiction' ? 'selected' : '' }}>Fiction</option>
                        <option value="non-fiction" {{ request('txt_genre') == 'non-fiction' ? 'selected' : '' }}>Non-fiction</option>
                        <option value="sci-fi" {{ request('txt_genre') == 'sci-fi' ? 'selected' : '' }}>Science Fiction</option>
                    </select>
                </div>

                <div class="md:w-48">
                    <select name="txt_filter" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 py-2">
                        <option value="title" {{ request('txt_filter') == 'title' ? 'selected' : '' }}>Title A-Z</option>
                        <option value="author" {{ request('txt_filter') == 'author' ? 'selected' : '' }}>Author A-Z</option>
                        <option value="price_asc" {{ request('txt_filter') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('txt_filter') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>

                <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white px-6 py-2 rounded-md font-semibold transition">
                    Search
                </button>
            </form>
        </div>

        <p class="text-gray-500 mb-6 font-medium">Showing {{ $books->total() ?? $books->count() }} books</p>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
    @forelse ($books as $book)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-lg transition-all duration-300">
            
            <a href="{{ route('books.show', $book->id) }}" class="block relative group">
                <img class="w-full aspect-[3/4] object-cover transition-transform duration-500 group-hover:scale-105" 
                    src="{{ asset('images/' . ($book->img_url ?? 'default_book.png')) }}" 
                    alt="{{ $book->title }}">
                
                @if($book->genre)
                    <span class="absolute top-3 right-3 bg-gray-900/90 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">
                        {{ $book->genre }}
                    </span>
                @endif
            </a>

            <div class="p-5 flex flex-col flex-grow">
                <div class="flex-grow">
                    <h3 class="text-lg font-bold text-gray-900 leading-tight mb-1">{{ $book->title }}</h3>
                    <p class="text-sm text-gray-500 italic mb-4">by {{ $book->author }}</p>
                    
                    <div class="flex items-center gap-1 mb-4">
                        <span class="text-yellow-400 text-xs">★★★★★</span>
                        <span class="text-gray-400 text-xs">(0.0)</span>
                    </div>
                </div>

                <div class="space-y-1 mb-6 pt-4 border-t border-gray-50">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Buy:</span>
                        <span class="font-bold text-gray-900">Rs {{ $book->price }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Rent (7 days):</span>
                        <span class="font-bold text-blue-600">Rs {{ $book->rental_fee }}</span>
                    </div>
                </div>

                <a href="{{ route('books.show', $book->id) }}" 
                   class="block w-full py-2.5 border-2 border-gray-900 text-gray-900 hover:bg-gray-900 hover:text-white text-center text-[11px] font-black uppercase tracking-[0.2em] rounded-lg transition-all duration-300">
                    Buy / Rent
                </a>
            </div>
        </div>
    @empty
        <div class="col-span-full py-20 text-center">
            <p class="text-gray-400">No books found in the collection.</p>
        </div>
    @endforelse
</div>

        <div class="mt-8">
            {{ $books->links() }}
        </div>

    </div>
</x-app-layout>