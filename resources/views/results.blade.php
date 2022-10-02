<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://cdn.tailwindcss.com"></script>

        <title>Translate Search</title>
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
        <script>
          window.translations = @json(array_values($languagesAndTranslations->items()));
        </script>
        @vite(['resources/css/app.css', 'resources/js/googleStuff.js'])
        <script async src="https://cse.google.com/cse.js?cx=c6e7ebcdec9ef45cb"></script>
    </head>
    <body>
    @foreach($languagesAndTranslations as $language => $translation)
      LANGUAGE = {{$language}}
      QUERY = {{$translation}}
      <div id="gResults{{$loop->iteration}}" class="gcse-searchresults-only" data-gname="gname{{$loop->iteration}}" data-defaultToImageSearch="true" enableImageSearch="true" defaultToImageSearch="true"></div>
    @endforeach

    {!! $languagesAndTranslations->withQueryString()->links() !!}
    <script>
    </script>
    </body>
</html>
