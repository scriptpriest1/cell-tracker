<?php
session_start();
include 'connect_db.php';   // sets up $conn as a PDO instance
include 'functions.php';

if (isset($_POST['content-type'])) {
  $content_type = $_POST['content-type'];

  if ($content_type === 'add-a-cell-form') {
    echo <<<HTML
      <form id="add-cell-form" class="action-modal-form position-relative">
        <div class="body px-4 pt-2">
          <div class="form-group">
            <label for="cell-name">Name of Cell:</label>
            <input type="text" name="cell_name" id="cell-name" class="form-control" placeholder="e.g Haven (don't add &quot;Cell&quot; to the name)" required>
          </div>

          <div class="admin-assignment-section mt-4">
            <h6 class="title mt-1 mb-2 text-center py-2 fw-normal">Assign a Cell leader/admin to this Cell (can be done later)</h6>

            <div class="d-flex gap-3 justify-content-center mb-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="assign_to" value="self" id="assign-self">
                <label class="form-check-label" for="assign-self">Assign Myself</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="assign_to" value="someone_else" id="assign-someone-else">
                <label class="form-check-label" for="assign-someone-else">Assign Someone Else</label>
              </div>
            </div>

            <div class="form-group">
              <label for="admin-role">Admin's role:</label>
              <select name="admin_role" id="admin-role" class="form-control form-select">
                <option value="">Select</option>
                <option value="leader">Cell Leader</option>
                <option value="executive">Cell Executive</option>
              </select>
            </div>

            <div class="form-group other-only">
              <label for="admin-first-name">Admin's first name:</label>
              <input type="text" name="admin_first_name" id="admin-first-name" class="form-control">
            </div>

            <div class="form-group other-only">
              <label for="admin-last-name">Admin's last name:</label>
              <input type="text" name="admin_last_name" id="admin-last-name" class="form-control">
            </div>

            <div class="form-group other-only">
              <label for="admin-email">Admin's email:</label>
              <input type="email" name="admin_email" id="admin-email" class="form-control">
            </div>

            <div class="form-group other-only">
              <label for="admin-password">Create Admin's login password:</label>
              <input type="password" name="admin_password" id="admin-password" class="form-control">
            </div>

            <div class="form-group other-only">
              <label for="admin-password-confirm">Confirm password:</label>
              <input type="password" name="admin_password_confirm" id="admin-password-confirm" class="form-control">
            </div>
          </div>
        </div>

        <footer class="position-absolute bottom-0 py-3 px-4 w-100">
          <button type="submit" class="submit-btn w-100" disabled>Add Cell</button>
        </footer>
      </form>
    HTML;
  }

  if ($content_type === 'assign-cell-admin') {
    echo <<<HTML
      <form id="assign-cell-admin-form" class="action-modal-form position-relative">
        <input type="hidden" name="cell_id" value="" id="cell-id">
        <div class="body px-4 pt-2">
          <div class="form-group">
            <label for="choose-admin">Choose who to assign:</label>
            <select
              name="choose_admin"
              id="choose-admin"
              class="form-control form-select"
            >
              <option value="">Select</option>
              <option value="self">Assign youself</option>
              <option value="else">Assign someone else</option>
            </select>
          </div>

          <div class="form-group role-container d-none">
            <label for="role">Role:</label>
            <select
              name="role"
              id="role"
              class="form-control form-select"
            >
              <option value="">Select</option>
              <option value="leader">Cell Leader</option>
              <option value="executive">Cell Executive</option>
            </select>
          </div>

          <div class="hidden-section mt-3 d-none">
            <div class="form-group">
              <label for="first-name">First name:</label>
              <input
                type="text"
                name="first_name"
                id="first-name"
                class="form-control"
              />
            </div>

            <div class="form-group">
              <label for="last-name">Last name:</label>
              <input
                type="text"
                name="last_name"
                id="last-name"
                class="form-control"
              />
            </div>

            <div class="form-group">
              <label for="email">Email:</label>
              <input
                type="email"
                name="email"
                id="email"
                class="form-control"
              />
            </div>

            <div class="form-group">
              <label for="password">Create login password:</label>
              <input
                type="password"
                name="password"
                id="password"
                class="form-control"
              />
            </div>

            <div class="form-group">
              <label for="password-confirm">Confirm password:</label>
              <input
                type="password"
                name="password_confirm"
                id="password-confirm"
                class="form-control"
              />
            </div>
          </div>
        </div>

        <footer class="position-absolute bottom-0 py-3 px-4 w-100">
          <button type="submit" class="submit-btn w-100" disabled>Assign</button>
        </footer>
      </form>
    HTML;
  }

  /*=======================================
      Fetch Admins for a viewed Cell 
            - Functionality
  =======================================*/
  if ($content_type === 'view-cell-details' || $content_type === 'fetch-cell-admins') {
    $cell_id = $_POST['cell-id'] ?? null;

    if (!$cell_id) {
      echo "Missing cell ID";
      exit;
    }

    $user_login = $_SESSION['user_login'];
    $admins = [];

    // Fetch all users assigned to this cell
    $stmt = $conn->prepare("SELECT id, first_name, last_name, user_login, cell_role, church_id FROM users WHERE cell_id = ?");
    $stmt->execute([$cell_id]);
    $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $loggedInUser = null;
    $cellLeader = null;
    $others = [];

    foreach ($all_users as $user) {
      $name = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
      $email = htmlspecialchars($user['user_login']);
      $userId = $user['id'];
      $labels = [];

      if ($email === $user_login) {
        $labels[] = 'You';
        if ($user['cell_role'] === 'leader') {
          $labels[] = 'Cell Leader';
        }
        $loggedInUser = [
          'id' => $userId,
          'name' => $name,
          'email' => $email,
          'labels' => $labels,
        ];
        continue;
      }

      if ($user['cell_role'] === 'leader') {
        $labels[] = 'Cell Leader';
        $cellLeader = [
          'id' => $userId,
          'name' => $name,
          'email' => $email,
          'labels' => $labels,
        ];
        continue;
      }

      $others[] = [
        'id' => $userId,
        'name' => $name,
        'email' => $email,
        'labels' => [],
      ];
    }

    ob_start();
    ?>
    <ol class="cell-admins-list p-0 m-0 ps-4">
      <?php
      if ($loggedInUser):
        $labelText = !empty($loggedInUser['labels']) ? ' (' . implode(') (', $loggedInUser['labels']) . ')' : '';
        $nameWithLabels = $loggedInUser['name'] . $labelText;
        $email = $loggedInUser['email'];
        $id = $loggedInUser['id'];
      ?>
        <li>
          <div class="d-flex justify-content-between gap-3 align-items-start">
            <div class="identity">
              <p class="admin-name p-0 m-0"><?= $nameWithLabels ?></p>
              <p class="admin-email p-0 m-0"><?= $email ?></p>
            </div>
            <button type="button" class=" px-3 py-1 unassign-btn" data-user-id="<?= $id ?>" data-content-type="fetch-cell-admins" data-cell-id="<?= $cell_id ?>">Unassign</button>
          </div>
        </li>
      <?php endif; ?>

      <?php
      if ($cellLeader && (!$loggedInUser || $cellLeader['email'] !== $loggedInUser['email'])):
        $labelText = !empty($cellLeader['labels']) ? ' (' . implode(') (', $cellLeader['labels']) . ')' : '';
        $nameWithLabels = $cellLeader['name'] . $labelText;
        $email = $cellLeader['email'];
        $id = $cellLeader['id'];
      ?>
        <li>
          <div class="d-flex justify-content-between gap-3 align-items-start">
            <div class="identity">
              <p class="admin-name p-0 m-0"><?= $nameWithLabels ?></p>
              <p class="admin-email p-0 m-0"><?= $email ?></p>
            </div>
            <button type="button" class=" px-3 py-1 unassign-btn delete" data-user-id="<?= $id ?>" data-content-type="fetch-cell-admins" data-cell-id="<?= $cell_id ?>">Remove</button>
          </div>
        </li>
      <?php endif; ?>

      <?php foreach ($others as $admin):
        $name = $admin['name'];
        $email = $admin['email'];
        $id = $admin['id'];
      ?>
        <li>
          <div class="d-flex justify-content-between gap-3 align-items-start">
            <div class="identity">
              <p class="admin-name p-0 m-0"><?= $name ?></p>
              <p class="admin-email p-0 m-0"><?= $email ?></p>
            </div>
            <button type="button" class=" px-3 py-1 unassign-btn delete" data-user-id="<?= $id ?>" data-content-type="fetch-cell-admins" data-cell-id="<?= $cell_id ?>">Remove</button>
          </div>
        </li>
      <?php endforeach; ?>
    </ol>
    <?php
    $html = ob_get_clean();

    echo <<<HTML
      <div class="action-modal-inner scrollable px-4 pt-2">
        <span class="p-0 pb-1 m-0 mb-2 fw-bold">Admins</span>
        <div class="cell-admins-list-container mt-2">
          {$html}
          <p class="text-center admins-list-info m-0 p-0 fs-6"></p>
        </div>
      </div>
    HTML;

    exit;
  }

}