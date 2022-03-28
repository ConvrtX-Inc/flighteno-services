<html>
  <head>
    <title>Verify your identity</title>
    <script src="https://js.stripe.com/v3/"></script>
  </head>
  <body>
    <button id="verify-button">Verify</button>

    <script type="text/javascript">
      var verifyButton = document.getElementById('verify-button');

      verifyButton.addEventListener('click', function() {
        // Get the VerificationSession client secret using the server-side
        // endpoint you created in step 3.
        fetch('<?php echo SURL;?>index.php/Rest_calls/createverificationsession', {
          method: 'POST',
        })
        .then(function(response) {
          return response.json();
        })
        .then(function(session) {
          // When the user clicks on the button, redirect to the session URL.
          console.log(session.url);
          //window.location.href = session.url;
        })
        .catch(function(error) {
          console.error('Error:', error);
        });
      });
    </script>
  </body>
</html>