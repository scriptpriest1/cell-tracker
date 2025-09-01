<section id="screen" class="screen w-100 h-100 m-0 p-0 m-0">
  <header class="screen-header m-0 px-3 py-2 d-flex align-items-center justify-content-between gap-3 position-sticky top-0">
    <div class="account-name-bar d-flex align-items-center gap-2">
      <h4 class="account-name m-0 p-0 fs-5">
        <?php
        if ($_SESSION['admin_type'] === 'group') {
          echo 'CE ' . $_SESSION['entity_name'] . ' Group';
        }
        if ($_SESSION['admin_type'] === 'church') {
          echo 'CE ' . $_SESSION['entity_name'] . ' Church';
        }
        if ($_SESSION['admin_type'] === 'cell') {
          echo $_SESSION['entity_name'] . ' Cell';
        }
        ?>
      </h4>
      <span class="divider">/</span>
      <span class="page-title">Dashboard</span>
    </div>
    <button class="menu-btn d-md-none p-0">
      <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="http://www.w3.org/2000/svg" width="24px" fill=""><path d="M160-240q-17 0-28.5-11.5T120-280q0-17 11.5-28.5T160-320h640q17 0 28.5 11.5T840-280q0 17-11.5 28.5T800-240H160Zm0-200q-17 0-28.5-11.5T120-480q0-17 11.5-28.5T160-520h640q17 0 28.5 11.5T840-480q0 17-11.5 28.5T800-440H160Zm0-200q-17 0-28.5-11.5T120-680q0-17 11.5-28.5T160-720h640q17 0 28.5 11.5T840-680q0 17-11.5 28.5T800-640H160Z"/></svg>
    </button>
  </header>
  <div class="body m-0 px-3 py-3">
    <!-- Dashboard page -->
    <div id="dashboard-page" class="data-container">
      <?php
      if ($_SESSION['admin_type'] === 'church') {
        include 'pages/dashboard/church-dashboard.php';
      } elseif ($_SESSION['admin_type'] === 'cell') {
        include 'pages/dashboard/cell-dashboard.php';
      }
      ?>
    </div>
    <!-- Cells Page -->
    <div id="cells-page" class="data-container d-none">
      <?php
      if ($_SESSION['admin_type'] === 'church') {
        include 'pages/cells/church-cells.php';
      }
      ?>
    </div>
    <!-- Memmbers Page -->
    <div id="members-page" class="data-container d-none">
      <?php
      if ($_SESSION['admin_type'] === 'cell') {
        include 'pages/members/cell-members.php';
      }
      ?>
    </div>
    <!-- Reports page -->
    <div id="reports-page" class="data-container d-none position-relative">
      <?php if ($_SESSION['admin_type'] === 'church') {
        include 'pages/reports/church-reports.php';
      } elseif ($_SESSION['admin_type'] === 'cell') {
        include 'pages/reports/cell-reports.php';
      }
      ?>
    </div>
    <!-- Settings page -->
    <div id="settings-page" class="data-container d-none">
      <?php if ($_SESSION['admin_type'] === 'church') {
        include 'pages/settings/church-settings.php';
      } elseif ($_SESSION['admin_type'] === 'cell') {
        include 'pages/settings/cell-settings.php';
      }
      ?>
    </div>
  </div>
</section>