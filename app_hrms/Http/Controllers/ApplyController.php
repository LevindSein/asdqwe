<?php

namespace App\Http\Controllers;

use PDF;
use App\User;
use Exception;
use App\Models\Employee;
use App\Models\JobTitle;
use App\Exports\ApplyExport;
use App\Models\Department;
use App\Models\Applier;
use App\Models\Division;
use App\Models\GradeTitle;
use App\Models\HCApproval;
use Illuminate\Http\Request;
use App\Models\CompanyRegion;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class ApplyController extends Controller
{
   public function cetak($id)
    {
		$query = "select a.*, b.job_id, c.job_title_name from applier as a, jobs as b, job_titles as c where a.id_job = b.id and b.job_id = c.id and a.id=$id";
		
        $pengajuans = DB::select($query);

        return view('apply.cetak', [
            'pengajuans' => $pengajuans,
        ]);	
	}
    
    
    public function index(Request $request)
    {
        $employee_id = auth()->user()->employee->id;
		
		
        $query = "select a.*, b.job_id, c.job_title_name, d.region_city from applier as a, jobs as b, job_titles as c, company_regions as d where a.id_job = b.id and b.job_id = c.id and b.region_id = d.id";
		
	    if ($request->has('status_data')) {
            $query .= " and a.status_data = '{$request->status_data}'";
        }
		
		if ($request->has('id_job')) {
            $query .= " and a.id_job = '{$request->id_job}'";
        }
		
		$query .= " order by a.insert_date desc limit 1500";
		
		$sp = DB::table('status_pelamar')->get();
		$sr = DB::table('vw_jobin')->get();
		
		$job = DB::table('v_job')->get();
		
        $pengajuans = DB::select($query);

        return view('apply.index', [
            'pengajuans' => $pengajuans,
            'employee_id' => $employee_id,
			'job' => $job,
			'sp' => $sp,
			'sr' => $sr
        ]);
    }
/*	
	public function karyawan(Request $request)
    {
        DB::beginTransaction();
        try {
			
			$requestor = auth()->user()->employee;
            $id = $request->id;
			
			$now = now();
			
            //Update table utama
            DB::table('applier')->where('id', $id)->update([              
				'input_status' => 1,
            ]);
			
			
			DB::table('f_job_newer_perihal')->insert([
                'registration_number' => $request->registration_number,
                'date_of_work' => $request->date_of_work,
                'fullname' => $request->fullname,
                'grade' => $request->grade,
                'level' => $request->level,
                'status' => $request->status,
				'updated_by' => \Auth::user()->employee->id,
                'division_id' => $request->division_id,
                'department_id' => $request->department_id,
                'grade_title_id' => $request->grade_title_id,
                'level_title_id' => $request->level_title_id,
                'job_title_id' => $request->job_title_id,
                'company_region_id' => $request->company_region_id,
            ]);
			
			DB::table('f_job_newer_perihal')->insert([
                'place_of_birth',
                'date_of_birth',
                'ID_number',
                'ID_number_expiration',
                'mother_name',
                'marital_status',
                'sex',
                'religion',
                'phone_number',
                'npwp',
                'last_education',
                'education_focus',
                'address',
            ]);
			
			DB::table('f_job_newer_perihal')->insert([
                'basic_salary' => preg_replace('/[^0-9]/', '', $request->basic_salary),
                'payroll_type' => $request->payroll_type,
                'meal_allowance' => $request->meal_allowance,
                'salary_post' => $request->salary_post,
                'bank' => $request->bank,
                'bank_account_number' => $request->bank_account_number
            ]);
			
			DB::table('f_job_newer_perihal')->insert([
                 'email' => $request->email,
                 'password' => bcrypt($request->password)
            ]);
			
			$user->assignRole($request->role);
            $users = User::all();
          
         
			$approval_by = $requestor->superior->user;

            $approval_by->notify(new NotifNewFPK([
                'email' => $approval_by->email,
                'RequestorName' => $requestor->fullname,
                'ReqNo' => $ReqId,
            ]));


            DB::commit();
            return redirect(route('pengajuan.fpk'))->with('alert', [
                'type' => 'success',
                'msg' => 'Berhasil Edit FPK, menunggu approval',
            ]);
			
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with('alert', [
                'type' => 'danger',
                'msg' => $ex->getMessage()
            ]);
        }
    }
*/
	
	public function approve(Request $request, Applier $apply)
    {
        DB::beginTransaction();
        try {
	            $apply->update([
	                'status_data' => $request->status,
	            ]);
			
            DB::commit();
            session()->flash('alert', [
                'type' => 'success',
                'msg' => 'Berhasil Update Status Pelamar'
            ]);
        } catch (Exception $ex) {
            DB::rollBack();
            session()->flash('alert', [
                'type' => 'danger',
                'msg' => $ex->getMessage()
            ]);
        }

        return response()->json();
    }

    public function detail($id)
    {
		$apply = Applier::find($id);
        
        if ($apply->read == 0)
        {
            $apply->read = 1;
        }
        if ($apply->read_date == null)
        {
            $apply->read_date = now();
        }
        
        $apply->save();
		
		$meal_allowanceOptions = [
                "Tidak",
                "Ya"
            ];
			
		$payroll_typeOptions = [
                "Bulan",
                "Hari"
            ];
			
		$salary_postOptions = [
                "direksi",
                "hcm",
                "pontianak"
            ];
		$bankOptions = 	[
                "BCA",
                "Mandiri"
            ];
			
		$marital_statusOptions = [
                ['view' => 'K', 'value' => 'K'],
                ['view' => 'K.0', 'value' => 'K.0'],
                ['view' => 'K.1', 'value' => 'K.1'],
                ['view' => 'K.2', 'value' => 'K.2'],
                ['view' => 'K.3', 'value' => 'K.3'],
                ['view' => 'T.K', 'value' => 'T.K']
            ];
			
		$grade_titleOptions = GradeTitle::all();
			
	    $gradeOptions = ['I', 'II', 'III', 'IV', 'V', 'VI'];
		
		$statusOptions = ['Kontrak', 'Probation', 'Tetap'];
			
		$roles  = Role::where('name', '!=', 'Super Admin')->get();
		
        $pengajuan = DB::select('select a.*, b.job_id, b.dept_id, b.level_id, b.region_id, c.job_title_name, d.division_id
								from applier as a, jobs as b, job_titles as c, departments as d where a.id_job = b.id and b.job_id = c.id and b.dept_id = d.id and a.id = ?',[$id]);
        $pengajuan = $pengajuan[0];


        return view('apply.detail', [
            'pengajuan' => $pengajuan,           
			'meal_allowanceOptions' => $meal_allowanceOptions,
			'payroll_typeOptions' => $payroll_typeOptions,
			'salary_postOptions' => $salary_postOptions,
			'bankOptions' => $bankOptions,
			'roles' => $roles,
			'gradeOptions' => $gradeOptions,
			'statusOptions' => $statusOptions,
			'marital_statusOptions' => $marital_statusOptions,
			'grade_titleOptions' => $grade_titleOptions,
        ]);
    }

    public function close(Request $request)
    {
        $request->validate([
            'FilledDate' => 'required|date'
        ]);

        DB::table('t_apply_request')->where('ReqId', $request->ReqId)->update([
            'FilledDate' => $request->FilledDate,
            'ReqSts' => 1
        ]);

        return redirect(route('pengajuan.ptk'))->with('alert', [
            'type' => 'success',
            'msg' => 'Job berhasil ditutup'
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'bail|nullable|date',
            'end_date' => 'bail|nullable|date',
            'job_id' => 'required',
			'status_data' => 'required',
        ]);

        $params = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'job_id' => $request->job_id,
			'status_data' => $request->status_data
        ];

        return Excel::download(new ApplyExport($params), 'Apply-export-'.date('Y-m-d').'.xlsx');
    }
	
	
    public function remove(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric'
        ]);

        $id = $request->id;

        DB::beginTransaction();
        try {
            DB::table('applier')->where('id', $id)->delete();

            DB::commit();
            return redirect()->back()->with('alert', [
                'type' => 'success',
                'msg' => 'Berhasil Hapus Pelamar',
            ]);
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->back()->with('alert', [
                'type' => 'danger',
                'msg' => $ex->getMessage()
            ]);
        }
    }
}
