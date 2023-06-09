<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Employees;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Mail;
use App\Mail\AdminMail;
use App\Mail\AssigneeMail;
use DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
     {

$employees = Employees::latest()->where('status',1)->get();
         if ($request->ajax()) {
$task = Task::latest()->get();
if(isset($request->status)){
  $task = $task->where('status',$request->status);
}
if($request->assignee){
  $task = $task->where('assignee',$request->assignee);
}
             return DataTables::of($task)
                     ->addIndexColumn()
                     ->addColumn('action', function($row){

                            $start_btn = '';
                            $five_min_before = date('Y-m-d H:i:s', strtotime('5 minutes ago'));
                            $btn = ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deletePost">Delete</a>';
                            if($row->status==1)
                            $start_btn = ' <a href="javascript:void(0)"  onclick="update_status('.$row->id.',2);" class="btn btn-primary btn-sm">Start</a>';
                            if($row->status==2 && $row->updated_at <= $five_min_before)
                            $start_btn = ' <a href="javascript:void(0)"  onclick="update_status('.$row->id.',3);" class="btn btn-success btn-sm">Done</a>';

                            return $btn.$start_btn;
                     })
                     ->editColumn('assignee',function($row) use($employees){
                       if($row->status != 2 && $row->status != 3){
                         $html = '<select name="assignee" id="assignee" class="form-control" onChange="assign_employee('.$row->id.',this.value);">
                           <option value="">-Assign-</option>';
                           foreach ($employees as $employee) {
                             if($employee->id == $row->assignee)
                             $html .= '<option value="'.$employee->id.'" selected="true">'.$employee->name.'</option>';
                             else
                             $html .= '<option value="'.$employee->id.'">'.$employee->name.'</option>';
                           }
                           $html .='</select>';
                         }else{
                           $employees = Employees::latest()->where('id',$row->assignee)->first();
                           return $employees->name;
                         }
                       return $html;
                     })
                     ->editColumn('status',function($row){
                       if($row->status==0)
                       return 'Unassigned';
                       else if($row->status==1)
                       return 'Assigned';
                       else if($row->status==2)
                       return 'In Progress';
                       else if($row->status==3)
                       return 'Done';
                     })
                     ->rawColumns(['action','assignee'])
                     ->make(true);
         }

         return view('task',compact('employees'));
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
     {

         Task::updateOrCreate(['id' => $request->id],
                 ['title' => $request->title, 'description' => $request->description, 'assignee' => null, 'status' => 0]);

         $admin_mail = [
             'title' => 'Mail from Otaskit.com',
             'body' => 'New task "'.$request->title.'" has been created.'
         ];

         Mail::to('admin@otaskit.com')->send(new AdminMail($admin_mail));

         return response()->json(['success'=>'Task saved successfully.']);
     }

    /**
     * Display the specified resource.
     */
    public function show(Employees $employees)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
     public function edit($id)
     {
         $post = Task::find($id);
         return response()->json($post);
     }

     public function update(Request $request,$id)
     {
       if($request->btn==1){
         Task::where('id',$id)->update(
                 [ 'assignee' => $request->emp_id, 'status' => 1]
               );
               $assignee = Employees::find($request->emp_id);
               $admin_mail = [
                   'title' => 'Mail from Otaskit.com',
                   'body' => 'One new task is assigned to you.'
               ];

               Mail::to($assignee->email)->send(new AdminMail($admin_mail));
       }elseif ($request->btn==2) {
         Task::where('id',$id)->update(
                 [ 'status' => $request->status]
               );
       }


         return response()->json(['success'=>'Task updated successfully.']);
     }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy($id)
     {
         Task::find($id)->delete();

         return response()->json(['success'=>'Task deleted successfully.']);
     }

     public function generateCsvFile() {

      $today = date('dmY_his');
     $tasks = DB::table('tasks')->leftjoin('employees','tasks.assignee','employees.id')
     ->where('tasks.status','!=',3)->orderBy('employees.name','ASC')->orderBy('tasks.status','ASC')->select('title','employees.name','tasks.status')->get();
     $data[] = ['sl_no', 'title', 'assignee', 'status'];
     $sl_no = 1;
     $status=[
       0=>'Unassigned',
       1=>'Assigned',
       2=>'In Progress',
     ];

     foreach ($tasks as $task) {

       $data[] =
           [
             $sl_no,
             $task->title,
             $task->name,
             $status[$task->status]
       ];
       $sl_no++;
     }

     $file_path = '/storage/app/report_' . $today . '.csv';
     $pathToFile=rtrim(public_path(),'public').$file_path;
     $file = fopen($pathToFile, 'w+');

     foreach ($data as $fields) {
         fputcsv($file, $fields);
     }
     fclose($file);

     $mailData = [
         'title' => 'Mail from Otaskit.com',
         'body' => 'Daily Report.'
     ];
     Mail::send('emails.mail', compact('mailData'), function($message)use($pathToFile) {

         $message->to('admin@otaskit.com')
                  ->subject('Otaskit - Daily report')
                  ->attach($pathToFile);

     });

 }
}
