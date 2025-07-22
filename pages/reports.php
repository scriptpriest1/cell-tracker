<div class="content">
  <div class="block">
    <div id="report-modal-box">
      <div id="report-modal">
        <div id="report-form-container">
          <form id="report-form">
            <div class="field-group">
              <label for="members-attendance-dropdown">Attendance</label>
              <div id="members-attendance-dropdown" class="custom-dropdown">
                <div class="dropdown-title">0</div>
                <div class="dropdown-menu">
                  <label><input type="checkbox" value="Alice" /> Alice</label>
                  <label><input type="checkbox" value="Bob" /> Bob</label>
                  <label
                    ><input type="checkbox" value="Charlie" /> Charlie</label
                  >
                  <label><input type="checkbox" value="David" /> David</label>
                  <label><input type="checkbox" value="Eva" /> Eva</label>
                </div>
              </div>
            </div>
            <div class="field-group">
              <label for="venue-dropdown">Venue</label>
              <div id="venue-dropdown" class="custom-single-dropdown">
                <div class="dropdown-title">Select</div>
                <div class="dropdown-menu">
                  <div class="option" data-value="Main Venue">Main Venue</div>
                  <div
                    class="option"
                    data-value="Custom"
                    style="color: #7b8ebd"
                  >
                    Add somewhere else
                  </div>
                  <input
                    type="text"
                    class="custom-input hidden"
                    placeholder="Press the Enter Key to enter your venue"
                  />
                </div>
              </div>
            </div>
            <div class="field-group">
              <label for="first-timers-dropdown">First timers</label>
              <div id="first-timers-dropdown" class="custom-dropdown">
                <div class="dropdown-title">0</div>
                <div class="dropdown-menu">
                  <label><input type="checkbox" value="Alice" /> Alice</label>
                  <label><input type="checkbox" value="Bob" /> Bob</label>
                  <label
                    ><input type="checkbox" value="Charlie" /> Charlie</label
                  >
                  <label><input type="checkbox" value="David" /> David</label>
                  <label><input type="checkbox" value="Eva" /> Eva</label>
                </div>
              </div>
            </div>
            <div class="field-group">
              <label for="new-converts-dropdown">New converts</label>
              <div id="new-converts-dropdown" class="custom-dropdown">
                <div class="dropdown-title">0</div>
                <div class="dropdown-menu">
                  <label><input type="checkbox" value="Alice" /> Alice</label>
                  <label><input type="checkbox" value="Bob" /> Bob</label>
                  <label
                    ><input type="checkbox" value="Charlie" /> Charlie</label
                  >
                  <label><input type="checkbox" value="David" /> David</label>
                  <label><input type="checkbox" value="Eva" /> Eva</label>
                </div>
              </div>
            </div>
            <div class="field-group">
              <label for="">Offering</label>
              <input type="text" id="offr-input" placeholder="Enter amount" />
            </div>
            <div class="footer">
              <button type="submit" id="report-form-publ-btn">
                Publish Report
              </button>
              <button
                type="button"
                id="report-form-close-btn"
                class="flex items-center"
              >Close
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <h2 class="heading">Reports</h2>

    <section id="reports-section">
      <div id="cat-nav-bar">
        <button id="cm-cat-btn" class="cat-btn active">Meetings</button>

        <button id="o-cat-btn" class="cat-btn">Tab 2</button>
      </div>

      <div id="report-cards-container">
        <div class="card-group">
          <div class="date-block">
            <h3 class="date">Feb 2025</h3>
          </div>

          <div class="card">
            <div class="card-item">
              <p>Week 1: <i>Prayer and Planning</i></p>
            </div>

            <button class="publ-btn" id="publ-btn">
              <span>Publish</span>
              <span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  height="24px"
                  viewBox="0 -960 960 960"
                  width="24px"
                  fill="#2eac57"
                >
                  <path
                    d="M440-320v-326L336-542l-56-58 200-200 200 200-56 58-104-104v326h-80ZM240-160q-33 0-56.5-23.5T160-240v-120h80v120h480v-120h80v120q0 33-23.5 56.5T720-160H240Z"
                  />
                </svg>
              </span>
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<script>
  $(document).ready(() => {
    const $reportModalBox = $('#report-modal-box');
    const $reportFormContainer = $('#report-form-container');

    $('.card .publ-btn').on('click', () => {
      displayReportFormModal();
    });

    $('.cat-btn').on('click', function () {
      $('.cat-btn').removeClass('active');
      $(this).addClass('active');
    });

    $('#report-form-close-btn').on('click', function () {
      displayReportFormModal();
    });


    function displayReportFormModal() {
      if ($reportModalBox.is(':visible')) {
        // Hide with animation
        $reportModalBox.animate(
          {
            opacity: 0,
          },
          200,
          function () {
            $(this).css('display', 'none');
          },
        );
        $reportFormContainer.animate(
          {
            scale: 0,
          },
          200,
        );
      } else {
        // Reveal with animation
        $reportModalBox.css({ display: 'block', opacity: 0 }).animate(
          {
            opacity: 1,
          },
          200,
        );
        $reportFormContainer.animate(
          {
            scale: 1,
          },
          200,
        );
      }
    }

    // Multi-select Dropdown: Toggle menu and update count
    $('.custom-dropdown .dropdown-title').on('click', function () {
      $(this).siblings('.dropdown-menu').toggle();
    });

    $('.custom-dropdown .dropdown-menu input[type="checkbox"]').on(
      'change',
      function () {
        let dropdown = $(this).closest('.custom-dropdown');
        let count = dropdown.find('input[type="checkbox"]:checked').length;
        dropdown.find('.dropdown-title').text(count);
      },
    );

    // Hide multi-select dropdown when clicking outside
    $(document).on('click', function (e) {
      if (!$(e.target).closest('.custom-dropdown').length) {
        $('.custom-dropdown .dropdown-menu').hide();
      }
    });

    // Single-select Dropdown with Custom Input (for Venue)
    // Toggle dropdown menu when title is clicked
    $('#venue-dropdown .dropdown-title').on('click', function () {
      $(this).siblings('.dropdown-menu').toggle();
    });

    // Handle option clicks in single-select dropdown
    $('#venue-dropdown .dropdown-menu .option').on('click', function () {
      let value = $(this).data('value');
      if (value === 'Custom') {
        $('#venue-dropdown .custom-input').removeClass('hidden').focus();
      } else {
        $('#venue-dropdown .dropdown-title').text(value);
        $('#venue-dropdown .dropdown-menu').hide();
      }
    });

    // Process custom input when Enter is pressed
    $('#venue-dropdown .custom-input').on('keypress', function (e) {
      if (e.which === 13) {
        // Enter key
        e.preventDefault();
        let customValue = $(this).val().trim();
        if (customValue !== '') {
          $('#venue-dropdown .dropdown-title').text(customValue);
        }
        $(this).addClass('hidden').val('');
        $('#venue-dropdown .dropdown-menu').hide();
      }
    });

    // Hide single-select dropdown when clicking outside
    $(document).on('click', function (e) {
      if (!$(e.target).closest('#venue-dropdown').length) {
        $('#venue-dropdown .dropdown-menu').hide();
        $('#venue-dropdown .custom-input').addClass('hidden').val('');
      }
    });

    // Close Ready Function
  });
</script>
