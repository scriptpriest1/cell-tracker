<?php
session_start();
include 'connect_db.php';
include 'functions.php';

$action = isset($_REQUEST['action']) ? clean_input($_REQUEST['action']) : '';

/*=======================================
         Add a Cell Functionality
=======================================*/
if ($action === 'add_a_cell') {
  if (!isset($_SESSION['entity_id']) || $_SESSION['admin_type'] !== 'church') {
    echo 'unauthorized';
    exit;
  }

  $churchId = clean_input($_SESSION['entity_id']);
  $cellName = clean_input($_POST['cell_name'] ?? '');
  $adminType = clean_input($_POST['choose_admin'] ?? ''); // 'self' or 'else'

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
    $stmt->execute([clean_input($_SESSION['user_id'])]);
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
    $stmt->execute([clean_input($adminEmail)]);
    if ($stmt->fetchColumn()) {
      echo 'admin email taken or already assigned';
      exit;
    }
  }

  // === Passed all checks, now insert cell ===
  $stmt = $conn->prepare('INSERT INTO cells (cell_name, church_id) VALUES (?, ?)');
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
    // Hash password before insert
    $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('
      INSERT INTO users (
        cell_role, first_name, last_name, user_login, phone_number, password, cell_id
      ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    $success = $stmt->execute([
      $adminRole,
      $firstName,
      $lastName,
      $adminEmail,
      $adminPhone,
      $hashed_pw,
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
  $user_login = clean_input($_POST['user-login'] ?? '');
  $password   = clean_input($_POST['password'] ?? '');

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

  // Use password_verify for authentication
  if (!password_verify($password, $user['password'])) {
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
  $churchId = clean_input($_SESSION['entity_id']);

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
  $cell_id     = clean_input($_POST['cell_id']        ?? null);
  $assign_type = clean_input($_POST['choose_admin']   ?? '');
  $role        = clean_input($_POST['role']           ?? '');

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
    $stmt->execute([clean_input($_SESSION['user_login'])]);
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
    $stmt->execute([clean_input($cell_id), clean_input($role), clean_input($_SESSION['user_login'])]);
    echo "success";
    exit;
  }

  if ($assign_type === 'else') {
    $first_name       = clean_input($_POST['first_name']        ?? '');
    $last_name        = clean_input($_POST['last_name']         ?? '');
    $email            = clean_input($_POST['email']             ?? '');
    $phone            = clean_input($_POST['phone']             ?? '');
    $password         = clean_input($_POST['password']          ?? '');
    $password_confirm = clean_input($_POST['password_confirm']  ?? '');

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
    $stmt->execute([clean_input($email)]);
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
      clean_input($first_name),
      clean_input($last_name),
      clean_input($email),
      clean_input($phone),
      $hashed_pw,
      clean_input($cell_id),
      clean_input($role)
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
    $userId = clean_input($_POST['user_id'] ?? null);
    $cellId = clean_input($_POST['cell_id'] ?? null);

    if (!$userId || !$cellId) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters.']);
        exit;
    }

    $loggedInUserId = clean_input($_SESSION['user_id'] ?? null);

    // Fetch the user being unassigned
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([clean_input($userId)]);
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
        $stmt->execute([clean_input($userId)]);
    } else {
        // Delete the cell-only admin
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND church_id IS NULL AND group_id IS NULL");
        $stmt->execute([clean_input($userId)]);
    }

    echo json_encode(['status' => 'success']);
    exit;
}

/*=======================================
      Edit Cell Name Functionality
=======================================*/
if ($action === 'edit_cell_name') {
  $inputValue = clean_input(trim($_POST['input_value'] ?? ''));
  $cellId = intval(clean_input($_POST['cell_id'] ?? 0));

  if ($inputValue === '' || $cellId === 0) {
    echo json_encode([
      'status' => 'error',
      'message' => 'Invalid input or cell ID.',
    ]);
    exit;
  }

  $churchId = clean_input($_SESSION['entity_id']);

  // ✅ Case-sensitive check using BINARY
  $checkQuery = $conn->prepare("SELECT id FROM cells WHERE cell_name = ? AND church_id = ? AND id != ?");
  $checkQuery->execute([clean_input($inputValue), clean_input($churchId), clean_input($cellId)]);

  if ($checkQuery->rowCount() > 0) {
    echo json_encode([
      'status' => 'error',
      'message' => 'A cell with this name already exists.',
    ]);
    exit;
  }

  $updateQuery = $conn->prepare("UPDATE cells SET cell_name = ? WHERE id = ? AND church_id = ?");
  $updated = $updateQuery->execute([clean_input($inputValue), clean_input($cellId), clean_input($churchId)]);

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
  $cellId  = intval(clean_input($_POST['cell_id']  ?? 0));
  $adminId = intval(clean_input($_POST['admin_id'] ?? 0));

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
    $check->execute([clean_input($cellId), clean_input($adminId)]);
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
    clean_input($newRole), clean_input($first), clean_input($last), clean_input($email), clean_input($phone), clean_input($adminId), clean_input($cellId)
  ]);

  if ($success) {
    echo json_encode(['status'=>'success']);
  } else {
    echo json_encode(['status'=>'error','message'=>'Update failed.']);
  }
  exit;
}

