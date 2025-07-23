<?php

include 'connect_db.php';
include 'functions.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

/*=======================================
    Check if email already exists
=======================================*/
if ($action === 'check_user_login') {
  $user_login = clean_input($_POST['user-login']);

  $stmt = $conn->prepare('SELECT * FROM users WHERE login = ?');
  $stmt->bind_param('s', $user_login);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    echo 'notAvailable'; // An account is already registered with this email
  } else {
    echo 'available'; // Proceed
  }

  $stmt->close();
}

/*=======================================
    Insert Cell Data into the database
=======================================*/
if ($action === 'add_cell') {
  // Add a new cell to the database
  $cell_name = clean_input($_POST['cell-name']);

  // Insert into the database
  $stmt = $conn->prepare(
    'INSERT INTO cells (cell_name) VALUES (?)'
  );
  $stmt->bind_param('s', $cell_name);

  if ($stmt->execute()) {
    echo 'success'; // Successfully added
  } else {
    echo 'error: ' . $stmt->error; // Return error message
  }

  $stmt->close();
}

/*=======================================
          Login Functionality
=======================================*/
if ($action === 'login') {
  // Collect and sanitize form data
  $user_login = clean_input($_POST['user-login']);
  $password = clean_input($_POST['password']);

  // Fetch user and linked church info
  $stmt = $conn->prepare("
    SELECT users.id AS user_id, users.password, users.church_id, churches.church_name
    FROM users 
    LEFT JOIN churches ON users.church_id = churches.id 
    WHERE users.user_login = ? 
    LIMIT 1
  ");
  $stmt->execute([$user_login]);

  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    // Compare plaintext passwords (use password_hash in production)
    if ($user['password'] === $password) {
      session_start();
      $_SESSION['id'] = $user['user_id'];
      $_SESSION['church_id'] = $user['church_id'];
      $_SESSION['church_name'] = $user['church_name']; // Optional for convenience
      echo 'success';
      exit;
    } else {
      echo 'wrongDetails'; // Incorrect password
    }
  } else {
    echo 'wrongDetails'; // No such user
  }
}

/*=======================================
          Logout Functionality
=======================================*/
if ($action === 'logout') {
  session_start();

  if (isset($_SESSION['id'])) {
    session_unset();
    session_destroy();
    echo 'loggedOut';
  }
}