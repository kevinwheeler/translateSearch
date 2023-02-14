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
      google.search.cse.element.render({div: "gResults1", tag: "searchresults-only", gname: "gname1", attributes:{defaultToImageSearch: true, mobileLayout: "forced"} });
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

 let gResultsElSizes = {};

 const imageResultsRenderedCallback = function(gname, query) {
  // If we hid these elements while waiting for a user to solve a captcha,
  // go ahead and show them again.
  // $("[id^='gResults']").show()
  $(".gResultsContainer").show()
  $("nav:last-of-type").show();


  var elNumber =  gname.replace('gname','');
  elNumber = parseInt(elNumber, 10);


  let el = document.querySelector(`#gResults${elNumber}`);
  new ResizeObserver(function(entries){
    let el = entries[0].target;
    let height = el.offsetHeight;
    if (!gResultsElSizes.hasOwnProperty(elNumber)) {
      gResultsElSizes[elNumber] = height;
    } else {
      if (height < gResultsElSizes[elNumber]) {
        // If the new height of the element is less than the old height of the element,
        // then the element is smaller because Google has removed the other image results
        // and maximized a single image result that the user clicked on. This can mess up
        // the user's scroll position and make it jump. So, we'll scroll the user back to
        // where they should be.


      // We are forcing mobile layout when rendering the element because Google was messing up scrolling as 
      // noted elsewhere in this file. Forcing mobile is necessary because it allows us to just scroll el into
      // view when a resize makes el smaller. When on desktop, sometimes el would get smaller and we wouldn't
      // want to scroll el into view, because the amount of scroll was already correct.

        let currentScrollPosition = window.scrollY;
        let offsetFromEl = currentScrollPosition

        // Scroll to the top of el.
        el.scrollIntoView();
          
      }      gResultsElSizes[elNumber] = height;
    }
  }).observe(el);

  var nextElNumber = elNumber + 1;

  var numTranslations = translations.length;
  
  // If the image results that just got rendered aren't the last image search results
  // that need to be rendered, render the next one.
  if (elNumber < numTranslations) {
   // wait a little bit before rendering the next set of Google Image search results,
   // so that Google doesn't rate limit us. Not sure this matters.
   setTimeout(function() {
        google.search.cse.element.render({div: "gResults"+nextElNumber, tag: "searchresults-only", gname: "gname"+nextElNumber, attributes:{defaultToImageSearch: true, mobileLayout: "forced"}});
     //  }, 1100);
      }, 500);
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