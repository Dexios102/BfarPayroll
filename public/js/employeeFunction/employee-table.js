/* Border Bottom - Status */
const listItems = document.querySelectorAll('.all-status-windows li');
listItems[0].classList.add('active');
listItems.forEach(function (listItem) {
  listItem.addEventListener('click', function () {
    listItems.forEach(function (item) {
      item.classList.remove('active');
    });
    this.classList.add('active');
  });
});

/* All Count */
const table_count_pos_main = document.getElementById("table-main");
const countSpans_count_pos_main = document.querySelector(".count");
const rowCounts_count_pos_main = table_count_pos_main.querySelectorAll(".primary-table-row").length;
countSpans_count_pos_main.textContent = rowCounts_count_pos_main;

/* Full Screen */
function openFullscreen(maxHeight) {
  const div = document.querySelector('.all-table-container');
  const table = div.querySelector('.table-container-wrapper');
  table.style.height = '90vh';
  if (div.requestFullscreen) {
    div.requestFullscreen();
  } else if (div.webkitRequestFullscreen) {
    div.webkitRequestFullscreen();
  } else if (div.msRequestFullscreen) {
    div.msRequestFullscreen();
  }
}

/* Status Effect */
function changeStatusColor() {
  const wrapperStatus = document.querySelectorAll('.wrapper-status');

  wrapperStatus.forEach(status => {
    if (status.innerText === 'active') {
      status.parentElement.style.backgroundColor = '#013a63';
      status.parentElement.style.borderRadius = '50px';
      status.parentElement.style.color = '#fff';
    } else if (status.innerText === 'inactive') {
      status.parentElement.style.backgroundColor = '#a4161a';
      status.parentElement.style.borderRadius = '50px';
      status.parentElement.style.color = '#fff';
    }
  });
}
document.addEventListener('DOMContentLoaded', () => {
  changeStatusColor();
});
/* CheckBox and Printing*/
var checkboxes = document.querySelectorAll('tbody.tbody-main input[type="checkbox"]');
var selectAll = document.querySelector('thead.thead-main input[type="checkbox"]');
var rowst = document.querySelectorAll('tbody.tbody-main .primary-table-row');

function selectAllRows() {
  for (var i = 0; i < checkboxes.length; i++) {
    checkboxes[i].checked = selectAll.checked;
    if (checkboxes[i].checked) {
      rowst[i].classList.add('selected');
    } else {
      rowst[i].classList.remove('selected');
    }
  }
}

for (var i = 0; i < checkboxes.length; i++) {
  checkboxes[i].addEventListener('click', function () {
    var isChecked = this.checked;
    var row = this.parentNode.parentNode;
    if (isChecked) {
      row.classList.add('selected');
    } else {
      row.classList.remove('selected');
    }
    if (document.querySelectorAll('tbody.tbody-main input[type="checkbox"]:checked').length === checkboxes.length) {
      selectAll.checked = true;
    } else {
      selectAll.checked = false;
    }
  });
}
selectAll.addEventListener('click', selectAllRows);

var printBtn = document.getElementById('print-btn');
printBtn.addEventListener('click', function () {
  var selectedRows = [];
  for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].checked) {
      selectedRows.push(rowst[i]);
    }
  }
  var tableData = `
  <div class="print-check">
  <table><thead>
  <tr>
  <th colspan="9" style="padding: 1rem; font-size: 1.1rem;">Employee Data</th>
  </tr><tr>
  <th>ID</th>
  <th>Status</th>
  <th></th>
  <th>Name</th>
  <th>Division</th>
  <th>Daily Rate</th>
  <th>Position</th>
  <th>Recorded Date</th>
  </tr></thead><tbody></div>`;

  for (var i = 0; i < selectedRows.length; i++) {
    var rowCells = selectedRows[i].querySelectorAll('td');
    tableData += '<tr>';
    for (var j = 1; j < rowCells.length - 1; j++) {
      tableData += '<td>' + rowCells[j].textContent + '</td>';
    }
    tableData += '</tr>';
  }
  tableData += '</tbody></table>';
  var printWindow = window.open('', '', 'height=500,width=800');
  printWindow.document.write('<html><head><title>Generated by: BFAR Payroll System</title></head><body>');
  printWindow.document.write(`
  <style>
  table {
    border-collapse: collapse;
    font-family: 'Roboto', sans-serif;
  } 
  td, th 
  {
    border: 1px solid black; 
    padding: 5px;
  }
  th {
    font-weight: bold;
  }
  td:nth-child(1), td:nth-child(8) {
    color: #2d6a4f;
  }
  td:nth-child(4) {
    font-weight: 600;
  }
  </style>`);
  printWindow.document.write(tableData);
  printWindow.document.write('</body></html>');
  printWindow.print();
});

