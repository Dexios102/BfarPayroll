<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Allowance;
use App\Models\Deduction;
use App\Models\Department;
use App\Models\Employee;
use App\Models\FixedDeduction;
use App\Models\Payroll;
use App\Models\PayrollAdditional;
use App\Models\PayrollDeduction;
use App\Models\PayrollFixedDeduction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\GeneratedPayslip;


class PayrollController extends Controller
{
    public function list(){
        
        $emp = Employee::where('isActive','1')->get();
        $dept = Department::all();

        $fixed_deduc = FixedDeduction::all();
        $ded = PayrollDeduction::with(['deduction_info','employee_info'])
            ->withoutTrashed()->get();
       
        $monthlyDeductions =PayrollDeduction::selectRaw('employee_id, sum(monthly_deduction) as total_deduction')
        ->where('status','active')
        ->groupBy('employee_id')->withoutTrashed()
        ->get();
        $monthlyDeductions2 =PayrollFixedDeduction::selectRaw('employee_id, sum(monthly_deduction) as total_deduction')
        ->where('status','active')
        ->groupBy('employee_id')->withoutTrashed()
        ->get();

        $TotalDeduction = [];
        foreach($monthlyDeductions as $d1){
           $TotalDeduction[$d1->employee_id] = $d1->total_deduction;
        }
        foreach($monthlyDeductions2 as $d2){
            if (isset($TotalDeduction[$d2->employee_id])) {
                $TotalDeduction[$d2->employee_id] += $d2->total_deduction;
            } else {
                // Otherwise, add a new key-value pair
                $TotalDeduction[$d2->employee_id] = $d2->total_deduction;
            }
          
        }

        //Additional
        $payrollAdd = PayrollAdditional::where('status','active')->withoutTrashed()->get();
        $TotalAdditional = [];
        foreach($payrollAdd as $d1){
          if (isset($TotalAdditional[$d1->employee_id])) {
              $TotalAdditional[$d1->employee_id] += $d1->amount;
          } else {
              // Otherwise, add a new key-value pair
              $TotalAdditional[$d1->employee_id] = $d1->amount;
          }
        }

        
        
        //TotalNet
        $TotalNet = []; 
        //MOnthlyRate
        foreach($emp as $d3){
          if (isset($TotalNet[$d3->id])) {
              $TotalNet[$d3->id] += $d3->monthly_rate;
          } else {
              // Otherwise, add a new key-value pair
              $TotalNet[$d3->id] = $d3->monthly_rate;
          }
        } 
        //Additional
        foreach($payrollAdd as $d1){
          if (isset($TotalNet[$d1->employee_id])) {
              $TotalNet[$d1->employee_id] += $d1->amount;
          } else {
              // Otherwise, add a new key-value pair
              $TotalNet[$d1->employee_id] = $d1->amount;
          }
        }

        foreach($TotalNet as $key1 => $value1){
          foreach($TotalDeduction as $key2 => $value2){
              if($key1 == $key2){
                  $TotalNet[$key1] = $value1 - $value2;
              }
          }
          
        
      }

    // WorkingDays and datenow
    $now = Carbon::now();
    $daysInMonth = $now->daysInMonth;
    $workingDays = 0;
    for ($i = 1; $i <= $daysInMonth; $i++) {
        $date = Carbon::createFromDate($now->year, $now->month, $i);
        if (!$date->isWeekend()) {
            $workingDays++;
        }
    }
    $workinMinutes = (60 * 8) * $workingDays;
    $monthtoday = $now->format('F Y');




        return view('hr.payroll-list',[
            'dept' => $dept,
            'emp' => $emp,
            'fixed_deduc' =>$fixed_deduc,
            'ded' => $ded,
            'nameko' => 8979,
            'monthlyDeductions' => $monthlyDeductions, 
            'monthlyDeductions2' => $monthlyDeductions2,
            'TotalDeduction' => $TotalDeduction,  
            'TotalAdditional' => $TotalAdditional,              
            'TotalNet' => $TotalNet,
            'workindays' => $workingDays, 
            'workinminutes' => $workinMinutes,
            'dateToday'=> $monthtoday,
        ]);
            
    }

