<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <style>
            body {
                font-family: 'Nunito', sans-serif;
                background-color: #f8fafc;
            }
            .welcome-section {
                padding: 100px 0;
                text-align: center;
            }
            .btn-custom {
                margin: 10px;
                padding: 10px 30px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="welcome-section">
                <h1 class="display-4">Welcome to {{ config('app.name', 'Laravel') }}</h1>
                <p class="lead">Educational Management System</p>

                @if (Route::has('login'))
                    <div class="text-center mt-4">
                        @auth
                            <a href="{{ url('/home') }}" class="btn btn-primary btn-custom">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-custom">Log in</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-success btn-custom">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
