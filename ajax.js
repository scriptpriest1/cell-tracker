$(document).ready(() => {
  // Load Cells into Cells Table
  fetchAllCells();
  // Load Cell Members into the Cell Members Table
  fetchAllCellMembers();

  // Feedback alerts
  const successMsg = $("#success-msg");
  const errMsg = $("#err-msg");

  // Validation for empty form fields
  function isFilled($input) {
    return $.trim($input.val()) !== "";
  }
  window.isFilled = isFilled;

  function isNotFilled($input) {
    return $.trim($input.val()) === "";
  }

  /*********************************************
                Login Functionality
  *********************************************/

  //Post Login Details to the Backend
  $("#login-form").on("submit", (e) => {
    e.preventDefault();

    const data = $("#login-form").serialize();

    $.ajax({
      url: "./php/ajax.php?action=login",
      method: "POST",
      data,
      success: (res) => {
        if (res === "success") {
          successMsg.text("Logging in...");
          window.location.href = "./dashboard";
        } else if (res === "wrongDetails") {
          errMsg.text("Wrong username or password");
        }
      },
      error: () => {
        errMsg.text("Error logging in");
      },
    });
  });

  /*********************************************
                Logout Functionality
  *********************************************/

  $(document).on("click", ".logout-btn", function () {
    if (!confirm("Are you sure you want to log out?")) return;

    $.ajax({
      url: "../php/ajax.php?action=logout",
      success: (res) => {
        if (res === "loggedOut") {
          successMsg.text("Logging out...");
          window.location.href = "../login.php";
        }
      },
      error: () => {
        errMsg.text("Unable to log out! Please try again.");
      },
    });
  });

  /*********************************************
          Add A Cell Functionality starts
  *********************************************/

  // Utility function for password match
  function passwordsMatch($pw, $confPw) {
    return $pw.val() === $confPw.val();
  }

  // Validate full admin form (for 'else')
  function validateCellAdminAssignment() {
    const $role = $("#add-cell-form #admin-role"),
      $firstName = $("#add-cell-form #admin-first-name"),
      $lastName = $("#add-cell-form #admin-last-name"),
      $email = $("#add-cell-form #admin-email"),
      $pw = $("#add-cell-form #admin-password"),
      $confPw = $("#add-cell-form #admin-password-confirm");

    return (
      isFilled($role) &&
      isFilled($firstName) &&
      isFilled($lastName) &&
      isFilled($email) &&
      isFilled($pw) &&
      isFilled($confPw) &&
      passwordsMatch($pw, $confPw)
    );
  }

  // Toggle visibility of form sections based on choose-admin selection
  function handleAdminSelection(val) {
    const $roleContainer = $("#add-cell-form .role-container");
    const $hiddenSection = $("#add-cell-form .hidden-section");

    if (val === "self") {
      $roleContainer.removeClass("d-none");
      $hiddenSection.addClass("d-none");
    } else if (val === "else") {
      $roleContainer.removeClass("d-none");
      $hiddenSection.removeClass("d-none");
    } else {
      $roleContainer.addClass("d-none");
      $hiddenSection.addClass("d-none");
    }
  }

  // Watch all relevant inputs for validation
  $(document).on(
    "input change",
    "#add-cell-form .form-control, #add-cell-form .form-select",
    function () {
      const $form = $("#add-cell-form");
      const cellNameFilled = isFilled($form.find("#cell-name"));
      const chooseAdmin = $form.find("#choose-admin").val();
      let formValid = false;

      if (chooseAdmin === "self") {
        const roleFilled = isFilled($form.find("#admin-role"));
        formValid = cellNameFilled && roleFilled;
      } else if (chooseAdmin === "else") {
        formValid = cellNameFilled && validateCellAdminAssignment();
      } else {
        // no admin assigned
        formValid = cellNameFilled;
      }

      $form.find(".submit-btn").prop("disabled", !formValid);
    }
  );

  // Handle change of choose-admin to toggle fields
  $(document).on("change", "#add-cell-form #choose-admin", function () {
    const selected = $(this).val();
    handleAdminSelection(selected);
    $("#add-cell-form").trigger("input"); // trigger validation re-check
  });

  // Submit Add Cell form
  $(document).on("submit", "#add-cell-form", function (e) {
    e.preventDefault();

    const $btn = $(this)
      .find(".submit-btn")
      .prop("disabled", true)
      .text("Adding…");
    const data = $(this).serialize();

    $.ajax({
      url: "../php/ajax.php?action=add_a_cell",
      method: "POST",
      data,
      success: (res) => {
        if (res === "success") {
          alert("Cell added successfully!");
          fetchAllCells();
          $("#add-cell-form").trigger("reset");
          $("#add-cell-form .submit-btn")
            .prop("disabled", true)
            .text("Add Cell");
          handleAdminSelection(""); // reset visibility
        } else {
          alert("Error: " + res);
          $btn.prop("disabled", false).text("Add Cell");
        }
      },
      error: () => {
        alert("Server error");
        $btn.prop("disabled", false).text("Add Cell");
      },
    });
  });

  /*********************************************
    Assign Cell Admin when Cell already exists
                 - Function
  *********************************************/
  $(document).on("submit", "#assign-cell-admin-form", function (e) {
    e.preventDefault();

    const $form = $(this);
    const $btn = $form
      .find(".submit-btn")
      .text("Assigning...")
      .prop("disabled", true);
    const data = $form.serialize();

    $.ajax({
      url: "../php/ajax.php?action=assign_cell_admin",
      method: "POST",
      data,
      success: (res) => {
        // If self-reassignment is successful, reload the browser
        let result;
        try {
          result = typeof res === "string" ? JSON.parse(res) : res;
        } catch {
          result = { status: res };
        }
        if (result.status === "success" && result.profile) {
          window.location.reload();
        } else if (res === "success") {
          window.location.reload();
        } else {
          alert("Error: " + (result.message || res));
          $btn.text("Assign").prop("disabled", false);
        }
      },
      error: () => {
        alert("Server error");
        $btn.text("Assign").prop("disabled", false);
      },
    });
  });

  /*********************************************
        Load Cells into the Cells' Table 
                 - Function
  *********************************************/
  function fetchAllCells() {
    if ($("#site-header").is(":visible")) return;

    $.ajax({
      url: "../php/ajax.php",
      method: "POST",
      data: { action: "fetch_all_cells" },
      dataType: "json",
      success: function (cells) {
        const tbody = $("#cells-table tbody");
        tbody.empty();

        $(".cell-count").text(cells.length);

        if (cells.length === 0) {
          $("#cells-table-info-block .info").text("No data found!");
          return;
        } else {
          $("#cells-table-info-block .info").text("");
        }

        cells.forEach(function (cell, index) {
          const row = `
          <tr>
            <td class="sn-col"></td> <!-- SN will be filled by another function -->
            <td>${cell.cell_name + " Cell"}</td>
            <td>${cell.date_created}</td>
            <td>${cell.cell_leader_name || ""}</td>
            <td>${cell.cell_members_count}</td>
            <td class="d-flex align-items-center gap-2"><button type="button" class="load-action-modal-dyn-content view-cell-details-btn action-btn px-3 py-1" data-content-type="view-cell-details" data-cell-name="${
              cell.cell_name
            }" data-cell-id="${
            cell.id
          }">View</button> <button type="button" class="load-action-modal-dyn-content assign-cell-admin-btn action-btn px-3 py-1" data-content-type="assign-cell-admin" data-cell-name="${
            cell.cell_name
          }" data-cell-id="${cell.id}">Assign admin</button></td>
          </tr>`;
          tbody.append(row);
        });

        updateTableSN(); // Call to number rows
      },
      error: () => {
        alert("Error fetching cells.");
      },
    });
  }

  /*********************************************
      Auto-insert Serial Number to Table rows 
                  - Function
  *********************************************/
  function updateTableSN() {
    $("#cells-table, #cell-members-table").each(function () {
      $(this)
        .find("tbody tr")
        .each(function (index) {
          $(this)
            .find("td.sn-col")
            .text(`${index + 1}.`);
        });
    });
  }

  /*********************************************
      Load content dynamically into html
                  - Function
  *********************************************/

  // Action Modal

  $(document).on(
    "click",
    ".load-action-modal-dyn-content",
    loadDynamicContentfunction
  );
  function loadDynamicContentfunction(e) {
    let $thisElement = $(this);
    let contentType = $thisElement.data("content-type");
    let cellId = $thisElement.data("cell-id");
    let cellName = $thisElement.data("cell-name");
    let adminId = $thisElement.data("admin-id");
    let cellMemberId = $thisElement.data("member-id");
    const $editModalTitleContent = `
      <div class="m-0 mb-1 p-0">
        <button class="p-0 m-0 dropdown-btn" id="editTitleBtn" title="Edit Cell Name">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill=""><path class= "p-0 m-0" d="M200-200h57l391-391-57-57-391 391v57Zm-40 80q-17 0-28.5-11.5T120-160v-97q0-16 6-30.5t17-25.5l505-504q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L313-143q-11 11-25.5 17t-30.5 6h-97Zm600-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/></svg>
        </button>

        <div class="position-absolute d-none align-items-center gap-2 p-0 m-0 edit-title-bar" style="border: none !important; left: 0; top: 30px">
          <input type="text" class="form-control edit-title-input" placeholder="Edit Cell name">
          <button class="check-btn save-btn px-2" data-cell-id>
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill=""><path d="m382-354 339-339q12-12 28-12t28 12q12 12 12 28.5T777-636L410-268q-12 12-28 12t-28-12L182-440q-12-12-11.5-28.5T183-497q12-12 28.5-12t28.5 12l142 143Z"/></svg>
          </button>
        </div>
      </div>
    `;

    $.ajax({
      url: "../php/load_dynamic_content.php",
      method: "POST",
      data: {
        "content-type": contentType != undefined ? contentType : null,
        "cell-id": cellId != undefined ? cellId : null,
        "admin-id": adminId != undefined ? adminId : null,
        "cell-member-id": cellMemberId != undefined ? cellMemberId : null,
      },
      success: (res) => {
        if (contentType === "add-a-cell-form") {
          $("#action-modal header .title").text("Add a Cell");
          $("#action-modal .content-container").html(res);
          toggleActionModal();
        } else if (contentType === "assign-cell-admin") {
          $("#action-modal header .title").text(cellName + " Cell");
          $("#action-modal .content-container").html(res);
          $("#action-modal .content-container #cell-id").val(
            $thisElement.data("cell-id")
          );
          toggleActionModal();
        } else if (contentType === "view-cell-details") {
          $("#action-modal header .title").text(cellName + " Cell");
          $("#action-modal #edit-title-container").html($editModalTitleContent);
          $.trim(
            $("#action-modal #edit-title-container .edit-title-input").val(
              cellName
            )
          );
          $("#action-modal #edit-title-container .save-btn").data(
            "cell-id",
            cellId
          );
          $("#action-modal .content-container").html(res);
          if ($("#action-modal .cell-admins-list li").length === 0) {
            $(
              "#action-modal .cell-admins-list-container .admins-list-info"
            ).text("No admins found.");
          }
          toggleActionModal();
        } else if (contentType === "fetch-cell-admins") {
          $("#action-modal .content-container").html(res);
          if ($("#action-modal .cell-admins-list li").length === 0) {
            $(
              "#action-modal .cell-admins-list-container .admins-list-info"
            ).text("No admins found.");
          }
        } else if (contentType === "edit-cell-admin") {
          $("#action-modal .side-panel").html(res);
          toggleActionModalSidePanel();
        } else if (contentType === "add-cell-member-form") {
          $("#action-modal header .title").text("Add a member");
          $("#action-modal .content-container").html(res);
          toggleActionModal();
        } else if (contentType === "edit-cell-member-details") {
          $("#action-modal header .title").text(
            $thisElement.data("member-name")
          );
          $("#action-modal .content-container").html(res);
          toggleActionModal();
        } else return;
      },
    });
  }

  /*********************************************
            Unassign cell admin logic
  *********************************************/
  $(document).on("click", ".unassign-admin-btn", function (e) {
    const $thisElement = $(this);
    const userId = $thisElement.data("user-id");
    const cellId = $thisElement.data("cell-id");

    if (!userId || !cellId) {
      alert("Missing user or cell ID.");
      return;
    }

    if (!confirm("Are you sure you want to unassign this admin?")) return;

    $.ajax({
      url: "../php/ajax.php?action=unassign_cell_admin",
      method: "POST",
      dataType: "json",
      data: {
        user_id: userId,
        cell_id: cellId,
      },
      success: (res) => {
        if (res.status === "success") {
          loadDynamicContentfunction.call(this, e);
          fetchAllCells();
          alert("Unassigned admin successfully.");
          window.location.reload();
        } else {
          alert(res.message || "Failed to unassign admin.");
        }
      },
      error: () => {
        alert("Server error!");
      },
    });
  });

  /*********************************************
              Edit cell Name logic
  *********************************************/
  $(document).on(
    "click",
    "#action-modal #edit-title-container .save-btn",
    function () {
      let $thisElement = $(this);
      let cellId = $thisElement.data("cell-id");
      let inputValue = $.trim(
        $("#action-modal #edit-title-container .edit-title-input").val()
      );
      if (inputValue != "") {
        $.ajax({
          url: "../php/ajax.php?action=edit_cell_name",
          method: "POST",
          dataType: "json",
          data: {
            input_value: inputValue,
            cell_id: cellId,
          },
          success: (res) => {
            if (res.status === "success") {
              alert("Cell name successfully changed");
              $("#action-modal header .title").text(
                res.new_cell_name + " Cell"
              );
              $("#action-modal .edit-title-bar").addClass("d-none");
              fetchAllCells();
            } else {
              alert(res.message);
            }
          },
          error: () => {
            alert("Server error!");
          },
        });
      }
    }
  );

  /*********************************************
              Edit cell Admin details logic
  *********************************************/
  $(document).on("submit", "#edit-cell-admin-form", function (e) {
    e.preventDefault();
    const $form = $(this);
    const $btn = $form
      .find(".submit-btn")
      .prop("disabled", true)
      .text("Saving…");

    const cellId = $form.find("input[name='cell_id']").val();
    const data = $form.serialize();

    $.ajax({
      url: "../php/ajax.php?action=update_cell_admin",
      method: "POST",
      dataType: "json",
      data,
      success: (res) => {
        if (res.status === "success") {
          toggleActionModalSidePanel();
          loadDynamicContentfunction.call(this, e);
          fetchAllCells();
          alert("Admin details updated.");
        } else {
          alert(res.message || "Update failed.");
        }
      },
      error: () => {
        alert("Server error.");
      },
      complete: () => {
        // always re-enable the button
        $btn.prop("disabled", false).text("Save");
      },
    });
  });

  /*********************************************
              Switch account profile logic
  *********************************************/
  $(document).on("click", ".switch-profile .dropdown-item", function (e) {
    e.preventDefault();
    var profileType = $(this).data("profile-type");
    var entityId = $(this).data("entity-id");

    $.post(
      "../php/switch_profile.php",
      { profile_type: profileType, entity_id: entityId },
      function (response) {
        if (response === "success") {
          window.location.href = "/dashboard";
        } else {
          alert("Could not switch profile.");
        }
      }
    );
  });

  /*********************************************
              Add a cell member
                 - Function
  *********************************************/
  $(document).on(
    "input change",
    "#add-cell-member-form .form-control, #add-cell-member-form .form-select",
    function () {
      let $form = $("#add-cell-member-form");
      const firstNameFilled = isFilled($form.find("#first-name"));
      const lastNameFilled = isFilled($form.find("#last-name"));

      if (firstNameFilled && lastNameFilled) {
        $($form).find(".submit-btn").prop("disabled", false);
      } else {
        $($form).find(".submit-btn").prop("disabled", true);
      }
    }
  );

  $(document).on("submit", "#add-cell-member-form", function (e) {
    e.preventDefault();

    const $btn = $(this)
      .find(".submit-btn")
      .prop("disabled", true)
      .text("Adding…");
    const data = $(this).serialize();

    $.ajax({
      url: "../php/ajax.php?action=add_cell_member",
      method: "POST",
      data,
      success: (res) => {
        if (res === "success") {
          alert("Member added successfully!");
          fetchAllCellMembers(); // Reload members table
          $("#add-cell-member-form").trigger("reset");
          $btn.prop("disabled", false).text("Add Member");
        } else {
          alert("Error: " + res);
          $btn.prop("disabled", false).text("Add Member");
        }
      },
      error: () => {
        alert("Server error");
        $btn.prop("disabled", false).text("Add Member");
      },
    });
  });

  /*********************************************
        Load Cell Members into the Cell Members' Table 
                 - Function
  *********************************************/
  function fetchAllCellMembers() {
    if ($("#site-header").is(":visible")) return;

    $.ajax({
      url: "../php/ajax.php",
      method: "POST",
      data: { action: "fetch_all_cell_members" },
      dataType: "json",
      success: function (cellMembers) {
        const tbody = $("#cell-members-table tbody");
        tbody.empty();

        $(".cell-member-count").text(cellMembers.length);

        if (cellMembers.length === 0) {
          $("#cell-members-table-info-block .info").text("No data found!");
          return;
        } else {
          $("#cell-members-table-info-block .info").text("");
        }

        cellMembers.forEach(function (member, index) {
          const row = `
        <tr>
          <td class="sn-col"></td> <!-- SN will be filled by another function -->
          <td>${member.title || ""}</td>
          <td>${member.first_name}</td>
          <td>${member.last_name}</td>
          <td>${member.phone_number || ""}</td>
          <td>${member.email || ""}</td>
          <td>${member.dob_month + " " + member.dob_day || ""}</td>
          <td>${member.occupation || ""}</td>
          <td>${member.residential_address || ""}</td>
          <td>${member.foundation_sch_status || ""}</td>
          <td>${member.delg_in_cell || ""}</td>
          <td>${member.dept_in_church || ""}</td>
          <td>${member.date_joined_ministry || ""}</td>
          <td>${member.date_added || ""}</td>
          <td class="d-flex align-items-center gap-2"><button class="px-3 py-1 action-btn edit--member-btn load-action-modal-dyn-content" data-content-type="edit-cell-member-details" data-member-name="${
            member.first_name + " " + member.last_name
          }" data-member-id="${
            member.id
          }">Edit</button> <button class="px-3 py-1 action-btn delete-member-btn" data-member-id="${
            member.id
          }">Delete</button></td>
        </tr>`;
          tbody.append(row);
        });

        updateTableSN();
      },
      error: () => {
        alert("Error fetching cell members.");
      },
    });
  }

  /*********************************************
            Edit Cell Members Details
                  - Function
  *********************************************/
  $(document).on("submit", "#edit-cell-member-form", function (e) {
    e.preventDefault();

    const $btn = $(this).find(".submit-btn");
    $btn.prop("disabled", true).text("Saving…");

    const data = $(this).serialize();

    $.ajax({
      url: "../php/ajax.php?action=edit_cell_member",
      method: "POST",
      data,
      dataType: "json",
      success: (res) => {
        if (res.status === "success") {
          alert("Member details updated!");
          fetchAllCellMembers();
          $("#edit-cell-member-form .submit-btn")
            .prop("disabled", true)
            .text("Save");
          toggleActionModal();
        } else {
          alert(res.message || "Error updating member's details.");
          $btn.prop("disabled", false).text("Save");
        }
      },
      error: () => {
        alert("Server error!");
        $btn.prop("disabled", false).text("Save");
      },
    });
  });

  /*********************************************
              Delete Cell Member
                 - Function
  *********************************************/
  $(document).on(
    "click",
    "#cell-members-table .delete-member-btn",
    function () {
      const $thisElement = $(this);
      const memberId = $thisElement.data("member-id");

      if (!memberId) {
        alert("Missing member ID.");
        return;
      }

      if (!confirm("Are you sure you want to delete this member?")) return;

      $.ajax({
        url: "../php/ajax.php?action=delete_cell_member",
        method: "POST",
        dataType: "json",
        data: {
          member_id: memberId,
        },
        success: (res) => {
          if (res.status === "success") {
            fetchAllCellMembers();
            alert("Cell member deleted successfully.");
          } else {
            alert(res.message || "Failed to delete cell member.");
          }
        },
        error: () => {
          alert("Server error!");
        },
      });
    }
  );

  /*********************************************
            Cell Reporting System
                 - Function
  *********************************************/
  // Helper: format month-year from a date string "YYYY-MM-DD HH:MM:SS"
  const formatMonthYear = (dateStr) => {
    if (!dateStr) return "";
    // Make it ISO-friendly for Date parsing
    const iso = dateStr.replace(" ", "T");
    const d = new Date(iso);
    const monthName = d.toLocaleString(undefined, { month: "short" }); // e.g. "Aug"
    const year = d.getFullYear();
    return `${monthName} ${year}`; // e.g. "Aug 2025"
  };

  // Helper: return yyyy-mm-01 string used as data-date attribute (01-MM-YYYY format in your sample)
  const monthDataDate = (dateStr) => {
    const iso = dateStr.replace(" ", "T");
    const d = new Date(iso);
    const mm = String(d.getMonth() + 1).padStart(2, "0");
    const yyyy = d.getFullYear();
    return `01-${mm}-${yyyy}`;
  };

  // Create single draft DOM element (matching your sample)
  const buildDraftElement = (draft) => {
    // draft: object with id, type, week, status, date_generated, description (optional)
    const desc = draft.description;
    const status = draft.status ?? "pending";
    const $el = $(`
    <div class="report-draft px-3 py-2 d-flex align-items-center justify-content-between gap-2"
         data-report-type="${draft.type}"
         data-week="${draft.week}"
         data-report-status="${status}"
         data-id="${draft.id}"
         data-date-generated="${draft.date_generated}">
      <div class="text-bar d-flex align-items-center gap-2">
        <h6 class="m-0 p-0 week">Week ${draft.week}:</h6>
        <p class="m-0 p-0 description">${$("<div>").text(desc).html()}</p>
      </div>

      <div class="action-bar d-flex align-items-center justify-content-between gap-2">
        <span class="label">${status === "published" ? "published" : ""}</span>
        <button class="${
          status === "published" ? "view-btn" : "publish-btn"
        } m-0 p-0" data-cell-id="${draft.cell_id}">${
      status === "published" ? "View" : "Publish"
    }</button>
      </div>
    </div>
  `);

    return $el;
  };

  // Insert a draft into its month container in ascending order by date_generated
  const insertDraftIntoMonthContainer = (draft) => {
    const monthYear = formatMonthYear(draft.date_generated);
    const monthData = monthDataDate(draft.date_generated); // like "01-08-2025"
    // find existing date-bar with that month
    let $monthBlock = null;
    $(".reports-block").each(function () {
      // there could be multiple .reports-block sections; find the .date-bar whose h5.date matches monthYear
      const $h = $(this).find(".date-bar > .date, .date-bar > h5.date");
      if ($h.length && $h.text().trim() === monthYear) {
        $monthBlock = $(this).find(".reports-container").first();
        return false; // break each
      }
    });

    // if not found, create the html structure and insert it in chronological order
    if (!$monthBlock || $monthBlock.length === 0) {
      const $newBlock = $(`
      <div class="reports-block mt-4">
        <div class="date-bar">
          <h5 class="date">${monthYear}</h5>
        </div>

        <div class="reports-container mt-2" data-date="${monthData}"></div>
      </div>
    `);

      // Insert into the DOM in chronological order by data-date attribute (01-MM-YYYY)
      // Convert data-date to YYYY-MM-01 for easy comparison
      const toCompare = (dStr) => {
        // dStr is 01-MM-YYYY
        const parts = dStr.split("-");
        if (parts.length !== 3) return null;
        const mm = parts[1];
        const yyyy = parts[2];
        return `${yyyy}-${mm}-01`;
      };

      const $existingBlocks = $(".reports-block");
      if ($existingBlocks.length === 0) {
        // place in .reports-body -> keep the same structure as your page sample
        $(".reports-body").append($newBlock);
      } else {
        let inserted = false;
        $existingBlocks.each(function () {
          const $rc = $(this).find(".reports-container").first();
          const existingDate = $rc.attr("data-date"); // 01-MM-YYYY
          if (!existingDate) return; // continue
          const existingComp = toCompare(existingDate);
          const newComp = toCompare(monthData);
          if (!existingComp || !newComp) return;
          // we want chronological ascending order: earlier months appear first.
          if (newComp < existingComp) {
            $(this).before($newBlock);
            inserted = true;
            return false; // break
          }
        });
        if (!inserted) {
          // append at the end
          $(".reports-section .reports-body").append($newBlock);
        }
      }
      $monthBlock = $newBlock.find(".reports-container").first();
    }

    // Now insert the draft inside $monthBlock in ascending order by date_generated
    const $newDraft = buildDraftElement(draft);

    // convert date_generated to comparable ISO string
    const newDateISO = draft.date_generated.replace(" ", "T");

    let placed = false;
    $monthBlock.find(".report-draft").each(function () {
      const existingDate = $(this).attr("data-date-generated") || "";
      // normalize
      const existingISO = existingDate.replace(" ", "T");
      if (!existingISO) return;
      if (new Date(newDateISO) < new Date(existingISO)) {
        $(this).before($newDraft);
        placed = true;
        return false; // break
      }
    });

    if (!placed) {
      $monthBlock.append($newDraft);
    }

    // Update status counts
    updateStatusCounts();
  };

  // Update status counters in the status bar
  const updateStatusCounts = () => {
    const $section = $(".reports-section");
    if ($section.length === 0) return;
    const $published = $section.find(
      ".report-draft[data-report-status='published']"
    ).length;
    const $pending = $section.find(
      ".report-draft[data-report-status='pending']"
    ).length;
    // unpublished (I assume means drafts not published yet OR some 'unpublished' state)
    const $unpublished = $section.find(
      ".report-draft[data-report-status!='published']"
    ).length;
    $section.find(".report-status.published .count").text($published);
    $section.find(".report-status.pending .count").text($pending);
    $section.find(".report-status.unpublished .count").text($unpublished);
  };

  // Fetch all drafts for the logged-in cell and render everything (fresh)
  window.fetchReportDrafts = () => {
    $.ajax({
      url: "../php/ajax.php",
      type: "POST",
      data: { action: "fetch_report_drafts" },
      dataType: "json",
      success: (res) => {
        if (res.status === "success" && Array.isArray(res.data)) {
          // clear existing month blocks under .reports-body (but keep UI header/filter etc)
          // We'll remove only generated .reports-blocks to avoid destroying other UI elements
          $(".reports-section .reports-block").remove();

          // Build month blocks + drafts
          // Sort server results by date_generated ascending just to be safe
          res.data.sort(
            (a, b) =>
              new Date(a.date_generated.replace(" ", "T")) -
              new Date(b.date_generated.replace(" ", "T"))
          );

          res.data.forEach((draft) => {
            insertDraftIntoMonthContainer(draft);
          });
        } else {
          // no drafts - clear blocks and update counts
          $(".reports-section .reports-block").remove();
          updateStatusCounts();
        }
      },
      error: (xhr, status, err) => {
        console.error("fetchReportDrafts error:", err);
      },
    });
  };

  // Generate (create) a new draft row (manual button)
  window.generateReportDraft = (type = "meeting") => {
    $.ajax({
      url: "../php/ajax.php",
      type: "POST",
      data: { action: "generate_report_draft", type: type },
      dataType: "json",
      success: (res) => {
        if (res.status === "success" && res.draft) {
          // Insert returned draft into DOM in correct place
          insertDraftIntoMonthContainer(res.draft);
          // Optionally, animate or highlight it
          const $inserted = $(`.report-draft[data-id='${res.draft.id}']`);
        } else {
          alert(res.message || "Failed to generate draft");
        }
      },
      error: (xhr, status, err) => {
        console.error("generateReportDraft error:", err);
        alert("Error generating draft. See console.");
      },
    });
  };

  // Load all cell report drafts
  fetchReportDrafts();

  // create draft button: you can change behavior later to choose meeting/outreach
  $(".reports-body button:contains('Create draft')")
    .off("click")
    .on("click", (e) => {
      e.preventDefault();
      // default to 'meeting' for manual test
      generateReportDraft("meeting");
    });

  // Delegated click handlers for publish/view buttons (placeholders)
  $(document).on("click", ".publish-btn", (e) => {
    const $btn = $(e.currentTarget);
    const draftId = $btn.closest(".report-draft").data("id");
    // TODO: open publish modal/form — left as placeholder
    console.log("Publish clicked for draft", draftId);
  });

  $(document).on("click", ".view-btn", (e) => {
    const draftId = $(e.currentTarget).closest(".report-draft").data("id");
    console.log("View clicked for draft", draftId);
    // TODO: open a view modal
  });

  // Close ready() function
});