/*=======================================
             Add Cell Members 
              Functionality
=======================================*/
if ($action === 'add_cell_member') {
  $title        = clean_input($_POST['title'] ?? '');
  $firstName    = clean_input($_POST['first_name'] ?? '');
  $lastName     = clean_input($_POST['last_name'] ?? '');
  $phone        = clean_input($_POST['phone_number'] ?? '');
  $email        = clean_input($_POST['email'] ?? '');
  $dobMonth     = clean_input($_POST['dob_month'] ?? '');
  $dobDay       = clean_input($_POST['dob_day'] ?? '');
  $occupation   = clean_input($_POST['occupation'] ?? '');
  $resAddress   = clean_input($_POST['res_address'] ?? '');
  $fsStatus     = clean_input($_POST['fs_status'] ?? '');
  $delgInCell   = clean_input($_POST['delg_in_cell'] ?? '');
  $deptInChurch = clean_input($_POST['dept_in_church'] ?? '');
  $joinedDate   = clean_input($_POST['date_joined_ministry'] ?? '');

  // Server-side validation (first & last name required)
  if ($firstName === '' || $lastName === '') {
      echo 'First name and last name are required.';
      exit;
  }

  // Determine cell_id from session
  $cellId = clean_input($_SESSION['entity_id'] ?? null);
  if (empty($cellId)) {
      echo 'No cell recognized.';
      exit;
  }

  // Check if the email already exists
  if ($email !== '') {
    // compare case-insensitively
    $q = $conn->prepare("SELECT id FROM cell_members WHERE LOWER(email) = LOWER(?) LIMIT 1");
    $q->execute([clean_input($email)]);
    $foundId = $q->fetchColumn();

    if ($foundId) {
      echo "Email address taken! Please use another email.";
      exit;
    }
  }

  // Insert cell member
  $stmt = $conn->prepare("
      INSERT INTO cell_members 
        (title, first_name, last_name, phone_number, email, dob_month, dob_day, occupation, residential_address, foundation_sch_status, delg_in_cell, dept_in_church, date_joined_ministry, cell_id)
      VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");

  $executeResult = $stmt->execute([
      clean_input($title), clean_input($firstName), clean_input($lastName), clean_input($phone), clean_input($email), clean_input($dobMonth), clean_input($dobDay), clean_input($occupation), clean_input($resAddress), clean_input($fsStatus), clean_input($delgInCell), clean_input($deptInChurch), clean_input($joinedDate), clean_input($cellId)
  ]);

  if ($executeResult) {
      echo 'success';
      exit;
  } else {
      echo 'Could not add member.';
      exit;
  }
}

/*=======================================
      Fetch Cell Members Functionality
=======================================*/
if ($action === 'fetch_all_cell_members') {
  $cellId = clean_input($_SESSION['entity_id']); // Assuming entity_id is the current cell's ID for a Cell Admin

  $stmt = $conn->prepare("
    SELECT 
      id,
      CONCAT(UPPER(LEFT(title, 1)), SUBSTRING(title, 2)) AS title,
      first_name,
      last_name,
      phone_number,
      email,
      CONCAT(UPPER(LEFT(dob_month, 1)), SUBSTRING(dob_month, 2)) AS dob_month,
      dob_day,
      occupation,
      residential_address,
      CONCAT(UPPER(LEFT(foundation_sch_status, 1)), SUBSTRING(foundation_sch_status, 2)) AS foundation_sch_status,
      delg_in_cell,
      dept_in_church,
      date_joined_ministry,
      DATE_FORMAT(date_added, '%d/%m/%Y') AS date_added,
      cell_id
    FROM cell_members
    WHERE cell_id = :cell_id;
  ");

  $stmt->bindValue(':cell_id', clean_input($cellId), PDO::PARAM_INT);
  $stmt->execute();

  $cell_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($cell_members);
  exit;
}

/*=======================================
      Edit Cell Members' details 
          - Functionality
=======================================*/
if ($action === 'edit_cell_member') {
  $member_id = clean_input($_POST['member_id'] ?? '');

  if (!$member_id) {
    echo json_encode(["status" => "error", "message" => "Cell member ID not found!"]);
    exit;
  }

  $title = clean_input($_POST['title']);
  $first_name = clean_input($_POST['first_name']);
  $last_name = clean_input($_POST['last_name']);
  $phone_number = clean_input($_POST['phone_number']);
  $email = clean_input($_POST['email']);
  $dob_month = clean_input($_POST['dob_month']);
  $dob_day = clean_input($_POST['dob_day']);
  $occupation = clean_input($_POST['occupation']);
  $residential_address = clean_input($_POST['res_address']);
  $foundation_sch_status = clean_input($_POST['fs_status']);
  $delg_in_cell = clean_input($_POST['delg_in_cell']);
  $dept_in_church = clean_input($_POST['dept_in_church']);
  $date_joined_ministry = clean_input($_POST['date_joined_ministry']);

  // Server-side validation (first & last name required)
  if ($first_name === '' || $last_name === '') {
      echo 'First name and last name are required.';
      exit;
  }

  // Check if the email already exists
  if ($email !== '') {
    // compare case-insensitively
    $q = $conn->prepare("SELECT id FROM cell_members WHERE LOWER(email) = LOWER(?) LIMIT 1");
    $q->execute([clean_input($email)]);
    $foundId = $q->fetchColumn();

    if ($foundId && $foundId != $member_id) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Email address taken! Please use another email.'
      ]);
      exit;
    }
  }

  $stmt = $conn->prepare(
    "UPDATE cell_members 
    SET title = ?, first_name = ?, last_name = ?, phone_number = ?, email = ?, dob_month = ?, dob_day = ?, occupation = ?, residential_address = ?, foundation_sch_status = ?, delg_in_cell = ?, dept_in_church = ?, date_joined_ministry = ?
    WHERE id = ?");
  
  $success = $stmt->execute([
    clean_input($title),
    clean_input($first_name),
    clean_input($last_name),
    clean_input($phone_number),
    clean_input($email),
    clean_input($dob_month),
    clean_input($dob_day),
    clean_input($occupation),
    clean_input($residential_address),
    clean_input($foundation_sch_status),
    clean_input($delg_in_cell),
    clean_input($dept_in_church),
    clean_input($date_joined_ministry),
    clean_input($member_id)
  ]);

  if ($success) {
    echo json_encode(["status" => "success"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Failed to update member's details."]);
  }
  exit;
}


/*=======================================
      Delete Cell Members Functionality
=======================================*/
if ($action === 'delete_cell_member') {
  $member_id = clean_input($_POST['member_id'] ?? '');

  if (empty($member_id)) {
    echo json_encode([
      'status' => 'error',
      'message' => 'Missing member ID.'
    ]);
    exit;
  }

  $stmt = $conn->prepare("DELETE FROM cell_members WHERE id = ?");
  $stmt->execute([clean_input($member_id)]);

  if ($stmt->rowCount() > 0) {
    echo json_encode(['status' => 'success']);
  } else {
    echo json_encode([
      'status' => 'error',
      'message' => 'No member found or already deleted.'
    ]);
  }
  exit;
}

/*=======================================
      Generate Cell Report Draft 
          - Functionality
=======================================*/
if ($action === 'generate_report_draft') {
  $cell_id = clean_input($_SESSION['entity_id'] ?? null);
  if (!$cell_id) {
    echo json_encode(['status' => 'error', 'message' => 'Cell ID not found in session']);
    exit;
  }

  // Week calculation: week 1 starts on first Monday of the month
  $today = new DateTime();
  $year = (int)$today->format('Y');
  $month = (int)$today->format('m');
  $day = (int)$today->format('j');

  // Find first Monday of the month
  $firstOfMonth = new DateTime("$year-$month-01 00:00:00");
  $dow = (int)$firstOfMonth->format('N'); // 1=Mon, 7=Sun
  $firstMonday = clone $firstOfMonth;
  if ($dow !== 1) {
    $firstMonday->modify('next Monday');
  }
  $firstMondayDay = (int)$firstMonday->format('j');

  // Calculate week number (week 1 starts on first Monday, next weeks start on following Mondays)
  if ($day < $firstMondayDay) {
    $week = 0; // before first Monday, not a reporting week
  } else {
    $week = 1 + floor(($day - $firstMondayDay) / 7);
  }

  // If not a reporting week, do not generate draft
  if ($week < 1 || $week > 5) {
    echo json_encode(['status' => 'error', 'message' => 'Not a reporting week']);
    exit;
  }

  // Set expiry_date: Sunday 23:59:59 of the reporting week
  $draftMonday = clone $firstMonday;
  $draftMonday->modify('+' . ($week - 1) * 7 . ' days');
  $expiryDate = clone $draftMonday;
  $expiryDate->modify('next Sunday');
  $expiryDate->setTime(23, 59, 59);

  // Set description using helper
  $description = getMeetingDescription($week);

  // Set type based on week
  $type = getReportTypeByWeek($week);

  try {
    $stmt = $conn->prepare("
      INSERT INTO cell_report_drafts (type, week, description, status, date_generated, expiry_date, cell_id)
      VALUES (?, ?, ?, 'pending', NOW(), ?, ?)
    ");
    $stmt->execute([$type, $week, $description, $expiryDate->format('Y-m-d H:i:s'), clean_input($cell_id)]);

    $lastId = $conn->lastInsertId();

    if ($lastId) {
      // return the newly created row
      $sel = $conn->prepare("SELECT id, type, week, description, status, DATE_FORMAT(date_generated, '%Y-%m-%d %H:%i:%s') AS date_generated, expiry_date, cell_id FROM cell_report_drafts WHERE id = ? LIMIT 1");
      $sel->execute([clean_input($lastId)]);
      $draft = $sel->fetch(PDO::FETCH_ASSOC);

      echo json_encode(['status' => 'success', 'message' => 'Draft generated', 'draft' => $draft]);
      exit;
    } else {
      echo json_encode(['status' => 'error', 'message' => 'Failed to insert draft']);
      exit;
    }

  } catch (PDOException $ex) {
    error_log("generate_report_draft error: " . $ex->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
    exit;
  }
}

/*=======================================
        Fetch Cell Report Draft 
          - Functionality
=======================================*/
if ($action === 'fetch_report_drafts') {
  $cell_id = clean_input($_SESSION['entity_id'] ?? null);
  if (!$cell_id) {
    echo json_encode(['status' => 'error', 'message' => 'Cell ID not found in session']);
    exit;
  }

  try {
    $q = $conn->prepare("
      SELECT id, type, week, description, status, DATE_FORMAT(date_generated, '%Y-%m-%d %H:%i:%s') AS date_generated, cell_id
      FROM cell_report_drafts
      WHERE cell_id = ?
      ORDER BY date_generated ASC
    ");
    $q->execute([clean_input($cell_id)]);
    $rows = $q->fetchAll(PDO::FETCH_ASSOC);
    // Ensure description and type are set correctly for each draft
    foreach ($rows as &$row) {
      $row['description'] = getMeetingDescription($row['week']);
      $row['type'] = getReportTypeByWeek($row['week']);
    }
    echo json_encode(['status' => 'success', 'data' => $rows]);
    exit;
  } catch (PDOException $ex) {
    error_log("fetch_report_drafts error: " . $ex->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
    exit;
  }
}

/*=======================================
      Search Cells Functionality
=======================================*/
if ($action === 'search_cells') {
  $keyword = clean_input($_POST['keyword'] ?? '');
  $churchId = clean_input($_SESSION['entity_id']);
  if ($keyword === '') {
    // fallback to all
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
      AND cells.cell_name LIKE :keyword
  ");
  $stmt->bindValue(':church_id', $churchId, PDO::PARAM_INT);
  $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
  $stmt->execute();
  $cells = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($cells);
  exit;
}

/*=======================================
      Search Cell Members Functionality
=======================================*/
if ($action === 'search_cell_members') {
  $keyword = clean_input($_POST['keyword'] ?? '');
  $cellId = clean_input($_SESSION['entity_id']);
  if ($keyword === '') {
    // fallback to all
    $stmt = $conn->prepare("
      SELECT 
        id,
        CONCAT(UPPER(LEFT(title, 1)), SUBSTRING(title, 2)) AS title,
        first_name,
        last_name,
        phone_number,
        email,
        CONCAT(UPPER(LEFT(dob_month, 1)), SUBSTRING(dob_month, 2)) AS dob_month,
        dob_day,
        occupation,
        residential_address,
        CONCAT(UPPER(LEFT(foundation_sch_status, 1)), SUBSTRING(foundation_sch_status, 2)) AS foundation_sch_status,
        delg_in_cell,
        dept_in_church,
        date_joined_ministry,
        DATE_FORMAT(date_added, '%d/%m/%Y') AS date_added,
        cell_id
      FROM cell_members
      WHERE cell_id = :cell_id
    ");
    $stmt->bindValue(':cell_id', $cellId, PDO::PARAM_INT);
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($members);
    exit;
  }
  $stmt = $conn->prepare("
    SELECT 
      id,
      CONCAT(UPPER(LEFT(title, 1)), SUBSTRING(title, 2)) AS title,
      first_name,
      last_name,
      phone_number,
      email,
      CONCAT(UPPER(LEFT(dob_month, 1)), SUBSTRING(dob_month, 2)) AS dob_month,
      dob_day,
      occupation,
      residential_address,
      CONCAT(UPPER(LEFT(foundation_sch_status, 1)), SUBSTRING(foundation_sch_status, 2)) AS foundation_sch_status,
      delg_in_cell,
      dept_in_church,
      date_joined_ministry,
      DATE_FORMAT(date_added, '%d/%m/%Y') AS date_added,
      cell_id
    FROM cell_members
    WHERE cell_id = :cell_id
      AND (
        first_name LIKE :keyword
        OR last_name LIKE :keyword
        OR phone_number LIKE :keyword
        OR email LIKE :keyword
      )
  ");
  $stmt->bindValue(':cell_id', $cellId, PDO::PARAM_INT);
  $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
  $stmt->execute();
  $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($members);
  exit;
}

/*=======================================
   Fetch total cell members for church dashboard stats
=======================================*/
if ($action === 'fetch_church_cell_member_count') {
  $churchId = clean_input($_SESSION['entity_id']);
  $stmt = $conn->prepare("
    SELECT COUNT(cm.id) AS count
    FROM cell_members cm
    INNER JOIN cells c ON cm.cell_id = c.id
    WHERE c.church_id = :church_id
  ");
  $stmt->bindValue(':church_id', $churchId, PDO::PARAM_INT);
  $stmt->execute();
  $count = $stmt->fetchColumn();
  echo json_encode(['count' => intval($count)]);
  exit;
}

/*=======================================
      Submit Cell Report Form 
          - Functionality
=======================================*/
if ($action === 'submit_cell_report') {
  // Backend validation for report form
  $requiredFields = [
    'draft_id', 'cell_id', 'week', 'report_type', 'description',
    'venue', 'date', 'time', 'offering'
  ];
  $missing = [];
  foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
      $missing[] = $field;
    }
  }

  $reportType = $_POST['report_type'] ?? '';

  if ($reportType === 'outreach') {
    // outreach requires numeric attendance count, new_converts and outreach-kind
    foreach (['attendance', 'new_converts', 'outreach-kind'] as $field) {
      if (empty($_POST[$field]) && $field !== 'first_timers') {
        $missing[] = $field;
      }
    }
  } else {
    // For meeting: attendance[] required (must have at least one checked).
    if (empty($_POST['attendance']) || !is_array($_POST['attendance']) || count($_POST['attendance']) === 0) {
      $missing[] = 'attendance';
    }
    // NOTE: first_timers and new_converts are optional for meetings (do not mark missing)
  }

  if (!empty($missing)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing fields: ' . implode(', ', $missing)]);
    exit;
  }

  // Fetch draft info for values
  $draft_id = clean_input($_POST['draft_id']);
  $stmt = $conn->prepare("SELECT * FROM cell_report_drafts WHERE id = ?");
  $stmt->execute([$draft_id]);
  $draft = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$draft) {
    echo json_encode(['status' => 'error', 'message' => 'Draft not found']);
    exit;
  }

  $cell_id = clean_input($_POST['cell_id']);
  $week = $draft['week'];
  $type = $draft['type'];
  $description = $draft['description'];
  $date_generated = $draft['date_generated'];
  $expiry_date = $draft['expiry_date'];

  $venue = clean_input($_POST['venue']);
  $date = clean_input($_POST['date']);
  $time = clean_input($_POST['time']);
  $offering = clean_input($_POST['offering']);

  // Detect edit mode: if report_id present then update existing report
  $report_id = isset($_POST['report_id']) ? intval(clean_input($_POST['report_id'])) : 0;

  // Conditional logic by report type
  if ($reportType === 'outreach') {
    // outreach requires numeric attendance count, new_converts and outreach-kind
    foreach (['attendance', 'new_converts', 'outreach-kind'] as $field) {
      if (empty($_POST[$field]) && $field !== 'first_timers') {
        $missing[] = $field;
      }
    }
  } else {
    // meeting: attendance[] required (must have at least one checked).
    if (empty($_POST['attendance']) || !is_array($_POST['attendance']) || count($_POST['attendance']) === 0) {
      $missing[] = 'attendance';
    }
    // NOTE: first_timers and new_converts are optional for meetings (do not mark missing)
  }

  if (!empty($missing)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing fields: ' . implode(', ', $missing)]);
    exit;
  }

  // If editing an existing report, update instead of insert
  if ($report_id && $report_id > 0) {
    // OUTREACH update
    if ($reportType === 'outreach') {
      $attendance = intval($_POST['attendance']);
      $new_converts = intval($_POST['new_converts']);
      $outreach_kind = clean_input($_POST['outreach-kind']);
      $upd = $conn->prepare("
        UPDATE cell_reports
        SET attendance = ?, new_converts = ?, outreach_kind = ?, venue = ?, `date` = ?, `time` = ?, offering = ?, date_reported = NOW()
        WHERE id = ? AND cell_id = ?
      ");
      $success = $upd->execute([
        $attendance, $new_converts, $outreach_kind, $venue, $date, $time, $offering,
        $report_id, $cell_id
      ]);
    } else {
      // MEETING update: update main row then replace attendees
      $upd = $conn->prepare("
        UPDATE cell_reports
        SET venue = ?, `date` = ?, `time` = ?, offering = ?, date_reported = NOW()
        WHERE id = ? AND cell_id = ?
      ");
      $success = $upd->execute([
        $venue, $date, $time, $offering,
        $report_id, $cell_id
      ]);

      if ($success) {
        // Remove existing attendees for this report, then insert the new set
        $del = $conn->prepare("DELETE FROM cell_report_attendees WHERE cell_report_id = ?");
        $del->execute([$report_id]);

        $attendanceArr = isset($_POST['attendance']) && is_array($_POST['attendance']) ? $_POST['attendance'] : [];
        $firstTimersArr = isset($_POST['first_timers']) && is_array($_POST['first_timers']) ? $_POST['first_timers'] : [];
        $newConvertsArr = isset($_POST['new_converts']) && is_array($_POST['new_converts']) ? $_POST['new_converts'] : [];

        foreach ($attendanceArr as $memberId) {
          $memberId = clean_input($memberId);
          $first_timer = in_array($memberId, $firstTimersArr) ? 1 : 0;
          $new_convert = in_array($memberId, $newConvertsArr) ? 1 : 0;
          $memStmt = $conn->prepare("SELECT first_name, last_name FROM cell_members WHERE id = ?");
          $memStmt->execute([$memberId]);
          $mem = $memStmt->fetch(PDO::FETCH_ASSOC);
          $name = $mem ? ($mem['first_name'] . ' ' . $mem['last_name']) : '';
          $attStmt = $conn->prepare("
            INSERT INTO cell_report_attendees (name, first_timer, new_convert, cell_member_id, cell_report_id)
            VALUES (?, ?, ?, ?, ?)
          ");
          $attStmt->execute([$name, $first_timer, $new_convert, $memberId, $report_id]);
        }
      }
    }

    // Ensure the draft remains published (or set published)
    if ($success) {
      $updDraft = $conn->prepare("UPDATE cell_report_drafts SET status = 'published' WHERE id = ?");
      $updDraft->execute([$draft_id]);
    }

  } else {
    // INSERT (original publish behavior)
    if ($reportType === 'outreach') {
      $attendance = intval($_POST['attendance']);
      $new_converts = intval($_POST['new_converts']);
      $outreach_kind = clean_input($_POST['outreach-kind']);
      // Insert into cell_reports (first_timers NULL for outreach)
      $ins = $conn->prepare("
        INSERT INTO cell_reports (
          type, week, description, attendance, first_timers, new_converts, outreach_kind, venue, `date`, `time`, offering, date_generated, expiry_date, date_reported, cell_report_draft_id, cell_id
        ) VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)
      ");
      $success = $ins->execute([
        $type, $week, $description, $attendance, $new_converts, $outreach_kind, $venue, $date, $time, $offering,
        $date_generated, $expiry_date, $draft_id, $cell_id
      ]);
    } else {
      // meeting: attendance members inserted into attendees table; cell_reports keeps attendance-related fields NULL
      $ins = $conn->prepare("
        INSERT INTO cell_reports (
          type, week, description, attendance, first_timers, new_converts, outreach_kind, venue, `date`, `time`, offering, date_generated, expiry_date, date_reported, cell_report_draft_id, cell_id
        ) VALUES (?, ?, ?, NULL, NULL, NULL, NULL, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)
      ");
      $success = $ins->execute([
        $type, $week, $description, $venue, $date, $time, $offering,
        $date_generated, $expiry_date, $draft_id, $cell_id
      ]);

      // Insert attendees into cell_report_attendees if any attendance members were provided
      if ($success) {
        $cell_report_id = $conn->lastInsertId();
        $attendanceArr = isset($_POST['attendance']) && is_array($_POST['attendance']) ? $_POST['attendance'] : [];
        $firstTimersArr = isset($_POST['first_timers']) && is_array($_POST['first_timers']) ? $_POST['first_timers'] : [];
        $newConvertsArr = isset($_POST['new_converts']) && is_array($_POST['new_converts']) ? $_POST['new_converts'] : [];

        foreach ($attendanceArr as $memberId) {
          $memberId = clean_input($memberId);
          $first_timer = in_array($memberId, $firstTimersArr) ? 1 : 0;
          $new_convert = in_array($memberId, $newConvertsArr) ? 1 : 0;
          // Get member name for record (safe lookup)
          $memStmt = $conn->prepare("SELECT first_name, last_name FROM cell_members WHERE id = ?");
          $memStmt->execute([$memberId]);
          $mem = $memStmt->fetch(PDO::FETCH_ASSOC);
          $name = $mem ? ($mem['first_name'] . ' ' . $mem['last_name']) : '';
          $attStmt = $conn->prepare("
            INSERT INTO cell_report_attendees (name, first_timer, new_convert, cell_member_id, cell_report_id)
            VALUES (?, ?, ?, ?, ?)
          ");
          $attStmt->execute([$name, $first_timer, $new_convert, $memberId, $cell_report_id]);
        }
      }
    }
  }

  // If insert/ update succeeded, update draft status to published
  if ($success) {
    $upd = $conn->prepare("UPDATE cell_report_drafts SET status = 'published' WHERE id = ?");
    $upd->execute([$draft_id]);
    echo json_encode(['status' => 'success', 'message' => 'Report submitted and published']);
    exit;
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to submit report']);
    exit;
  }
}

// Helper function for meeting description
function getMeetingDescription($week) {
  if ($week == 1) return 'Prayer and Planning';
  if ($week == 2) return 'Bible Study Class 1';
  if ($week == 3) return 'Bible Study Class 2';
  if ($week == 4) return 'Cell Outreach';
  return 'Cell Fellowship';
}

// Helper function for report type by week
function getReportTypeByWeek($week) {
  if ($week == 4) return 'outreach';
  // week 1,2,3,5 are 'meeting'
  return 'meeting';
}

