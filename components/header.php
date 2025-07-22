<?php
session_start();
include './php/connect_db.php';

$isLoggedIn = false;

if (isset($_SESSION['id'])) {
  $isLoggedIn = true;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cell Tracker</title>
    <link
      href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="dist/css/style.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
      $(document).on('DOMContentLoaded', () => {
        const siteContent = $('#site-container');
        if (siteContent) {
          siteContent.css('display', 'block');
        }
      })
    </script>
  </head>
  <body class="h-screen">

    <!-- Display this message if JavaScript is disabled in the browser -->
    <noscript>
      <div class='header'>
        <h1 style="text-align: center; width: 100%; padding: 5px">Cell Tracker</h1>
      </div>
      <div class="no-js-warning">
        This website requires JavaScript to function properly. Please enable JavaScript in your browser settings.
      </div>
    </noscript>

    <div id="site-container" class="max-w-full">
      <?php if ($isLoggedIn === false) {
        echo "
          <header class='header'>
            <h1>Cell Tracker</h1>
            <div class='nav-bar'>
              <a href='./add-cell.php' class='add-cell-link'>Add a Cell</a>
            </div>
          </header>
          ";
      } ?>
      
      <!-- Feedback Pop-ups -->
      <span id="success-msg" class="feedback"></span>
      <span id="err-msg" class="feedback"></span>

