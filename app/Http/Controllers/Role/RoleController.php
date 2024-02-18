<?php

namespace App\Http\Controllers\Role;

use App\Helpers\Utility;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $roles = Role::all();

        if ($roles->isEmpty()) {
            return Utility::outputData(false, 'No records found', [], 200);
        }

        return Utility::outputData(true, 'Fetched role records', $roles, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        try {
            # Define validation rules
            $rules = [
                'role_name' => ['required', 'string', 'max:255', Rule::unique('roles')],
            ];

            # Validate the incoming request
            $validatedData = $request->validate($rules);

            # Create the role
            $role = Role::create([
                'role_name' => $validatedData['role_name'],
            ]);

            return Utility::outputData(true, "Role created successfully", $role, 201);
        } catch (ValidationException $e) {
            return Utility::outputData(false, "Validation failed", $e->errors(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        #
    }
}
