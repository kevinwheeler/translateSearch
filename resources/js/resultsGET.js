// JS file for the reults page from a GET request
window.initiateCaptcha = function(token) {
    console.log("in after captcha");
    let href = window.location.pathname + window.location.search;
    let postBodyPameters = {
        "g-recaptcha-response": token,
        "_token": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
       
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