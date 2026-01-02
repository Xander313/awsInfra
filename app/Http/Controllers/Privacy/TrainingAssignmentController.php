<?php

namespace App\Http\Controllers\Privacy;

use App\Http\Controllers\Controller;
use App\Models\Privacy\TrainingAssignment;
use App\Models\Privacy\TrainingCourse;
use App\Models\IAM\AppUser; 
use Illuminate\Http\Request;

class TrainingAssignmentController extends Controller
{
    public function index()
    {
        $assignments = TrainingAssignment::with(['course', 'user'])
            ->whereHas('course', function ($q) {
                $q->where('org_id', session('org_id'));
            })
            ->orderBy('due_at')
            ->get();

        return view('training.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $courses = TrainingCourse::where('org_id', session('org_id'))->get();
        $users   = AppUser::where('status', 'active')->get();

        return view('training.assignments.create', compact('courses', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'   => 'required|integer',
            'user_id'     => 'required|integer',
            'assigned_at' => 'required|date',
            'due_at'      => 'nullable|date|after_or_equal:assigned_at',
        ]);

        // Evitar duplicados
        $exists = TrainingAssignment::where('course_id', $data['course_id'])
            ->where('user_id', $data['user_id'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['course_id' => 'Este curso ya está asignado a este usuario'])
                ->withInput();
        }

        TrainingAssignment::create([
            'course_id'   => $data['course_id'],
            'user_id'     => $data['user_id'],
            'assigned_at' => $data['assigned_at'],
            'due_at'      => $data['due_at'],
            'status'      => 'pending',
        ]);

        return redirect()
            ->route('training.assignments.index')
            ->with('success', 'Asignación creada correctamente');
    }
}
