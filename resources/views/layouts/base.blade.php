<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('js/select2/select2.min.css') }}">
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.js') }}"></script>
    <style>
        .light-theme {
            background-color: #f8f9fa; /* bg-light */
            color: #000; /* text-black */
        }

        .dark-theme {
            background-color: #000; /* bg-black */
            color: #fff; /* text-white */
        }
        .theme-global{
            background-color: #f8f9fa; /* bg-light */
            color: #000; /* text-black */
        }

    </style>
    <title>CRUD laravel 10</title>
</head>
<body class="theme-global">
<div class="container">
    @yield('content')
</div>




@yield('js')

</body>
</html>

<!-- Esta es una plantilla base en la que se define una
estructura y un diseño comun para tu sitio web, ya sea
header, menu, footer o lo que se vaya a usar en todo
el sitio web y es enlazado al resto de paginas -->
