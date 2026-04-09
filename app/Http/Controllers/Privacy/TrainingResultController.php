<?php

namespace App\Http\Controllers\Privacy;

use App\Http\Controllers\Controller;
use App\Support\DashboardCache;
use App\Models\Privacy\TrainingAssignment;
use App\Models\Privacy\TrainingResult;
use Illuminate\Http\Request;

class TrainingResultController extends Controller
{
    public function index()
    {
        $orgId = $this->currentOrgId();

        $results = TrainingResult::with([
                'assignment.user',
                'assignment.course'
            ])
            ->whereHas('assignment.course', function ($q) use ($orgId) {
                $q->where('org_id', $orgId);
            })
            ->get();

        return view('training.results.index', compact('results'));
    }

    public function create(TrainingAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        return view('training.results.create', compact('assignment'));
    }

    public function store(Request $request, TrainingAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $data = $request->validate([
            'completed_at' => 'required|date',
            'score'        => 'nullable|integer|min:0|max:100',
        ]);

        TrainingResult::query()->updateOrCreate(
            ['assign_id' => $assignment->getKey()],
            [
                'completed_at' => $data['completed_at'],
                'score' => $data['score'] ?? null,
            ]
        );

        $assignment->update(['status' => 'COMPLETED']);
        DashboardCache::forgetForOrg((int) $assignment->course->org_id);

        return redirect()
            ->route('training.results.index')
            ->with('success', 'Resultado registrado correctamente');
    }

    public function show(TrainingResult $result)
    {
        $this->authorizeResult($result);
        return view('training.results.show', compact('result'));
    }

    public function edit(TrainingResult $result)
    {
        $this->authorizeResult($result);
        return view('training.results.edit', compact('result'));
    }

    public function update(Request $request, TrainingResult $result)
    {
        $this->authorizeResult($result);

        $data = $request->validate([
            'completed_at' => 'required|date',
            'score'        => 'nullable|integer|min:0|max:100',
        ]);

        $result->update($data);

        if ($result->assignment) {
            $result->assignment->update(['status' => 'COMPLETED']);
            DashboardCache::forgetForOrg((int) optional($result->assignment->course)->org_id);
        }

        return redirect()
            ->route('training.results.index')
            ->with('success', 'Resultado actualizado correctamente');
    }

    private function authorizeResult(TrainingResult $result): void
    {
        $result->loadMissing('assignment.course');

        if ((int) $result->assignment->course->org_id !== $this->currentOrgId()) {
            abort(403);
        }
    }

    private function authorizeAssignment(TrainingAssignment $assignment): void
    {
        $assignment->loadMissing('course');

        if ((int) $assignment->course->org_id !== $this->currentOrgId()) {
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
