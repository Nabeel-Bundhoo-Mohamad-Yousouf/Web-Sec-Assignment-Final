@extends("layout.default")

@section("main_content")
    <div class="container-fluid preview_container">
        <p class="preview-title">Preview</p>
        <div class="row justify-content-center preview_grid">

            <!-- Image + Buttons -->
            <div class="col-lg-5 col-md-6 col-sm-12 flex-column align-items-center ">

                <img class="card-img-top img-responsive rounded book-card__image" 
                    src="{{ asset ('images/' . ($book->img_url ?? 'default_book.png')) }}"
                    alt="{{ $book->title}} by {{ $book->author}}"
                />

                <!--Display buy and rental fees-->
                <div class="btn-tabs-container pt-3 justify-content-start">
                    <div class="btn-tabs" id="tab-btn" role="tablist">

                        <!-- Buy Tab -->
                        <button type="button" class="tab_btn active" id="buy_tab"
                            data-bs-toggle="tab" data-bs-target="#buy" role="tab"
                            aria-controls="buy" aria-selected="true">
                            <span style=" font-weight: var(--font-weight-normal);">Buy </span>
                            &nbsp;
                            <span class="book-card__price">Rs {{ $book->price}} </span>
                        </button>

                        <!-- Rent Tab -->
                        <button type="button" class="tab_btn" id="borrow_tab"
                            data-bs-toggle="tab" data-bs-target="#borrow" role="tab"
                            aria-controls="borrow" aria-selected="false">
                            <span style=" font-weight: var(--font-weight-normal);">Rent </span>
                            &nbsp;
                            <span class="book-card__price_borrow">Rs {{$book->rental_fee}} </span>
                        </button>
                    </div>
                </div>

                <div class="tab-content" id="tab_content">
                    <!-- Buy Content -->
                    <div class="tab-pane fade show active p-3" id="buy" role="tabpanel"
                        aria-labelledby="buy_tab">

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
                            <button type="submit" class="button add_to_cart_btn w-70 align-center">
                                + Add to cart
                            </button>
                        </form>
                        @else
                            <a href="#" class="btn primary_btn">
                                <i class="bi bi-cart-plus icons"> + Add to cart </i>
                            </a>
                        @endif
                    </div>

                    <!-- Rent Content -->
                    <div class="tab-pane fade p-3" id="borrow" role="tabpanel"
                        aria-labelledby="borrow_tab">

                        <!--Submits book to cart to rent only if user is logged in-->
                        @if (session()-> has("logged_in"))
                        <form action="#" method="post">
                            @csrf
                            <input type="hidden" name="id" value="{{$book->id}}">
                            <input type="hidden" name="title" value="{{$book->title}}">
                            <input type="hidden" name="price" value="{{$book->price}}">
                            <input type="hidden" name="image" value="{{$book->img_url}}">
                            <input type="hidden" name="author" value="{{$book->author}}">
                            <input type="hidden" name="rent_or_buy" value="rent">
                            <input type="hidden" name="qty" value="1">
                            <button type="submit" class="button add_to_cart_btn w-70 align-center">
                                + Add to cart
                            </button>
                        </form>
                        @else
                            <a href="#" class="btn primary_btn">
                                <i class="bi bi-cart-plus icons"> + Add to cart </i>
                            </a>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Book Info -->
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="preview-card__title">{{ $book->title}}</p>
                <p class="preview-card__author">By {{ $book->author}}</p>

                <!--Star ratings-->
                <div class="star-rating">
                    @php
                        $star_rating = round($book->reviews_avg_rating ?? 0);
                    @endphp
                    
                    @for ($i=1; $i<= 5; $i++)
                        <span>
                            {{$i <= $star_rating ? '★' : '☆'}}
                        </span>
                    @endfor
                    <span style="color: var(--text-primary);"> {{$book->reviews_avg_rating}}</span>
                </div>
                
                <p class="preview-card__description">
                    {{$book->book_description}}
                </p>
            </div>
        </div>
    </div>

    <!--Customer Reviews-->
    <div class="container-fluid preview_container align-items-center pt-3 py-5">
        <hr class="footer_divider">

        <!--Reviews Header-->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0 preview-title">Customer Reviews</h4>

            <!-- Write Review btn -->
            <a href="#" class="btn primary_btn review_btn"> Write a review </a>
        </div>

        <!--User review cards-->
        <div id="carousel_controls" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner pt-3 pb-3">

                @forelse ($book-> reviews as $review)
                <!--Review Card-->
                <div class="carousel-item active">
                     <div class="review-card mx-auto">

                        <!--User star ratings-->
                        <div class="star-rating">
                            @php
                                $star_rating = floor($review->rating);
                            @endphp
                            
                            @for ($i=1; $i<= 5; $i++)
                                <span>
                                    {{$i <= $star_rating ? '★' : '☆'}}
                                </span>
                            @endfor
                        </div>

                        <!--Review card body-->
                        <p style="font-weight: 600; margin-bottom: 5px; color: #777;">
                            Print "customer_name" " • " "date"
                        </p>
                        <p class="review-title">{{ $review->title}}</p>
                        <p class="review-text truncate_multi_line">
                            {{ $review->review_description}}
                        </p>
                    </div>
                </div>
                @empty
                    <h4 class="mb-3 text-muted">No reviews yet.</h4>
                @endforelse
            </div>

            <!--Carousel Controls-->
            <button type="btn" class="carousel-control-prev" data-bs-target="#carousel_controls" role="btn" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </btn>
            <button type="btn" class="carousel-control-next" data-bs-target="#carousel_controls" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </btn>
        </div>
    </div>

@endsection