    public function deduc($id){
        $employee = Employee::find($id);

        $deduction2 = DB::table('payroll_deduction')->where('employee_id',$id)->get();
       
        $empl_array = array(
            'emp_id' => $employee->id,
            'f_name'  => $employee->first_name, 
            'l_name' =>  $employee->last_name,
            'deduc2' => array($deduction2)
        );
            
        
        return $empl_array ;
    }

    public function monthlyratemodal($id){
        $emp = Employee::find($id);

        $emp_array = array(
            'id' =>  $emp->id,
            'lname' => $emp->last_name,
            'mrate' => $emp->monthly_rate,
        );

        return $emp_array;
    }

    public function mrateupdate(Request $request){
        $id = $request->input('id2');
        $emp = Employee::find($id);
        $emp->monthly_rate = $request->input('mrate2');
        $emp->save();

        return back()->with('success','Updated Succesfully');
    }

    
    public function checkdeduction($id){
        $decrypt = Crypt::decrypt($id);
        $emp = Employee::find($decrypt);
        $dept = Department::all();
        $ded = Deduction::where('type','!=','fixed')->get();
        $fixed_deduc = FixedDeduction::all();
        $p_dec = PayrollDeduction::with(['deduction_info','employee_info'])
        ->where('status','!=','fixed')->where('employee_id',$decrypt)->withoutTrashed()->get();

        $f_dec = PayrollFixedDeduction::with(['deduction_info','employee_info'])->where('employee_id',$decrypt)->withoutTrashed()->get();

        $f_count = $f_dec->count();
        return view('hr.payroll-checkdeduction', compact('dept','emp','fixed_deduc','ded','p_dec','f_dec','f_count'));
    }

    public function checkPayroll($id){
        $decryptID= Crypt::decrypt($id);
        $emp = Employee::find($decryptID);
        $fixed_deduc = PayrollFixedDeduction::with(['deduction_info'])->where('employee_id',$decryptID)
        ->withoutTrashed()->get();

        $other_ded = PayrollDeduction::where('employee_id',$decryptID)
            ->where('status','active')
            ->with(['deduction_info','employee_info'])->withoutTrashed()->get();
        $total_ded = PayrollDeduction::where('employee_id',$decryptID)
            ->where('status','active')
            ->with(['deduction_info','employee_info'])->withoutTrashed()->sum('monthly_deduction');

            $dept = Department::all();
            $ded = Deduction::where('type','!=','fixed')->withoutTrashed()->get();
            $fixed_deduc2 = FixedDeduction::all();
            $p_dec = PayrollDeduction::with(['deduction_info','employee_info'])
            ->where('status','!=','fixed')->where('employee_id',$decryptID)->withoutTrashed()->get();
    
            $f_dec = PayrollFixedDeduction::with(['deduction_info','employee_info'])->where('employee_id',$decryptID)
            ->where('status','active')->withoutTrashed()->get();
            $f_ded_all = PayrollFixedDeduction::with(['deduction_info','employee_info'])->where('employee_id',$decryptID)
            ->withoutTrashed()->get();
            $f_count = $f_dec->count();

            $allowance = Allowance::withoutTrashed()->get();

            $payrollAdd_all = PayrollAdditional::where('employee_id',$decryptID)->withoutTrashed()->get();
            

            $total_f = $f_dec->sum('monthly_deduction');

        $payrollAdd = PayrollAdditional::where('employee_id',$decryptID)->where('status','active')->withoutTrashed()->get();
        $total_add = $payrollAdd->sum('amount');
        $add_count = $payrollAdd->count();
        $total_net = ($emp->monthly_rate +  $total_add) - ($total_ded + $total_f) ;
        $half_net = $total_net / 2 ;


        return view('hr.payroll-check', compact('emp','fixed_deduc','other_ded', 'total_ded','dept',
    'ded','fixed_deduc2','p_dec','f_dec','f_count','allowance','payrollAdd','total_add','add_count','total_f',
    'total_net','half_net','f_ded_all','payrollAdd_all'));
    }
    

    public function deductionSave(Request $request){

    $id = $request->input('employee_id');
    $emp_rate = Employee::find($id)->first('monthly_rate');

        

        $ded2 = new PayrollDeduction();
        $ded2->employee_id = $request->input('employee_id');
        $ded2->deduction_id = $request->input('deduction');
        

        $ded2->total_amount = $request->input('total_amount');
        $ded2->balance = $request->input('total_amount');
        
        if(!empty($request->input('percentage'))){
            $ded2->interest = $request->input('percentage');
            $m_deduction = ($request->input('percentage')/100) * $request->input('total_amount');
            $ded2->monthly_deduction = (string)$m_deduction;
        }
        else{
            $ded2->interest = 0;
            $ded2->monthly_deduction = 0;
        }
        
        $ded2->status = "active";
        $ded2->save();

        return back()->with('success','New Deduction Saved!');

    }

