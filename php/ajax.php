<?php

include 'connect_db.php';
include 'functions.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

/*=======================================
    Check if username already exists
=======================================*/
if ($action === 'check_username') {
  $user_name = clean_input($_POST['username']);

  $stmt = $conn->prepare('SELECT * FROM cells WHERE user_name = ?');
  $stmt->bind_param('s', $user_name);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    echo 'notAvailable'; // Username is taken
  } else {
    echo 'available'; // Username is available
  }

  $stmt->close();
}

/*=======================================
    Insert Cell Data into the database
=======================================*/
if ($action === 'add_cell') {
  // Add a new cell to the database
  $cell_name = clean_input($_POST['cell-name']);
  $user_name = clean_input($_POST['user-name']);
  $password = clean_input($_POST['password']);

  // Insert into the database
  $stmt = $conn->prepare(
    'INSERT INTO cells (cell_name, user_name, password) VALUES (?, ?, ?)'
  );
  $stmt->bind_param('sss', $cell_name, $user_name, $password);

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
  // Collect form data and sanitize
  $user_name = clean_input($_POST['user-name']);
  $password = clean_input($_POST['password']);

  // Reading from database to compare data
  $query = $conn->prepare('SELECT * FROM cells WHERE user_name = ? limit 1');
  $query->bind_param('s', $user_name);
  $query->execute();

  $result = $query->get_result();

  if ($result) {
    if ($result && mysqli_num_rows($result) > 0) {
      while ($user_data = mysqli_fetch_assoc($result)) {
        if ($user_data['password'] === $password) {
          session_start();
          $_SESSION['id'] = $user_data['id'];
          echo 'success';
          die();
        }
        echo 'wrongDetails';
      }
    } else {
      echo 'wrongDetails';
    }
  }

  //Close prepared statement
  $query->close();
}

/*=======================================
          Logout Functionality   
=======================================*/
if ($action === 'logout') {
  session_start();

  if (isset($_SESSION['id'])) {
    unset($_SESSION['id']);
    echo 'loggedOut';
  }
}

// Close database connection
$conn->close();
?>
