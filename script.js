$(document).ready(function () {
  // Toggle Action Modal
  function toggleActionModal() {
    const actionModal = $("#action-modal");
    const actionPanel = $("#action-modal .action-panel");

    // $('#action-modal .action-panel .heading').text(title);

    if (actionModal.is(":visible")) {
      actionModal.animate({ opacity: 0 }, 200, function () {
        $(actionModal).css("display", "none");
      });
      actionPanel.animate({ top: "45%" }, 200);
      $("#action-modal #edit-title-container").empty();
      $("#action-modal .side-panel").empty();
    } else {
      actionModal
        .css({ display: "block", opacity: 0 })
        .animate({ opacity: 1 }, 200);
      actionPanel.animate({ top: "50%" }, 200);
    }
    return this;
  }
  window.toggleActionModal = toggleActionModal;

  function toggleActionModalSidePanel() {
    const sidePanel = $("#action-modal .side-panel");
    sidePanel.toggleClass("reveal");
    if (sidePanel.hasClass("reveal")) {
      $("#action-modal .action-panel .panel-body").css({
        height: `400px`
      });
    }
  }
  window.toggleActionModalSidePanel = toggleActionModalSidePanel;

  // Toggle Sidebar

  $(document).on("click", ".screen .menu-btn", function () {
    $(".sidebar").css({ left: "0" });
  });

  $(document).on("click", ".sidebar .close-btn", function () {
    $(".sidebar").css({ left: "-100%" });
  });

  // Page navigation
  const sidebarLinks = $(".sidebar nav li");
  $(document).on("click", ".sidebar nav li", function () {
    const clickedLinkId = $(this).attr("id");
    $(".sidebar").css({ left: "-100%" });

    switch (clickedLinkId) {
      case "dashboard-link":
        $(".screen header .page-title").text("Dashboard");
        sidebarLinks.removeClass("active");
        $(this).addClass("active");
        $(".data-container").removeClass("d-none").addClass("d-none");
        $("#dashboard-page").removeClass("d-none");
        break;

      case "cells-link":
        $(".screen header .page-title").text("Cells");
        sidebarLinks.removeClass("active");
        $(this).addClass("active");
        $(".data-container").removeClass("d-none").addClass("d-none");
        $("#cells-page").removeClass("d-none");
        break;

      case "reports-link":
        $(".screen header .page-title").text("Reports");
        sidebarLinks.removeClass("active");
        $(this).addClass("active");
        $(".data-container").removeClass("d-none").addClass("d-none");
        $("#reports-page").removeClass("d-none");
        break;

      case "settings-link":
        $(".screen header .page-title").text("Settings");
        sidebarLinks.removeClass("active");
        $(this).addClass("active");
        $(".data-container").removeClass("d-none").addClass("d-none");
        $("#settings-page").removeClass("d-none");
        break;
    }
  });

  // Call action modal when the Add a cell btn is clicked
  $(document).on("click", "#action-modal header .close-btn", function () {
    $("#action-modal .side-panel").removeClass("reveal");
    toggleActionModal();
  });

  // Show/Hide Assign Cell Admin Fields based on Admin Selection
  $(document).on(
    "change",
    "#assign-cell-admin-form #choose-admin",
    function () {
      const val = $(this).val();
      const $roleContainer = $("#assign-cell-admin-form .role-container");
      const $hiddenSection = $("#assign-cell-admin-form .hidden-section");

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

      validateAssignAdminForm();
    }
  );

  // Validate function for Assign Admin Form
  function validateAssignAdminForm() {
    const $form = $("#assign-cell-admin-form");
    const who = $form.find("#choose-admin").val();
    const role = $form.find("#role").val();

    let valid = false;

    if (who === "self") {
      valid =
        isFilled($form.find("#choose-admin")) && isFilled($form.find("#role"));
    } else if (who === "else") {
      valid =
        isFilled($form.find("#choose-admin")) &&
        isFilled($form.find("#role")) &&
        isFilled($form.find("#first-name")) &&
        isFilled($form.find("#last-name")) &&
        isFilled($form.find("#email")) &&
        isFilled($form.find("#password")) &&
        isFilled($form.find("#password-confirm")) &&
        $form.find("#password").val() === $form.find("#password-confirm").val();
    }

    $form.find(".submit-btn").prop("disabled", !valid);
  }

  $(document).on(
    "input change",
    "#assign-cell-admin-form input, #assign-cell-admin-form select",
    validateAssignAdminForm
  );
  window.validateAssignAdminForm = validateAssignAdminForm;

  // Edit Cell Info
  $(document).on("click", "#action-modal #editTitleBtn", function () {
    let inputVal = $.trim($("#action-modal .edit-title-input").val());
    $("#action-modal .edit-title-bar").toggleClass("d-none");
    $("#action-modal .edit-title-input").val(inputVal);
    $("#action-modal .edit-title-input").focus();
  });

  // Close Ready function
});
