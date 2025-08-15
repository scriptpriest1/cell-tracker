<section id="screen"class="screen w-100 h-100 m-0 p-0 m-0">

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
      <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill=""><path d="M160-240q-17 0-28.5-11.5T120-280q0-17 11.5-28.5T160-320h640q17 0 28.5 11.5T840-280q0 17-11.5 28.5T800-240H160Zm0-200q-17 0-28.5-11.5T120-480q0-17 11.5-28.5T160-520h640q17 0 28.5 11.5T840-480q0 17-11.5 28.5T800-440H160Zm0-200q-17 0-28.5-11.5T120-680q0-17 11.5-28.5T160-720h640q17 0 28.5 11.5T840-680q0 17-11.5 28.5T800-640H160Z"/></svg>
    </button>
  </header>

  <div class="body m-0 px-3 py-3">

    <!-- Dashboard page -->
    <div id="dashboard-page" class="data-container">

      <div class="stats">

        <!-- <h3>Stats</h3> -->

        <?php if ($_SESSION['admin_type'] === 'church'): ?>
        <div class="data-block stats-block d-grid">
          <div class="stat px-3 py-2">
            <span class="value fs-4 fw-bold d-block cell-count">--</span>
            <span class="title d-block">Cells</span>
          </div>

          <div class="stat px-3 py-2">
            <span class="value fs-4 fw-bold d-block">--</span>
            <span class="title d-block">Total cell members</span>
          </div>

          <div class="stat px-3 py-2">
            <span class="value fs-4 fw-bold d-block">--</span>
            <span class="title d-block">Total cell meetings reported</span>
          </div>

          <div class="stat px-3 py-2">
            <span class="value fs-4 fw-bold d-block">--</span>
            <span class="title d-block">Total cell outreaches reported</span>
          </div>
        </div>
        <?php endif ?>

        <?php if ($_SESSION['admin_type'] === 'cell'): ?>
        <div class="data-block stats-block d-grid">
          <div class="stat px-3 py-2">
            <span class="value fs-4 fw-bold d-block cell-member-count">--</span>
            <span class="title d-block">Cell members</span>
          </div>

          <div class="stat px-3 py-2">
            <span class="value fs-4 fw-bold d-block">--</span>
            <span class="title d-block">Cell meetings reported</span>
          </div>

          <div class="stat px-3 py-2">
            <span class="value fs-4 fw-bold d-block">--</span>
            <span class="title d-block">Cell outreaches reported</span>
          </div>
        </div>
        <?php endif ?>

      </div>

    </div>

    <?php if ($_SESSION['admin_type'] === 'church'): ?>
    <!-- Cells page -->
    <div id="cells-page" class="data-container d-none">

      <div class="cell-table-section">
        <div class="control-panel d-flex align-items-center gap-3 justify-content-between mb-2">
          <h6 class="m-0">Total No.: &nbsp;<span class="cell-count span-box fw-normal"></span></h6>

          <button id="add-cell-btn" class="load-action-modal-dyn-content add-cell-btn px-3" data-content-type="add-a-cell-form">Add a Cell
          </button>
        </div>

        <div class="search-bar m-0 mb-2 p-0 position-relative">
          <input type="text" class="search-input form-control w-100 pe-5" placeholder="Search" style="border-radius: 10px; width: 100%;">
          <span class="search-icon position-absolute" style="top: 50%; right: 0; transform: translateY(-50%); padding: 0; padding-right: 12px">
            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill=""><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
          </span>
          <span class="loader d-none"></span>
        </div>

        <div class="cells-table-container w-100 p-0 m-0">
          <table class="w-100" id="cells-table">
            <thead>
              <tr>
                <th class="sn-col">SN</th>
                <th>Cell name</th>
                <th>Date created</th>
                <th>Cell leader</th>
                <th>Cell members</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>

        </div>
      </div>

      <div id="cells-table-info-block" class="table-info-block">
        <p class="info p-2 m-0 text-center"></p>
      </div>
    </div>
    <?php endif ?>

    <?php if ($_SESSION['admin_type'] === 'cell'): ?>
    <!-- Cell Members page -->
    <div id="cell-members-page" class="data-container d-none">

      <div class="cell-table-section">
        <div class="control-panel d-flex align-items-center gap-3 justify-content-between mb-2">
          <h6 class="m-0">Total No.: &nbsp;<span class="cell-member-count span-box fw-normal"></span></h6>

          <button id="add-members-btn" class="load-action-modal-dyn-content add-member-btn px-3" data-content-type="add-cell-member-form">Add members
          </button>
        </div>

        <div class="search-bar m-0 mb-2 p-0 position-relative">
          <input type="text" class="search-input form-control w-100 pe-5" placeholder="Search" style="border-radius: 10px; width: 100%;">
          <span class="search-icon position-absolute" style="top: 50%; right: 0; transform: translateY(-50%); padding: 0; padding-right: 12px">
            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill=""><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
          </span>
          <span class="loader d-none"></span>
        </div>

        <div class="cells-table-container w-100 p-0 m-0">
          <table class="w-100" id="cell-members-table">
            <thead>
              <tr>
                <th class="sn-col">SN</th>
                <th>Title</th>
                <th>First name</th>
                <th>Last name</th>
                <th>Phone number</th>
                <th>Email</th>
                <th>Date of birth</th>
                <th>Occupation</th>
                <th>Residencial address</th>
                <th>Foundation school status</th>
                <th>Delegation in cell</th>
                <th>Dept in church</th>
                <th>Date joined ministry</th>
                <th>Date added</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>

        </div>

        <div id="cell-members-table-info-block" class="table-info-block">
          <p class="info p-2 m-0 text-center"></p>
        </div>

      </div>

    </div>
    <?php endif ?>

    <!-- Reports page -->
    <div id="reports-page" class="data-container d-none">
      <!-- Church Dashboard Reports Section -->
      <?php if ($_SESSION['admin_type'] === 'church'): ?>
      <section class="reports-section">
        Church Report Section
      </section>
      <?php endif ?>

      <!-- Cell Dashboard Reports Section -->
      <?php if ($_SESSION['admin_type'] === 'cell'): ?>
      <section class="reports-section">

        <div class="reports-status-bar d-flex align-items-center gap-4 mt-1">
          <div class="report-status published d-flex align-items-center gap-2">
            <h6 class="text m-0 p-0">Published:</h6>
            <span class="count span-box">0</span>
          </div>

          <div class="report-status pending d-flex align-items-center gap-2">
            <h6 class="text m-0 p-0">Pending:</h6>
            <span class="count span-box">0</span>
          </div>

          <div class="report-status unpublished d-flex align-items-center gap-2">
            <h6 class="text m-0 p-0">Unpublished:</h6>
            <span class="count span-box">0</span>
          </div>
        </div>

        <div class="filter-bar d-flex align-items-center gap-3 mt-4">
          <button id="all" class="filter active">All</button>
          <button id="meetings" class="filter">Meetings</button>
          <button id="outreached" class="filter">Outreaches</button>
        </div>

        <div class="reports-body">

          <div class="reports-block mt-4">
            <div class="date-bar">
              <h5 class="date">Aug 2025</h5>

              <div class="reports-container mt-2">
                <div class="report" data-report-type="meeting">

                </div>
              </div>
            </div>
          </div>

        </div>

      </section>
      <?php endif ?>
    </div>

    <!-- Settings page -->
    <div id="settings-page" class="data-container d-none">
      <h3>Settings</h3>
    </div>

  </div>
      
</section>