    public function additionalSave(Request $request){

        $emp_id = $request->input('employee_id');
        $emp_rate = Employee::find($emp_id)->first('monthly_rate');
    
            
    
            $ded2 = new PayrollAdditional();
            $ded2->employee_id = $request->input('employee_id');
            $ded2->allowance_id = $request->input('deduction');
            
    
            $ded2->amount = $request->input('total_amount');
            $ded2->due = $request->input('due');
            $ded2->status = "active";
            $ded2->save();
    
            return back()->with('success','Allowance Saved!');
    
        }

    public function deductionUpdate(Request $request, $id){
       
        $ded = PayrollDeduction::find($id);
        $inputname = "f_deduction".$id;
        $ded->monthly_deduction = $request->input($inputname);
        $ded->save();
        return back()->with('success','Updated!');
    }
    public function deductionUpdate2(Request $request, $id){
       
        $ded =PayrollFixedDeduction::find($id);
       

        $inputname = "f_deduction".$id;
        $ded->monthly_deduction = $request->input($inputname);
        $ded->interest = ($request->input($inputname) / $ded->employee_info->monthly_rate) * 100;
        $ded->save();
        return back()->with('success','Updated!');
    }

    public function deductionDelete(Request $request, $id){
       
        $ded = PayrollDeduction::find($id);
        $ded->delete();
        // $success = $request->session()->put('cookies', 'success'); 
        return response()->json(['Successfully Deleted!']);
    }

    public function deductionDelete2(Request $request, $id){
       
        $ded = PayrollFixedDeduction::find($id);
        $ded->delete();
        
        // $success = $request->session()->put('cookies', 'success'); 
        
        return response()->json(['Successfully Deleted']);

    }

    public function fixedDeductionSave($id){
        $fixed_deduction = FixedDeduction::withoutTrashed()->get();
        $emp = Employee::where('id',$id)->first();
          

        foreach($fixed_deduction as $item){
            $ded2 = new PayrollFixedDeduction();
            $ded2->employee_id = $id;
            $ded2->deduction_id = $item->id;
            $ded2->total_amount = "0";
            $ded2->balance = "0";
            $ded2->interest = $item->percentage;
            if($item->percentage != 0 or $item->percentage != null ){
                $ded2->monthly_deduction = ($item->percentage/100) * $emp->monthly_rate;
            }
            else{
                $ded2->monthly_deduction = 0;
            }
         
            $ded2->status = "active";
            $ded2->save();
        }
        return back()->with('success','Success!');

    }

    public function contributionStatus($id){
        $ded = PayrollFixedDeduction::find($id);
        if($ded->status == 'active'){
            $ded->status = 'inactive';
        }elseif($ded->status == 'inactive'){
            $ded->status = 'active';
        }else{
            $ded->status = 'active';
        }
        $ded->save();
        return back()->with('success','Success!');

    }

    public function contributionStatus2($id){
        $ded = PayrollDeduction::find($id);
        if($ded->status == 'active'){
            $ded->status = 'inactive';
        }elseif($ded->status == 'inactive'){
            $ded->status = 'active';
        }else{
            $ded->status = 'active';
        }
        $ded->save();
        return back()->with('success','Success!');

    }

    public function additionalStatus($id){
        $ded = PayrollAdditional::find($id);
        if($ded->status == 'active'){
            $ded->status = 'inactive';
        }elseif($ded->status == 'inactive'){
            $ded->status = 'active';
        }else{
            $ded->status = 'active';
        }
        $ded->save();
        return back()->with('success','Success!');

    }

