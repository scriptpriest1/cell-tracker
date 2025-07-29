<?php
include 'connect_db.php';   // sets up $conn as a PDO instance
include 'functions.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

/*=======================================
    Check if user_login already exists
=======================================*/
if ($action === 'check_user_login') {
  $user_login = clean_input($_POST['user-login']);

  $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE user_login = ?');
  $stmt->execute([$user_login]);
  echo $stmt->fetchColumn() ? 'notAvailable' : 'available';
  exit;
}

/*=======================================
    Insert Cell Data into the database
=======================================*/
if ($action === 'add_cell') {
  $cell_name = clean_input($_POST['cell-name']);
  $stmt = $conn->prepare('INSERT INTO cells (cell_name, date_created) VALUES (?, NOW())');
  if ($stmt->execute([$cell_name])) {
    echo 'success';
  } else {
    echo 'error: ' . ($stmt->errorInfo()[2] ?? 'Unknown error');
  }
  exit;
}

/*=======================================
          Login Functionality
=======================================*/
if ($action === 'login') {
  $user_login = clean_input($_POST['user-login']);
  $password   = clean_input($_POST['password']);

  // Fetch user with related names
  $stmt = $conn->prepare("
    SELECT DISTINCT
      users.id AS user_id,
      users.password,
      users.cell_id,
      users.church_id,
      users.group_id,
      cells.cell_name,
      churches.church_name,
      `groups`.group_name
    FROM users
    LEFT JOIN cells ON users.cell_id = cells.id
    LEFT JOIN churches ON users.church_id = churches.id
    LEFT JOIN `groups` ON users.group_id = groups.id
    WHERE users.user_login = ?
    LIMIT 1
  ");
  $stmt->execute([$user_login]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    echo 'wrongDetails'; // User not found
    exit;
  }

  if (trim($user['password']) !== trim($password)) {
    echo 'wrongDetails'; // Incorrect password
    exit;
  }

  // Determine highest admin level
  $adminType  = 'user';
  $entityId   = null;
  $entityName = null;

  if (($user['group_id'] ?? null) && $user['group_id'] != 0) {
    $adminType  = 'group';
    $entityId   = $user['group_id'];
    $entityName = $user['group_name'] ?? null;
  }
  elseif (($user['church_id'] ?? null) && $user['church_id'] != 0) {
    $adminType  = 'church';
    $entityId   = $user['church_id'];
    $entityName = $user['church_name'] ?? null;
  }
  elseif (($user['cell_id'] ?? null) && $user['cell_id'] != 0) {
    $adminType  = 'cell';
    $entityId   = $user['cell_id'];
    $entityName = $user['cell_name'] ?? null;
  }

  // Set session variables
  session_start();
  $_SESSION['user_id']     = $user['user_id'];
  $_SESSION['admin_type']  = $adminType;
  $_SESSION['entity_id']   = $entityId;
  $_SESSION['entity_name'] = $entityName;

  echo 'success';
  exit;
}




/*=======================================
          Logout Functionality
=======================================*/
if ($action === 'logout') {
  session_start();
  session_unset();
  session_destroy();
  echo 'loggedOut';
  exit;
}
