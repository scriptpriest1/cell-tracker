<!-- Cells Table -->
<div class="cell-table-section">
  <div class="control-panel d-flex align-items-center gap-3 justify-content-between mb-2">
    <h6 class="m-0">Total No.: &nbsp;<span class="cell-count span-box fw-normal"></span></h6>
    <button id="add-cell-btn" class="load-action-modal-dyn-content add-cell-btn px-3" data-content-type="add-a-cell-form">Add a Cell
    </button>
  </div>
  <div class="search-bar m-0 mb-2 p-0 position-relative">
    <input type="text" class="search-input form-control w-100 pe-5" placeholder="Search" style="border-radius: 10px; width: 100%;">
    <span class="search-icon position-absolute" style="top: 50%; right: 0; transform: translateY(-50%); padding: 0; padding-right: 12px">
      <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="http://www.w3.org/2000/svg" width="20px" fill=""><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
    </span>
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
  <div id="cells-table-info-block" class="table-info-block">
    <p class="info p-2 m-0 text-center"></p>
  </div>
</div>

