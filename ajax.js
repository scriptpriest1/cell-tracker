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
              Add A Cell Functionality
  *********************************************/

  function validateCellAdminAssignment() {
    const $role = $("#add-cell-form #admin-role");
    const role = $role.val();
    if (role === "") return;

    const $firstName = $("#add-cell-form #admin-first-name"),
      $lastName = $("#add-cell-form #admin-last-name"),
      $email = $("#add-cell-form #admin-email"),
      $pw = $("#add-cell-form #admin-password"),
      $confPw = $("#add-cell-form #admin-password-confirm");

    return (
      isFilled($firstName) &&
      isFilled($lastName) &&
      isFilled($email) &&
      isFilled($pw) &&
      isFilled($confPw) &&
      $pw.val() === $confPw.val()
    );
  }

  function inValidateCellAdminAssignment() {
    const $role = $("#add-cell-form #admin-role");
    const role = $role.val();
    if (role !== "") return;

    const $firstName = $("#add-cell-form #admin-first-name"),
      $lastName = $("#add-cell-form #admin-last-name"),
      $email = $("#add-cell-form #admin-email"),
      $pw = $("#add-cell-form #admin-password"),
      $confPw = $("#add-cell-form #admin-password-confirm");

    return (
      isNotFilled($firstName) &&
      isNotFilled($lastName) &&
      isNotFilled($email) &&
      isNotFilled($pw) &&
      isNotFilled($confPw)
    );
  }

  $(document).on(
    "input change",
    "#add-cell-form .form-control, #add-cell-form .form-select",
    function () {
      const validityOne =
          isFilled($("#add-cell-form #cell-name")) &&
          validateCellAdminAssignment(),
        validityTwo =
          isFilled($("#add-cell-form #cell-name")) &&
          inValidateCellAdminAssignment(),
        valid = validityOne || validityTwo;
      $("#add-cell-form .submit-btn").prop("disabled", !valid);
    }
  );

  $(document).on("submit", "#add-cell-form", function (e) {
    e.preventDefault();

    const $btn = $("#add-cell-form .submit-btn")
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
          $btn.prop("disabled", false).text("Add Cell");
        } else {
          alert("Error: " + res);
        }
      },
      error: () => {
        alert("Server error");
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

        $("#cell-count").text(cells.length);

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
              cell.cell_name + " Cell"
            }" data-cell-id="${
            cell.id
          }">View</button> <button type="button" class="load-action-modal-dyn-content assign-cell-admin action-btn px-3 py-1" data-content-type="assign-cell-admin" data-cell-name="${
            cell.cell_name + " Cell"
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

  $(document).on("click", ".load-action-modal-dyn-content", loadDynamicContentfunction); 
  
  window.loadDynamicContentfunction = function () {
    const $thisElement = $(this);
    let contentType = $thisElement.data("content-type");
    let cellId = $thisElement.data("cell-id");

    $.ajax({
      url: "../php/load_dynamic_content.php",
      method: "POST",
      data: { "content-type": contentType, "cell-id": cellId },
      success: (res) => {
        if (contentType === "add-a-cell-form") {
          $("#action-modal header .title").text("Add a Cell");
          $("#action-modal .content-container").html(res);
          toggleActionModal();
        } else if (contentType === "assign-cell-admin") {
          $("#action-modal header .title").text($thisElement.data("cell-name"));
          $("#action-modal .content-container").html(res);
          $("#action-modal .content-container #cell-id").val(
            $thisElement.data("cell-id")
          );
          toggleActionModal();
        } else if (contentType === "view-cell-details") {
          $("#action-modal header .title").text($thisElement.data("cell-name"));
          $("#action-modal .content-container").html(res);
          toggleActionModal();
        } else if (contentType === "fetch-cell-admins") {
          $("#action-modal .content-container").html(res);
        } else return;
      },
    });
  };

  // Unassign cell admin logic
  $(document).on("click", ".unassign-btn", function () {
    const userId = $(this).data("user-id");
    const cellId = $(this).data("cell-id");

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
          // Refresh the list
          fetchCellAdmins(); // Make sure this function exists globally
        } else {
          alert(res.message || "Failed to unassign admin.");
        }
      },
      error: () => {
        alert("Server error.");
      },
    });
  });
});
