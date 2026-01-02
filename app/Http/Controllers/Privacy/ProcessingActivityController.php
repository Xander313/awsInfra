<?php



namespace App\Http\Controllers\Privacy;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privacy\ProcessingActivity;
use App\Models\Privacy\DataCategory;
use App\Models\Privacy\Recipient;
use App\Models\Privacy\Country;
use Illuminate\Support\Facades\DB;


class ProcessingActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = ProcessingActivity::orderBy('pa_id', 'desc')->get();

        //dd($activities);
        return view('privacy.rat.index', compact('activities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {


        $categories = DataCategory::all();
        $recipients = Recipient::all();
        $countries = Country::all();

        return view('privacy.rat.create', compact('categories', 'recipients', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {


            $activity = ProcessingActivity::create([
                /*'org_id' => 1, aqui se espera el id de la session de org se coloca un 1 para poder insertar TEMPORALMENTE*/
                'org_id' => 1,
                /*'owner_unit_id' => auth()->user()->unit_id,----aqui se espera el id de la session del user actual se coloca un 1 para poder insertar TEMPORALMENTE*/
                'owner_unit_id' => 1,
                'name' => $request->name
            ]);


            

            if ($request->has('data_categories')) {
                foreach ($request->data_categories as $cat_id => $data) {
                    if (isset($data['checked'])) {
                        DB::table('privacy.pa_data_category')->insert([
                            'pa_id' => $activity->pa_id,
                            'data_cat_id' => $cat_id,
                            'collection_source' => $data['collection_source'] ?? 'N/A'
                        ]);
                    }
                }
            }


            if ($request->has('retention_rules')) {
                foreach ($request->retention_rules as $rule) {
                    DB::table('privacy.retention_rule')->insert([
                        'pa_id' => $activity->pa_id,
                        'retention_period_days' => $rule['retention_period_days'] ?? null,
                        'trigger_event' => $rule['trigger_event'] ?? null,
                        /*
                        'disposal_method' => $rule['disposal_method'] ?? null,
                        'legal_hold_flag' => false*/
                        'disposal_method' => $rule['disposal_method'] ?? null,
                        'legal_hold_flag' => isset($rule['legal_hold_flag']) ? true : false
                    ]);
                }
            }


            if ($request->has('transfers')) {
                foreach ($request->transfers as $transfer) {
                    DB::table('privacy.transfer')->insert([
                        'pa_id' => $activity->pa_id,
                        'recipient_id' => $transfer['recipient_id'] ?? null,
                        'country_id' => $transfer['country_id'] ?? null,
                        'transfer_type' => $transfer['transfer_type'] ?? null,
                        'safeguard' => $transfer['safeguard'] ?? null,
                        'legal_basis_text' => $transfer['legal_basis_text'] ?? null,
                        'created_at' => now()
                    ]);
                }
            }
        });

        return redirect()->route('rat.index')->with('exito', 'Actividad creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Cargar actividad con relaciones
        $activity = ProcessingActivity::with([
            'categories',
            'retentionRules',
            'transfers'
        ])->findOrFail($id);

        // Datos auxiliares
        $categories = DataCategory::all();
        $recipients = Recipient::all();
        $countries  = Country::all();

        // Categorías seleccionadas + pivot
        $selectedCategories = $activity->categories->pluck('data_cat_id')->toArray();
        $categoryPivot = $activity->categories
            ->pluck('pivot.collection_source', 'data_cat_id')
            ->toArray();

        // ESTO ES LO QUE FALTABA
        $retention = $activity->retentionRules->first();
        $transfer  = $activity->transfers->first();

        return view('privacy.rat.edit', compact(
            'activity',
            'categories',
            'recipients',
            'countries',
            'selectedCategories',
            'categoryPivot',
            'retention',
            'transfer'
        ));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $activity = ProcessingActivity::findOrFail($id);

        DB::transaction(function () use ($request, $activity) {

            // ================================
            // 1. ACTUALIZAR ACTIVIDAD
            // ================================
            $activity->update([
                'name' => $request->name,
                'description' => $request->description,
                'legal_basis' => $request->legal_basis,
                'org_id' => 1,          // Ajusta si aplica
                'owner_unit_id' => 1,   // Ajusta si aplica
            ]);

            // ================================
            // 2. ACTUALIZAR CATEGORÍAS (FIX)
            // ================================
            $syncData = [];

            if ($request->has('data_categories')) {
                foreach ($request->data_categories as $catId => $data) {

                    // Solo sincroniza las que estén marcadas
                    if (isset($data['checked'])) {
                        $syncData[$catId] = [
                            'collection_source' => $data['collection_source'] ?? 'N/A'
                        ];
                    }
                }
            }

            // AQUÍ ESTÁ LA CLAVE
            $activity->categories()->sync($syncData);

            // ================================
            // 3. ACTUALIZAR RETENTION RULES
            // ================================
            if ($request->has('retention_rules')) {

                // Borrar existentes
                $activity->retentionRules()->delete();

                foreach ($request->retention_rules as $rule) {
                    $activity->retentionRules()->create([
                        'retention_period_days' => $rule['retention_period_days'] ?? null,
                        'trigger_event' => $rule['trigger_event'] ?? null,
                        'disposal_method' => $rule['disposal_method'] ?? null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('rat.index')
            ->with('exito', 'Processing Activity actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $activity = ProcessingActivity::findOrFail($id);

        // Relaciones críticas
        $tieneRelacionesCriticas =
            $activity->retentionRules()->exists() ||
            $activity->transfers()->exists();

        if ($tieneRelacionesCriticas) {
            return redirect()
                ->route('rat.index')
                ->with('erro', 'No se puede eliminar la actividad porque tiene relaciones críticas.');
        }

        DB::transaction(function () use ($activity) {

            //Limpiar pivotes
            $activity->categories()->detach();

            //Eliminar actividad
            $activity->delete();
        });

        return redirect()
            ->route('rat.index')
            ->with('exito', 'Actividad eliminada correctamente.');
    }
}
