<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

        <title>Translate Search</title>
        <script>
          window.translations = @json(array_values($languagesAndTranslations->items()));
        </script>
        @vite(['resources/css/app.css', 'resources/js/googleStuff.js'])
        <script defer src="https://cse.google.com/cse.js?cx=c6e7ebcdec9ef45cb"></script>
    </head>
    <body>
    <nav>
      <a href="/" style="color: blue;font-weight: bold">Home</a>
    </nav>
    <br>
    @foreach($languagesAndTranslations as $language => $translation)
      <div class="gResultsContainer">
        LANGUAGE = {{$language}}
        QUERY = {{$translation}}
        <div id="gResults{{$loop->iteration}}" class="gcse-searchresults-only" data-gname="gname{{$loop->iteration}}" data-defaultToImageSearch="true" enableImageSearch="true" defaultToImageSearch="true"></div>
      </div>
    @endforeach

    {!! $languagesAndTranslations->withQueryString()->links() !!}
    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Google search failed to load.</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Google search failed to load. This can happen for a number of reasons including
            that Google may be rate limiting you due to too many 
            requests. This can happen when Google sends you a captcha / tells you to
            prove you are a human and you don't do it. In this case, everything should
            start working again within a couple of hours (or you can use a VPN to bypass
            the problem immediately).
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Okay</button>
            {{-- <button type="button" class="btn btn-primary">Understood</button> --}}
          </div>
        </div>
      </div>
    </div>

   </body>
</html>
