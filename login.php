<?php

include 'components/header.php';

if ($isLoggedIn) {
  header('Location: ./');
}
?>

  <div class="form-container">   
    <h2 class="heading">Log in</h2>
    <form id="login-form">

      <div class="field-group">
        <label>Username:</label>
        <input type="text" name="user-name" id="login-user-name" class="entries"/>
        <p class="input-error-msg" style="font-size: 14px;"></p>
      </div>

      <div class="field-group">
        <label>Password:</label>
        <input type="password" name="password" id="login-password" class="entries"/>
        <p class="input-error-msg" style="font-size: 14px;"></p>
      </div>

      <div class="footer">
        <button type="submit" class="submit-btn">Log in</button>
      </div>
      
    </form>
  </div>


  <!-- Scripts -->
  <script src="js/functions.js"></script>
  <script src="js/formValidation.js"></script>

  <script>

    $(document).ready( () => {

      const successMsg = $('#success-msg');
      const errMsg = $('#err-msg');

      //Post Login Details to the Backend
      $("#login-form").on('submit', (e) => {
        e.preventDefault();

        if (validateForm()) {

          let formData = $("#login-form").serialize();

          $.ajax({
            url: 'php/ajax.php?action=login',
            method: 'POST',
            data: formData,
            success: (res) => {
              if (res === 'success') {
                window.location.href = './';
              } else if (res === 'wrongDetails') {;
                displayFeedback(0);
                errMsg.text('Wrong username or password');
              }
            },
            error: () => {
              displayFeedback(0);
              errMsg.text('Error logging in');
            }
          }) 
        }
      })

    })
  
  </script>
</body>
</html>