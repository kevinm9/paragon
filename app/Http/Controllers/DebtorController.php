<?php
namespace App\Http\Controllers;

use App\Models\Debtor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DebtorController extends Controller
{
    public function index(Request $request)
    {
        $debtors = Debtor::query()
            ->when($request->search, fn($q) =>
                $q->where('nombre', 'like', "%{$request->search}%")
                  ->orWhere('documento', 'like', "%{$request->search}%")
            )
            ->orderBy('id', 'desc')
            ->get();
            //->paginate(10);
            //->withQueryString();

        return Inertia::render('Debtors/Index', [
            'debtors' => $debtors,
            'kevinindexdebtor' => "xd",
            'kevinindexdebtor22' => "xd2",
            'create_url' => route('debtors.create'),
            'filters' => $request->only('search'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'required|string|max:50',
        ]);

        Debtor::create($data);

        return redirect()->route('debtors.index');
    }

    public function update(Request $request, Debtor $debtor)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'required|string|max:50',
        ]);

        $debtor->update($data);

        return redirect()->route('debtors.index');
    }

    public function destroy(Debtor $debtor)
    {
        $debtor->delete();

        return redirect()->route('debtors.index');
    }
}
