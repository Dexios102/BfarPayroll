@extends('layout.master')
@section('payroll')
active
@endsection
@section('content')
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Payroll Section</title>
  <link rel="stylesheet" href="css/Payroll/payroll-main.css" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <style>
    /* Styles for the custom modal */
    .modal2 {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content2 {
      border-radius: 15px;
      text-align: center;
      background-color: #fefefe;
      margin: 5% auto;
      padding: 3rem 2rem 3rem 2rem;
      border: 5px solid #2e3255;
      width: 40%;
      box-shadow: 9px 8px 6px 0px rgb(154, 154, 156);
      transition: 1s ease;

    }

    .button-cont {
      display: flex;
      gap: 1rem;
    }

    #confirmButton2 {
      width: 100%;
      padding: 5px;
      background-color: #157fd1;
      border: 2px solid gray;
      border-radius: 5px;
      color: white;
      box-shadow: 2px 2px 6px 0px rgba(103, 101, 119, 1);
    }

    #cancelButton2 {
      width: 100%;
      padding: 5px;
      background-color: #fe3333;
      border: 2px solid gray;
      border-radius: 5px;
      color: white;
      box-shadow: 2px 2px 6px 0px rgba(103, 101, 119, 1);
    }

    #cancelButton2:hover {
      background-color: #f76a6a;
    }

    #confirmButton2:hover {
      background-color: #4ea0df;
    }
  </style>
</head>

