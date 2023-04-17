<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
     {
$employees = Employees::latest()->get();
         if ($request->ajax()) {

             return DataTables::of($employees)
                     ->addIndexColumn()
                     ->addColumn('action', function($row){

                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editPost">Edit</a>';

                            $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deletePost">Delete</a>';

                             return $btn;
                     })
                     ->editColumn('department',function($row){
                       if($row->department==1)
                       return 'Sales';
                       if($row->department==2)
                       return 'Marketing';
                       if($row->department==3)
                       return 'IT';
                     })
                     ->editColumn('status',function($row){
                       if($row->status==1)
                       return 'Active';
                       else
                       return 'Inactive';
                     })
                     ->rawColumns(['action'])
                     ->make(true);
         }

         return view('employees',compact('employees'));
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
         Employees::updateOrCreate(['id' => $request->id],
                 ['name' => $request->name, 'email' => $request->email, 'mobile_no' => $request->mobile_no, 'department' => $request->department, 'status' => $request->status]);

         return response()->json(['success'=>'Employee saved successfully.']);
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
         $post = Employees::find($id);
         return response()->json($post);
     }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employees $employees)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
     public function destroy($id)
     {
         Employees::find($id)->delete();

         return response()->json(['success'=>'Employee deleted successfully.']);
     }
}
