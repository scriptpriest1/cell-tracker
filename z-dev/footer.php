</div> <!-- Close container-fluid -->
  
  <!-- Bootstrap javascript CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- <script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-3gJwYp4g3sm7oJpP2ZQ2TqF3vZ2FGmSytI0ZjU52F7A="
    crossorigin="anonymous"
  ></script> -->

  <!-- JQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-3gJwYp4g3sm7oJpP2ZQ2TqF3vZ2FGmSytI0ZjU52F7A=" crossorigin="anonymous"></script> -->

  <!-- Javascript File -->
  <?php if ($isLoggedIn) {
    echo '
      <script src="../script.js"></script>
    ';
  } ?>

  <script>
    $(document).ready(() => {
    const successMsg = $('#success-msg');
    const errMsg = $('#err-msg');

    //Post Login Details to the Backend
    $('#login-form').on('submit', (e) => {
      e.preventDefault();

      //if (validateForm()) {
        let formData = $('#login-form').serialize();

        $.ajax({
          url: 'php/ajax.php?action=login',
          method: 'POST',
          data: formData,
          success: (res) => {
            if (res === 'success') {
              window.location.href = './dashboard';
            } else if (res === 'wrongDetails') {
              //displayFeedback(0);
              errMsg.text('Wrong username or password');
            }
          },
          error: () => {
            //displayFeedback(0);
            errMsg.text('Error logging in');
          },
        });
      //}
    });
  });
</script>

</body>
</html>