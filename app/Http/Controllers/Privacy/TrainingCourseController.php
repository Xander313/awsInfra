<?php

namespace App\Http\Controllers\Privacy;

use App\Http\Controllers\Controller;
use App\Support\DashboardCache;
use App\Models\Privacy\TrainingCourse;
use Illuminate\Http\Request;

class TrainingCourseController extends Controller
{
    public function index()
    {
        $orgId = $this->currentOrgId();
        $courses = TrainingCourse::where('org_id', $orgId)->get();

        return view('training.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('training.courses.create');
    }

    public function store(Request $request)
    {
        $orgId = $this->currentOrgId();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'renewal_days' => 'nullable|integer|min:1',
            'mandatory_flag' => 'nullable|boolean',
        ]);

        TrainingCourse::create([
            'org_id' => $orgId,
            'name' => $data['name'],
            'renewal_days' => $data['renewal_days'] ?? null,
            'mandatory_flag' => $request->has('mandatory_flag'),
        ]);

        DashboardCache::forgetCurrentOrg();

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

        DashboardCache::forgetForOrg((int) $course->org_id);

        return redirect()
            ->route('training.courses.index')
            ->with('success', 'Curso actualizado correctamente');
    }

    public function destroy(TrainingCourse $course)
    {
        $this->authorizeCourse($course);

        $course->delete();
        DashboardCache::forgetForOrg((int) $course->org_id);

        return redirect()
            ->route('training.courses.index')
            ->with('success', 'Curso eliminado correctamente');
    }

    /**
     * Seguridad: evitar acceder a cursos de otra organización
     */
    private function authorizeCourse(TrainingCourse $course): void
    {
        if ((int) $course->org_id !== $this->currentOrgId()) {
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
