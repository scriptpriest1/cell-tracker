<?php
include_once "header.php";

class DB {
  private static $instance = null;
  private $pdo;
  private function __construct() {
    // Update with your DB credentials
    $this->pdo = new PDO("mysql:host=localhost;dbname=cell_tracker", "root", "");
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  public static function getInstance() {
    if (self::$instance === null) {
      self::$instance = new DB();
    }
    return self::$instance->pdo;
  }
}

class Auth {
  private $db;
  public function __construct($dbConn) {
    $this->db = $dbConn;
  }
  public function login($userLogin, $password) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE user_login = ?");
    $stmt->execute([$userLogin]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      return true;
    }
    return false;
  }
}

class AuthController {
  private $auth;
  public function __construct(Auth $auth) {
    $this->auth = $auth;
  }
  public function handleLogin() {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $userLogin = $_POST['user-login'] ?? '';
      $password = $_POST['password'] ?? '';
      if ($this->auth->login($userLogin, $password)) {
        header('Location: ./dashboard');
        exit;
      } else {
        $error = "Invalid login credentials.";
      }
    }
    return $error;
  }
}

$error = '';
if ($isLoggedIn) {
  header('Location: ./dashboard');
  exit;
} else {
  $auth = new Auth(DB::getInstance());
  $controller = new AuthController($auth);
  $error = $controller->handleLogin();
}
?>

<div class="auth-panel login-panel m-0 position-fixed">
  <form class="auth-form login-form m-0" id="login-form" method="POST">
    <header>
      <h4 class="form-heading text-center mb-4">
        Log in to your account
      </h4>
    </header>
    <section class="step">
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>
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
        <a href="forgot-password.html" class="forgot-password">Forgot Password</a>
      </footer>
    </section>
  </form>
</div>
<?php include_once "footer.php"; ?>