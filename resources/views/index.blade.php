@extends("layout.default")

@section("main_content")
    <div class="container-fluid" id= "hero-section">
        <h2 class="hero-section__title">Welcome to Bibliohaha!</h2>
        <p class="hero-section__description">Discover your next favorite book from our curated collection</p>
    </div>

    <!--search form-->
    <div class="container-fluid mt-3">
        <div class="row g-2">
            <!--Search Input-->
            <div class="col-12 col-md-6">
                <div class="search">
                    <button type="submit"><i class="fa fa-search"></i></button>
                    <input type="text" name="txt_search" id="txt_search" placeholder="Search books or authors.."
                    style="padding-left: 0%;">
                </div>
            </div>
            <form action="{{ route('books.arrange') }}" method="get">
                <!--Search Genre-->
                <div class="col-6 col-md-3">
                    <select class = "search-input dropdown" id="txt_filter" name="txt_filter">
                    <option value="">All</option>
                    <option value="fiction">Fiction</option>
                    <option value="non-fiction">Non-fiction</option>
                    <option value="sci-fi">Science fiction</option>
                    </select>
                </div>
                <!--Search Filter-->
                <div class="col-6 col-md-3">
                    <select class = "search-input dropdown" id="txt_sort" name="txt_sort" >
                    <option value="title">Title A-Z</option>
                    <option value="author">Author A-Z</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!--Display books-->
    <div class="container-fluid">
        <!--Shows the number of books on display-->
        <p style="color:  #99a1af; margin-top: 10px;"> Showing {{ $books->count() }} books</p>
    </div>

    <!--Search results-->
    <div class="container-fluid list-group list-group-flush" id="display-search-results" style="display: none;">
        
    </div>

    <!--Display books-->
    <div class="container-fluid" id="books-display">
        @forelse ($books as $book)
        <!--Book Card-->
                <div class="col-lg-2 col-md-4 col-sm-6 col-6 book-card">
                    <a href="{{ route('books.show', $book->book_id) }}" style="text-decoration: none;">

                        <!--Card Image-->
                        <img class="card-img-top img-responsive rounded book-card__image"
                            src="{{ asset ('images/' .($book->img_url ?? 'default_book.png')) }}"
                            alt="{{ $book->title}} by {{ $book->author}}"
                        />

                        <!--Card Body-->
                        <div class="card-body ms-3 ms-sm-0">
                            <div style="display: flex;">
                                <p class="book-card__badge badge">{{$book->genre}}</p>
                                <div class="star-rating">
                                    @php
                                        $star_rating = round($book->reviews_avg_rating ?? 0);
                                    @endphp
                                    
                                    @for ($i=1; $i<= 5; $i++)
                                        <span>
                                            {{$i <= $star_rating ? '★' : '☆'}}
                                        </span>
                                    @endfor
                                    <span style="color: var(--text-primary);"> {{number_format($book->reviews_avg_rating, 1)}}</span>
                                </div>
                            </div>

                            <p class="book-card__title"> {{ $book->title}} </p>
                            <p class="book-card__author"> {{ $book->author}} </p>
                            <p class="book-card__description truncate_multi_line book-description"> {{$book->book_description}}  </p>
                            <p> Buy: <span class="book-card__price"> Rs {{ $book->price}} </span></p>
                            <p> Borrow (7 days): <span class="book-card__price_borrow"> Rs {{$book->rental_fee}} </span></p>
                            <p class="book-card__stock"> {{ $book->stock_num}} in stock </p>
                        </div>
                        
                        <!--Card Footer-->
                        <div class="mt-3">
                            <!--Submits book to cart to buy only if user is logged in-->
                            @if (session()-> has("logged_in"))
                                <form action="#" method="post">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$book->book_id}}">
                                    <input type="hidden" name="title" value="{{$book->title}}">
                                    <input type="hidden" name="price" value="{{$book->price}}">
                                    <input type="hidden" name="image" value="{{$book->img_url}}">
                                    <input type="hidden" name="author" value="{{$book->author}}">
                                    <input type="hidden" name="rent_or_buy" value="buy">
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="primary_btn">
                                        <i class="bi bi-cart-plus icons"> Buy </i>
                                    </button>
                                </form>
                            @else
                                <a href="#" class="btn primary_btn">
                                    <i class="bi bi-cart-plus icons"> Buy </i>
                                </a>
                            @endif

                            <!--Submits book to cart to rent only if user is logged in-->
                            @if (session()-> has("logged_in"))
                                <form action="#" method="post">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$book->id}}">
                                    <input type="hidden" name="title" value="{{$book->title}}">
                                    <input type="hidden" name="price" value="{{$book->rental_fee}}">
                                    <input type="hidden" name="image" value="{{$book->img_url}}">
                                    <input type="hidden" name="author" value="{{$book->author}}">
                                    <input type="hidden" name="rent_or_buy" value="rent">
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="secondary_btn">
                                        <i class="bi bi-calendar-week icons"> Borrow </i>
                                    </button>
                                </form>
                            @else
                                <a href="#" class="btn secondary_btn">
                                    <i class="bi bi-calendar-week icons"> Borrow </i>
                                </a>
                            @endif
                        </div>
                    </a>
                </div>

        @empty
            <h2 id= "hero-section__title" style="color: var(--text-primary);"> Sorry! No books found. </h2>
        
        @endforelse

        <!--Pagination Links-->
        {{ $books -> links() }}

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function() {

            //Handles search
            $("#txt_search").on("keyup", function() {
                let txt_search = $(this).val();

                if (txt_search.length < 2) {
                    $("#display-search-results").hide();
                    return;
                }

                $.ajax ({
                    url: "{{ route('api.books.search') }}",
                    type: "GET",
                    data: {txt_search: txt_search},
                    dataType: "json",
                    success: function(response) {

                        if (response.message == "error") {
                            $("#display-search-results")
                            .html("<h4> Book not found</h4>")
                            .fadeIn();
                            return;
                        }
                        $("#display-search-results").empty();

                        if (response.data.length === 0) {
                            $("#display-search-results")
                            .html("<h4> Book not found</h4>")
                            .fadeIn();
                            return;
                        }
                        else {

                            $.each(response.data, function(i, book) {
                                let result = `<a href="/books/${book.book_id}" class="list-group-item list-group-item-action search-result">
                                                <span class="book-title">${book.title}</span>
                                                by
                                                <span class="book-author">${book.author}</span>
                                            </a>`;
                                
                                $("#display-search-results").append(result);
                            });

                            $("#display-search-results").fadeIn();
                            $("#books-display").hide();
                        }
                    },
                    error: function(xhr){
                            alert("An error occured: " + xhr.status + " " + xhr.statusText);
                    }
                });
            });
        });
    </script>
@endsection

