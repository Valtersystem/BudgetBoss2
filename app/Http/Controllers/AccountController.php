<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('accounts/Index', [
            'accounts' => $request->user()->accounts()->with('bankInstitution')->latest()->get(),
            'bankInstitutions' => $request->user()->bankInstitutions()->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_institution_id' => 'required|exists:bank_institutions,id',
            'initial_balance' => 'required|numeric',
            'description' => 'nullable|string',
            'source_of_money' => 'nullable|string',
            'color' => 'required|string|max:7',
            'dashboard' => 'required|boolean',
        ]);

        $request->user()->accounts()->create($validated);

        return redirect()->route('accounts.index')->with('success', 'Conta criada com sucesso.');
    }

    public function update(Request $request, Account $account)
    {
        if ($account->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_institution_id' => 'required|exists:bank_institutions,id',
            'initial_balance' => 'required|numeric',
            'description' => 'nullable|string',
            'source_of_money' => 'nullable|string',
            'color' => 'required|string|max:7',
            'dashboard' => 'required|boolean',
        ]);


        $account->update($validated);

        return redirect()->back()->with('success', 'Conta atualizada com sucesso.');
    }

    public function destroy(Request $request, Account $account)
    {
        if ($account->user_id !== $request->user()->id) {
            abort(403);
        }

        // Adicionar lógica para impedir a exclusão se houver transações associadas, se desejar
        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Conta eliminada com sucesso.');
    }
}