    public function payslipCheck(Request $request){

       $input = $request->input_data;
        
       $emp = Employee::where('fullname', 'like', '%' . $input . '%')
            ->orWhere('employee_id',  $input )
            ->first();
        $emp_arr = array(
            'id' => $emp->employee_id,
            'fullname' => $emp->fullname,
            'dept' => $emp->department,
            'pos' => $emp->position,
            'salary' => $emp->monthly_rate,
            'id2' => $emp->id,
        );

        $generated = GeneratedPayslip::where('employee_id', $emp->id)
        ->whereMonth('payroll_date', Carbon::now())
        ->count();

        // ALL DEDUCTIONS
        // OTHER DEDUCTIONS
        $deductions = PayrollDeduction::whereHas('deduction_info', function ($query) { $query->where('type','contribution');
        })->where('employee_id',$emp->id)->get();
        $sumDed = 0;
        $deductionHTML = '';
        $dedAmount = '';
        foreach($deductions as $items){
            // Deductionlist
            $deductionHTML .= '<li>'.$items->deduction_info->name.'</li>';
            // AmountList
            $dedAmount .= '<li><input type="number" name="" id="deductions" class="text-right deductions" value="'.$items->monthly_deduction.'" onchange="dedChange()"></li>';
            //sum
            $sumDed += $items->monthly_deduction;
        }
        // CONTRIBUTIONS
        $contributions = PayrollFixedDeduction::where('employee_id', $emp->id)
        ->where('status','active')->get();
        $contriHTML = '';
        $contriAmount = '';
        $sumContri = 0;
        foreach($contributions as $items){
            // Deductionlist
            $contriHTML .= '<li>'.$items->deduction_info->name.'</li>';
            // AmountList
            $contriAmount .= '<li><input type="number" name="" id="deductions" class="text-right deductions" value="'.$items->monthly_deduction.'" onchange="dedChange()"></li>';
            //sum
            $sumContri += $items->monthly_deduction;
        }

        $totalDed = $sumDed + $sumContri;


        // ALL Additionals
        $additionals = PayrollAdditional::where('employee_id', $emp->id)
        ->where('status','active')->get();

        $addHTML = '';
        $addAmount = '';
        $addTotal = 0;
        foreach($additionals as $items){
             // Deductionlist
             $addHTML .= '<li>'.$items->allowance_info->name.'</li>';
             // AmountList
             $addAmount .= '<li><input type="number" name="" id="additionals" class="text-right additionals" value="'.$items->amount.'" onchange="addChange()"></li>';
             //sum
             $addTotal += $items->amount;
        }


        // WorkingDays  and datenow
        $now = Carbon::now();
        $daysInMonth = $now->daysInMonth;

        //WorikingDays in whole month
        $workingDays = 0;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::createFromDate($now->year, $now->month, $i);
            if (!$date->isWeekend()) {
                $workingDays++;
            }
        }

