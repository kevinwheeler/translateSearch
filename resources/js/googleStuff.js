// import './handleGoogleError';
import _ from 'lodash';

// If a captcha element has been added to the DOM, hide most of the page
// except for the captcha, so the user will see the captcha. Google sends 
// a captcha from time to time, and if the user doesn't solve it,
// Google will rate limit them, so we want the user to see it front and center.
function mutationCallback(mutationList, observer) {
 mutationList.forEach((mutation) => {
   if (mutation.target.id === "recaptcha-element") {
     $("nav:last-of-type").hide();
     // $("[id^='gResultsContainer']").hide();
     $(".gResultsContainer").hide();
     const closestgResultsContainer = mutation.target.closest(".gResultsContainer");
     $(closestgResultsContainer).show()
   }
 });
}

const observer = new MutationObserver(mutationCallback);
const elementToObserve = document.querySelector("html");
observer.observe(elementToObserve, {subtree: true, childList: true});

//When Google's search fails to load, call this function
var onGSearchLoadError = _.throttle(function() {
   $("#staticBackdrop").modal("show");
 },
 5000, {leading: true, trailing: false}
);

window.addEventListener('error', ((e) => {
 if (e.target.src.startsWith("https://cse.google.com")){
   onGSearchLoadError();
 }
}), true)


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
 const searchStartingCallback = function(gname, query) {
  var el = google.search.cse.element.getElement(gname);
  //grab the number at the end of the html element's attribute data-gname="gname{x}"
  var elNumber =  gname.replace('gname','');
  elNumber = parseInt(elNumber, 10);

  return translations[elNumber-1];
 }

 const imageResultsRenderedCallback = function(gname, query) {
  // If we hid these elements while waiting for a user to solve a captcha,
  // go ahead and show them again.
  // $("[id^='gResults']").show()
  $(".gResultsContainer").show()
  $("nav:last-of-type").show();

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
         google.search.cse.element.render({div: "gResults"+nextElNumber, tag: "searchresults-only", gname: "gname"+nextElNumber, attributes:{defaultToImageSearch: true} });
      //  }, 1100);
       }, 1);
   }
}
 const imageResultsReadyCallback = function() {}
 const webSearchStartingCallback = function() {}
 const webResultsReadyCallback = function() {}
 const webResultsRenderedCallback = function() {}

//Do this before running the (external) Google cse script.
window.__gcse = {
  parsetags: 'explicit', // Defaults to 'onload'
  initializationCallback: googleInitializationCallback,
  searchCallbacks: {
    image: {
      starting: searchStartingCallback,
      ready: imageResultsReadyCallback,
      rendered: imageResultsRenderedCallback,
    },
    web: {
      starting: searchStartingCallback,
      ready: webResultsReadyCallback,
      rendered: webResultsRenderedCallback,
    },
  },
};