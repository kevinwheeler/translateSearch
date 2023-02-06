
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Translate Search</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Arizonia">
        @vite(['resources/css/app.css'])
        {{$headSlot}}
    </head>
    <body>
        {{$slot}}
    </body>
</html>