        //WorikingDays in half
        //30Days
        if($daysInMonth == 30){
            $first_half = 0;
            $second_half = 0;
                for ($i = 1; $i <= 15; $i++) {
                    $date = Carbon::createFromDate($now->year, $now->month, $i);
                    if (!$date->isWeekend()) {
                        $first_half++;
                    }
                }
                for ($i = 16; $i <= 30; $i++) {
                    $date = Carbon::createFromDate($now->year, $now->month, $i);
                    if (!$date->isWeekend()) {
                        $second_half++;
                    }
                }
                $first_range = "1-15";
                $second_range = "16-30";

                $monthHTML = '<option value="'.$daysInMonth.'" selected id="select_whole">Whole month</option>';
                $monthHTML .= '<option value="'.$first_half.'"  id="select_whole">'.$first_range.'</option>';
                $monthHTML .= '<option value="'.$second_half.'"  id="select_whole">'.$second_range.'</option>';
            }
        //31Days
       elseif($daysInMonth == 31){
            $first_half = 0;
            $second_half = 0;
                for ($i = 1; $i <= 15; $i++) {
                    $date = Carbon::createFromDate($now->year, $now->month, $i);
                    if (!$date->isWeekend()) {
                        $first_half++;
                       
                    }
                }
                for ($i = 16; $i <= 31; $i++) {
                    $date = Carbon::createFromDate($now->year, $now->month, $i);
                    if (!$date->isWeekend()) {
                        $second_half++;
                        
                    }
                }
                $first_range = "1-15";
                $second_range = "16-31";

                $monthHTML = '<option value="'.$daysInMonth.'" selected id="select_whole">Whole month</option>';
                $monthHTML .= '<option value="'.$first_half.'"  id="select_whole">'.$first_range.'</option>';
                $monthHTML .= '<option value="'.$second_half.'"  id="select_whole">'.$second_range.'</option>';
        }
        //28Days
       elseif($daysInMonth == 28){
        $first_half = 0;
        $second_half = 0;
            for ($i = 1; $i <= 15; $i++) {
                $date = Carbon::createFromDate($now->year, $now->month, $i);
                if (!$date->isWeekend()) {
                    $first_half++;
                }
            }
            for ($i = 16; $i <= 28; $i++) {
                $date = Carbon::createFromDate($now->year, $now->month, $i);
                if (!$date->isWeekend()) {
                    $second_half++;
                }
            }
            $first_range = "1-15";
            $second_range = "16-28";

            $monthHTML = '<option value="'.$daysInMonth.'" selected id="select_whole">Whole month</option>';
            $monthHTML .= '<option value="'.$first_half.'"  id="select_whole">'.$first_range.'</option>';
            $monthHTML .= '<option value="'.$second_half.'"  id="select_whole">'.$second_range.'</option>';

        }
        else{
            $first_half = 0;
            $second_half = 0;
                for ($i = 1; $i <= 15; $i++) {
                    $date = Carbon::createFromDate($now->year, $now->month, $i);
                    if (!$date->isWeekend()) {
                        $first_half++;
                    }
                }
                for ($i = 16; $i <= 27; $i++) {
                    $date = Carbon::createFromDate($now->year, $now->month, $i);
                    if (!$date->isWeekend()) {
                        $second_half++;
                    }
                }
                $first_range = "1-15";
                $second_range = "16-27";

                $monthHTML = '<option value="'.$daysInMonth.'" selected id="select_whole">Whole month</option>';
                $monthHTML .= '<option value="'.$first_half.'"  id="select_whole">'.$first_range.'</option>';
                $monthHTML .= '<option value="'.$second_half.'"  id="select_whole">'.$second_range.'</option>';
            }


       




        $workinMinutes = (60 * 8) * $workingDays;
        $monthtoday = $now->format('F Y');

        $amountEarned = $workingDays * $emp->daily_rate;
        $daily_rate = $emp->daily_rate;
        $hour_rate = $emp->daily_rate / 8;
        $minute_rate = $hour_rate / 60 ;
        
        
        $totalEarned = $amountEarned + $addTotal;

         return response()->json([
                        'emp_array'=>$emp_arr,
                        'ded_array'=>$deductions,
                        'dedHTML' => $deductionHTML, 
                        'amountHTML' =>$dedAmount,
                        'dedTotal' =>$sumDed,
                        'addHTML'=> $addHTML,
                        'addAmount'=> $addAmount,
                        'addTotal' => $addTotal,
                        'contriHTML'=> $contriHTML,
                        'contriAmount'=> $contriAmount,
                        'totalDed' => $totalDed,
                        'amountEarned' => $amountEarned,
                        'totalEarned' => $totalEarned,
                        'first_half' => $first_half,
                        'second_half' => $second_half,
                        'first_range' => $first_range,
                        'second_range' => $second_range,
                        'monthHTML' => $monthHTML,
                        'daily_rate' => $daily_rate,
                        'hour_rate' => $hour_rate,
                        'minute_rate' => $minute_rate,
                        'generated' =>$generated,

                    ]);
    }

    public function payslipGenerate(Request $request){
        $emp_id = $request->emp_id;
        $emp = Employee::find($emp_id);

        $emp_array = array(
            'emp_id' =>  $emp->id,
            'emp_fullname' => $emp->fullname,
            'emp_department' => $emp->department,
            'absents' => $request->absents,
            'late_hours' => $request->late_hours,
            'late_minutes' => $request->late_minutes,
            'total_deduction' => $request->total_deduction,
            'total_net' => $request->total_net,
        );

        $generate = new GeneratedPayslip();
        $generate->employee_id = $emp->id;
        $generate->employee_name = $emp->fullname;
        $generate->department = $emp->department;
        $generate->payroll_date = Carbon::now();
        $generate->absents = $request->absents;
        $generate->	late_hours = $request->	late_hours;
        $generate->late_minutes = $request->late_minutes;
        $generate->salary = $request->salary;
        
        $generate->total_deduction = $request->total_deduction;
        $generate->total_net = $request->total_net;
     

        $generate->save();





        return response()->json([
            'emp_array' => $emp_array,
        ]);
    }
    
}
