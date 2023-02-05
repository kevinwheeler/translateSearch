// addEventListener('DOMContentLoaded', (event) => {
//     //intercept link clicks and 
//     document.querySelectorAll("a.page-link").foreEach(function(link){

//     })
// });

const afterCaptcha = function(token) {
    console.log("in after captcha");
  let href = window.kmwPaginationHref;
  href += "&g-recaptcha-response=" + token;
  let postBodyPameters = {
      "g-recaptcha-response": token,
      "_token": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
  };
     
  window.kmwPost(href, postBodyPameters);
  // You must return false to prevent the default form behavior
  return false;
};

const beforeCaptcha = function(event) {
  console.log("in before captcha");
  event.preventDefault();
  window.kmwPaginationHref = event.target.getAttribute('href');
  grecaptcha.execute();
};

window.onRecaptchaLoad = function() {
  grecaptcha.render('recaptcha', {
    'sitekey' : '6Ld2FkEkAAAAAEhvtS6WWLaBqemOc693wkN6qS0W',
    'callback' : afterCaptcha
  });

  console.log("in on recaptcha load");
  const nodeList = document.querySelectorAll("a.page-link");
  nodeList.forEach(function(node) {
    node.addEventListener('click', beforeCaptcha);
  })
};