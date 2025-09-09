<?php

include_once '../header.php';
include_once '../php/functions.php';

$user_data = check_login($conn);
?>

<?php include_once "../modals.php"; ?>

<div id="mainframe" class="mainframe position-relative d-block d-md-grid w-100 h-100 m-0 p-0 m-0">

  <?php include_once "sidebar.php"; ?>

  <?php include_once "screen.php"; ?>

</div>


<?php include_once "../footer.php"; ?>