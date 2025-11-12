<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $student = Student::paginate(10);
        return response()->json($student);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|max:20|unique:students,student_id',
            'name' => 'required|string|max:255',
            'course' => 'required|string|max:100',
            'year_lvl' => 'required|integer|min:1|max:10',
            'campus' => 'required|string|max:100',
        ]);

        $student = Student::create($validated);
        return response()->json([
            'message' => 'Student created successfully',
            'student' => $student
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::find($id);
        return response()->json($student);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'student_id' => 'sometimes|required|string|max:20|unique:students,student_id,' . $id,
            'name' => 'sometimes|required|string|max:255',
            'course' => 'sometimes|required|string|max:100',
            'year_lvl' => 'sometimes|required|integer|min:1|max:10',
            'campus' => 'sometimes|required|string|max:100',
        ]);

        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        $student->update($validated);
        return response()->json([
            'message' => 'Student updated successfully',
            'student' => $student
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        $student->delete();
        return response()->json(['message' => 'Student deleted successfully'], 200);
    }
}
