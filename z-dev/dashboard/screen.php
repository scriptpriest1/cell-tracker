<section class="screen w-100 h-100 m-0 p-0 m-0">

  <header class="screen-header m-0 px-3 py-2 d-flex align-items-center justify-content-between gap-3 position-sticky top-0">
    <div class="masthead d-flex align-items-center gap-2">
      <h4 class="account m-0 p-0 fs-5">CE Orogwe Church</h4>

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

        <h3>Stats</h3>

        <div class="data-block stats-block d-grid">
          <div class="stat px-3 py-2">
            <span class="value fs-4 fw-bold d-block">10</span>
            <span class="title d-block">Total no. of cells</span>
          </div>

          <div class="stat px-3 py-2">
            <span class="value fs-4 fw-bold d-block">121</span>
            <span class="title d-block">Total no. of cell members</span>
          </div>

          <div class="stat px-3 py-2">
            <span class="value fs-4 fw-bold d-block">19</span>
            <span class="title d-block">Cell meetings reported</span>
          </div>

          <div class="stat px-3 py-2">
            <span class="value fs-4 fw-bold d-block">7</span>
            <span class="title d-block">Cell outreaches reported</span>
          </div>
        </div>

      </div>

    </div>

    <!-- Cells page -->
    <div id="cells-page" class="data-container d-none">

      <div class="cell-table-section">
        <div class="control-panel d-flex align-items-center gap-3 justify-content-between mb-2">
          <h6 class="m-0">Total no. of Cells: <span>2</span></h6>

          <button id="add-cell-btn" class="add-cell-btn px-3">Add a Cell
          </button>
        </div>

        <div class="search-bar m-0 mb-2 p-0 position-relative">
          <input type="text" class="search-input form-control w-100 pe-5" placeholder="Search by Cell name" style="border-radius: 10px; width: 100%;">
          <span class="search-icon position-absolute" style="top: 50%; right: 0; transform: translateY(-50%); padding: 0; padding-right: 12px">
            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill=""><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
          </span>
          <span class="loader d-none"></span>
        </div>

        <div class="cells-table-container w-100 p-0 m-0">
          <table class="w-100">
            <thead>
              <th>SN</th>
              <th>Cell name</th>
              <th>Date created</th>
              <th>Cell leader</th>
              <th>Cell members</th>
              <th>Action</th>
            </thead>
            <tr>
              <td colspan="6" class="no-data-found p-2 m-0 text-center">No data found</td>
            </tr>
            <tr class="d-none">
              <td>1.</td>
              <td>Lorem cell</td>
              <td>20/01/2025</td>
              <td>Steven Ifeanyi</td>
              <td>16</td>
              <td><button type="button" class="view-details-btn px-3 py-1">View details</button></td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <!-- Reports page -->
    <div id="reports-page" class="data-container d-none">
      <h3>Reports</h3>
    </div>

    <!-- Settings page -->
    <div id="settings-page" class="data-container d-none">
      <h3>Settings</h3>
    </div>

  </div>
      
</section>