<body>
  <div class="all-main-container">
    <div class="all-main-header-label">
      Payroll Data
    </div><!-- !All main header END -->
    <div class="all-status-windows">
      <ul>
        <li class="generate" id="generate-btn" onclick="openTable('window-generate')" style="font-weight: 600; opacity: 0.9; color: #023047">Genarate Payslip</li>
        <li class="all" id="all-btn" onclick="openTable('table-all')">All Data <span class="count">0</span></li>
      </ul>
    </div><!-- !All status windows END -->
    <div class="all-table-container">
      <div class="table-change active" id="window-generate">
        <div class="search-employee-container">
          <div class="flex-search">
            <p class="search-employee-label"><span class="required">*</span>Search Employee</p>
            <div class="wrap-search">
              <span class="search-icon"><i class="fa-solid fa-magnifying-glass search-icon"></i></span>
              <input type="text" class="search-employee" list="names" name="" id="inputEmployee" onkeyup="inputEmployee()" placeholder="Full Name or ID" />
              <datalist id="names">
                @foreach ($emp as $item)
                <option>{{$item->first_name}} {{$item->middle_name}} {{$item->last_name}}</option>
                @endforeach
              </datalist>
            </div><!-- !Wrap search END-->
          </div><!-- !Flex-search END -->
          <div class="flex-search">
            <div class="flex-container">
              <button class="action" onclick="generatePayrollSlip()">
                <span class="action-icons">
                  <i class="fa-solid fa-print" style="color: white;"></i>
                </span>
                Generate
              </button>
            </div>
          </div><!-- !Flex-search END -->
        </div><!-- !Employee search END -->
        <div class="slip-container" id="super-main-slip">
          <div class="information-slip-wrapper" id="header-content">
            <div class="information-slip-seperator">
              <p>For the Period: <span>{{$dateToday}}</span>
                <select name="month_select" id="month_select">
                  <option value="" selected id="select_whole">Full Month</option>
                  <option value="" id="select_first">XX-XX</option>
                  <option value="" id="select_second">XX-XX</option>
                </select>
              </p>
              <p>Fund Cluster: <span>Unavailable</span></p>
            </div>
            <div class="information-slip-seperator">
              <div class="flex-container">
                <p>No. of Days: <span>{{$workindays}} days</span></p>
                <p>N0. of Minutes: <span>{{$workinminutes}} minutes</span></p>
              </div>
            </div>
          </div>

          <input type="number" id="daily_rate" hidden>
          <input type="number" id="hour_rate" hidden>
          <input type="number" id="minute_rate" hidden>
          <input type="text" id="empId2" hidden>
          <input type="number" id="totalnet2" hidden>

          <div class="slip-main-container" id="table-content">
            <table class="slip-table">
              <thead>
                <tr class="slip-header">
                  <th>
                    <p id="empFullname">Name: <span>No Data</span></p>
                  </th>
                  <th>
                    <p id="empId">Employee ID: <span>No Data</span></p>
                  </th>
                  <th>
                    <p id="empDepartment">Department: <span>No Data</span></p>
                  </th>
                  <th>
                    <p id="empPosition">Designation/Position: <span>No Data</span></p>
                  </th>
                </tr>
              </thead>
              <thead class="second-slip-header">
                {{-- Header --}}
                <tr class="slip-tbody-header">
                  <th style="width:100%">Payments</th>
                  <th style="width:10%">Amount</th>
                  <th style="width:40%"><span class="other-pay">Other Deductions</span></th>
                  <th style="width:10%"><span class="other-pay">Amount</span></th>
                </tr>
                {{-- EndHEader --}}
              </thead>
              <tbody class="slip-tbody">
                <tr>
                  <td>
                    <ul>
                      {{-- <li>Amount Earn</li> --}}
                      <li>Salary :</li>
                    </ul>
                    <ul id="addlist">
                    </ul>
                    <p>
                      <b>Absent/Late</b>
                    </p>
                    <ul>
                      <li>Days :</li>
                      <li>Hours :</li>
                      <li>Minutes :</li>
                    </ul>
                  </td>
                  <td>
                    <ul>
                      {{-- <li><input type="number" name="empSalary" id="empSalary" class="text-right" value="0000">
                      </li> --}}
                      <li><input type="number" name="empEarned" id="empEarned" class="text-right additionals" value="0000" onchange="addChange()"></li>
                    </ul>
                    <ul id="addamount">
                    </ul>
                    <p class="text-right"><sub> </sub></p>
                    <ul class="m-t-5">
                      <li><input type="number" name="" id="input_days" class="text-right additionals" value="0" onchange="ded_days()"></li>
                      <li><input type="number" name="" id="input_hours" class="text-right additionals" value="0" onchange="ded_hours()"></li>
                      <li><input type="number" name="" id="input_minutes" class="text-right additionals" value="0" onchange="ded_minutes()"></li>
                    </ul>
                  </td>
                  <td>
                    <ul id="contrilist">
                      <li>Taxes :</li>
                    </ul>
                    <ul id="dedlist">
                    </ul>
                  </td>
                  <td>
                    <ul id="contriamount">
                      <input type="number" name="" id="" value="0000">
                    </ul>
                    <ul id="dedamount">
                    </ul>
                  </td>
                </tr>
                <tr>
                  <td>Gross after deduction of <br> Absent/Late (Php)</td>
                  <td><input type="number" name="totalAdd" id="totalAdd" value="0000" readonly></td>
                  <td><span class="other-pay2">Total Deductions :</span></td>
                  <td class="total-deduc"><input type="number" name="totalDed" id="totalDed" value="0000" class="total-deduc" readonly></td>
                </tr>
              </tbody><!-- !Tbody END -->
              <tfoot class="slip-tfoot">
                <tr>
                  <td colspan="4">
                    <p class="totalnet" id="totalnet">
                      Total Net Amount: No Data
                    </p>
                  </td>
                </tr>
              </tfoot><!-- !Table Foot END -->
            </table><!-- !Table END -->
          </div><!-- !Slip-main-container END-->
        </div><!-- !Slip-container END -->
      </div><!-- !Table change END -->

      <div class="table-change" id="table-all">
        <div class="action-container">
          <div class="action-wrapper">
            <div class="action-buttons">
              <button class="action fullscreen" id="fullscreen-button" onclick="openFullscreen()">
                <span class="action-icons" id="mark-as-unpaid">
                  <i class="fa-solid fa-expand fa-beat-fade" style="color: #0c3d92;"></i>
                </span>
                Expand
              </button>
              <button class="action" id="print-btn">
                <span class="action-icons">
                  <i class="fa-solid fa-print" style="color: #606671;"></i>
                </span>
                Print PDF/Printer
              </button>
              <button class="action" id="print-excel" onclick="exportToExcel()">
                <span class="action-icons">
                  <i class="fa-solid fa-table" style="color: #2d9211;"></i>
                </span>
                Spreadsheet
              </button>
            </div>
            <div class="filtering">
              <select id="filter">
                <option value="all">All</option>
                <option value="category1">Category 1</option>
                <option value="category2">Category 2</option>
                <option value="category3">Category 3</option>
              </select>
              <div class="action-search">
                <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="search" placeholder="Search" id="search-input">
              </div>
            </div>
          </div><!-- !Action Wrapper Close -->
        </div><!-- !Action Container Close -->

        <!-- TODO---------------------------------------------------------------------------------------------------------------- -->

        <div class="table-container-wrapper">
          <table id="table-main">
            <thead class="thead-main">
              <tr>
                <th class="table-selectAll"><input type="checkbox"></th>
                <th>Action</th>
                <th>Employee ID<button class="sort-btn" data-sortby="id"><i class="fa fa-sort"></i></th>
                <th>Name<button class="sort-btn" data-sortby="name"><i class="fa fa-sort"></i></th>
                <th>Department<button class="sort-btn" data-sortby="department"><i class="fa fa-sort"></i></th>
                <th>Monthly Salary</th>
                <th>Bonus/Allowance</th>
                <th>Total Deduction</th>
                <th>Net Amount Received</th>
              </tr>
            </thead>
            <tbody class="tbody-main" id="table-body">
              @foreach ($emp as $item)
              <tr class="primary-table-row">
                <td class="select-checkBox"><input type="checkbox" data-id="{{ $item->id }}"></td>
                <td class="table-action-icons">
                  <button>
                    <span class="action-icons"><i class="fa-solid fa-eye eye-main-pos" style="color: #157fd1;"></i></span>
                  </button>
                  <button><a href="/payroll-check/{{Crypt::encrypt($item->id)}}">
                      <span class="action-icons"><i class="fa-solid fa-table" style="color: #264f97;"></i></span>
                    </a>
                  </button>
                  <button><a href="/printPayslipTable/{{Crypt::encrypt($item->id)}}">
                      <span class="action-icons"><i class="fa-solid fa-file-export" style="color: #40916c;"></i></span>
                    </a>
                  </button>

                </td><!-- ?Action Buttons END -->
                <td><!-- ?ID -->
                  ID-00{{$item->id}}
                </td>
                <td><!-- ?Name -->
                  {{$item->first_name}} {{$item->middle_name}} {{$item->last_name}} {{$item->suffix}}
                </td>
                <td class="department-main-td"><!-- ?Department -->
                  <div class="department-main" style="background-color: #219ebc;
                  color: white;
                  border-radius: 0px 10px 10px 0px;
                  font-weight: 600;">
                    {{$item->department}}
                  </div>
                </td>
                <td><!-- ?Monthly Salary -->
                  <div style="font-weight: 600;">
                    <span class="department-main">&#8369;</span>{{$item->monthly_rate}}.00
                    <button class="edit-btn-monthly" title="Edit" style="border: none;
                    outline: none; background-color: transparent; margin-left: 5px;">
                      <i class="fas fa-edit"></i>
                    </button>
                  </div>
                </td>
                <td><!-- ?Bonus -->
                  @if (isset($TotalAdditional[$item->id]))
                  @foreach ($TotalAdditional as $key => $value)
                  @if ($key == $item->id)
                  <div style="font-weight: 600;">
                    <span>&#8369;</span>{{$value}}.00
                  </div>
                  @endif
                  @endforeach
                  @else
                  <i>No Data</i>
                  @endif
                </td>
                <td><!-- ?Deduction -->
                  @if (isset($TotalDeduction[$item->id]))
                  @foreach ($TotalDeduction as $key => $value)
                  @if ($key == $item->id)
                  <div style="border-radius: 10px;
                        color: red;
                        font-weight: 600;">
                    <span>&#8369;</span>{{$value}}.00
                  </div>
                  @endif
                  @endforeach
                  @else
                  <i>No Data</i>
                  @endif
                </td>
                <td><!-- ?Net Amount -->
                  @foreach ($TotalNet as $key => $value)
                  @if ($key == $item->id)
                  <div style="background-color: #b7e4c7;
                  border-radius: 10px;">
                    <span>&#8369;</span>{{$value}}.00
                  </div>
                  @endif
                  @endforeach
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div><!-- !Table-change END -->
    </div><!-- !All Table Container END -->
  </div><!-- !All main container END -->

  <!-- !Modal -->
  <div id="customModal2" class="modal2">
    <div class="modal-content2">
      <h2>Payslip Generated succesfully. </h2>
      <p>You want to print the Payslip?</p>
      <div class="button-cont">
        <button id="confirmButton2">Confirm</button>
        <button id="cancelButton2">Cancel</button>
      </div>
    </div>
  </div><!-- !Modal END -->
