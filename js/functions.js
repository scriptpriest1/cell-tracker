// Logout Modal Display
$('#logout-modal-cnc-btn').on('click', displayLogoutModal);
$('#logout-btn').on('click', displayLogoutModal);

function displayLogoutModal() {
  const logoutModalBox = $('#logout-modal-box');
  const logoutModal = $('#logout-modal');

  if (logoutModalBox.is(':visible')) {
    // Hide with animation
    logoutModalBox.animate(
      {
        opacity: 0,
      },
      200,
      function () {
        $(this).css('display', 'none');
      },
    );
    logoutModal.animate(
      {
        top: '-5%',
      },
      200,
    );
  } else {
    // Reveal with animation
    logoutModalBox.css({ display: 'block', opacity: 0 }).animate(
      {
        opacity: 1,
      },
      200,
    );
    console.table(logoutModalBox);
    logoutModal.animate(
      {
        top: '5%',
      },
      200,
    );
  }
}

//Display Feedback (Success and Error) Messages
function displayFeedback(type) {
  const successMsg = $('#success-msg');
  const errMsg = $('#err-msg');

  let feedback;
  if (type === 0) {
    feedback = errMsg;
  } else if (type === 1) {
    feedback = successMsg;
  }

  feedback.css({ display: 'block', opacity: 0 }).animate(
    {
      opacity: 1,
      left: '30px',
    },
    200,
  );

  setTimeout(() => {
    if (feedback.is(':visible')) {
      // Hide with animation
      feedback.animate(
        {
          opacity: 0,
          left: '-20px',
        },
        200,
        function () {
          $(this).css('display', 'none');
        },
      );
    }
  }, 5000);
}




