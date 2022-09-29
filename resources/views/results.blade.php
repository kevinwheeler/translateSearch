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
            console.log("rendering initial search element inside myInitializiationCallabck")
          //  {{--  google.search.cse.element.render({div: "gResults{{$loop->iteration}}", tag: "searchresults-only", gname: "gname{{$loop->iteration}}", attributes:{defaultToImageSearch: true} }); --}}
            google.search.cse.element.render({div: "gResults1", tag: "searchresults-only", gname: "gname1", attributes:{defaultToImageSearch: true} });
            var el = google.search.cse.element.getElement("gname1");
            el.execute("I don't know why adding this line of code fixes things");
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
        

        var items = @json(array_values($languagesAndTranslations->items()));
        console.log("items = ",items);
        return items[elNumber-1];
      }
       const myImageResultsReadyCallback = function() { console.log("in myImageResultsReadyCallback")}

       const myImageResultsRenderedCallback = function(gname, query) { 

        console.log("in myImageResultsRenderedCallback")
         var elNumber =  gname.replace('gname','');
         elNumber = parseInt(elNumber, 10);
         var nextElNumber = elNumber + 1;

        var translations = @json(array_values($languagesAndTranslations->items()));
         
         if (elNumber < translations.length) {
          //  var nextElToRender = document.querySelector("[data-gname='gname" + (elNumber+1) +"']");
          setTimeout(function() {
               google.search.cse.element.render({div: "gResults"+nextElNumber, tag: "searchresults-only", gname: "gname"+nextElNumber, attributes:{defaultToImageSearch: true} });
             }, 500);
         }
      }
       const myWebSearchStartingCallback = function() { console.log("in myWebSearchStartingCallback")}
       const myWebResultsReadyCallback = function() { console.log("in myWebResultsReadyCallback")}
       const myWebResultsRenderedCallback = function() {console.log("in myWebResultsRenderedCallback");}


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
