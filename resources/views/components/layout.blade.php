
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-CSM44133HX"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
    
          gtag('config', 'G-CSM44133HX');
        </script>

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