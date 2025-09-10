<?php
class Footer {
  public static function render($isLoggedIn) {
    ?>
    </div> <!-- Close container-fluid -->

    <!-- JQuery CDN -->
    <script
      src="https://code.jquery.com/jquery-3.7.1.min.js"
      integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
      crossorigin="anonymous"
    ></script>
    <!-- Bootstrap CDN -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
      crossorigin="anonymous"
    ></script>
    <!-- Javascript File -->
    <?php if ($isLoggedIn) {
      echo '<script src="' . BASE_URL .'script.js"></script>';
    }
    echo '<script src="' . BASE_URL .'ajax.js"></script>';
    ?>
    </body>
    </html>
    <?php
  }
}

Footer::render($isLoggedIn);