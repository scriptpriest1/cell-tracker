$(document).ready(() => {
  // Load Cells into Cells Table
  fetchAllCells();
  // Load Cell Members into the Cell Members Table
  fetchAllCellMembers();

  // If church admin, fetch total cell members for dashboard stats
  if ($("body").hasClass("church-admin") || $("#dashboard-page .cell-count").length) {
    fetchChurchCellMemberCount();
  }

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
            <td class="d-flex align-items-center gap-2"><button type="button" class="load-action-modal-dyn-content view-cell-details-btn action-btn px-3 py-1" data-content-type="view-cell-details" data-cell-name="${cell.cell_name
            }" data-cell-id="${cell.id
            }">View</button> <button type="button" class="load-action-modal-dyn-content assign-cell-admin-btn action-btn px-3 py-1" data-content-type="assign-cell-admin" data-cell-name="${cell.cell_name
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
  window.updateTableSN = updateTableSN;

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
        } else if (typeof contentType !== "undefined" && contentType.startsWith("report-")) {
          // For report forms
          const mode = contentType === "report-publish" ? "publish" : "view";
          const reportType = contentType.split("-")[1]; // e.g. "meeting" or "outreach"
          const week = $("#action-modal .report-week").val(); // Get week from the hidden input
          const description = $("#action-modal .report-description").val(); // Get description
          loadDynamicContentfunction.call(this, {
            type: "report-form",
            draftId: cellId, // Reuse cellId for draftId
            week,
            description,
            status: "pending", // Default to pending
            reportType,
            mode
          });
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
          <td class="d-flex align-items-center gap-2"><button class="px-3 py-1 action-btn edit--member-btn load-action-modal-dyn-content" data-content-type="edit-cell-member-details" data-member-name="${member.first_name + " " + member.last_name
            }" data-member-id="${member.id
            }">Edit</button> <button class="px-3 py-1 action-btn delete-member-btn" data-member-id="${member.id
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
    const status = (draft.status || "pending").toString().toLowerCase();
    const desc = getMeetingDescription(draft.week);

    // Label and button behavior per status
    let labelHtml = "";
    let buttonHtml = "";
    if (status === "published") {
      labelHtml = `<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill=""><path d="m382-354 339-339q12-12 28-12t28 12q12 12 12 28.5T777-636L410-268q-12 12-28 12t-28-12L182-440q-12-12-11.5-28.5T183-497q12-12 28.5-12t28.5 12l142 143Z"/></svg>`;
      buttonHtml = `<button class="view-btn m-0 p-0" data-cell-id="${draft.cell_id}">View</button>`;
    } else if (status === "pending") {
      // pending -> show Publish button
      labelHtml = "";
      buttonHtml = `<button class="publish-btn m-0 p-0" data-cell-id="${draft.cell_id}">Publish</button>`;
    } else if (status === "expired") {
      // expired -> show expired label and NO publish button
      labelHtml = "expired";
      buttonHtml = ""; // intentionally no publish button
    }

    const $el = $(`
      <div class="report-draft px-3 py-2 d-flex align-items-center justify-content-between gap-2"
           data-report-type="${draft.type}"
           data-week="${draft.week}"
           data-report-status="${status}"
           data-id="${draft.id}"
           data-date-generated="${draft.date_generated}">
        <div class="text-bar d-flex align-items-center gap-2">
          <h6 class="m-0 p-0 week">W${draft.week}:</h6>
          <p class="m-0 p-0 description">${$("<div>").text(desc).html()}</p>
        </div>

        <div class="action-bar d-flex align-items-center justify-content-between gap-2">
          <span class="label">${labelHtml}</span>
          ${buttonHtml}
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

    // if not found, create the html structure and insert it in reverse chronological order (newest months first)
    if (!$monthBlock || $monthBlock.length === 0) {
      const $newBlock = $(`
      <div class="reports-block mt-4">
        <div class="date-bar">
          <h5 class="date">${monthYear}</h5>
        </div>

        <div class="reports-container mt-2" data-date="${monthData}"></div>
      </div>
    `);

      // Insert into the DOM in reverse chronological order by data-date attribute (01-MM-YYYY)
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
          // We want newest months first: if newComp > existingComp, put newBlock before existing block
          if (newComp > existingComp) {
            $(this).before($newBlock);
            inserted = true;
            return false; // break
          }
        });
        if (!inserted) {
          // append at the end (oldest)
          $(".reports-section .reports-body").append($newBlock);
        }
      }
      $monthBlock = $newBlock.find(".reports-container").first();
    }

    // Now insert the draft inside $monthBlock so newer drafts appear above older ones
    const $newDraft = buildDraftElement(draft);

    // convert date_generated to comparable ISO string
    const newDateISO = draft.date_generated.replace(" ", "T");

    let placed = false;
    $monthBlock.find(".report-draft").each(function () {
      const existingDate = $(this).attr("data-date-generated") || "";
      // normalize
      const existingISO = existingDate.replace(" ", "T");
      if (!existingISO) return;
      // Place newer drafts before older ones
      if (new Date(newDateISO) > new Date(existingISO)) {
        $(this).before($newDraft);
        placed = true;
        return false; // break
      }
    });

    if (!placed) {
      // if not placed, append at end (oldest in that month)
      $monthBlock.append($newDraft);
    }

    // Update status counts
    updateStatusCounts();
  };

  // Update status counters in the status bar
  const updateStatusCounts = () => {
    const $section = $(".reports-section");
    if ($section.length === 0) return;

    const published = $section.find(".report-draft[data-report-status='published']").length;
    const pending = $section.find(".report-draft[data-report-status='pending']").length;
    const unpublished = $section.find(".report-draft[data-report-status!='published']").length;

    // Ensure each status widget has a .span-box element so the CSS selectors can apply
    $section.find(".report-status").each(function () {
      const $rs = $(this);
      if ($rs.find(".span-box").length === 0) {
        // prepend a small element used purely for background/indicator coloring
        $rs.prepend('<span class="span-box" aria-hidden="true"></span>');
      }
    });

    // Update counts for the three valid statuses
    $section.find(".report-status.published .count").text(published);
    $section.find(".report-status.pending .count").text(pending);
    if ($section.find(".report-status.unpublished .count").length) {
      $section.find(".report-status.unpublished .count").text(unpublished);
    }
  };

  // Fetch all cell report drafts (accepts optional filter: 'all' | 'meeting' | 'outreach')
  window.fetchReportDrafts = (filter = 'all') => {
    // Map UI filter ids to backend type values
    let typeParam = null; // null => fetch all
    if (filter === 'meeting') typeParam = 'meeting';
    else if (filter === 'outreach') typeParam = 'outreach';
    // else 'all' -> null

    $.ajax({
      url: "../php/ajax.php",
      type: "POST",
      data: { action: "fetch_report_drafts", ...(typeParam ? { type: typeParam } : {}) },
      dataType: "json",
      success: (res) => {
        if (res.status === "success" && Array.isArray(res.data)) {
          // clear existing month blocks under .reports-body (but keep UI header/filter etc)
          $(".reports-section .reports-block").remove();

          // Sort server results by date_generated DESC so newest first
          res.data.sort(
            (a, b) =>
              new Date(b.date_generated.replace(" ", "T")) -
              new Date(a.date_generated.replace(" ", "T"))
          );

          res.data.forEach((draft) => {
            insertDraftIntoMonthContainer(draft);
          });

          // Ensure status counters always reflect current (possibly empty) filtered results
          updateStatusCounts();
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
  // Read current URL filter (if any) so direct links / manual URL edits work
  const initialUrlFilter = new URLSearchParams(window.location.search).get('filter') || 'all';
  fetchReportDrafts(initialUrlFilter);

  // Also respond to history navigation (back/forward) by re-fetching using the URL filter
  window.addEventListener('popstate', () => {
    const f = new URLSearchParams(window.location.search).get('filter') || 'all';
    if (typeof window.fetchReportDrafts === 'function') {
      fetchReportDrafts(f);
    }
  });

  // Create draft button (stable selector). Default type = "meeting"
  $(document).on('click', '#create-draft-btn', function (e) {
    e.preventDefault();
    // call the existing generator; change argument later to support outreach if needed
    if (typeof window.generateReportDraft === 'function') {
      window.generateReportDraft('meeting');
    } else {
      console.error('generateReportDraft not defined');
    }
  });

  // Delegated click handlers for publish/view buttons
  $(document).on("click", ".publish-btn, .view-btn", function (e) {
    e.preventDefault();
    const $btn = $(this);
    const $draftDiv = $btn.closest(".report-draft");
    const draftId = $draftDiv.data("id");
    const week = $draftDiv.data("week");
    const description = $draftDiv.find(".description").text();
    const status = ($draftDiv.data("report-status") || "").toString().toLowerCase();
    const type = $draftDiv.data("report-type");

    // If draft is expired (server may have updated status) block action with alert
    if (status === "expired") {
      alert("Report is expired and cannot be published!");
      return;
    }

    // Load the report form via AJAX and show in modal
    $.ajax({
      url: "../php/load_dynamic_content.php",
      method: "POST",
      data: {
        "content-type": "cell-report-form",
        "draft-id": draftId,
        "week": week,
        "description": description,
        "status": status,
        "report-type": type,
        "mode": $btn.hasClass("publish-btn") ? "publish" : "view"
      },
      success: function (res) {
        $("#action-modal header .title").text(`Week ${week}: ${description}`);
        $("#action-modal .content-container").html(res);
        toggleActionModal();
      }
    });
  });

  // Custom dropdown show/hide logic for report form
  $(document).on("click", ".attendance-select", function (e) {
    e.stopPropagation();
    $(".custom-dropdown").not($(this).closest(".form-group").find(".attendance-dropdown")).hide();
    $(this).closest(".form-group").find(".attendance-dropdown").toggle();
  });
  $(document).on("click", ".first-timers-select", function (e) {
    e.stopPropagation();
    $(".custom-dropdown").not($(this).closest(".form-group").find(".first-timers-dropdown")).hide();
    $(this).closest(".form-group").find(".first-timers-dropdown").toggle();
  });
  $(document).on("click", ".new-converts-select", function (e) {
    e.stopPropagation();
    $(".custom-dropdown").not($(this).closest(".form-group").find(".new-converts-dropdown")).hide();
    $(this).closest(".form-group").find(".new-converts-dropdown").toggle();
  });

  // Hide dropdowns when clicking outside
  $(document).on("mousedown", function (e) {
    if (!$(e.target).closest('.custom-dropdown, .attendance-select, .first-timers-select, .new-converts-select').length) {
      $(".custom-dropdown").hide();
    }
  });

  // Report form validation logic
  function validateReportForm() {
    const $form = $("#cell-report-form");
    if ($form.length === 0) return;
    let valid = true;
    const reportType = $form.find("input[name='report_type']").val();

    // Specific checks per report type
    if (reportType === "outreach") {
      // outreach: attendance (number), new_converts (number), outreach-kind required
      const attendanceVal = $.trim($form.find("input[name='attendance']").val() || "");
      const newConvertsVal = $.trim($form.find("input[name='new_converts']").val() || "");
      const outreachKindVal = $.trim($form.find("input[name='outreach-kind']").val() || "");
      if (attendanceVal === "" || newConvertsVal === "" || outreachKindVal === "") {
        valid = false;
      }
    } else {
      // meeting: require at least one attendance checkbox selected; first_timers/new_converts optional
      // NOTE: some dropdown checkboxes may not have name="attendance[]" (dynamic UI). Check both selectors.
      const attendanceCheckedNamed = $form.find("input[name='attendance[]']:checked").length;
      const attendanceCheckedGeneric = $form.find(".attendance-list input[type='checkbox']:not(.select-all-options):checked").length;
      const attendanceChecked = attendanceCheckedNamed || attendanceCheckedGeneric;
      if (!attendanceChecked) {
        valid = false;
      }
    }

    // Generic required elements check, but skip first_timers and new_converts (they are conditionally optional)
    $form.find("[required]").each(function () {
      if (!valid) return false; // short-circuit if already invalid
      const name = $(this).attr("name") || "";
      // Skip conditional fields (they were handled above)
      if (name.indexOf("first_timers") !== -1 || name.indexOf("new_converts") !== -1) {
        return true; // continue
      }
      // For checkbox groups, handled above for attendance; skip checkbox group names containing "attendance" to avoid duplicate check
      if (name.indexOf("attendance") !== -1 && reportType !== "outreach") {
        return true;
      }
      if ($(this).is(":checkbox")) {
        const chkName = $(this).attr("name");
        // If it's a direct required checkbox (rare), ensure at least one in that group is checked
        if ($form.find(`[name='${chkName}']:checked`).length === 0) {
          valid = false;
          return false;
        }
      } else {
        if ($.trim($(this).val()) === "") {
          valid = false;
          return false;
        }
      }
    });

    $form.find(".submit-btn").prop("disabled", !valid);
  }

  // Validate on input/change for report form
  $(document).on("input change", "#cell-report-form [required]", validateReportForm);

  // Also validate on dropdown selection for custom dropdowns
  $(document).on("change", "#cell-report-form .attendance-list input[type='checkbox'], #cell-report-form .first-timers-list input[type='checkbox'], #cell-report-form .new-converts-list input[type='checkbox']", validateReportForm);

  // Initial validation when form loads
  $(document).on("ready", "#cell-report-form", validateReportForm);

  // Cell Report Form Submission (publish / update)
  $(document).on("submit", "#cell-report-form", function (e) {
    e.preventDefault(); // Prevent default form submission

    const $form = $(this);
    const isEdit = !!$form.find("input[name='report_id']").val();
    const $btn = $form.find(".submit-btn");
    if (isEdit) {
      $btn.prop("disabled", true).text("Saving...");
    } else {
      $btn.prop("disabled", true).text("Publishing…");
    }

    // ---- NEW: Build explicit hidden inputs from current checked dropdown checkboxes ----
    // Remove any previously injected temp inputs
    $form.find(".__tmp_payload_container").remove();
    const $tmp = $('<div class="__tmp_payload_container d-none"></div>').appendTo($form);

    const reportType = $form.find("input[name='report_type']").val();

    if (reportType !== 'outreach') {
      // collect checked attendance member ids
      const attendanceIds = [];
      $form.find(".attendance-list input[type='checkbox']:not(.select-all-options):checked").each(function () {
        const v = $(this).val();
        if (v !== undefined && v !== null && String(v).trim() !== "") attendanceIds.push(String(v));
      });

      // Only inject hidden inputs if there are NO enabled checkbox inputs named attendance[].
      // This avoids posting duplicate values (checkboxes + hidden inputs) when checkboxes already have name="attendance[]"
      const hasEnabledNamedAttendance = $form.find(".attendance-list input[type='checkbox'][name='attendance[]']:not(:disabled)").length > 0;
      if (!hasEnabledNamedAttendance) {
        attendanceIds.forEach(id => {
          $tmp.append($(`<input type="hidden" name="attendance[]" />`).val(id));
        });
      }

      // collect checked first_timers ids
      const firstTimersIds = [];
      $form.find(".first-timers-list input[type='checkbox']:not(.select-all-options):checked").each(function () {
        const v = $(this).val();
        if (v !== undefined && v !== null && String(v).trim() !== "") firstTimersIds.push(String(v));
      });
      const hasEnabledNamedFirstTimers = $form.find(".first-timers-list input[type='checkbox'][name='first_timers[]']:not(:disabled)").length > 0;
      if (!hasEnabledNamedFirstTimers) {
        firstTimersIds.forEach(id => {
          $tmp.append($(`<input type="hidden" name="first_timers[]" />`).val(id));
        });
      }

      // collect checked new_converts ids
      const newConvertsIds = [];
      $form.find(".new-converts-list input[type='checkbox']:not(.select-all-options):checked").each(function () {
        const v = $(this).val();
        if (v !== undefined && v !== null && String(v).trim() !== "") newConvertsIds.push(String(v));
      });
      const hasEnabledNamedNewConverts = $form.find(".new-converts-list input[type='checkbox'][name='new_converts[]']:not(:disabled)").length > 0;
      if (!hasEnabledNamedNewConverts) {
        newConvertsIds.forEach(id => {
          $tmp.append($(`<input type="hidden" name="new_converts[]" />`).val(id));
        });
      }
    } else {
      // outreach: numeric inputs should already have names - nothing special
    }
    // ---- END NEW ----

    // Serialize form data
    const formData = $form.serialize();

    $.ajax({
      url: "../php/ajax.php?action=submit_cell_report",
      method: "POST",
      data: formData,
      // NOTE: do NOT force dataType: "json" here — parse safely below so PHP warnings/HTML don't trigger a silent parseerror
      success: function (rawRes, textStatus, xhr) {
        let res = null;
        // Try to parse JSON safely. If server returned non-JSON (warnings, HTML), surface it to the user for debugging.
        try {
          if (typeof rawRes === "object") {
            res = rawRes;
          } else {
            res = JSON.parse(rawRes);
          }
        } catch (err) {
          // Show raw server response so you can see the PHP warning / SQL error causing the failure
          const preview = String(rawRes).trim();
          alert("Server returned unexpected response:\n\n" + (preview || "[empty response]"));
          $btn.prop("disabled", false).text(isEdit ? "Save changes" : "Publish");
          // cleanup temp inputs
          $form.find(".__tmp_payload_container").remove();
          return;
        }

        if (res && res.status === "success") {
          alert(isEdit ? "Report updated successfully!" : "Report published successfully!");
          toggleActionModal();
          // Immediately fetch updated report drafts
          fetchReportDrafts();
        } else {
          alert((res && res.message) || "Failed to submit report.");
          $btn.prop("disabled", false).text(isEdit ? "Save changes" : "Publish");
        }
        // cleanup temp inputs
        $form.find(".__tmp_payload_container").remove();
      },
      error: function (xhr, status, err) {
        // Provide the server response text (if any) to help diagnose the cause
        const serverText = (xhr && xhr.responseText) ? xhr.responseText : status;
        alert("Server error!\n\n" + serverText);
        $btn.prop("disabled", false).text(isEdit ? "Save changes" : "Publish");
        // cleanup temp inputs
        $form.find(".__tmp_payload_container").remove();
      }
    });
  });

  // Enable edit-mode for report forms (toggle Edit / Cancel and show Save)
  $(document).on("click", "#cell-report-form .edit-btn", function (e) {
    e.preventDefault();
    const $editBtn = $(this);
    const $form = $("#cell-report-form");

    // If this is a published report and it is expired, block edit and show alert.
    // A published report is represented by mode === 'view' or presence of report_id.
    const isExpired = String($form.data("expired") || "").replace(/\s+/g, "") === "1";
    const hasReportId = $form.find("input[name='report_id']").length > 0;
    const mode = String($form.find("input[name='mode']").val() || "").toLowerCase();
    if (isExpired && (hasReportId || mode === "view")) {
      alert("Report expired and cannot be edited.");
      return; // do not proceed to toggle edit-mode
    }

    // track editing state via data attribute
    const editing = !!$editBtn.data("editing");

    // Selectors to always keep enabled (dropdown toggles and dropdown search inputs)
    const keepEnabledSelectors = ".attendance-select, .first-timers-select, .new-converts-select, .attendance-search, .first-timers-search, .new-converts-search";

    if (!editing) {
      // Enter edit mode: enable all inputs/selects/textareas and buttons except the keep-enabled selectors (they are already enabled)
      $form.find("input, select, textarea, button").not(keepEnabledSelectors).prop("disabled", false);

      // For meeting attendance, do NOT set required on each checkbox.
      // Validation uses the count of checked attendance checkboxes instead of per-checkbox required attributes.

      // Enable checkboxes inside custom dropdowns for edit mode
      $form.find(".custom-dropdown input[type='checkbox']").prop("disabled", false);

      // Insert or show a submit button (Save changes) in the footer
      if ($form.find(".submit-btn").length === 0) {
        const $saveBtn = $(`<button type="submit" class="submit-btn w-100">Save changes</button>`);
        $form.find("footer").append($saveBtn);
      } else {
        $form.find(".submit-btn").show().text("Save changes");
      }

      // Update button state/text
      $editBtn.text("Cancel").data("editing", true).addClass("cancel-btn");

      // Trigger validation so Save is enabled only when valid
      if (typeof validateReportForm === "function") validateReportForm();

    } else {
      // Cancel edit:
      // Disable all controls except dropdown toggle buttons and dropdown search inputs (keep them usable)
      $form.find("input, select, textarea, button").not(keepEnabledSelectors).prop("disabled", true);

      // Ensure the dropdown toggle buttons remain enabled
      $form.find(".attendance-select, .first-timers-select, .new-converts-select").prop("disabled", false);

      // Keep the dropdown search inputs enabled so users can search even in view mode
      $form.find(".attendance-search, .first-timers-search, .new-converts-search").prop("disabled", false);

      // Remove per-checkbox required attributes if present (cleanup) — not required for validation
      $form.find("input[name='attendance[]']").removeAttr("required");

      // Disable checkboxes inside dropdowns to match view mode (user can still open and search)
      $form.find(".custom-dropdown input[type='checkbox']").prop("disabled", true);

      // Remove the save button if it was injected by edit-mode
      $form.find(".submit-btn").remove();

      // Restore Edit button text/state and re-enable it
      $editBtn.text("Edit report").data("editing", false).prop("disabled", false).removeClass("cancel-btn");

      // Re-run validation to update any state
      if (typeof validateReportForm === "function") validateReportForm();
    }
  });

  // --- Church Reports: populate cell select and load chosen cell's reports ---
  // Called on pages where #select-cell exists (church admin page)
  function initChurchReports() {
    const $cellsReportsRoot = $(".cells-reports");
    if ($cellsReportsRoot.length === 0) return;

    const isChurchAdmin = String($cellsReportsRoot.data("is-church-admin") || "0") === "1";
    const $select = $("#select-cell");

    // Fill select with cells (reuse existing endpoint)
    $.ajax({
      url: "../php/ajax.php",
      method: "POST",
      dataType: "json",
      data: { action: "fetch_all_cells" },
      success: function (cells) {
        // remove existing options except placeholder
        $select.find("option:not(:first)").remove();
        if (!cells || !cells.length) return;
        cells.forEach(c => {
          // append " Cell" to each option label
          $select.append($(`<option/>`).val(c.id).text(`${c.cell_name} Cell`));
        });

        // Pre-select cell from URL if provided (?p=reports&cell-id=...)
        const urlCellId = new URLSearchParams(window.location.search).get('cell-id');
        if (urlCellId) {
          // only set if option exists
          if ($select.find(`option[value='${urlCellId}']`).length) {
            $select.val(urlCellId).trigger('change');
          }
        }
        // Optionally select first cell if you want automatic load when none in URL
        // else { /* $select.val(cells[0].id).trigger('change'); */ }
      },
      error: function () {
        console.error("Failed to load cells for church-reports.");
      }
    });

    // When a cell is selected, fetch and render its reports
    $select.on("change", function () {
      const cellId = $(this).val();
      const cellName = $(this).find("option:selected").text() || "";
      if (!cellId) {
        // remove appended sections if any
        $cellsReportsRoot.find(".reports-section.for-church-cell").remove();
        // Reset URL to canonical reports page (no duplicate params)
        history.replaceState({}, "", "?p=reports");
        return;
      }

      // Push cell-id into URL so selection persists on refresh
      const params = new URLSearchParams(window.location.search);
      params.set('cell-id', cellId);
      const filter = params.get('filter');
      const url = `?p=reports&cell-id=${encodeURIComponent(cellId)}${filter ? `&filter=${encodeURIComponent(filter)}` : ''}`;
      history.replaceState({}, "", url);

      loadAndRenderCellReports(cellId, cellName, isChurchAdmin);
    });
  }

  // Fetch drafts for a cell and render a reports-section appended to .cells-reports
  function loadAndRenderCellReports(cellId, cellName, isChurchAdmin) {
    $.ajax({
      url: "../php/ajax.php",
      method: "POST",
      dataType: "json",
      data: { action: "fetch_reports_for_cell", cell_id: cellId },
      success: function (res) {
        if (!res || res.status !== "success") {
          alert(res && res.message ? res.message : "Failed to load reports.");
          return;
        }
        const drafts = res.data || [];

        // Clean previous injected section for this container
        $(".cells-reports .reports-section.for-church-cell").remove();

        // Build a reports-section structure similar to cell-reports.php
        const $section = $(`
           <section class="reports-section for-church-cell">
             <div class="reports-status-bar d-flex align-items-center gap-4 mt-2">
               <div class="report-status published">
                 <h6 class="text m-0 p-0">Published: <span class="count span-box">0</span></h6>
               </div>
               <div class="report-status pending">
                 <h6 class="text m-0 p-0">Pending: <span class="count span-box">0</span></h6>
               </div>
               <div class="report-status unpublished">
                 <h6 class="text m-0 p-0">Unpublished: <span class="count span-box">0</span></h6>
               </div>
             </div>
            <div class="filter-bar d-flex align-items-center gap-3 mt-4">
              <button id="all" class="filter" data-filter="all">All</button>
              <button id="meeting" class="filter" data-filter="meeting">Meetings</button>
              <button id="outreach" class="filter" data-filter="outreach">Outreaches</button>
            </div>
             <div class="reports-body mt-3"></div>
           </section>
         `);

        // NOTE: removed visual header prepend (handled by page layout)
        // Store drafts on the section so filter buttons can re-render using the same render function
        $section.data('drafts', drafts);
        // expose the selected cell id too so filter handlers can read it
        $section.data('cell-id', String(cellId));

        // Append to DOM
        $(".cells-reports").append($section);

        // Read URL filter param and set active class for buttons inside this injected section
        const urlParams = new URLSearchParams(window.location.search);
        const urlFilter = urlParams.get('filter') || 'all';
        $section.find('.filter-bar .filter').removeClass('active');
        $section.find(`.filter-bar .filter[data-filter="${urlFilter}"]`).addClass('active');

        // Apply filter from URL if present (keeps behavior consistent on refresh)
        const filteredDrafts = (urlFilter === 'all') ? drafts : drafts.filter(d => d.type === urlFilter);

        // Render drafts grouped by month inside this section (use filtered set)
        renderDraftsIntoSection(filteredDrafts, $section, isChurchAdmin);

        // Wire filter buttons (local to this section)
        $section.find(".filter-bar .filter").on("click", function (e) {
          // Prevent the global ".filter" click handler from also running
          e.stopPropagation();
          const $btn = $(this);
          $section.find(".filter-bar .filter").removeClass("active");
          $btn.addClass("active");
          const filter = $btn.data("filter") || 'all';

          // Use the stored drafts and re-render via the existing function (keeps logic DRY)
          const allDrafts = $section.data('drafts') || [];
          const filtered = (filter === 'all') ? allDrafts : allDrafts.filter(d => d.type === filter);
          renderDraftsIntoSection(filtered, $section, isChurchAdmin);

          // Update URL to include both cell-id and filter so state persists on refresh
          const cellParam = $section.data('cell-id') || cellId;
          const newUrl = `?p=reports&cell-id=${encodeURIComponent(cellParam)}&filter=${encodeURIComponent(filter)}`;
          history.replaceState({}, "", newUrl);
        });
      },
      error: function () {
        alert("Error loading reports for the selected cell.");
      }
    });
  }

  // Render drafts grouped by month into a given section
  function renderDraftsIntoSection(drafts, $section, isChurchAdmin) {
    const $body = $section.find(".reports-body");
    $body.empty();

    // Helper to format month-year from date_generated
    const monthKey = d => {
      if (!d || !d.date_generated) return "Unknown";
      const iso = d.date_generated.replace(" ", "T");
      const dt = new Date(iso);
      return dt.toLocaleString(undefined, { month: "short", year: "numeric" });
    };
    const group = {};
    drafts.forEach(d => {
      const key = monthKey(d);
      group[key] = group[key] || [];
      group[key].push(d);
    });

    // Insert months in descending order by parsed date (newest first)
    const months = Object.keys(group).sort((a, b) => {
      // parse first draft date in group
      const da = new Date((group[a][0].date_generated || "").replace(" ", "T"));
      const db = new Date((group[b][0].date_generated || "").replace(" ", "T"));
      return db - da;
    });

    months.forEach(month => {
      const $block = $(`
        <div class="reports-block mt-4">
          <div class="date-bar"><h5 class="date">${month}</h5></div>
          <div class="reports-container mt-2"></div>
        </div>
      `);
      const $container = $block.find(".reports-container");

      // Sort drafts in this month by date_generated desc
      group[month].sort((a, b) => new Date(b.date_generated.replace(" ", "T")) - new Date(a.date_generated.replace(" ", "T")));

      group[month].forEach(draft => {
        const status = (draft.status || "pending").toLowerCase();
        const desc = $('<div>').text(draft.description || getMeetingDescription(draft.week)).html();

        // Build status label HTML for visibility in the action-bar
        let labelHtml = "";
        if (status === "published") {
          labelHtml = `<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill=""><path d="m382-354 339-339q12-12 28-12t28 12q12 12 12 28.5T777-636L410-268q-12 12-28 12t-28-12L182-440q-12-12-11.5-28.5T183-497q12-12 28.5-12t28.5 12l142 143Z"/></svg>`;
        } else if (status === "pending") {
          labelHtml = `<span class="label"></span>`;
        } else if (status === "expired") {
          labelHtml = `<span class="label">expired</span>`;
        }

        // Choose allowed actions for church admin:
        // - If church admin: do not show Publish or Edit buttons.
        // - If published: show View button.
        // - If pending or expired and user is church admin: do not render View/Publish.
        let $actionBtn = $();
        if (status === "published") {
          // published -> show View (allowed to church admin)
          $actionBtn = $(`<button class="view-btn m-0 p-0" data-draft-id="${draft.id}" data-cell-id="${draft.cell_id}">View</button>`);
        } else if (status === "pending") {
          // pending -> only show Publish to non-church-admins
          if (!isChurchAdmin) {
            $actionBtn = $(`<button class="publish-btn m-0 p-0" data-draft-id="${draft.id}" data-cell-id="${draft.cell_id}">Publish</button>`);
          } else {
            $actionBtn = $(); // no button for church admin
          }
        } else if (status === "expired") {
          $actionBtn = $(); // no action for expired
        }

        const $draftEl = $(`
          <div class="report-item report-draft px-3 py-2 d-flex align-items-center justify-content-between gap-2"
               data-report-type="${draft.type}" data-week="${draft.week}" data-report-status="${status}" data-id="${draft.id}" data-date-generated="${draft.date_generated}">
            <div class="text-bar d-flex align-items-center gap-2">
              <h6 class="m-0 p-0 week">W${draft.week}:</h6>
              <p class="m-0 p-0 description">${desc}</p>
            </div>
            <div class="action-bar d-flex align-items-center justify-content-between gap-2">
              <span class="label">${labelHtml}</span>
            </div>
          </div>
        `);

        // attach action (if any) after label so both appear
        if ($actionBtn && $actionBtn.length) {
          $draftEl.find(".action-bar").append($actionBtn);
        }

        $container.append($draftEl);
      });

      $body.append($block);
    });

    // Update status counters for this section
    updateSectionStatusCounts($section);

    // Wire view/publish buttons inside this injected section
    $section.off("click", ".view-btn").on("click", ".view-btn", function (e) {
      // Prevent document-level handlers from also running (they toggle the modal too)
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();

      const $btn = $(this);
      const draftId = $btn.data("draft-id");
      const status = $btn.closest(".report-item").data("report-status");
      // Prevent opening pending/expired drafts for church admins: they shouldn't be rendered, but double-guard
      if (String(status) !== "published") {
        alert("You cannot open this draft.");
        return;
      }
      // Open read-only view via existing load_dynamic_content flow
      $.ajax({
        url: "../php/load_dynamic_content.php",
        method: "POST",
        data: {
          "content-type": "cell-report-form",
          "draft-id": draftId,
          "mode": "view"
        },
        success: function (res) {
          $("#action-modal header .title").text("View Report");
          $("#action-modal .content-container").html(res);
          // Ensure all fields are disabled in the returned form (server also enforces)
          $("#action-modal #cell-report-form input, #action-modal #cell-report-form select, #action-modal #cell-report-form textarea").prop("disabled", true);
          // For outreach forms keep dropdown buttons enabled but disable checkboxes inside them
          $("#action-modal .custom-dropdown input[type='checkbox']").prop("disabled", true);
          toggleActionModal();
        }
      });
    });

    // publish buttons should work only for non-church-admins; they exist only for non-church-admins
    $section.off("click", ".publish-btn").on("click", ".publish-btn", function (e) {
      // Prevent document-level handlers from also running
      e.preventDefault();
      e.stopPropagation();
      e.stopImmediatePropagation();
      if (isChurchAdmin) {
        alert("Publishing is not allowed for Church admins.");
        return;
      }
      const $btn = $(this);
      const draftId = $btn.data("draft-id");
      // reuse existing publish flow if available; here simply open publish form (mode publish)
      $.ajax({
        url: "../php/load_dynamic_content.php",
        method: "POST",
        data: {
          "content-type": "cell-report-form",
          "draft-id": draftId,
          "mode": "publish"
        },
        success: function (res) {
          $("#action-modal header .title").text("Publish Report");
          $("#action-modal .content-container").html(res);
          toggleActionModal();
        }
      });
    });
  }

  // update status counters in a specific injected section
  function updateSectionStatusCounts($section) {
    const published = $section.find(".report-item[data-report-status='published']").length;
    const pending = $section.find(".report-item[data-report-status='pending']").length;
    const unpublished = $section.find(".report-item").length - published; // includes pending & expired
    $section.find(".report-status.published .count").text(published);
    $section.find(".report-status.pending .count").text(pending);
    $section.find(".report-status.unpublished .count").text(unpublished);
  }

  // initialize church reports on DOM ready (run only on pages with #select-cell)
  $(document).ready(function () {
    try { initChurchReports(); } catch (e) { /* ignore if not on page */ }
  });
});

class SearchBar {
  constructor(options) {
    this.inputSelector = options.inputSelector;
    this.iconSelector = options.iconSelector;
    this.tableSelector = options.tableSelector;
    this.infoBlockSelector = options.infoBlockSelector;
    // Use static SVG and rotate via CSS class
    this.loaderSvg = `<svg class="search-icon search-loading" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 0 50 50" width="20px"><circle cx="25" cy="25" r="20" stroke="#0d6efd" stroke-width="5" fill="none" stroke-dasharray="31.4 31.4" stroke-linecap="round"/></svg>`;
    this.defaultSvg = `<svg class="search-icon" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill=""><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>`;
    this.delay = options.delay || 350;
    this.timer = null;
    this.searchType = options.searchType; // 'cells' or 'members'
    this.fetchAllFn = options.fetchAllFn;
    this.renderFn = options.renderFn;
    this.urlParam = options.urlParam;
    this.init();
  }

  setLoading(isLoading) {
    const $iconSpan = $(this.iconSelector);
    if (isLoading) {
      $iconSpan.html(this.loaderSvg);
    } else {
      $iconSpan.html(this.defaultSvg);
    }
  }

  updateURL(query) {
    const params = new URLSearchParams(window.location.search);
    if (query) {
      params.set(this.urlParam, query);
    } else {
      params.delete(this.urlParam);
    }
    history.replaceState({}, "", `${window.location.pathname}?${params.toString()}`);
  }

  handleSearch(query) {
    this.setLoading(true);
    this.updateURL(query);
    if (!query) {
      this.setLoading(false);
      this.fetchAllFn();
      return;
    }
    $.ajax({
      url: "../php/ajax.php",
      method: "POST",
      dataType: "json",
      data: {
        action: this.searchType === "cells" ? "search_cells" : "search_cell_members",
        keyword: query
      },
      success: (results) => {
        this.setLoading(false);
        this.renderFn(results, query);
      },
      error: () => {
        this.setLoading(false);
        $(this.infoBlockSelector).text("Error searching.");
        $(this.tableSelector + " tbody").empty();
      }
    });
  }

  init() {
    const $input = $(this.inputSelector);
    $input.on("input change", (e) => {
      clearTimeout(this.timer);
      this.setLoading(true);
      const val = $input.val().trim();
      this.timer = setTimeout(() => {
        this.handleSearch(val);
      }, this.delay);
    });
  }
}

// Cells search rendering
function renderCellsTable(cells, query) {
  const tbody = $("#cells-table tbody");
  tbody.empty();
  if (!cells || cells.length === 0) {
    $("#cells-table-info-block .info").text(query ? "No cells matched your search" : "No data found!");
  } else {
    $("#cells-table-info-block .info").text("");
    cells.forEach(function (cell, index) {
      const row = `
        <tr>
          <td class="sn-col"></td>
          <td>${cell.cell_name + " Cell"}</td>
          <td>${cell.date_created}</td>
          <td>${cell.cell_leader_name || ""}</td>
          <td>${cell.cell_members_count}</td>
          <td class="d-flex align-items-center gap-2">
            <button type="button" class="load-action-modal-dyn-content view-cell-details-btn action-btn px-3 py-1" data-content-type="view-cell-details" data-cell-name="${cell.cell_name}" data-cell-id="${cell.id}">View</button>
            <button type="button" class="load-action-modal-dyn-content assign-cell-admin-btn action-btn px-3 py-1" data-content-type="assign-cell-admin" data-cell-name="${cell.cell_name}" data-cell-id="${cell.id}">Assign admin</button>
          </td>
        </tr>`;
      tbody.append(row);
    });
  }
  updateTableSN(); // Always call, even if empty
}

// Cell members search rendering
function renderCellMembersTable(members, query) {
  const tbody = $("#cell-members-table tbody");
  tbody.empty();
  if (!members || members.length === 0) {
    $("#cell-members-table-info-block .info").text(query ? "No cell members matched your search" : "No data found!");
  } else {
    $("#cell-members-table-info-block .info").text("");
    members.forEach(function (member, index) {
      const row = `
        <tr>
          <td class="sn-col"></td>
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
          <td class="d-flex align-items-center gap-2">
            <button class="px-3 py-1 action-btn edit--member-btn load-action-modal-dyn-content" data-content-type="edit-cell-member-details" data-member-name="${member.first_name + " " + member.last_name
        }" data-member-id="${member.id
        }">Edit</button>
            <button class="px-3 py-1 action-btn delete-member-btn" data-member-id="${member.id
        }">Delete</button>
          </td>
        </tr>`;
      tbody.append(row);
    });
  }
  updateTableSN(); // Always call, even if empty
}

// Override fetchAllCells and fetchAllCellMembers to be used by search
window.fetchAllCells = function () {
  $.ajax({
    url: "../php/ajax.php",
    method: "POST",
    data: { action: "fetch_all_cells" },
    dataType: "json",
    success: function (cells) {
      renderCellsTable(cells, "");
    },
    error: () => {
      $("#cells-table-info-block .info").text("Error loading cells.");
      $("#cells-table tbody").empty();
    }
  });
};

window.fetchAllCellMembers = function () {
  $.ajax({
    url: "../php/ajax.php",
    method: "POST",
    data: { action: "fetch_all_cell_members" },
    dataType: "json",
    success: function (members) {
      renderCellMembersTable(members, "");
    },
    error: () => {
      $("#cell-members-table-info-block .info").text("Error loading members.");
      $("#cell-members-table tbody").empty();
    }
  });
};

// Initialize search bars after DOM ready
$(function () {
  // Cells page search - only initialize when the input exists on the page
  if ($("#cells-page .search-bar .search-input").length) {
    new SearchBar({

      inputSelector: "#cells-page .search-bar .search-input",
      iconSelector: "#cells-page .search-bar .search-icon",
      tableSelector: "#cells-table",
      infoBlockSelector: "#cells-table-info-block .info",
      searchType: "cells",
      fetchAllFn: window.fetchAllCells,
      renderFn: renderCellsTable,
      urlParam: "cells_search"
    });
  }

  // Cell members page search - support renamed id 'members-page' and fallback to legacy 'cell-members-page'
  let membersInputSelector = "";
  if ($("#members-page .search-bar .search-input").length) {
    membersInputSelector = "#members-page .search-bar .search-input";
  } else if ($("#cell-members-page .search-bar .search-input").length) {
    membersInputSelector = "#cell-members-page .search-bar .search-input";
  }

  if (membersInputSelector) {
    // derive icon selector by replacing input with icon in the same page block
    const membersIconSelector = membersInputSelector.replace(".search-input", ".search-icon").replace("#members-page", "#members-page").replace("#cell-members-page", "#cell-members-page");
    new SearchBar({
      inputSelector: membersInputSelector,
      iconSelector: membersIconSelector,
      tableSelector: "#cell-members-table",
      infoBlockSelector: "#cell-members-table-info-block .info",
      searchType: "members",
      fetchAllFn: window.fetchAllCellMembers,
      renderFn: renderCellMembersTable,
      urlParam: "members_search"
    });
  }
});

/*********************************************
      Fetch total cell members for church dashboard stats
*********************************************/
function fetchChurchCellMemberCount() {
  $.ajax({
    url: "../php/ajax.php",
    method: "POST",
    data: { action: "fetch_church_cell_member_count" },
    dataType: "json",
    success: function (res) {
      if (res && typeof res.count !== "undefined") {
        $("#dashboard-page .stat .value").eq(1).text(res.count); // 2nd stat block: Total cell members
      }
    }
  });
}

/*********************************************
  Custom Dropdown Member Selection Logic
*********************************************/
function initDropdownCheckboxLogic() {
  // Helper to update count span for a dropdown
  function updateDropdownCount($dropdown, checkboxSelector, countSpanSelector) {
    const checkedCount = $dropdown.find(checkboxSelector + ":checked").length;
    $dropdown.closest(".form-group").find(countSpanSelector).text(checkedCount);
  }

  // Helper to sync select-all checkbox state
  function syncSelectAll($dropdown, checkboxSelector, selectAllSelector) {
    const $checkboxes = $dropdown.find(checkboxSelector);
    const $selectAll = $dropdown.find(selectAllSelector);
    const checkedCount = $checkboxes.filter(":checked").length;
    if (checkedCount === $checkboxes.length && $checkboxes.length > 0) {
      $selectAll.prop("checked", true);
    } else {
      $selectAll.prop("checked", false);
    }
  }

  // Attendance dropdown: update count and select-all on change
  $(document).on("change", ".attendance-list input[type='checkbox']:not(.select-all-options)", function () {
    const $dropdown = $(this).closest(".attendance-dropdown");
    updateDropdownCount($dropdown, "input[type='checkbox']:not(.select-all-options)", ".attendance-count");
    syncSelectAll($dropdown, "input[type='checkbox']:not(.select-all-options)", ".select-all-attendance.select-all-options");
  });

  // First timers dropdown: update count and select-all on change
  $(document).on("change", ".first-timers-list input[type='checkbox']:not(.select-all-options)", function () {
    const $dropdown = $(this).closest(".first-timers-dropdown");
    updateDropdownCount($dropdown, "input[type='checkbox']:not(.select-all-options)", ".first-timers-count");
    syncSelectAll($dropdown, "input[type='checkbox']:not(.select-all-options)", ".select-all-first-timers.select-all-options");
  });

  // New converts dropdown: update count and select-all on change
  $(document).on("change", ".new-converts-list input[type='checkbox']:not(.select-all-options)", function () {
    const $dropdown = $(this).closest(".new-converts-dropdown");
    updateDropdownCount($dropdown, "input[type='checkbox']:not(.select-all-options)", ".new-converts-count");
    syncSelectAll($dropdown, "input[type='checkbox']:not(.select-all-options)", ".select-all-new-converts.select-all-options");
  });

  // Select-all logic for attendance
  $(document).on("change", ".select-all-attendance.select-all-options", function () {
    const checked = $(this).prop("checked");
    const $dropdown = $(this).closest(".attendance-dropdown");
    $dropdown.find("input[type='checkbox']:not(.select-all-options)").prop("checked", checked);
    updateDropdownCount($dropdown, "input[type='checkbox']:not(.select-all-options)", ".attendance-count");
  });

  // Select-all logic for first-timers
  $(document).on("change", ".select-all-first-timers.select-all-options", function () {
    const checked = $(this).prop("checked");
    const $dropdown = $(this).closest(".first-timers-dropdown");
    $dropdown.find("input[type='checkbox']:not(.select-all-options)").prop("checked", checked);
    updateDropdownCount($dropdown, "input[type='checkbox']:not(.select-all-options)", ".first-timers-count");
  });

  // Select-all logic for new-converts
  $(document).on("change", ".select-all-new-converts.select-all-options", function () {
    const checked = $(this).prop("checked");
    const $dropdown = $(this).closest(".new-converts-dropdown");
    $dropdown.find("input[type='checkbox']:not(.select-all-options)").prop("checked", checked);
    updateDropdownCount($dropdown, "input[type='checkbox']:not(.select-all-options)", ".new-converts-count");
  });

  // --- Scoped search/filter logic for dropdowns ---
  function filterDropdownList($dropdown, listSelector, searchSelector) {
    const query = $dropdown.find(searchSelector).val().toLowerCase();
    $dropdown.find(listSelector + " .dropdown-option").each(function () {
      // Only filter member options, not select-all
      if ($(this).find(".select-all-options").length) return;
      const labelText = $(this).text().toLowerCase();
      $(this).toggle(labelText.includes(query));
    });
  }

  // Attendance search
  $(document).on("input", ".attendance-dropdown .attendance-search", function () {
    const $dropdown = $(this).closest(".attendance-dropdown");
    filterDropdownList($dropdown, ".attendance-list", ".attendance-search");
  });

  // First timers search
  $(document).on("input", ".first-timers-dropdown .first-timers-search", function () {
    const $dropdown = $(this).closest(".first-timers-dropdown");
    filterDropdownList($dropdown, ".first-timers-list", ".first-timers-search");
  });

  // New converts search
  $(document).on("input", ".new-converts-dropdown .new-converts-search", function () {
    const $dropdown = $(this).closest(".new-converts-dropdown");
    filterDropdownList($dropdown, ".new-converts-list", ".new-converts-search");
  });

  // Restore all options when search is cleared
  $(document).on("change", ".attendance-dropdown .attendance-search", function () {
    if (!$(this).val()) {
      const $dropdown = $(this).closest(".attendance-dropdown");
      $dropdown.find(".attendance-list .dropdown-option").show();
    }
  });
  $(document).on("change", ".first-timers-dropdown .first-timers-search", function () {
    if (!$(this).val()) {
      const $dropdown = $(this).closest(".first-timers-dropdown");
      $dropdown.find(".first-timers-list .dropdown-option").show();
    }
  });
  $(document).on("change", ".new-converts-dropdown .new-converts-search", function () {
    if (!$(this).val()) {
      const $dropdown = $(this).closest(".new-converts-dropdown");
      $dropdown.find(".new-converts-list .dropdown-option").show();
    }
  });

  // --- Dynamic population of first-timers and new-converts dropdowns based on attendance ---

  // Helper to get checked attendance member IDs and names
  function getCheckedAttendanceMembers() {
    const members = [];
    $(".attendance-dropdown .attendance-list input[type='checkbox']:not(.select-all-options):checked").each(function () {
      const $option = $(this).closest(".dropdown-option");
      const memberId = $(this).val();
      // Get label text (member name)
      const memberName = $option.text().trim();
      members.push({ id: memberId, name: memberName });
    });
    return members;
  }

  // Sync a dependent dropdown (first-timers or new-converts) with the currently checked attendance members.
  // This adds new options for newly-checked attendance members and removes options for unchecked ones,
  // preserving the rest of the dropdown DOM (no full refresh).
  function syncDependentList($dropdownList, members, selectAllClass) {
    if (!$dropdownList || $dropdownList.length === 0) return;

    // Determine input name and count selector for this dependent list
    const isFirstTimers = selectAllClass.indexOf('first-timers') !== -1;
    const isNewConverts = selectAllClass.indexOf('new-converts') !== -1;
    const inputName = isFirstTimers ? 'first_timers[]' : (isNewConverts ? 'new_converts[]' : '');
    const countSelector = isFirstTimers ? '.first-timers-count' : '.new-converts-count';

    // Determine whether new options should be disabled (inherit select-all disabled state)
    const $selectAll = $dropdownList.find('.dropdown-option').has(`.${selectAllClass}`).first();
    const shouldDisable = $selectAll.find('.select-all-options').prop('disabled') || false;

    // Build a map of existing member options (exclude select-all)
    const existingMap = {};
    $dropdownList.find('.dropdown-option').not(':has(.select-all-options)').each(function () {
      const $opt = $(this);
      const $inp = $opt.find("input[type='checkbox']");
      const val = $inp.val();
      if (val !== undefined && val !== '') existingMap[String(val)] = $opt;
    });

    // Remove any existing option that's no longer in checked attendance
    for (const existingId in existingMap) {
      if (!members.some(m => String(m.id) === String(existingId))) {
        existingMap[existingId].remove();
      }
    }

    // Add any member from attendance that's missing in the dependent list
    members.forEach(member => {
      const idStr = String(member.id);
      if (!existingMap[idStr]) {
        const disabledAttr = shouldDisable ? 'disabled' : '';
        const nameAttr = inputName ? ` name="${inputName}"` : '';
        const $newOpt = $(
          `<div class="dropdown-option">
             <label>
               <input type="checkbox" class="form-check-input me-2"${nameAttr} value="${member.id}" ${disabledAttr}>
               ${member.name}
             </label>
           </div>`
        );
        // Append after select-all option (which should remain at top)
        if ($selectAll.length) {
          $selectAll.after($newOpt);
        } else {
          $dropdownList.append($newOpt);
        }
      }
    });

    // After sync, ensure select-all state and counts are consistent
    updateDropdownCount($dropdownList, "input[type='checkbox']:not(.select-all-options)", countSelector);
    syncSelectAll($dropdownList, "input[type='checkbox']:not(.select-all-options)", "." + selectAllClass);
  }

  // Update first-timers and new-converts dropdowns when attendance changes
  function updateDependentDropdowns() {
    const members = getCheckedAttendanceMembers();
    // Sync lists instead of fully repopulating so dropdown state is preserved
    const $firstTimersList = $(".first-timers-dropdown .first-timers-list");
    syncDependentList($firstTimersList, members, "select-all-first-timers");
    const $newConvertsList = $(".new-converts-dropdown .new-converts-list");
    syncDependentList($newConvertsList, members, "select-all-new-converts");
  }

  // Whenever attendance selection changes, update dependent dropdowns
  $(document).on("change", ".attendance-list input[type='checkbox']:not(.select-all-options)", function () {
    updateDependentDropdowns();
    // ...existing count/select-all sync logic
    const $dropdown = $(this).closest(".attendance-dropdown");
    updateDropdownCount($dropdown, "input[type='checkbox']:not(.select-all-options)", ".attendance-count");
    syncSelectAll($dropdown, "input[type='checkbox']:not(.select-all-options)", ".select-all-attendance.select-all-options");
  });

  // Also update dependent dropdowns when select-all attendance is toggled
  $(document).on("change", ".select-all-attendance.select-all-options", function () {
    updateDependentDropdowns();
    // ...existing count logic
    const $dropdown = $(this).closest(".attendance-dropdown");
    updateDropdownCount($dropdown, "input[type='checkbox']:not(.select-all-options)", ".attendance-count");
  });

  // ...existing code for counts, select-all, search, etc...
}

// Initialize dropdown logic (self-contained)
initDropdownCheckboxLogic();

// Helper for meeting description (JS, for frontend rendering if needed)
function getMeetingDescription(week) {
  if (week == 1) return "Prayer and Planning";
  if (week == 2) return "Bible Study Class 1";
  if (week == 3) return "Bible Study Class 2";
  if (week == 4) return "Cell Outreach";
  return "Cell Fellowship";
}

// When rendering report drafts, ensure description is set using helper
function buildDraftElement(draft) {
  // draft: object with id, type, week, status, date_generated, description (optional)
  const status = draft.status ?? "pending";
  // Use correct description (fix: use helper, not undefined variable)
  const desc = getMeetingDescription(draft.week);
  const $el = $(`
    <div class="report-draft px-3 py-2 d-flex align-items-center justify-content-between gap-2"
         data-report-type="${draft.type}"
         data-week="${draft.week}"
         data-report-status="${status}"
         data-id="${draft.id}"
         data-date-generated="${draft.date_generated}">
        <div class="text-bar d-flex align-items-center gap-2">
          <h6 class="m-0 p-0 week">W${draft.week}:</h6>
          <p class="m-0 p-0 description">${$("<div>").text(desc).html()}</p>
        </div>
        <div class="action-bar d-flex align-items-center justify-content-between gap-2">
          <span class="label">${status === "published" ? "published" : ""}</span>
          <button class="${status === "published" ? "view-btn" : "publish-btn"
    } m-0 p-0" data-cell-id="${draft.cell_id}">${status === "published" ? "View" : "Publish"
    }</button>
      </div>
    </div>
  `);

  return $el;
}
