  <!--  None of this code seems to be necessary anymore. Google seems to send the captcha every time -->
   <!-- and keep giving you a chance to fill it out instead of just locking you out. -->
<div class="modal hide fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Google search failed to load.</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Google search failed to load. This can happen for a number of reasons including
        that Google may be rate limiting you due to too many 
        requests. This can happen when Google sends you a captcha / tells you to
        prove you are a human and you don't do it. In this case, everything should
        start working again within a couple of hours (or you can use a VPN to bypass
        the problem immediately).
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Okay</button>
        {{-- <button type="button" class="btn btn-primary">Understood</button> --}}
      </div>
    </div>
  </div>
</div>
<script>
  
  // When Google's search fails to load, call this function
  var onGSearchLoadError = _.throttle(function() {
     $("#staticBackdrop").modal("show");
     console.log("i should never be in here");
   },
   5000, {leading: true, trailing: false}
  );
  
  window.addEventListener('error', ((e) => {
   if (e.target.src.startsWith("https://cse.google.com")){
     onGSearchLoadError();
   }
  }), true)
</script>