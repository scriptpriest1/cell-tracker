$(document).ready(function () {
  // Control Bootstrap dropdown behaviour
  $(document).on("show.bs.dropdown", ".dropdown.static", function () {
    var $dropdown = $(this);
    var $toggle = $dropdown.find('[data-bs-toggle="dropdown"]');
    var $menu = $dropdown.find(".dropdown-menu");

    // Clone menu instead of moving it (keeps Bootstrap happy)
    var $menuClone = $menu
      .clone(true)
      .addClass("cloned-dropdown")
      .appendTo("body");

    var rect = $toggle[0].getBoundingClientRect();

    // Align right side of dropdown with right side of button
    var leftPos = rect.right - $menuClone.outerWidth();

    $menuClone.css({
      position: "absolute",
      top: rect.bottom + "px",
      left: leftPos + "px",
      zIndex: 9999,
      display: "block",
    });

    // Store reference so we can remove it later
    $dropdown.data("cloned-menu", $menuClone);

    // Hide original to prevent double display
    $menu.hide();
  });

  $(document).on("hide.bs.dropdown", ".dropdown", function () {
    var $dropdown = $(this);
    var $menu = $dropdown.find(".dropdown-menu");
    var $menuClone = $dropdown.data("cloned-menu");

    // Remove cloned one
    if ($menuClone) {
      $menuClone.remove();
      $dropdown.removeData("cloned-menu");
    }

    // Show original again
    $menu.show();
  });


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
      $("#action-modal .panel-body").removeClass("h-100");
      $("#action-modal .panel-body").css('height', `${sidePanel.height() + 48}`);
    } else {
      $("#action-modal .panel-body").addClass("h-100");
      $("#action-modal .panel-body").css('height', '');
    }
  }
  window.toggleActionModalSidePanel = toggleActionModalSidePanel;

  // Toggle Action Modal Side Panel when action modal cancel btn is clicked
  $(document).on('click', '#action-modal .action-modal-form .cancel-btn', function() {
    toggleActionModalSidePanel();
    $("#action-modal .panel-body").addClass("h-100");
    $("#action-modal .panel-body").css("height", "");
  })

  // Toggle Sidebar

  $(document).on("click", ".screen .menu-btn", function () {
    $(".sidebar").css({ left: "0" });
  });

  $(document).on("click", ".sidebar .close-btn", function () {
    $(".sidebar").css({ left: "-100%" });
  });

  // URL-based page navigation
  function showPageFromURL() {
    const params = new URLSearchParams(window.location.search);
    let page = params.get("p") || "dashboard";
    let filter = params.get("filter") || null;

    // Hide all pages
    $(".data-container").addClass("d-none");
    // Show the requested page
    $(`#${page}-page`).removeClass("d-none");

    // Update page title
    let $activeSidebarLink = $(`.sidebar nav li[data-page-id='${page}-page']`);
    if ($activeSidebarLink.length) {
      $(".sidebar nav li").removeClass("active");
      $activeSidebarLink.addClass("active");
      $("#screen header .page-title").text($activeSidebarLink.data("page-title"));
    }

    // Reports filter logic
    if (page === "reports" && filter) {
      $(".filter").removeClass("active");
      $(`#${filter}`).addClass("active");
      // Optionally, filter report drafts here
      // $(".report-draft").hide();
      // $(".report-draft[data-report-type='" + filter + "']").show();
    }
  }

  // Initial page load
  showPageFromURL();

  // Listen for browser navigation (back/forward)
  window.addEventListener("popstate", showPageFromURL);

  // Sidebar navigation: update URL and show page
  $(document).on("click", ".sidebar nav li", function () {
    let pageId = $(this).data("page-id").replace("-page", "");
    let pageTitle = $(this).data("page-title");
    // Close sidebar
    $(".sidebar").css({ left: "-100%" });
    // Update URL
    history.pushState({}, "", `?p=${pageId}`);
    showPageFromURL();
  });

  // Reports filter navigation: update URL and show filtered reports
  $(document).on("click", ".filter", function () {
    let filterId = $(this).attr("id");
    let params = new URLSearchParams(window.location.search);
    params.set("filter", filterId);
    history.pushState({}, "", `?p=reports&filter=${filterId}`);
    showPageFromURL();
  });

  // Call action modal when the Add a cell btn is clicked
  $(document).on("click", "#action-modal header .close-btn", function () {
    $("#action-modal .side-panel").removeClass("reveal");
    $("#action-modal .panel-body").addClass("h-100");
    $("#action-modal .panel-body").css("height", "");
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
