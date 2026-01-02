<?php

namespace App\Http\Controllers\Privacy;

use App\Http\Controllers\Controller;
use App\Models\Privacy\TrainingResult;
use Illuminate\Http\Request;

class TrainingResultController extends Controller
{
    public function index()
    {
        $results = TrainingResult::with([
                'assignment.user',
                'assignment.course'
            ])
            ->whereHas('assignment.course', function ($q) {
                $q->where('org_id', session('org_id'));
            })
            ->get();

        return view('training.results.index', compact('results'));
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

        return redirect()
            ->route('training.results.index')
            ->with('success', 'Resultado actualizado correctamente');
    }

    private function authorizeResult(TrainingResult $result): void
    {
        if ($result->assignment->course->org_id !== session('org_id')) {
            abort(403);
        }
    }
}
