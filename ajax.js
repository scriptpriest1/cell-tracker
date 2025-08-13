$(document).ready(() => {
  // Load Cells into Cells Table
  fetchAllCells();

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
        if (res === "success") {
          alert("Admin successfully assigned!");
          // Optional: reload cells or close modal
          fetchAllCells();
          $btn.text("Assign").prop("disabled", false);
          $form.trigger("reset");
        } else {
          alert("Error: " + res);
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
          $("#table-info-block .info").text("No data found!");
          return;
        }

        cells.forEach(function (cell, index) {
          const row = `
          <tr>
            <td></td> <!-- SN will be filled by another function -->
            <td>${cell.cell_name + " Cell"}</td>
            <td>${cell.date_created}</td>
            <td>${cell.cell_leader_name || "—"}</td>
            <td>${cell.cell_members_count}</td>
            <td><button type="button" class="load-action-modal-dyn-content view-cell-details-btn action-btn px-3 py-1" data-content-type="view-cell-details" data-cell-name="${
              cell.cell_name
            }" data-cell-id="${
            cell.id
          }">View</button> <button type="button" class="load-action-modal-dyn-content assign-cell-admin action-btn px-3 py-1" data-content-type="assign-cell-admin" data-cell-name="${
            cell.cell_name
          }" data-cell-id="${cell.id}">Assign admin</button></td>
          </tr>`;
          tbody.append(row);
        });

        updateCellsTableSN(); // Call to number rows
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
  function updateCellsTableSN() {
    $("#cells-table tbody tr").each(function (index) {
      $(this)
        .find("td:first")
        .text(`${index + 1}.`);
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
        } else return;
      },
    });
  }

  /*********************************************
            Unassign cell admin logic
  *********************************************/
  $(document).on("click", ".unassign-btn", function (e) {
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

  // Close ready() function
});
