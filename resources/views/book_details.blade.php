<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $book->title }} Preview
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="bg-white p-6 rounded-lg shadow-sm grid grid-cols-1 md:grid-cols-12 gap-8">
            
            <div class="md:col-span-4 flex flex-col items-center">
                <img class="rounded-lg shadow-md w-full object-cover max-w-sm" 
                    src="{{ asset('images/' . ($book->img_url ?? 'default_book.png')) }}"
                    alt="{{ $book->title }} by {{ $book->author }}" />

                <div class="mt-6 w-full max-w-sm">
                    <div class="text-center mb-4 text-lg font-semibold text-gray-700 bg-gray-50 py-2 rounded border border-gray-100">
                        Rental Fee: Rs {{ $book->rental_fee }}
                    </div>
                    
                    @auth
                    <form action="{{ route('books.borrow') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{$book->id}}">
                        <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 px-4 rounded transition duration-150">
                            Borrow Book
                        </button>
                    </form>
                    @else
                        <a href="{{ route('login') }}" class="block text-center w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded transition duration-150">
                            Login to Borrow
                        </a>
                    @endauth
                </div>
            </div>

            <div class="md:col-span-8 flex flex-col justify-center">
                <h1 class="text-3xl font-bold text-gray-900">{{ $book->title }}</h1>
                <p class="text-lg text-gray-600 mt-1">By {{ $book->author ?? 'Unknown Author' }}</p>

                <div class="flex items-center mt-4 mb-6">
                    @php $rating = round($book->reviews_avg_rating ?? 0); @endphp
                    <div class="flex text-yellow-400 text-xl">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $rating)
                                <span>&#9733;</span>
                            @else
                                <span class="text-gray-200">&#9733;</span>
                            @endif
                        @endfor
                    </div>
                    <span class="ml-3 text-gray-500 font-medium">({{ number_format($book->reviews_avg_rating ?? 0, 1) }} out of 5)</span>
                </div>
                
                <div class="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $book->description }}
                </div>
            </div>
        </div>

        <div class="mt-12">
            <div class="flex justify-between items-end mb-6 border-b border-gray-200 pb-4">
                <h2 class="text-2xl font-bold text-gray-900">Customer Reviews</h2>
                <span class="text-gray-500 font-medium">Showing {{ $reviews->count() }} of {{ $reviews->total() }} reviews</span>
            </div>

            @auth
                <a href="{{ route('reviews.create', $book->id) }}" 
                    class="bg-gray-900 hover:bg-gray-800 text-white text-sm font-bold py-2 px-4 rounded-lg transition duration-150">
                     + Write a Review
                 </a>
             @endauth
        </div>

            <div class="space-y-6">
                @forelse ($reviews as $review)
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-bold text-gray-900 text-lg">{{ $review->user->name ?? "Anonymous" }}</h4>
                                <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="flex text-yellow-400">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        <span>&#9733;</span>
                                    @else
                                        <span class="text-gray-200">&#9733;</span>
                                    @endif
                                @endfor
                            </div>
                        </div>

                        <p class="text-gray-700 text-base leading-relaxed">
                            {{ $review->comment }}
                        </p>

                        @auth
                            @if(auth()->id() === $review->user_id)
                                <div class="mt-4 pt-4 border-t border-gray-50 flex justify-end">
                                    <form action="{{ route('reviews.destroy', $review->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold transition duration-150"
                                            onclick="return confirm('Do you want to delete this review?')">
                                            Delete Review
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>
                @empty
                    <div class="bg-gray-50 p-8 rounded-lg text-center border border-dashed border-gray-300">
                        <p class="text-gray-500 text-lg">No reviews yet.</p>
                        <p class="text-gray-400 text-sm mt-1">Be the first to review this book after borrowing it!</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-8">
            {{ $reviews->links() }}
            </div>

        </div>
        
    </div>
</x-app-layout>
