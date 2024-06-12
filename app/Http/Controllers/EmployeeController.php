<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function fetchAll()
    {
        $employee = Employee::all();
        $output = '';
        if ($employee->count() > 0) {
            $output .= '<table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>No</th>
                <th>ID</th>
                <th>Photos</th>
                <th>Items</th>
                <th>Desriptions</th>
                <th>Stocks</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>';
            foreach ($employee as $rs) {
                $output .= '<tr>
                <td>' . $rs-> id .'</td>
                <td>' . $rs->id . '</td>
                <td><img src="storage/images/' . $rs->avatar . '" width="100" class="img-thumbnail rounded"></td>
                <td>' . $rs->first_name . ' </td>
                <td>' . $rs->email . '</td>
                <td>' . $rs->last_name . ' </td>
                <td>
                  <a href="#" id="' . $rs->id . '" class="text-warning mx-1 editIcon" data-bs-toggle="modal" data-bs-target="#editEmployeeModal"><i class="bi-pencil-square h4"></i></a>
                  <a href="#" id="' . $rs->id . '" class="text-danger mx-1 deleteIcon"><i class="bi-trash h4"></i></a>
                </td>
              </tr>';
            }
            $output .= '</tbody></table>';
            echo $output;
        } else {
            echo '<h1 class="text-center text-secondary my-5">No record in the database!</h1>';
        }
    }

    // insert a new employee ajax request
    public function store(Request $request)
    {
        $file = $request->file('avatar');
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/images', $fileName); //php artisan storage:link

        $empData = ['first_name' => $request->fname, 'last_name' => $request->lname, 'email' => $request->email, 'avatar' => $fileName];
        Employee::create($empData);
        return response()->json([
            'status' => 200,
        ]);
    }

    // edit an employee ajax request
    public function edit(Request $request)
    {
        $id = $request->id;
        $emp = Employee::find($id);
        return response()->json($emp);
    }

    // update an employee ajax request
    public function update(Request $request)
    {
        $fileName = '';
        $emp = Employee::find($request->emp_id);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/images', $fileName);
            if ($emp->avatar) {
                Storage::delete('public/images/' . $emp->avatar);
            }
        } else {
            $fileName = $request->emp_avatar;
        }

        $empData = ['first_name' => $request->fname, 'last_name' => $request->lname, 'email' => $request->email, 'avatar' => $fileName];

        $emp->update($empData);
        return response()->json([
            'status' => 200,
        ]);
    }

    // delete an employee ajax request
    public function delete(Request $request)
    {
        $id = $request->id;
        $emp = Employee::find($id);
        if (Storage::delete('public/images/' . $emp->avatar)) {
            Employee::destroy($id);
        }
    }
}