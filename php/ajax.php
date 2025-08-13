<?php
session_start();
include 'connect_db.php';   // sets up $conn as a PDO instance
include 'functions.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

/*=======================================
    Check if user_login already exists
=======================================*/
// if ($action === 'check_user_login') {
//   $user_login = clean_input($_POST['user-login']);

//   $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE user_login = ?');
//   $stmt->execute([$user_login]);
//   echo $stmt->fetchColumn() ? 'notAvailable' : 'available';
//   exit;
// }

/*=======================================
         Add a Cell Functionality
=======================================*/
if ($action === 'add_a_cell') {
  if (!isset($_SESSION['entity_id']) || $_SESSION['admin_type'] !== 'church') {
    echo 'unauthorized';
    exit;
  }

  $churchId = $_SESSION['entity_id'];
  $cellName = clean_input($_POST['cell_name'] ?? '');
  $adminType = $_POST['choose_admin'] ?? ''; // 'self' or 'else'

  if (!$cellName) {
    echo 'missing cell name';
    exit;
  }

  // === Check if a cell with the same name already exists in this church ===
  $stmt = $conn->prepare('SELECT id FROM cells WHERE cell_name = ? AND church_id = ?');
  $stmt->execute([$cellName, $churchId]);
  if ($stmt->fetchColumn()) {
    echo 'A Cell with this name already exists';
    exit;
  }

  // === Validate admin (before inserting cell) ===
  if ($adminType === 'self') {
    $adminRole = clean_input($_POST['admin_role'] ?? '');
    if (!$adminRole) {
      echo 'missing admin role';
      exit;
    }

    // Check if current user already assigned to a cell
    $stmt = $conn->prepare('SELECT cell_id FROM users WHERE id = ? AND cell_id IS NOT NULL');
    $stmt->execute([$_SESSION['user_id']]);
    if ($stmt->fetchColumn()) {
      echo 'Already assigned to a cell';
      exit;
    }
  }

  if ($adminType === 'else') {
    $adminRole = clean_input($_POST['admin_role'] ?? '');
    $firstName = clean_input($_POST['admin_first_name'] ?? '');
    $lastName = clean_input($_POST['admin_last_name'] ?? '');
    $adminEmail = clean_input($_POST['admin_email'] ?? '');
    $adminPhone = clean_input($_POST['admin_phone'] ?? '');
    $password = clean_input($_POST['admin_password'] ?? '');
    $confPw = clean_input($_POST['admin_password_confirm'] ?? '');

    if (!$adminRole || !$firstName || !$lastName || !$adminEmail || !$adminPhone || !$password || !$confPw) {
      echo 'incomplete admin fields';
      exit;
    }

    if ($password !== $confPw) {
      echo 'password mismatch';
      exit;
    }

    // Check if email already exists AND is already assigned to a cell
    $stmt = $conn->prepare('SELECT cell_id FROM users WHERE user_login = ? AND cell_id IS NOT NULL');
    $stmt->execute([$adminEmail]);
    if ($stmt->fetchColumn()) {
      echo 'admin email taken or already assigned';
      exit;
    }
  }

  // === Passed all checks, now insert cell ===
  $stmt = $conn->prepare('INSERT INTO cells (cell_name, church_id, date_created) VALUES (?, ?, NOW())');
  if (!$stmt->execute([$cellName, $churchId])) {
    echo 'cell_insert_failed';
    exit;
  }

  $cellId = $conn->lastInsertId();

  // === Assign self as admin ===
  if ($adminType === 'self') {
    $stmt = $conn->prepare('UPDATE users SET cell_id = ?, cell_role = ? WHERE id = ?');
    $stmt->execute([$cellId, $adminRole, $_SESSION['user_id']]);
    echo 'success';
    exit;
  }

  // === Assign someone else as admin ===
  if ($adminType === 'else') {
    $stmt = $conn->prepare('
      INSERT INTO users (
        cell_role, first_name, last_name, user_login, phone_number, password, cell_id, date_created
      ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ');
    $success = $stmt->execute([
      $adminRole,
      $firstName,
      $lastName,
      $adminEmail,
      $adminPhone,
      $password, // hash later
      $cellId
    ]);

    if (!$success) {
      echo 'admin assignment failed';
      exit;
    }

    echo 'success';
    exit;
  }

  // === No admin assigned ===
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
      users.user_login,
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
  $_SESSION['user_id']     = $user['user_id'];
  $_SESSION['user_login']  = $user['user_login'];
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
  session_unset();
  session_destroy();
  echo 'loggedOut';
  exit;
}

/*=======================================
        Fetch Cells Functionality
=======================================*/
if ($action === 'fetch_all_cells') {
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

/*=======================================
      Assign Cell Admin Functionality
=======================================*/
if ($action === 'assign_cell_admin') {
  $cell_id     = $_POST['cell_id']        ?? null;
  $assign_type = $_POST['choose_admin']   ?? '';
  $role        = $_POST['role']           ?? '';

  if (!$cell_id || !$assign_type || !$role) {
    echo "Missing required fields";
    exit;
  }

  // Only one leader per cell
  if ($role === 'leader') {
    $stmt = $conn->prepare(
      'SELECT COUNT(*) FROM users WHERE cell_role = ? AND cell_id = ?'
    );
    $stmt->execute(['leader', $cell_id]);
    if ($stmt->fetchColumn() > 0) {
      echo 'A Cell Leader has already been assigned! Unassign first to assign a new Cell Leader.';
      exit;
    }
  }

  if ($assign_type === 'self') {
    // Fetch current assignment
    $stmt = $conn->prepare(
      'SELECT cell_id FROM users WHERE user_login = ?'
    );
    $stmt->execute([$_SESSION['user_login']]);
    $current_cell = $stmt->fetchColumn();

    if ($current_cell == $cell_id) {
      echo 'User already assigned';
      exit;
    }
    if ($current_cell && $current_cell != $cell_id) {
      echo 'Already assigned to a cell.';
      exit;
    }

    // Assign self
    $stmt = $conn->prepare(
      'UPDATE users SET cell_id = ?, cell_role = ? WHERE user_login = ?'
    );
    $stmt->execute([$cell_id, $role, $_SESSION['user_login']]);
    echo "success";
    exit;
  }

  if ($assign_type === 'else') {
    $first_name       = $_POST['first_name']        ?? '';
    $last_name        = $_POST['last_name']         ?? '';
    $email            = $_POST['email']             ?? '';
    $phone            = $_POST['phone']             ?? '';
    $password         = $_POST['password']          ?? '';
    $password_confirm = $_POST['password_confirm']  ?? '';

    if (
      !$first_name || !$last_name ||
      !$email || !$phone     || !$password ||
      $password !== $password_confirm
    ) {
      echo "Invalid input";
      exit;
    }

    // Prevent duplicate email
    $stmt = $conn->prepare(
      'SELECT COUNT(*) FROM users WHERE user_login = ?'
    );
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
      echo 'User already exists';
      exit;
    }

    // Insert new admin
    $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare(
      'INSERT INTO users
         (first_name, last_name, user_login, phone_number, password, cell_id, cell_role)
       VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
      $first_name,
      $last_name,
      $email,
      $phone,
      $hashed_pw,
      $cell_id,
      $role
    ]);

    echo "success";
    exit;
  }

  echo "Invalid assignment type";
  exit;
}

/*=======================================
      Unassign Cell Admin Functionality
=======================================*/
if ($action === 'unassign_cell_admin') {
    $userId = $_POST['user_id'] ?? null;
    $cellId = $_POST['cell_id'] ?? null;

    if (!$userId || !$cellId) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters.']);
        exit;
    }

    $loggedInUserId = $_SESSION['user_id'] ?? null;

    // Fetch the user being unassigned
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $targetUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$targetUser) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        exit;
    }

    $isSelf = $userId == $loggedInUserId;
    $hasHigherRole = !empty($targetUser['church_id']) || !empty($targetUser['group_id']);

    if ($isSelf || $hasHigherRole) {
        // Just unassign (don’t delete)
        $stmt = $conn->prepare("UPDATE users SET cell_id = NULL, cell_role = '' WHERE id = ?");
        $stmt->execute([$userId]);
    } else {
        // Delete the cell-only admin
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND church_id IS NULL AND group_id IS NULL");
        $stmt->execute([$userId]);
    }

    echo json_encode(['status' => 'success']);
    exit;
}

