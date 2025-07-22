<div id="action-modal" class="action-modal position-absolute w-100 h-100 m-0 p-0">
  <div class="modal-overlay w-100 h-100 m-0 p-0 position-relative">
    <div class="action-panel position-absolute">

      <header class="d-flex align-items-center gap-2 justify-content-between px-4 py-3 position-sticky top-0">
        <div class="title-bar">
          <h5 class="title m-0 p-0">Create a Cell</h5>
        </div>
        <div class="action-bar">
          <button type="button" class="close-btn p-0 m-0">Close</button>
        </div>
      </header>

      <form action="" class="add-cell-form p-0 m-0 position-relative">
        <div class="body px-4 pt-2">
          <div class="form-group">
            <label for="">Name of Cell:</label>
            <input type="text" class="form-control" required>
          </div>

          <div class="admin-assignment-section mt-3">
            <h6 class="title mb-2 fw-normal">Assign a Cell leader/admin to this Cell (can be done later)</h6>

            <div class="form-group">
              <label for="">Admin's role:</label>
              <select class="form-control form-select">
                <option value="">Cell Leader</option>
                <option value="">Cell Executive</option>
              </select>
            </div>

            <div class="form-group">
              <label for="">Admin's first name:</label>
              <input type="text" class="form-control">
            </div>

            <div class="form-group">
              <label for="">Admin's last name:</label>
              <input type="text" class="form-control">
            </div>

            <div class="form-group">
              <label for="">Admin's email:</label>
              <input type="email" class="form-control">
            </div>

            <div class="form-group">
              <label for="">Create Admin's login password:</label>
              <input type="password" class="form-control">
            </div>

            <div class="form-group">
              <label for="">Confirm password:</label>
              <input type="password" class="form-control">
            </div>
          </div>
          
        </div>
        <footer class="position-absolute bottom-0 py-3 px-4 m-0 w-100">
          <button type="submit" class="submit-btn w-100">Add Cell</button>
        </footer>
      </form>

    </div>
  </div>
</div>