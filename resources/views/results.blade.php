<x-layout>
    <x-slot name="headSlot">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
      @vite(['resources/js/resultsPageRecaptcha.js', 'resources/js/helpers.js'])
      <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit" async defer></script>
      <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <script>
        window.translations = @json(array_values($languagesAndTranslations->items()));
      </script>
      @vite(['resources/js/googleStuff.js'])
      <script defer src="https://cse.google.com/cse.js?cx=c6e7ebcdec9ef45cb"></script>
    </x-slot>

    <x-navbar/>
    @foreach($languagesAndTranslations as $language => $translation)
      <div class="gResultsContainer">
        LANGUAGE = {{$language}}
        QUERY = {{$translation}}
        <div id="gResults{{$loop->iteration}}" class="gcse-searchresults-only" data-gname="gname{{$loop->iteration}}" data-defaultToImageSearch="true" enableImageSearch="true" defaultToImageSearch="true"></div>
      </div>
    @endforeach

    {!! $languagesAndTranslations->withQueryString()->links() !!}
    <x-pagination-captcha/>
    
  <x-alert-modal/>
</x-layout>