/*=======================================
      Edit Cell Name Functionality
=======================================*/
if ($action === 'edit_cell_name') {
  $inputValue = trim($_POST['input_value'] ?? '');
  $cellId = intval($_POST['cell_id'] ?? 0);

  if ($inputValue === '' || $cellId === 0) {
    echo json_encode([
      'status' => 'error',
      'message' => 'Invalid input or cell ID.',
    ]);
    exit;
  }

  $churchId = $_SESSION['entity_id'];

  // ✅ Case-sensitive check using BINARY
  $checkQuery = $conn->prepare("SELECT id FROM cells WHERE cell_name = ? AND church_id = ? AND id != ?");
  $checkQuery->execute([$inputValue, $churchId, $cellId]);

  if ($checkQuery->rowCount() > 0) {
    echo json_encode([
      'status' => 'error',
      'message' => 'A cell with this name already exists.',
    ]);
    exit;
  }

  $updateQuery = $conn->prepare("UPDATE cells SET cell_name = ? WHERE id = ? AND church_id = ?");
  $updated = $updateQuery->execute([$inputValue, $cellId, $churchId]);

  if ($updated) {
    echo json_encode([
      'status' => 'success',
      'new_cell_name' => $inputValue
    ]);
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'Failed to update cell name.'
    ]);
  }

  exit;
}

