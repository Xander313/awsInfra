<?php

namespace App\Http\Controllers\Privacy;

use App\Http\Controllers\Controller;
use App\Models\Privacy\Country;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
    // Listar todos los países
    public function index()
    {
        $countries = Country::orderBy('name')->get();
        return view('privacy.country.index', compact('countries'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('privacy.country.create');
    }

    // Guardar nuevo país
    public function store(Request $request)
    {
        $request->validate([
            'iso_code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('privacy.country', 'iso_code'), // ✅ Esquema incluido
            ],
            'name' => 'required|string|max:255',
        ]);

        Country::create($request->all());

        return redirect()
            ->route('privacy.country.index')
            ->with('success', 'País creado correctamente');
    }

    // Mostrar formulario de edición
    public function edit(Country $country)
    {
        return view('privacy.country.edit', compact('country'));
    }

    // Actualizar país
    public function update(Request $request, Country $country)
    {
        $request->validate([
            'iso_code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('privacy.country', 'iso_code')
                    ->ignore($country->country_id, 'country_id'),
            ],
            'name' => 'required|string|max:255',
        ]);

        $country->update($request->all());

        return redirect()
            ->route('privacy.country.index')
            ->with('success', 'País actualizado correctamente');
    }

    // Eliminar país
    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()
            ->route('privacy.country.index')
            ->with('success', 'País eliminado correctamente');
    }
}
