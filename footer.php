<?php
class Footer {
    public static function render($isLoggedIn) {
        ?>
        </div> <!-- Close container-fluid -->

        <!-- JQuery Local File -->
        <script src="<?php echo BASE_URL; ?>assets/js/jquery.min.js"></script>
        <!-- Bootstrap javascript Local File -->
        <script src="<?php echo BASE_URL; ?>assets/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
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