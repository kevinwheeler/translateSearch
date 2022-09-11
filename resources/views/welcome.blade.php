<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Translate Search</title>
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
        <style type="text/css" media="screen">
            iframe {
                width: 100%;
             }
            #kmw1 {
                position: static !important;
             display: block !important:
             }

            #kmw2 {
                position: static !important;
            display: block !important:
            }
        </style>
        <script async src="https://cse.google.com/cse.js?cx=c6e7ebcdec9ef45cb"></script>
    </head>
    <body>
      <form action="/results" method="GET">
        <label for="queryInput"></label>
        <input type="text" name="query", id="queryInput">
        <button>Submit</button>
      </form>
    </body>
</html>
