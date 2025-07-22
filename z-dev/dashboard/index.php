<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Cell Tracker</title>
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
  <link rel="stylesheet" href="../style.css" />
</head>
<body>
<div class="container-fluid w-100 h-100 m-0 p-0 m-0 position-relative">

  <?php include_once "../modals.php"; ?>

  <div class="mainframe position-relative d-block d-md-grid w-100 h-100 m-0 p-0 m-0">

    <?php include_once "sidebar.php"; ?>

    <?php include_once "screen.php"; ?>

  </div>


<?php include_once "../footer.php"; ?>