<!-- Cell Reports Section -->
<section class="reports-section">
  <div class="reports-status-bar d-flex align-items-center gap-4 mt-2">
    <div class="report-status published">
      <h6 class="text m-0 p-0">Published: <span class="count span-box"></span></h6>
      
    </div>
    <div class="report-status pending">
      <h6 class="text m-0 p-0">Pending: <span class="count span-box"></span></h6>
      
    </div>
    <div class="report-status unpublished">
      <h6 class="text m-0 p-0">Unpublished: <span class="count span-box"></span></h6>
      
    </div>
  </div>
  <div class="filter-bar d-flex align-items-center gap-3 mt-4">
    <button id="all" class="filter active">All</button>
    <button id="meeting" class="filter">Meetings</button>
    <button id="outreach" class="filter">Outreaches</button>
  </div>
  <div class="reports-body">
    <button type="button" id="create-draft-btn" class="bg-dark text-white position-absolute top-0 end-0">Create draft</button>
    <!-- Report block dynamically inserted here -->
  </div>
</section>