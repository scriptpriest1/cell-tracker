<?php
if (isset($_POST['content-type'])) {
  $content_type = $_POST['content-type'];

  if ($content_type === 'add-a-cell-form') {
    echo <<<HTML
      <form id="add-cell-form" class="add-cell-form position-relative">
        <div class="body px-4 pt-2">
          <div class="form-group">
            <label for="cell-name">Name of Cell:</label>
            <input type="text" name="cell_name" id="cell-name" class="form-control" placeholder="e.g Haven (don't add &quot;Cell&quot; to the name)" required>
          </div>

          <div class="admin-assignment-section mt-4">
            <h6 class="title mt-1 mb-2 text-center py-2 fw-normal">Assign a Cell leader/admin to this Cell (can be done later)</h6>

            <div class="form-group">
              <label for="admin-role">Admin's role:</label>
              <select name="admin_role" id="admin-role" class="form-control form-select">
                <option value="">Select</option>
                <option value="leader">Cell Leader</option>
                <option value="executive">Cell Executive</option>
              </select>
            </div>

            <div class="form-group">
              <label for="admin-first-name">Admin's first name:</label>
              <input type="text" name="admin_first_name" id="admin-first-name" class="form-control">
            </div>

            <div class="form-group">
              <label for="admin-last-name">Admin's last name:</label>
              <input type="text" name="admin_last_name" id="admin-last-name" class="form-control">
            </div>

            <div class="form-group">
              <label for="admin-email">Admin's email:</label>
              <input type="email" name="admin_email" id="admin-email" class="form-control">
            </div>

            <div class="form-group">
              <label for="admin-password">Create Admin's login password:</label>
              <input type="password" name="admin_password" id="admin-password" class="form-control">
            </div>

            <div class="form-group">
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
}