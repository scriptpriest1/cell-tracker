<?php

include 'components/header.php';
include 'php/functions.php';

$user_data = check_login($conn);
?>

<!-- Logout Modal Box -->
<div id="logout-modal-box" class="modal-box">
  <div id="logout-modal" class="modal">
    <div class="message">
      <p>Are you sure you want to logout?</p>
    </div>
    <div class="conf-box">
      <button class="opr" id="logout-modal-cnc-btn">Cancel</button>
      <button class="opr" id="logout-modal-logout-btn">Logout</button>
    </div>
  </div>
</div>

<!-- Dashboard starts here -->
<div class="dashboard">
  <?php include 'components/sidebar.php'; ?>

  <div id="page-body">
    <!-- Include Masthead -->
    <?php include 'components/masthead.php'; ?>

    <div id="page-container">

      <!-- Include the pages when their links are active -->
      <?php include 'pages/dashboard.php'; ?>

    </div>

  </div>
</div>

<?php include "components/footer.php"; ?>

<!-- Scripts -->
<script src="js/functions.js"></script>
<script>

  $(document).ready( () => {

    // Program to Logout of the Session
    $('#logout-modal-logout-btn').on('click', () => {
      $.ajax({
        url: 'php/ajax.php?action=logout',
        success: (res) => {
          if (res === 'loggedOut') {
            window.location.href = 'login.php';
          }
        },
        error: () => {
          alert('Unable to logout. Please try again.')
        }
      })
    })      

    // Event handler for clicking sidebar page navigation link
    $(document).on('click', '.dbrd-pg-link', function (e) {
      e.preventDefault();

      const file = $(this).attr('data-href');         // e.g., pages/lists.php
      const page = file.slice(6, -4);                 // e.g., "lists"
      history.pushState(null, '', `?page=${page}`);   // Update URL without reload

      // Load content via AJAX
      $.ajax({
        url: file,
        method: 'GET',
        success: (data) => {
          $('#page-container').html(data);

          // Update the sidebar UI
          $('.dbrd-pg-link li').removeClass('active');                     // Remove existing active
          $(`.dbrd-pg-link[data-href="${file}"] li`).addClass('active');   // Activate clicked one
        },
        error: () => {
          $('#page-container').html('<p>Error loading content.</p>').css({ fontSize: '33px' });
        }
      });
    });

    // Load content based on URL query param (on page refresh or direct access)
    $(document).ready(function () {
      const params = new URLSearchParams(window.location.search);
      const page = params.get('page');

      if (page) {
        const file = `pages/${page}.php`;

        $.ajax({
          url: file,
          method: 'GET',
          success: (data) => {
            $('#page-container').html(data);

            // Reset all active states
            $('.dbrd-pg-link li').removeClass('active');
            $(`.dbrd-pg-link[data-href="${file}"] li`).addClass('active');
          },
          error: () => {
            $('#page-container').html('<p>Error loading content.</p>').css({ fontSize: '33px' });
          }
        });
      }
    });


  //Close Ready Function  
  })

</script>

</body>
</html>