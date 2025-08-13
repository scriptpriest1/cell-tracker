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
            <label for="cell-name" class="">Name of Cell: &nbsp; <span class="text-warning d-block d-md-inline-block" style="font-size: 14px; margin-top: -5px;">(Don't add "Cell" to the name)</span></label>
            <input type="text" name="cell_name" id="cell-name" class="form-control" placeholder="e.g &quot;Haven&quot; not &quot;Haven Cell&quot;" required>
          </div>

          <div class="admin-assignment-section mt-4">
            <h6 class="title mt-1 mb-2 text-center py-2 fw-normal">Assign a Cell leader/admin to this Cell (can be done later)</h6>

            <div class="form-group">
              <label for="choose-admin">Choose who to assign:</label>
              <select name="choose_admin" id="choose-admin" class="form-control form-select">
                <option value="">Select</option>
                <option value="self">Assign yourself</option>
                <option value="else">Assign someone else</option>
              </select>
            </div>

            <div class="form-group role-container d-none">
              <label for="admin-role">Role:</label>
              <select name="admin_role" id="admin-role" class="form-control form-select">
                <option value="">Select</option>
                <option value="leader">Cell Leader</option>
                <option value="executive">Cell Executive</option>
              </select>
            </div>

            <div class="hidden-section mt-3 d-none">
              <div class="form-group">
                <label for="admin-first-name">First name:</label>
                <input type="text" name="admin_first_name" id="admin-first-name" class="form-control">
              </div>

              <div class="form-group">
                <label for="admin-last-name">Last name:</label>
                <input type="text" name="admin_last_name" id="admin-last-name" class="form-control">
              </div>

              <div class="form-group">
                <label for="admin-email">Email:</label>
                <input type="email" name="admin_email" id="admin-email" class="form-control">
              </div>

              <div class="form-group">
                <label for="admin-phone">Phone number:</label>
                <input type="phone" name="admin_phone" id="admin-phone" class="form-control">
              </div>

              <div class="form-group">
                <label for="admin-password">Create login password:</label>
                <input type="password" name="admin_password" id="admin-password" class="form-control">
              </div>

              <div class="form-group">
                <label for="admin-password-confirm">Confirm password:</label>
                <input type="password" name="admin_password_confirm" id="admin-password-confirm" class="form-control">
              </div>
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
              <label for="phone">Phone number:</label>
              <input type="phone" name="phone" id="phone" class="form-control">
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
      echo "Cannot access Cell";
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

            <!-- Dropdown Toggle -->
            <div class="dropdown static">
              <button
                class="px-2 py-0 m-0"
                type="button"
                id="adminOptions<?= $id ?>"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false"
              >
                ⋮
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminOptions<?= $id ?>">
                <?php if ($email !== $user_login): ?>
                  <li>
                    <a 
                      class="dropdown-item load-action-modal-dyn-content edit-admin-btn" 
                      href="#" 
                      data-content-type="edit-cell-admin"
                      data-cell-id="<?= $cell_id ?>"
                      data-admin-id="<?= $id ?>"
                    >Edit</a>
                  </li>
                <?php endif; ?>
                <li>
                  <?php if ($email === $user_login): ?>
                    <a class="dropdown-item unassign-btn" href="#" data-content-type="fetch-cell-admins" data-user-id="<?= $id ?>" data-cell-id="<?= $cell_id ?>">Unassign</a>
                  <?php else: ?>
                    <a class="dropdown-item delete unassign-btn" href="#" data-user-id="<?= $id ?>" data-cell-id="<?= $cell_id ?>">Remove Admin</a>
                  <?php endif; ?>
                </li>
              </ul>
            </div>
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

            <!-- Dropdown Toggle -->
            <div class="dropdown static">
              <button
                class="px-2 py-0 m-0"
                type="button"
                id="adminOptions<?= $id ?>"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false"
              >
                ⋮
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminOptions<?= $id ?>">
                <?php if ($email !== $user_login): ?>
                  <li>
                    <a 
                      class="dropdown-item load-action-modal-dyn-content edit-admin-btn" 
                      href="#" 
                      data-content-type="edit-cell-admin"
                      data-cell-id="<?= $cell_id ?>"
                      data-admin-id="<?= $id ?>"
                    >Edit</a>
                  </li>
                <?php endif; ?>
                <li>
                  <?php if ($email === $user_login): // self ?>
                    <a class="dropdown-item unassign-btn" href="#" data-content-type="fetch-cell-admins" data-user-id="<?= $id ?>" data-cell-id="<?= $cell_id ?>">Unassign</a>
                  <?php else: ?>
                    <a class="dropdown-item delete unassign-btn" href="#" data-user-id="<?= $id ?>" data-cell-id="<?= $cell_id ?>">Remove Admin</a>
                  <?php endif; ?>
                </li>
              </ul>
            </div>
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

            <!-- Dropdown Toggle -->
            <div class="dropdown static">
              <button
                class="px-2 py-0 m-0"
                type="button"
                id="adminOptions<?= $id ?>"
                data-bs-toggle="dropdown"
                data-bs-display="static"
                aria-expanded="false"
              >
                ⋮
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminOptions<?= $id ?>">
                <?php if ($email !== $user_login): ?>
                  <li>
                    <a 
                      class="dropdown-item load-action-modal-dyn-content edit-admin-btn" 
                      href="#" 
                      data-content-type="edit-cell-admin"
                      data-cell-id="<?= $cell_id ?>"
                      data-admin-id="<?= $id ?>"
                    >Edit</a>
                  </li>
                <?php endif; ?>
                <li>
                  <?php if ($email === $user_login): // self ?>
                    <a class="dropdown-item unassign-btn" href="#" data-content-type="fetch-cell-admins" data-user-id="<?= $id ?>" data-cell-id="<?= $cell_id ?>">Unassign</a>
                  <?php else: ?>
                    <a class="dropdown-item delete unassign-btn" href="#" data-user-id="<?= $id ?>" data-cell-id="<?= $cell_id ?>">Remove Admin</a>
                  <?php endif; ?>
                </li>
              </ul>
            </div>
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

  /*=======================================
      Fetch form for Editing a Cell Admin
            - Functionality
  =======================================*/
  if ($content_type === 'edit-cell-admin') {
    $cell_id = $_POST['cell-id'] ?? null;
    $admin_id = $_POST['admin-id'] ?? null;

    if (!$admin_id) {
      echo "<p class='text-center mt-4'>Cannot access Admin</p>";
      exit;
    } 

    $stmt = $conn->prepare("SELECT cell_role, first_name, last_name, user_login, phone_number FROM users WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
      echo "<p class='text-center mt-4'>Admin not found</p>";
      exit;
    }

    $role = htmlspecialchars($admin['cell_role']);
    $first_name = htmlspecialchars($admin['first_name']);
    $last_name = htmlspecialchars($admin['last_name']);
    $email = htmlspecialchars($admin['user_login']);
    $phone_number = htmlspecialchars($admin['phone_number']);

    ob_start();
    ?>
      <form id="edit-cell-admin-form" class="action-modal-form load-action-modal-dyn-content position-relative pt-2" data-content-type="fetch-cell-admins" data-cell-id="<?= $cell_id ?>">
        <input type="hidden" name="cell_id" value="<?= $cell_id ?>" id="cell-id" />
        <input type="hidden" name="admin_id" value="<?= $admin_id ?>" id="admin-id" />
        <div class="body px-4 pt-2">
          <div class="form-group">
            <label for="role">Role:</label>
            <select name="role" id="role" class="form-control form-select">
              <option value="<?= $role ?>" selected>Default</option>
              <option value="leader">Cell Leader</option>
              <option value="executive">Cell Executive</option>
            </select>
          </div>

          <div class="form-group">
            <label for="first-name">First name:</label>
            <input
              type="text"
              name="first_name"
              id="first-name"
              class="form-control"
              value="<?= $first_name ?>"
            />
          </div>

          <div class="form-group">
            <label for="last-name">Last name:</label>
            <input
              type="text"
              name="last_name"
              id="last-name"
              class="form-control"
              value="<?= $last_name ?>"
            />
          </div>

          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= $email ?>"/>
          </div>

          <div class="form-group">
            <label for="phone">Phone number:</label>
            <input type="phone" name="phone" id="phone" class="form-control" value="<?= $phone_number ?>"/>
          </div>
        </div>

        <footer class="position-absolute bottom-0 py-3 px-4 w-100 d-flex align-items-center gap-2">
          <button type="button" class="cancel-btn w-100">Cancel</button>
          <button type="submit" class="submit-btn w-100">Save</button>
        </footer>
      </form>
    <?php
    $form = ob_get_clean();
    echo $form;

    exit;
  }
}