<?php

namespace App\Http\Controllers;

use App\Models\Debtor;
use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index(Request $request, Debtor $debtor)
    {
        $contacts = $debtor->contacts()
            ->when(
                $request->search,
                fn($q) =>
                $q->where('detalle', 'like', "%{$request->search}%")
                    ->orWhere('observacion', 'like', "%{$request->search}%")
            )
            ->orderBy('id', 'desc')
            ->paginate(5)
            ->withQueryString();

        return Inertia::render('Debtors/Index', [
            'debtors' => Debtor::all(),
            'debtor' => $debtor,
            'contacts' => $debtor->contacts()->get(),
            'filters' => $request->only('search'),
        ]);
    }

    public function store(Request $request, Debtor $debtor)
    {
        $data = $request->validate([
            'tipo' => 'required|string|max:255',
            'categoria' => 'required|string|max:500',
            'detalle' => 'required|string|max:255',
        ]);

        $debtor->contacts()->create($data);

        return Inertia::render('Debtors/Index', [
            'debtors' => Debtor::all(),
            'debtor' => $debtor,
            'contacts' => $debtor->contacts()->get(),
            'filters' => $request->only('search'),
        ]);
    }

    public function update(Request $request, Debtor $debtor, Contact $contact)
    {
        $data = $request->validate([
            'detalle' => 'required|string|max:255',
            'observacion' => 'nullable|string|max:500',
        ]);

        $contact->update($data);

        return Inertia::render('Debtors/Index', [
            'debtors' => Debtor::all(),
            'debtor' => $debtor,
            'contacts' => $debtor->contacts()->get(),
        ]);
    }

    public function destroy(Debtor $debtor, Contact $contact)
    {
        $contact->delete();
        //return redirect()->route('debtors.index');

        return Inertia::render('Debtors/Index', [
            'debtors' => Debtor::all(),
            'debtor' => $debtor,
            'contacts' => $debtor->contacts()->get(),
        ]);
    }
}
