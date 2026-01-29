<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Http\Requests\BankRequest;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankController extends Controller
{
    use SoftDeletes;

    public function index()
    {
        $banks = Bank::paginate(15);
        return view('banks.index', compact('banks'));
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(BankRequest $request)
    {
        Bank::create($request->validated());
        return redirect()->route('banks.index')->with('success', 'Bank created successfully.');
    }

    public function show(Bank $bank)
    {
        return view('banks.show', compact('bank'));
    }

    public function edit(Bank $bank)
    {
        return view('banks.edit', compact('bank'));
    }

    public function update(BankRequest $request, Bank $bank)
    {
        $bank->update($request->validated());
        return redirect()->route('banks.show', $bank)->with('success', 'Bank updated successfully.');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return redirect()->route('banks.index')->with('success', 'Bank deleted successfully.');
    }

    public function toggleActive(Bank $bank)
    {
        $bank->update(['is_active' => !$bank->is_active]);
        $status = $bank->is_active ? 'activated' : 'deactivated';
        return redirect()->route('banks.index')->with('success', "Bank {$status} successfully.");
    }
}
