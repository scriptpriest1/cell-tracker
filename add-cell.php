<?php

include 'components/header.php';

if ($isLoggedIn) {
  header('Location: ./');
}
?>

    <div class="form-container">
      <form id="add-cell-form">

        <div class="field-group">
          <label>Name of Cell:</label>
          <input type="text" name="cell-name" id="cell-name" class="entries"/>
          <p id="" class="input-error-msg" style="font-size: 14px;"></p>
        </div>

        <div class="field-group">
          <label>Username:</label>
          <input type="text" name="user-name" id="user-name" class="entries">
          <p id="username-err-msg" class="input-error-msg" style="font-size: 14px;"></p>
        </div>

        <div class="field-group">
          <label>Create Password:</label>
          <input type="password" name="password" id="password" class="entries"/>
          <p id="" class="input-error-msg" style="font-size: 14px;"></p>
        </div>

        <div class="field-group">
          <label>Confirm Password:</label> 
          <input type="password" name="c-password" id="c-password" class="entries"/>
          <p class="input-error-msg" style="font-size: 14px;"></p>
        </div>

        <div class="footer">
          <button class="submit-btn" id="add-cell-btn">Add Cell</button>
          <?php if (!$isLoggedIn) {
            echo "<a href='login.php'>Login to a Cell</a>";
          } ?>
        </div>
        
      </form>
    </div>


    <!-- Scripts -->
    <script src="js/functions.js"> </script>
    <script src="js/formValidation.js"></script>

    <script>

      $(document).ready( () => {

        const successMsg = $('#success-msg');
        const errMsg = $('#err-msg');

        let user_exists = false;

        //Check if username exists from 'user-name' input
        let delayTimer;
        // setTimeout(() => {
          
        // }, 1000);
        $('#user-name').on('input', function () {
          if(validateInput($(this))) {
            let username = $('#user-name').val().trim();
            let usernameErrMsg = $('#username-err-msg');
            
            $.ajax({
              url: 'php/ajax.php?action=check_username',
              method: 'POST',
              data: { username },
              success: (res) => {
                if (res === 'notAvailable') {
                  user_exists = true;
                  $('#user-name').addClass('invalid').addClass('username-db-check');
                  usernameErrMsg.text('Username is taken').css('color', 'red');
                } else {
                  user_exists = false;
                  $('#user-name').addClass('username-db-check').removeClass('invalid');
                  usernameErrMsg.text('Username available').css('color', '#00cf34');
                }
              },
              error: () => {
                displayFeedback(0);
                errMsg.text('Error submitting request!');
              }
            })
          }
        })

        //Insert data from the form to the database if username is available
        $('#add-cell-form').on('submit', (e) => {
          e.preventDefault();

          if(validateForm()) {

            if (user_exists) {
              return;
            }

            let formData = $('#add-cell-form').serialize();

            $.ajax({
              url: 'php/ajax.php?action=add_cell',
              method: 'POST',
              data: formData,
              success: (res) => {
                if (res === "success") {
                  let params = new URLSearchParams(formData);
                  let cellName = params.get('cell-name');
                  displayFeedback(1);
                  successMsg.text(cellName + ' added successfully!');
                  $('#add-cell-form')[0].reset(); // Reset form
                  $('.input-error-msg').text(''); // Clear feedback
                } else {
                  displayFeedback(0);
                  errMsg.text('Failed to add cell');
                }
              },
              error: () => {
                displayFeedback(0);
                errMsg.text('Error submitting request!');
              }
            }) 
          }
        })

      })
     
    </script>
  </body>
</html>
