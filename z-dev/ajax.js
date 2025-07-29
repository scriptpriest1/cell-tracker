$(document).ready(() => {
  const successMsg = $('#success-msg');
  const errMsg = $('#err-msg');

  // Login Functionality

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

  // Logout Functionality

  $(document).on('click', '.logout-btn', function () {
    $.ajax({
      url: '../php/ajax.php?action=logout',
      success: (res) => {
        if (res === 'loggedOut') {
          successMsg.text('Logging out...');
          window.location.href = '../login.php';
        }
      },
      error: () => {
        errMsg.text("Unable to log out! Please try again.");
      },
    });
  });
});
