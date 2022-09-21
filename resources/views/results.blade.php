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
    <script>
      const myInitializationCallback = function() {
        console.log("z");
        const doTheThing = function() {
          var el;
          @foreach($translations as $translation)
            google.search.cse.element.render({div: "gResults{{$loop->iteration}}", defaultToImageSearch: true, defaulttoimagesearch: true, tag: "searchresults-only", gname: "gname{{$loop->iteration}}"});
            el = google.search.cse.element.getElement('gname{{$loop->iteration}}');
            console.log(el.uiOptions)
            el.execute("{{$translation}}");
          @endforeach
        }
        if (document.readyState == 'complete') {
          // Document is ready when Search Element is initialized.
          doTheThing();
        } else {
          // Document is not ready yet, when Search Element is initialized.
          google.setOnLoadCallback(doTheThing, true);
        }
      };

       //myInitializationCallback = function() {console.log("z");}
       const myImageSearchStartingCallback = function() {console.log("a")}
       const myImageResultsReadyCallback = function() { console.log("b")}
       const myImageResultsRenderedCallback = function() { console.log("c")}
       const myWebSearchStartingCallback = function() { console.log("d")}
       const myWebResultsReadyCallback = function() { console.log("e")}
       const myWebResultsRenderedCallback = function(gname, query, promoElts, resultElts) {
         var el = document.querySelector(`[data-gname='${gname}']`)
         el = el.querySelector(".gsc-tabhInactive");
         console.log("el = ");
         console.log(el);
         el.click();
        }


      // Insert it before the Search Element code snippet so the global properties like parsetags and callback
      // are available when cse.js runs.
      window.__gcse = {
        parsetags: 'explicit', // Defaults to 'onload'
        initializationCallback: myInitializationCallback,
        searchCallbacks: {
          image: {
            starting: myImageSearchStartingCallback,
            ready: myImageResultsReadyCallback,
            rendered: myImageResultsRenderedCallback,
          },
          web: {
            starting: myWebSearchStartingCallback,
            ready: myWebResultsReadyCallback,
            rendered: myWebResultsRenderedCallback,
          },
        },
      };

    </script>
    @foreach($translations as $translation)
      QUERY = {{$translation}}
      <div id="gResults{{$loop->iteration}}" class="gcse-searchresults-only" data-gname="gname{{$loop->iteration}}" data-defaultToImageSearch="true" ></div>
    @endforeach
    <script>
    </script>
    </body>
</html>
