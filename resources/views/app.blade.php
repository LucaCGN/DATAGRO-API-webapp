
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datagro Comercial Team Web Application</title>
    @livewireStyles
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <header>
        <!-- Header content goes here -->
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer>
        <!-- Footer content goes here -->
    </footer>

    @livewireScripts
</body>
</html>
