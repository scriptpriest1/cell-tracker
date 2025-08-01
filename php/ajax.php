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
         Add a Cell Functionality
=======================================*/
if ($action === 'add_a_cell') {
  session_start();
  if (!isset($_SESSION['entity_id']) || $_SESSION['admin_type'] !== 'church') {
    echo 'unauthorized';
    exit;
  }

  $cellName     = clean_input($_POST['cell_name'] ?? '');
  $adminRole    = clean_input($_POST['admin_role'] ?? '');
  $firstName    = clean_input($_POST['admin_first_name'] ?? '');
  $lastName     = clean_input($_POST['admin_last_name'] ?? '');
  $adminEmail   = clean_input($_POST['admin_email'] ?? '');
  $password     = clean_input($_POST['admin_password'] ?? '');
  $confPw       = clean_input($_POST['admin_password_confirm'] ?? '');

  if (!$cellName) {
    echo 'missing_cell_name';
    exit;
  }

  // Validate optional admin input: if any admin field is filled, all must be filled
  $adminFieldsFilled = $adminRole || $firstName || $lastName || $adminEmail || $password || $confPw;
  if ($adminFieldsFilled) {
    if (!$adminRole || !$firstName || !$lastName || !$adminEmail || !$password || !$confPw) {
      echo 'incomplete_admin_fields';
      exit;
    }

    if ($password !== $confPw) {
      echo 'password_mismatch';
      exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE user_login = ?');
    $stmt->execute([$adminEmail]);
    if ($stmt->fetchColumn() > 0) {
      echo 'admin_email_taken';
      exit;
    }
  }

  // Insert new cell
  $stmt = $conn->prepare('INSERT INTO cells (cell_name, church_id, date_created) VALUES (?, ?, NOW())');
  $churchId = $_SESSION['entity_id'];
  if (!$stmt->execute([$cellName, $churchId])) {
    echo 'cell_insert_failed';
    exit;
  }

  $cellId = $conn->lastInsertId();

  // If admin fields were filled, create the user
  if ($adminFieldsFilled) {
    $stmt = $conn->prepare('
      INSERT INTO users (
        cell_role, first_name, last_name, user_login, password, cell_id, date_created
      ) VALUES (?, ?, ?, ?, ?, ?, NOW())
    ');
    $success = $stmt->execute([
      $adminRole,
      $firstName,
      $lastName,
      $adminEmail,
      $password,  // hash later if needed
      $cellId
    ]);

    if (!$success) {
      echo 'admin_insert_failed';
      exit;
    }
  }

  echo 'success';
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

/*=======================================
        Fetch Cells Functionality
=======================================*/
if ($_POST['action'] === 'fetch_all_cells') {
  session_start();
  $churchId = $_SESSION['entity_id'];

  $stmt = $conn->prepare("
    SELECT 
      cells.id, 
      cells.cell_name, 
      DATE_FORMAT(cells.date_created, '%d/%m/%Y') AS date_created,
      CONCAT(users.first_name, ' ', users.last_name) AS cell_leader_name,
      (
        SELECT COUNT(*) 
        FROM cell_members 
        WHERE cell_members.cell_id = cells.id
      ) AS cell_members_count
    FROM cells
    LEFT JOIN users 
      ON users.cell_id = cells.id 
      AND users.cell_role = 'leader'
    WHERE cells.church_id = :church_id
  ");
  
  $stmt->bindValue(':church_id', $churchId, PDO::PARAM_INT);
  $stmt->execute();

  $cells = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($cells);
  exit;
}
