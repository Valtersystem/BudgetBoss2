<?php

namespace App\Http\Controllers;

use App\Models\BankInstitution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BankInstitutionController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $bankInstitutions = $request->user()->bankInstitutions()->latest()->get();

        return Inertia::render('settings/BankInstitutions/Index', [
            'bankInstitutions' => $bankInstitutions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        $request->user()->bankInstitutions()->create($validated);

        return redirect()->route('bank-institutions.index');
    }

    public function update(Request $request, BankInstitution $bankInstitution)
    {
        // 👇 Adicione esta linha para verificar a permissão
        $this->authorize('update', $bankInstitution);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        $bankInstitution->update($validated);

        return redirect()->route('bank-institutions.index');
    }

    public function destroy(BankInstitution $bankInstitution)
    {
        // 👇 É uma boa prática adicionar também para o delete
        $this->authorize('delete', $bankInstitution);

        $bankInstitution->delete();

        return redirect()->route('bank-institutions.index');
    }
}
