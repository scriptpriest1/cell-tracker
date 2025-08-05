<?php
// init.php

// Start session
session_start();

// Define BASE_URL
define('BASE_URL', 'http://cell-tracker.localhost/');

// Include database connection
require_once __DIR__ . '/php/connect_db.php';

$isLoggedIn = false;

if (isset($_SESSION['user_id'])) {
  $isLoggedIn = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cell Tracker</title>
  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Onest:wght@100..900&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

  <!-- Bootstrap CDN Link -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />

  <!-- CSS Link -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>style.css" />
</head>
<body class="w-100 h-100">
<div class="container-fluid w-100 vh-100 m-0 p-0">

  <?php if ($isLoggedIn === false) {
    echo '
      <header id="site-header" class="px-4 py-3 d-flex align-items-center justify-content-center w-100">
        <div class="w-100 site-name-bar">
          <h1 class="fs-5 fw-bold w-100 text-center m-0 p-0">Cell Tracker</h1>
        </div>
      </header>
    ';
  } ?> 

  <!-- Feedback Pop-ups -->
  <span id="success-msg" class="feedback"></span>
  <span id="err-msg" class="feedback"></span>
