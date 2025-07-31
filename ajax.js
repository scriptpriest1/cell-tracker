$(document).ready(() => {
  const successMsg = $('#success-msg');
  const errMsg = $('#err-msg');

  // Validation for empty form fields
  function isFilled($input) {
    return $.trim($input.val()) !== '';
  }

  // Login Functionality

  //Post Login Details to the Backend
  $('#login-form').on('submit', (e) => {
    e.preventDefault();

    //if (validateForm()) {
    const data = $('#login-form').serialize();

    $.ajax({
      url: './php/ajax.php?action=login',
      method: 'POST',
      data,
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
        errMsg.text('Unable to log out! Please try again.');
      },
    });
  });

  // Add a cell Functionality

  function validateAddCellForm() {
    const $role = $('#add-cell-form #admin-role');
    const role = $role.val();
    if (role === '') return true;

    const $firstName = $('#add-cell-form #admin-first-name'),
      $lastName = $('#add-cell-form #admin-last-name'),
      $email = $('#add-cell-form #admin-email'),
      $pw = $('#add-cell-form #admin-password'),
      $confPw = $('#add-cell-form #admin-password-confirm');

    return (
      isFilled($firstName) &&
      isFilled($lastName) &&
      isFilled($email) &&
      isFilled($pw) &&
      isFilled($confPw) &&
      $pw.val() === $confPw.val()
    );
  }

  $('#add-cell-form .form-control, #add-cell-form .form-select').on(
    'input change',
    function () {
      const valid = isFilled($('#add-cell-form #cell-name')) && validateAddCellForm();
      $('#add-cell-form .submit-btn').prop('disabled', !valid);
    },
  );

  $('#add-cell-form').on('submit', function (e) {
    e.preventDefault();

    const $btn = $('#add-cell-form .submit-btn').prop('disabled', true).text('Adding…');
    const data = $(this).serialize(); // Automatically serializes all input values with name attributes

    $.ajax({
      url: '../php/ajax.php?action=add_a_cell',
      method: 'POST',
      data,
      success: (res) => {
        if (res === 'success') {
          alert('Cell added successfully!');
          location.reload();
        } else {
          alert('Error: ' + res);
        }
      },
      error: () => {
        alert('Server error');
      },
      complete: () => {
        $btn.prop('disabled', false).text('Add Cell');
      },
    });
  });

  // Close Ready Function
});
