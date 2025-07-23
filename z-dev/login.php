<?php
include_once "header.php";

if ($isLoggedIn) {
  header('Location: ./dashboard');
}
?>

<div class="auth-panel login-panel m-0 position-fixed">

    <form

      class="auth-form login-form m-0"

      id="login-form"

    >

      <header>

        <h4 class="form-heading text-center mb-4">

          Log in to your account

        </h4>

      </header>



      <section class="step">

        <div class="form-group">

          <label for="">Enter your email address:</label>

          <input type="text" class="form-control" name="user-login" />

        </div>

        <div class="form-group">

          <label for="">Enter your password:</label>

          <input type="password" class="form-control" name="password" />

        </div>

        <button type="submit" class="btn submit-btn">Log in</button>



        <footer class="mt-2">

          <a href="forgot-password.html" class="forgot-password"

            >Forgot Password</a

          >

        </footer>

      </section>

    </form>

  </div>

  <?php include_once "footer.php"; ?>