function changeRowMoneyColor() {
  const moneyElements = document.querySelectorAll('.wrapper-money');
  moneyElements.forEach(element => {
    const dailyRate = parseFloat(element.textContent);
    element.textContent = `₱ ${dailyRate.toFixed(2)}`;
    if (dailyRate >= 1000) {
      element.style.backgroundColor = '#ced4da';
      element.style.borderRadius = '10px';
      element.style.color = '#3a5a40';
      element.style.fontWeight = 'bold';
      element.style.fontSize = '1.1rem';
    } else if (dailyRate >= 500) {
      element.style.backgroundColor = '#ced4da';
      element.style.borderRadius = '10px';
      element.style.color = '#003049';
      element.style.fontWeight = 'bold';
      element.style.fontSize = '1.1rem';
    } else {
      element.style.backgroundColor = '#ced4da';
      element.style.borderRadius = '10px';
      element.style.color = '#c1121f';
      element.style.fontWeight = 'bold';
      element.style.fontSize = '1.1rem';
    }
  });
}
window.onload = function () {
  changeRowMoneyColor();
}

/* Search */

/* Sorting */
const table_pos_main = document.getElementById("table-main");
const rows_pos_main = table_pos_main.getElementsByTagName("tr");
const headerRow_pos_main = rows_pos_main[0];
const sortButtons_pos_main = headerRow_pos_main.getElementsByClassName("sort-btn");
for (let i = 0; i < sortButtons_pos_main.length; i++) {
  const sortButton_pos_main = sortButtons_pos_main[i];

  sortButton_pos_main.addEventListener("click", function () {
    const sortBy_pos_main = sortButton_pos_main.dataset.sortby;

    const sortDirection_pos_main = sortButton_pos_main.classList.contains("desc") ? "asc" : "desc";

    for (let j = 0; j < sortButtons_pos_main.length; j++) {
      sortButtons_pos_main[j].classList.remove("asc");
      sortButtons_pos_main[j].classList.remove("desc");
    }
    sortButton_pos_main.classList.add(sortDirection_pos_main);

    let columnIndex_pos_main;
    switch (sortBy_pos_main) {
      case "id":
        columnIndex_pos_main = 2;
        break;
      case "name":
        columnIndex_pos_main = 5;
        break;
      case "division":
        columnIndex_pos_main = 6;
        break;
      case "position":
        columnIndex_pos_main = 8;
        break;
      case "created_at":
        columnIndex_pos_main = 9;
        break;
      default:
        columnIndex_pos_main = 0;
        break;
    }

    const rowsArray_pos_main = Array.from(rows_pos_main).slice(1);
    rowsArray_pos_main.sort(function (a_pos_main, b_pos_main) {
      const aValue_pos_main = a_pos_main.getElementsByTagName("td")[columnIndex_pos_main].textContent.trim();
      const bValue_pos_main = b_pos_main.getElementsByTagName("td")[columnIndex_pos_main].textContent.trim();
      return sortDirection_pos_main === "asc" ? aValue_pos_main.localeCompare(bValue_pos_main) : bValue_pos_main.localeCompare(aValue_pos_main);
    });
    rowsArray_pos_main.forEach(function (row_pos_main) {
      table_pos_main.appendChild(row_pos_main);
    });
  });
}

