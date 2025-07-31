<div id="action-modal" class="action-modal position-absolute w-100 h-100 m-0 p-0">
  <div class="modal-overlay w-100 h-100 m-0 p-0 position-relative">
    <div class="action-panel position-absolute">

      <header class="d-flex align-items-center gap-2 justify-content-between px-4 py-3 position-sticky top-0">
        <div class="title-bar">
          <h5 class="title m-0 p-0">Add a Cell</h5>
        </div>
        <div class="action-bar">
          <button type="button" class="close-btn p-0 m-0">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
              <path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/>
            </svg>
          </button>
        </div>
      </header>

      <form id="add-cell-form" class="add-cell-form position-relative">
        <div class="body px-4 pt-2">
          <div class="form-group">
            <label for="cell-name">Name of Cell:</label>
            <input type="text" name="cell_name" id="cell-name" class="form-control" required>
          </div>

          <div class="admin-assignment-section mt-3">
            <h6 class="title mb-2 fw-normal">Assign a Cell leader/admin to this Cell (can be done later)</h6>

            <div class="form-group">
              <label for="admin-role">Admin's role:</label>
              <select name="admin_role" id="admin-role" class="form-control form-select">
                <option value="">No selected role</option>
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
    </div>
  </div>
</div>