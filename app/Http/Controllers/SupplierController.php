<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;

class SupplierController extends Controller
{
    // public function __construct()
    // {
    //     $perm = \App\Http\Middleware\CheckPermission::class;
    //     $this->middleware($perm . ':suppliers.index')->only('index', 'show');
    //     $this->middleware($perm . ':suppliers.create')->only('create', 'store');
    //     $this->middleware($perm . ':suppliers.update')->only('edit', 'update');
    //     $this->middleware($perm . ':suppliers.delete')->only('destroy');
    // }

    public function index()
    {
        $suppliers = Supplier::paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(SupplierRequest $request)
    {
        $supplier = Supplier::create($request->validated());
        return redirect()->route('suppliers.index')->with('success', 'Supplier created');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());
        return redirect()->route('suppliers.show', $supplier)->with('success', 'Supplier updated');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier removed');
    }
}
