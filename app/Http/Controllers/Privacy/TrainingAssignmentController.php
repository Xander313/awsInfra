<?php

namespace App\Http\Controllers\Privacy;

use App\Http\Controllers\Controller;
use App\Support\DashboardCache;
use App\Models\Privacy\TrainingAssignment;
use App\Models\Privacy\TrainingCourse;
use App\Models\Privacy\TrainingResult;
use App\Models\IAM\AppUser; 
use Illuminate\Http\Request;

class TrainingAssignmentController extends Controller
{
    public function index()
    {
        $orgId = $this->currentOrgId();

        $assignments = TrainingAssignment::with(['course', 'user'])
            ->whereHas('course', function ($q) use ($orgId) {
                $q->where('org_id', $orgId);
            })
            ->orderBy('due_at')
            ->get();

        return view('training.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $orgId = $this->currentOrgId();

        $courses = TrainingCourse::where('org_id', $orgId)->get();
        $users   = AppUser::query()
            ->whereRaw("LOWER(COALESCE(status, 'activo')) != 'suspendido'")
            ->orderBy('full_name')
            ->get();

        return view('training.assignments.create', compact('courses', 'users'));
    }

    public function store(Request $request)
    {
        $orgId = $this->currentOrgId();

        $data = $request->validate([
            'course_id'   => 'required|integer',
            'user_id'     => 'required|integer',
            'assigned_at' => 'required|date',
            'due_at'      => 'nullable|date|after_or_equal:assigned_at',
        ]);

        $course = TrainingCourse::query()
            ->where('course_id', $data['course_id'])
            ->where('org_id', $orgId)
            ->first();

        if (!$course) {
            return back()
                ->withErrors(['course_id' => 'El curso no pertenece a la organización activa'])
                ->withInput();
        }

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
            'status'      => 'PENDING',
        ]);

        DashboardCache::forgetForOrg((int) $course->org_id);

        return redirect()
            ->route('training.assignments.index')
            ->with('success', 'Asignación creada correctamente');
    }

    public function show(TrainingAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $assignment->load(['course', 'user']);

        return view('training.assignments.show', compact('assignment'));
    }

    public function edit(TrainingAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $assignment->load(['course', 'user']);

        return view('training.assignments.edit', compact('assignment'));
    }

    public function update(Request $request, TrainingAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $data = $request->validate([
            'due_at' => 'nullable|date|after_or_equal:' . optional($assignment->assigned_at)->format('Y-m-d'),
            'status' => 'required|string|in:PENDING,COMPLETED,EXPIRED',
        ]);

        $assignment->fill([
            'due_at' => $data['due_at'] ?? null,
            'status' => $data['status'],
        ]);
        $assignment->save();
        $assignment->refresh();

        if ($data['status'] === 'COMPLETED') {
            TrainingResult::query()->firstOrCreate(
                ['assign_id' => $assignment->getKey()],
                ['completed_at' => now()->toDateString(), 'score' => null]
            );
        }

        DashboardCache::forgetForOrg((int) $assignment->course->org_id);

        return redirect()
            ->route('training.assignments.index')
            ->with('success', 'Asignación actualizada correctamente');
    }

    private function authorizeAssignment(TrainingAssignment $assignment): void
    {
        $assignment->loadMissing('course');

        if (!$assignment->course || (int) $assignment->course->org_id !== $this->currentOrgId()) {
            abort(403);
        }
    }

    private function currentOrgId(): int
    {
        $orgId = session('org_id');

        abort_if($orgId === null, 403, 'No existe una organización activa en sesión.');

        return (int) $orgId;
    }
}
