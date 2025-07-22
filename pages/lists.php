<div class="content">

  <div class="block">

    <h2 class="heading">Cell Members</h2>

    <section id="table-section">

      <div class="control-panel">
        <button id="edit-table-btn" title="Edit Table">
          <span class="edit-icon">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#666666"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h261q20 0 30 12.5t10 27.5q0 15-10.5 27.5T460-760H200v560h560v-261q0-20 12.5-30t27.5-10q15 0 27.5 10t12.5 30v261q0 33-23.5 56.5T760-120H200Zm280-360Zm-120 80v-97q0-16 6-30.5t17-25.5l344-344q12-12 27-18t30-6q16 0 30.5 6t26.5 18l56 57q11 12 17 26.5t6 29.5q0 15-5.5 29.5T897-728L553-384q-11 11-25.5 17.5T497-360h-97q-17 0-28.5-11.5T360-400Zm481-384-56-56 56 56ZM440-440h56l232-232-28-28-29-28-231 231v57Zm260-260-29-28 29 28 28 28-28-28Z"/></svg>
          </span>
          <span class="cancel-icon">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#666666"><path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/></svg>
          </span>
        </button>
      </div>
      
      <div id="cell-members-table-container">
        <table id="cell-members-table">
          <thead>
            <th id="title-theading">Title</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Phone Number</th>
            <th>Email Address</th>
            <th>Date of Birth</th>
            <th>Residential Address</th>
            <th>Occupation</th>
            <th id="fs-theading">Foundation School Status</th>
            <th>Dept. in Cell</th>
            <th>Dept. in Church</th>
            <th>Joined the Ministry On:</th>
          </thead>

          <tbody></tbody>

        </table>

        <div id="custom-dropdown" class="absolute bg-white border rounded-lg shadow-lg overflow-hidden hidden">
          <div class="p-2 bg-gray-100 text-gray-400">Select</div>
          <ul class="text-sm">
          </ul>
        </div>
      
        <!-- End table container -->
      </div>

      <button id="add-member-btn" title="Add a member">
        <span><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffffff"><path d="M440-440H240q-17 0-28.5-11.5T200-480q0-17 11.5-28.5T240-520h200v-200q0-17 11.5-28.5T480-760q17 0 28.5 11.5T520-720v200h200q17 0 28.5 11.5T760-480q0 17-11.5 28.5T720-440H520v200q0 17-11.5 28.5T480-200q-17 0-28.5-11.5T440-240v-200Z"/></svg></span>
      </button>

    </section>
    
  </div>

</div>

<!-- Scripts -->
<script>

  $(document).ready(() => {
    let editIcon = $('#edit-table-btn .edit-icon').clone();
    let cancelIcon = $('#edit-table-btn .cancel-icon').clone();

    // Function to assign dropdowns only to specific columns
    function addDropdownToColumn(tableId, columnId, options) {
      let columnIndex = $(`#${columnId}`).index(); // Get column index
      
      if (columnIndex !== -1) {
        $(`#${tableId} tbody tr`).each(function () {
          let $cell = $(this).find(`td:eq(${columnIndex})`);
          $cell.attr('data-options', options.join(', '));
          $cell.addClass('dropdown-cell'); // Add class for targeted columns
        });
      }
    }

    // Apply dropdowns to specific columns
    addDropdownToColumn('cell-members-table', 'title-theading', ['Brother', 'Sister', 'Deacon', 'Deaconess', 'Pastor']);
    addDropdownToColumn('cell-members-table', 'fs-theading', ['Student', 'Graduate']);

    $('#edit-table-btn').on('click', () => {
      editCells('editBtn');
    });

    function editCells(source) {
      let $editableCells = $('#cell-members-table tbody td');
      let isEditable = $editableCells.attr('contenteditable') === 'true';

      if (source === 'editBtn' && $editableCells.length === 0) {
        return;
      }

      if (source === 'addMemberFunc') {
        $editableCells.attr('contenteditable', 'true');
        $('#edit-table-btn').empty().append(cancelIcon.clone());
        let $firstCell = $('#cell-members-table tbody tr:last-child td:first-child').focus();
        setTimeout(() => {
          if ($firstCell.is('[data-options]')) {
            tableCellDropdownValue.call($firstCell[0]); // Call function immediately
          }
        }, 50);
      } else {
        $editableCells.attr('contenteditable', isEditable ? 'false' : 'true');
        $('#edit-table-btn').empty().append(isEditable ? editIcon.clone() : cancelIcon.clone());

        if (!isEditable) {
          $editableCells.first().focus();
          setTimeout(() => {
            if ($editableCells.first().is('[data-options]')) {
              tableCellDropdownValue.call($editableCells.first()[0]); // Call function immediately
            }
          }, 50);
        }
      }

      // Apply dropdown functionality only to targeted columns
      $('.dropdown-cell').on('focus', tableCellDropdownValue);
    }

    function tableCellDropdownValue() {
      let $cell = $(this);

      if ($cell.is(':focus')) {
        $cell.on('click', positionDropdown);
      } else {
        $cell.off('click');
        return;
      }

      $cell.on('keydown paste', function (event) {
        event.preventDefault();
      });

      let options = $cell.data('options');

      if (!options) return; // Prevent error if no data-options

      let dropdown = $('#custom-dropdown');
      let dropdownList = dropdown.find('ul').empty();

      options.split(', ').forEach(option => {
        dropdownList.append(`<li class="p-2 hover:bg-gray-200 cursor-pointer">${option}</li>`);
        positionDropdown();
      });

      function positionDropdown() {
        let tableOffset = $('#cell-members-table').position();
        let offset = $cell.position();
        let cellHeight = $cell.outerHeight();
        let cellWidth = $cell.outerWidth();

        dropdown.css({
          top: offset.top + cellHeight + tableOffset.top,
          left: offset.left + tableOffset.left,
          width: cellWidth
        }).show();
      }

      dropdownList.find('li').on('click', function () {
        $cell.text($(this).text()).focus();
        dropdown.hide();
      });

      $(document).on('click', function (e) {
        if (!$(e.target).closest('#custom-dropdown, .dropdown-cell').length) {
          dropdown.hide();
        }
      });

    }

    $('#add-member-btn').on('click', () => {
      let newRow = $('<tr>');
      
      $('#cell-members-table thead th').each(function (index) {
        let columnId = $(this).attr('id');
        let $newCell = $('<td contenteditable="true"></td>');

        if (columnId === 'title-theading') {
          $newCell.attr('data-options', 'Brother, Sister, Deacon, Deaconess, Pastor').addClass('dropdown-cell');
        } else if (columnId === 'fs-theading') {
          $newCell.attr('data-options', 'Student, Graduate').addClass('dropdown-cell');
        }

        newRow.append($newCell);
      });

      $('#cell-members-table tbody').append(newRow);
      editCells('addMemberFunc');

    });

  });


</script>
