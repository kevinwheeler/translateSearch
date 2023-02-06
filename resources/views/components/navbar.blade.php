@props(['homeUrl' => '/'])

<div class="container">
  {{-- <nav class="navbar">
    <a class="navbar-brand kmw-nav-brand" href="{{$homeUrl}}">Translate Search</a>
  </nav> --}}

  <nav class="navbar-custom navbar navbar-expand-lg mb-4">
    <a class="navbar-brand kmw-nav-brand" href="{{$homeUrl}}">Translate Search</a>
    <button class="custom-toggler navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="{{$homeUrl}}">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/about">About</a>
        </li>
      </ul>
    </div>
  </nav>

</div>