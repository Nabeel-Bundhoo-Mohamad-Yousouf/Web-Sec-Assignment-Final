<header class="sticky-top navbar-expand-lg">
    <div class="container-fluid">

        <!--Logo-->
        <a href="{{ route('books.index') }}" class="logo">{{config("app.name")}}</a>

        <!--Links-->
        <nav class="navbar">
            <ul>
                <li><a href="{{ route('books.index') }}">about us</a></li>
                <li><a href="{{ route('books.index') }}">contact us</a></li>
            </ul>
        </nav>

        <!--Icons-->
        <div class="icons">
            <a href="{{ route('books.index') }}" class="button"> <i class="bi bi-person"> </i></a>
            <a href="{{ route('books.index') }}" class="button"> <i class="bi bi-cart2"> </i></a>
        </div>
    </div>
    
</header>
