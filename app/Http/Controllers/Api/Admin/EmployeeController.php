<?php
// app/Http/Controllers/Api/EmployeeController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        return response()->json(Employee::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:employees,email',
            'avatar' => 'nullable|image|max:2048', // max 2MB image file
        ]);

        if ($request->hasFile('avatar')) {
            // Store avatar image in storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $employee = Employee::create($validated);

        return response()->json($employee, 201);
    }

    public function show(Employee $employee)
    {
        return response()->json($employee);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'role' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                Rule::unique('employees', 'email')->ignore($employee->id),
            ],
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        } else {
            // No new avatar uploaded — do not change this field
            unset($validated['avatar']);
        }

        // Update only the validated fields that are present
        $employee->update($validated);

        return response()->json($employee);
    }

    public function destroy(Employee $employee)
    {
        // Delete avatar file if exists
        if ($employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
        }

        $employee->delete();

        return response()->json(null, 204);
    }
}
