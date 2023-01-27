const googleInitializationCallback = function() {
  console.log("in outer initialize");
  const initialize = function() {
    console.log("in initialize");
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
    console.log("in image search starting");
  var el = google.search.cse.element.getElement(gname);
  //grab the number at the end of the html element's attribute data-gname="gname{x}"
  var elNumber =  gname.replace('gname','');
  elNumber = parseInt(elNumber, 10);

  //change the search query of this google search element to be 
  // the translation we want to google search
//*********** *** */
//   var translations = @json(array_values($languagesAndTranslations->items()));
  return translations[elNumber-1];
}
 const imageResultsRenderedCallback = function(gname, query) {
  // If we hid these elements while waiting for a user to solve a captcha,
  // go ahead and show them again.
  // $("[id^='gResults']").show()
  $(".gResultsContainer").show()
  $("nav:last-of-type").show();

  console.log("in image results rendered");

   var elNumber =  gname.replace('gname','');
   elNumber = parseInt(elNumber, 10);
   var nextElNumber = elNumber + 1;

   var numTranslations = translations.length;
   
   // If the image results that just got rendered aren't the last image search results
   // that need to be rendered, render the next one.
   if (elNumber < numTranslations) {
    // wait a little bit before rendering the next set of Google Image search results,
    // so that Google doesn't rate limit us.
    setTimeout(function() {
    console.log("in setTimeout");
         google.search.cse.element.render({div: "gResults"+nextElNumber, tag: "searchresults-only", gname: "gname"+nextElNumber, attributes:{defaultToImageSearch: true} });
      //  }, 1100);
       }, 1);
   }
}

 // We don't use/need these callbacks, but here they are in case you want to add
 // anything to them.
 const imageResultsReadyCallback = function() {console.log("in image results ready")}
 const webSearchStartingCallback = function() {console.log("in web search starting")}
 const webResultsReadyCallback = function() {console.log("in web results ready")}
 const webResultsRenderedCallback = function() {console.log("in web results rendered")}

console.log("about to set up gcse element")
//Do this before running the (external) Google cse script.
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