<?php

namespace App\Http\Controllers\Privacy;

use App\Http\Controllers\Controller;
use App\Models\Privacy\TrainingCourse;
use Illuminate\Http\Request;

class TrainingCourseController extends Controller
{
    public function index()
    {
        $courses = TrainingCourse::where('org_id', session('org_id'))->get();

        return view('training.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('training.courses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'renewal_days' => 'nullable|integer|min:1',
            'mandatory_flag' => 'nullable|boolean',
        ]);

        TrainingCourse::create([
            'org_id' => session('org_id'),
            'name' => $data['name'],
            'renewal_days' => $data['renewal_days'] ?? null,
            'mandatory_flag' => $request->has('mandatory_flag'),
        ]);

        return redirect()
            ->route('training.courses.index')
            ->with('success', 'Curso creado correctamente');
    }

    public function show(TrainingCourse $course)
    {
        $this->authorizeCourse($course);

        return view('training.courses.show', compact('course'));
    }

    public function edit(TrainingCourse $course)
    {
        $this->authorizeCourse($course);

        return view('training.courses.edit', compact('course'));
    }

    public function update(Request $request, TrainingCourse $course)
    {
        $this->authorizeCourse($course);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'renewal_days' => 'nullable|integer|min:1',
            'mandatory_flag' => 'nullable|boolean',
        ]);

        $course->update([
            'name' => $data['name'],
            'renewal_days' => $data['renewal_days'] ?? null,
            'mandatory_flag' => $request->has('mandatory_flag'),
        ]);

        return redirect()
            ->route('training.courses.index')
            ->with('success', 'Curso actualizado correctamente');
    }

    public function destroy(TrainingCourse $course)
    {
        $this->authorizeCourse($course);

        $course->delete();

        return redirect()
            ->route('training.courses.index')
            ->with('success', 'Curso eliminado correctamente');
    }

    /**
     * Seguridad: evitar acceder a cursos de otra organización
     */
    private function authorizeCourse(TrainingCourse $course): void
    {
        if ($course->org_id !== session('org_id')) {
            abort(403);
        }
    }
}