/* View Modal */
function showModal(rowv) {
  const id = rowv.cells[1].textContent;
  const status = rowv.cells[2].textContent;
  const name = rowv.cells[4].textContent;
  const division = rowv.cells[5].textContent;
  const d_rate = rowv.cells[6].textContent;
  const position = rowv.cells[7].textContent;
  const created_at = rowv.cells[8].textContent;
  const modalContent = `
  <div class="eye-modal">
  <h2 class="eye-header">Department Active</h2>
    <p><strong>ID: </strong>${id}</p>
    <p><strong>Status: </strong> <span class="active-status">${status}</span></p>
    <p><strong>Name: </strong> ${name}</p>
    <p><strong>Division: </strong> ${division}</p>
    <p><strong>Daily Rate: </strong> ${d_rate}</p>
    <p><strong>Position: </strong> ${position}</p>
    <p><strong>Created at: </strong><span class="created"> ${created_at}<span></p>
  </div>
`;
  const modal = document.createElement('div');
  modal.classList.add('modalview');
  modal.innerHTML = modalContent;
  document.body.appendChild(modal);
  modal.addEventListener('click', (event) => {
    if (event.target === modal) {
      modal.remove();
    }
  });
}
const eyeIcons = document.querySelectorAll('.eye-main-pos');
eyeIcons.forEach((icon) => {
  icon.addEventListener('click', (event) => {
    const rowe = event.target.closest('tr');
    showModal(rowe);
  });
});

/* Changetable */
function openTable(tableId) {
  const tables = document.querySelectorAll('.table-change');
  const allBtn = document.getElementById('all-btn');
  const archiveBtn = document.getElementById('archive-button');
  tables.forEach(table => {
    table.classList.remove('active');
  });
  allBtn.classList.remove('active');
  archiveBtn.classList.remove('active');
  const tableToShow = document.getElementById(tableId);
  tableToShow.classList.add('active');
  if (tableId === 'table-all') {
    allBtn.classList.add('active');
  } else if (tableId === 'table-archived') {
    archiveBtn.classList.add('active');
  }
}

/* Export to excel */
function exportToExcel() {
  const tableBody = document.getElementById("table-body");
  const selectedRows = tableBody.querySelectorAll('input[type="checkbox"]:checked');
  if (selectedRows.length === 0) {
    alert("Please select at least one row to export.");
    return;
  }

  const data = [];
  const headers = ["Id", "Status", "Name", "Division", "Daily Rate", "Position", "Date Recorded", "Type", "Contact", "Birthdate", "Address", "Gender", "Email"];

  data.push(headers);

  selectedRows.forEach((row) => {
    const rowData = [];
    const tableRow = row.closest("tr");
    const id = tableRow.querySelector("td:nth-child(2)").textContent.trim();
    const status = tableRow.querySelector(".wrapper-status").textContent.trim();
    const name = tableRow.querySelector("#full_name").textContent.trim();
    const division = tableRow.querySelector("td:nth-child(6)").textContent.trim();
    const dailyRate = tableRow.querySelector(".wrapper-money").textContent.trim();
    const position = tableRow.querySelector("td:nth-child(8)").textContent.trim();
    const dateRecorded = tableRow.querySelector(".created_at").textContent.trim();

    const extraRow = tableRow.nextElementSibling;
    const jobType = extraRow.querySelector(".job_type").textContent.trim();
    const contact = extraRow.querySelector("span:nth-child(2)").textContent.trim();
    const birthdate = extraRow.querySelector("span:nth-child(1)").textContent.trim();
    const address = extraRow.querySelector("span:nth-child(2)").textContent.trim();
    const gender = extraRow.querySelector("span:nth-child(1)").textContent.trim();
    const email = extraRow.querySelector("span:nth-child(2)").textContent.trim();

    rowData.push(id, status, name, division, dailyRate, position, dateRecorded, jobType, contact, birthdate, address, gender, email);
    data.push(rowData);
  });
  const worksheet = XLSX.utils.aoa_to_sheet(data);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
  XLSX.writeFile(workbook, "export.xlsx");
}
