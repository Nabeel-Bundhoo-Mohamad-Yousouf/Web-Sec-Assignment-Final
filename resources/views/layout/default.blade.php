<!DOCTYPE html>
<html lang="en">
<head>
    <title>Buy & Rent Books Online | {{config("app.name")}}</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name = "description" content="Buy & Rent books online at affordable prices. Fast delivery and best customer experience.">
    <meta name = "robots" content="index, follow">

    @vite(["resources/css/header.css",
        "resources/css/app.css",
        "resources/css/footer.css"
        ])
        
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <!--Include header.php-->
    @include("layout.header")
    
    <main>
        <!--Page content goes here.-->
        @yield ("main_content")
    </main>
    
    <!--Conditonal Footer Inclusion-->
    @if (Route::is("index") || Route::is("book_details") || Route::is("about") || Route::is("contact"))
        @include("layout.footer")
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-4.0.0.min.js" integrity="sha256-OaVG6prZf4v69dPg6PhVattBXkcOWQB62pdZ3ORyrao=" crossorigin="anonymous"></script>
</body>

</html>
