</div> <!-- Close container-fluid -->

  <!-- JQuery CDN -->
  <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->

  <!-- JQuery Local File -->
  <script src="<?php echo BASE_URL; ?>assets/js/jquery.min.js"></script>
  
  <!-- Bootstrap javascript CDN -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
  <!-- <script
    src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-3gJwYp4g3sm7oJpP2ZQ2TqF3vZ2FGmSytI0ZjU52F7A="
    crossorigin="anonymous"
  ></script> -->
  <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-3gJwYp4g3sm7oJpP2ZQ2TqF3vZ2FGmSytI0ZjU52F7A=" crossorigin="anonymous"></script> -->

  <!-- Bootstrap javascript Local File -->
  <script src="<?php echo BASE_URL; ?>assets/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Javascript File -->
  <?php if ($isLoggedIn) {
    echo '
      <script src="' . BASE_URL .'script.js"></script>
    ';
  } ?>
  <?php
    echo '
      <script src="' . BASE_URL .'ajax.js"></script>
    ';
  ?>

</body>
</html>