</body>
<script>
   function printPayrollSlip() {
    // Apply the CSS styles for printing
    var style = document.createElement('style');
    style.innerHTML = `
      @media print {
        body * {
          visibility: hidden;
        }
        .slip-container,
        .slip-container * {
          visibility: visible;
        }

        .slip-container {
          margin: 0;
          padding: 20px;
        }
        
        .slip-table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 20px;
        }
        
        .slip-table th,
        .slip-table td {
          padding: 8px;
          border: 1px solid #000;
        }
        
        .slip-header,
        .second-slip-header {
          background-color: #f2f2f2;
        }
        
        .slip-tfoot {
          font-weight: bold;
        }
        
        .totalnet {
          font-size: 16px;
        }
      }
    `;
    var printWindow = window.open('', '_blank');
    printWindow.document.open();
    printWindow.document.write('<html><head><title>Payroll Slip</title></head><body>');
    printWindow.document.write(style.outerHTML);
    printWindow.document.write(document.getElementById('super-main-slip').outerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    setTimeout(function() {
      printWindow.print();
      printWindow.close();
    }, 1000);
  }

  function generatePayrollSlip(){
    var emp_id = $('#empId2').val();
    var salary = $('#empEarned').val();
    var absents = $('#input_days').val();
    var late_hours = $('#input_hours').val();
    var late_minutes = $('#input_minutes').val();
    var total_deduction = $('#totalDed').val();
    var total_net = $('#totalnet2').val();
    
    $.ajax({
        url:"/payslip-generate",
        type:"GET",
        data:{
            'emp_id':emp_id,
            'salary':salary,
            'absents':absents,
            'late_hours':late_hours,
            'late_minutes':late_minutes,
            'total_deduction':total_deduction,
            'total_net':total_net,
            '_token':"{{ csrf_token() }}",
        },
        cache:false,
        success:function(data){
        var modal = document.getElementById('customModal2');
        modal.style.display = 'block';
        $('#genID').html('Generated');
        $('#genID').css('background-color', '#ff4d4d');

                // Confirm button click event
                $(document).on('click', '#confirmButton2', function() {
                    printPayrollSlip();
                });
                
                // Cancel button click event
                $(document).on('click', '#cancelButton2', function() {
                    var modal = document.getElementById('customModal2');
                    modal.style.display = 'none';
                });

        },
        error:function(data){
            console.log(data);
        }
    })
  
  }
</script>
<script src="js/payrollFunction/payroll-table.js"></script>
<script src="js/payrollFunction/payrollModal.js"></script>
<script src="js/payrollFunction/payroll-archived.js"></script>
<script src="js/payrollFunction/payroll.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js"></script>

</html>
@endsection