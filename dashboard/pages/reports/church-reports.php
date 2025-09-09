<!-- Church Reports Section -->
<section class="church-reports-section">
  <div class="cells-reports" data-is-church-admin="<?= (isset($_SESSION['admin_type']) && $_SESSION['admin_type'] === 'church') ? '1' : '0' ?>">
    <div class="top-bar mb-4">
      <div class="select-group d-md-flex align-items-center gap-2">
        <label for="select-cell fw-bold">Select a Cell to view their reports:</label>
        <select name="" id="select-cell" class="form-select form-control">
          <option value="">Select Cell</option>
          <!-- Options go here -->
        </select>
      </div>
    </div>
    <!-- Append selected cell report section here -->
  </div>
</section>