/*=======================================
      Edit Cell Admins' details 
            Functionality
=======================================*/
if ($action === 'update_cell_admin') {
  $cellId  = intval($_POST['cell_id']  ?? 0);
  $adminId = intval($_POST['admin_id'] ?? 0);

  // Prevent editing yourself
  if ($adminId === $_SESSION['user_id']) {
    echo json_encode(['status'=>'error','message'=>'Cannot edit your own admin record.']);
    exit;
  }

  // Capture new values
  $newRole  = clean_input($_POST['role']       ?? '');
  $first    = clean_input($_POST['first_name'] ?? '');
  $last     = clean_input($_POST['last_name']  ?? '');
  $email    = clean_input($_POST['email']      ?? '');
  $phone    = clean_input($_POST['phone']      ?? '');

  // Basic validation
  if (!$newRole || !$first || !$last || !$email || !$phone) {
    echo json_encode(['status'=>'error','message'=>'All fields are required.']);
    exit;
  }

  // 4. Prevent duplicate leader
  if ($newRole === 'leader') {
    $check = $conn->prepare(
      'SELECT COUNT(*) FROM users 
       WHERE cell_id = ? 
         AND cell_role = "leader" 
         AND id != ?'
    );
    $check->execute([$cellId, $adminId]);
    if ($check->fetchColumn() > 0) {
      echo json_encode(['status'=>'error','message'=>'A Cell Leader already exists.']);
      exit;
    }
  }

  // Perform update
  $upd = $conn->prepare(
    'UPDATE users
       SET cell_role  = ?,
           first_name = ?,
           last_name  = ?,
           user_login = ?,
           phone_number = ?
     WHERE id = ? AND cell_id = ?'
  );
  $success = $upd->execute([
    $newRole, $first, $last, $email, $phone, $adminId, $cellId
  ]);

  if ($success) {
    echo json_encode(['status'=>'success']);
  } else {
    echo json_encode(['status'=>'error','message'=>'Update failed.']);
  }
  exit;
}

