// JS file for the reults page from a GET request
window.initiateCaptcha = function(token) {
    console.log("in after captcha");
    let href = window.location.pathname + window.location.search;
    let postBodyPameters = {
        "g-recaptcha-response": token,
        "_token": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
       
    // If the user later presses the back button, we don't want them to return to this page
    // and then resubmit the form and end up back where they started, so we'll just replace
    // the current URL with our homepage, so that they go there instead.
    window.history.replaceState({},"","/"); 
    window.kmwPost(href, postBodyPameters);
    return false; // not sure this does anything
};

window.onRecaptchaLoad = function() {
  grecaptcha.render('recaptcha', {
    'sitekey' : '6Ld2FkEkAAAAAEhvtS6WWLaBqemOc693wkN6qS0W',
    'callback' : initiateCaptcha,
  });
  grecaptcha.execute();
};
