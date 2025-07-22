// Function to validate inputs
function validateInput(input) {
  const id = input.attr('id');
  const value = input.val().trim();
  let isValid = false;

  // Validation rules for each input
  if (id === 'cell-name') {
    const regex = /^[A-Za-z ]+$/;
    if (regex.test(value) && value.length >= 3) {
      isValid = true;
    }
  } else if (id === 'user-name') {
    const regex = /^[A-Za-z0-9]+$/;
    if (regex.test(value) && value.length >= 5 && !/\s/.test(value)) {
      isValid = true;
      // Skip clearing the message for username if it's already validated as available
      if (input.hasClass('username-db-check')) {
        return true;
      }
    }
  } else if (id === 'password') {
    const regex = /^[A-Za-z0-9!@#$%^&*(),.?":{}|<>]+$/;
    if (regex.test(value) && value.length >= 6 && !/\s/.test(value)) {
      isValid = true;
    }
  } else if (id === 'c-password') {
    const passwordValue = $('#password').val().trim();
    if (value === passwordValue) {
      isValid = true;
    }
    //For the Login Form Validation
  } else if (id === 'login-user-name') {
    if (value.length > 0) {
      isValid = true;
    }
   } else if (id === 'login-password') {
    if (value.length > 0) {
      isValid = true;
    }
  }

  // Update input styling based on validation
  if (isValid) {
    input.removeClass('invalid');
    input.next('.input-error-msg').text(''); // Clear error message
  } else {
    input.addClass('invalid');
    input
      .next('.input-error-msg')
      .text(getErrorMessage(id))
      .css({ color: 'red', lineHeight: '1.2', marginTop: '3px' }); // Show error message
  }

  return isValid;
}

// Function to generate error messages
function getErrorMessage(id) {
  switch (id) {
    case 'cell-name':
      return 'Minimum of 3 characters. Alphabets only.';
    case 'user-name':
      return 'Minimum of 5 characters. Alpha-numeric characters only.';
    case 'password':
      return 'Minimum of 6 characters.';
    case 'c-password':
      return 'Password mismatch';
    //For Login Error Messages
    case 'login-user-name':
      return 'Cannot be empty';
    case 'login-password':
      return 'Cannot be empty';
    default:
      return 'Invalid input.';
  }
}

// Function to check the entire form's validity
function validateForm() {
  const inputs = $('.entries');
  let isFormValid = true;

  inputs.each(function () {
    if (!validateInput($(this))) {
      isFormValid = false;
    }
  });

  return isFormValid;
}

//Bind events for individual inputs
$('.entries').on('blur', function () {
  validateInput($(this));
});
