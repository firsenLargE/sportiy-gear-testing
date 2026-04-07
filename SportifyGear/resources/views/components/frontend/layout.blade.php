<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Shop' }}</title>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #C9742C;
        }
    </style>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
    <x-frontend.topbar />
    <x-frontend.navbar />
    <main>
        {{ $slot }}
    </main>

    <x-frontend.footer />

</body>

</html>
