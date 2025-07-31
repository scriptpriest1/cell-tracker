$(document).ready(function () {
  // Toggle Action Modal
  function toggleActionModal() {
    const actionModal = $('#action-modal');
    const actionPanel = $('#action-modal .action-panel');

    // $('#action-modal .action-panel .heading').text(title);

    if (actionModal.is(':visible')) {
      actionModal.animate({ opacity: 0 }, 200, function () {
        $(actionModal).css('display', 'none');
      });
      actionPanel.animate({ top: '45%' }, 200);
    } else {
      actionModal
        .css({ display: 'block', opacity: 0 })
        .animate({ opacity: 1 }, 200);
      actionPanel.animate({ top: '50%' }, 200);
    }
  }

  // Toggle Sidebar

  $(document).on('click', '.screen .menu-btn', function () {
    $('.sidebar').css({ left: '0' });
  });

  $(document).on('click', '.sidebar .close-btn', function () {
    $('.sidebar').css({ left: '-100%' });
  });

  // Page navigation
  const sidebarLinks = $('.sidebar nav li');
  $(document).on('click', '.sidebar nav li', function () {
    const clickedLinkId = $(this).attr('id');
    $('.sidebar').css({ left: '-100%' });

    switch (clickedLinkId) {
      case 'dashboard-link':
        $('.screen header .page-title').text('Dashboard');
        sidebarLinks.removeClass('active');
        $(this).addClass('active');
        $('.data-container').removeClass('d-none').addClass('d-none');
        $('#dashboard-page').removeClass('d-none');
        break;

      case 'cells-link':
        $('.screen header .page-title').text('Cells');
        sidebarLinks.removeClass('active');
        $(this).addClass('active');
        $('.data-container').removeClass('d-none').addClass('d-none');
        $('#cells-page').removeClass('d-none');
        break;

      case 'reports-link':
        $('.screen header .page-title').text('Reports');
        sidebarLinks.removeClass('active');
        $(this).addClass('active');
        $('.data-container').removeClass('d-none').addClass('d-none');
        $('#reports-page').removeClass('d-none');
        break;

      case 'settings-link':
        $('.screen header .page-title').text('Settings');
        sidebarLinks.removeClass('active');
        $(this).addClass('active');
        $('.data-container').removeClass('d-none').addClass('d-none');
        $('#settings-page').removeClass('d-none');
        break;
    }
  });

  // Call action modal when the Add a cell btn is clicked
  $(document).on('click', '#add-cell-btn, #action-modal header .close-btn', toggleActionModal);

  // Close Ready function
});
