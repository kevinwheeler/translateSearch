
<div id="recaptcha" class="g-recaptcha"
    data-sitekey="6Ld2FkEkAAAAAEhvtS6WWLaBqemOc693wkN6qS0W"          
    data-callback="afterCaptcha"                
    data-size="invisible">                  
</div>

<script>
        const afterCaptcha = function(token) {
          let href = window.kmwPaginationHref;
          href += "&g-recaptcha-response=" + token;
          window.location.href = href;
        };

        const beforeCaptcha = function(event) {
          event.preventDefault();
          window.kmwPaginationHref = event.target.getAttribute('href');
          grecaptcha.execute();
        };

        onRecaptchaLoad = function() {
          grecaptcha.render('recaptcha', {
            'sitekey' : '6Ld2FkEkAAAAAEhvtS6WWLaBqemOc693wkN6qS0W',
            'callback' : afterCaptcha
          });

          const nodeList = document.querySelectorAll("a.page-link");
          nodeList.forEach(function(node) {
            node.addEventListener('click', beforeCaptcha);
          })
        };
</script>


<script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit" async defer></script>
