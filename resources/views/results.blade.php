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
      // const myInitializationCallback = function() {}
      const myInitializationCallback = function() {
        console.log("in myInitializationCallback");
        const doTheThing = function() {
          var el;
          @foreach($translations as $translation)
            console.log("in foreach. rendering element inside myInitializiationCallabck")
            google.search.cse.element.render({div: "gResults{{$loop->iteration}}", tag: "searchresults-only", gname: "gname{{$loop->iteration}}", attributes:{defaultToImageSearch: true} });
            el = google.search.cse.element.getElement('gname{{$loop->iteration}}');
            console.log("in foreach. ui options = ", el.uiOptions);
            el.uiOptions["defaultToImageSearch"] ="true";
            console.log("in foreach. executing element inside myInitializiationCallabck")
            // el.execute("{{$translation}}");
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
       const myImageSearchStartingCallback = function(gname, query) {
        var el = google.search.cse.element.getElement(gname);
        var elNumber =  gname.replace('gname','');
        elNumber = parseInt(elNumber, 10);
        console.log("el number = " + elNumber);
        console.log("in myImageSearchStartingCallback. el = ", el, "gname = " + gname, "query = " + query);
        

        var items = @json($translations->items());
        console.log("items = ",items);
        return items[elNumber-1];
      }
       const myImageResultsReadyCallback = function() { console.log("in myImageResultsReadyCallback")}
       const myImageResultsRenderedCallback = function() { console.log("in myImageResultsRenderedCallback")}
       const myWebSearchStartingCallback = function() { console.log("in myWebSearchStartingCallback")}
       const myWebResultsReadyCallback = function() { console.log("in myWebResultsReadyCallback")}
       const myWebResultsRenderedCallback = function(gname, query, promoElts, resultElts) {
         var el = document.querySelector(`[data-gname='${gname}']`)
         el = el.querySelector(".gsc-tabhInactive");
         console.log("in myWebResultsRenderedCallback. gname = " + gname + "el = ", el);
         
        //  el.click();
        }


      // Insert it before the Search Element code snippet so the global properties like parsetags and callback
      // are available when cse.js runs.
      window.__gcse = {
        parsetags: 'explicit', // Defaults to 'onload'
        // parsetags: 'onload', // Defaults to 'onload'
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
      <div id="gResults{{$loop->iteration}}" class="gcse-searchresults-only" data-gname="gname{{$loop->iteration}}" data-defaultToImageSearch="true" enableImageSearch="true" defaultToImageSearch="true"></div>
    @endforeach

    {!! $translations->withQueryString()->links() !!}
    <script>
    </script>
    </body>
</html>
