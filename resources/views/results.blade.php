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
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script async src="https://cse.google.com/cse.js?cx=c6e7ebcdec9ef45cb"></script>
    </head>
    <body>
    <script>
      // const googleInitializationCallback = function() {}
      const googleInitializationCallback = function() {
        const initialize = function() {
            google.search.cse.element.render({div: "gResults1", tag: "searchresults-only", gname: "gname1", attributes:{defaultToImageSearch: true} });
            var el = google.search.cse.element.getElement("gname1");
            //For some reason adding this execute line fixes everything.
            // My guess is that elements won't render unless the URL in the URL bar has
            // gsc parameter / a search query set. We'll just set this to "." since we are
            // going to update the search query 
            el.execute(".");
        }
        if (document.readyState == 'complete') {
          // Document is ready when Search Element is initialized.
          initialize();
        } else {
          // Document is not ready yet, when Search Element is initialized.
          google.setOnLoadCallback(initialize, true);
        }
      };

       // Each instance of a return value from this function will end up being the
       // search query that our image search uses. This is the one and only place
       // that we set the search query (not counting our el.execute(".") stub query).
       const imageSearchStartingCallback = function(gname, query) {
        var el = google.search.cse.element.getElement(gname);
        //grab the number at the end of the html element's attribute data-gname="gname{x}"
        var elNumber =  gname.replace('gname','');
        elNumber = parseInt(elNumber, 10);

        //change the search query of this google search element to be 
        // the translation we want to google search
        var translations = @json(array_values($languagesAndTranslations->items()));
        return translations[elNumber-1];
      }
       const imageResultsRenderedCallback = function(gname, query) { 

         var elNumber =  gname.replace('gname','');
         elNumber = parseInt(elNumber, 10);
         var nextElNumber = elNumber + 1;

        var numTranslations = @json(array_values($languagesAndTranslations->items())).length;
         
         // If the image results that just got rendered aren't the last image search results
         // that need to be rendered, render the next one.
         if (elNumber < numTranslations) {
          // wait a little bit before rendering the next set of Google Image search results,
          // so that Google doesn't rate limit us.
          setTimeout(function() {
               google.search.cse.element.render({div: "gResults"+nextElNumber, tag: "searchresults-only", gname: "gname"+nextElNumber, attributes:{defaultToImageSearch: true} });
             }, 500);
         }
      }

       // We don't use/need these callbacks, but here they are in case you want to add
       // anything to them.
       const imageResultsReadyCallback = function() {}
       const webSearchStartingCallback = function() {}
       const webResultsReadyCallback = function() {}
       const webResultsRenderedCallback = function() {}


      // Insert it before the Search Element code snippet so the global properties like parsetags and callback
      // are available when cse.js runs.
      window.__gcse = {
        parsetags: 'explicit', // Defaults to 'onload'
        initializationCallback: googleInitializationCallback,
        searchCallbacks: {
          image: {
            starting: imageSearchStartingCallback,
            ready: imageResultsReadyCallback,
            rendered: imageResultsRenderedCallback,
          },
          // We don't use/need these callbacks, but here they are if you want to add
          // anything to them.
          web: {
            starting: webSearchStartingCallback,
            ready: webResultsReadyCallback,
            rendered: webResultsRenderedCallback,
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
