<x-layout>
    <x-slot name="headSlot">
      {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous"> --}}
      @vite(['resources/js/helpers.js', 'resources/js/resultsGET.js'])
      <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit" defer></script>
      {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script> --}}
      <meta name="csrf-token" content="{{ csrf_token() }}" />
      
      <title>Translated Image Search Results</title>
      <meta name="description" content="Image search results for your translated search query." />
      <meta name="robots" content="noindex,nofollow">
    </x-slot>

    Your translated image search results will load shortly.
    <x-pagination-captcha/> {{-- TODO rename this component since we aren't using it only in the pagination case --}}
    
  </x-layout>