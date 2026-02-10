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
        $data = $request->validated();
        
        if ($request->hasFile('business_license')) {
            $data['business_license_path'] = $request->file('business_license')->store('business_licenses', 'public');
        }

        $supplier = Supplier::create($data);
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
        $data = $request->validated();

        if ($request->hasFile('business_license')) {
            // Delete old file if exists
            if ($supplier->business_license_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($supplier->business_license_path);
            }
            $data['business_license_path'] = $request->file('business_license')->store('business_licenses', 'public');
        }

        $supplier->update($data);
        return redirect()->route('suppliers.show', $supplier)->with('success', 'Supplier updated');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->business_license_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($supplier->business_license_path);
        }
        
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier removed');
    }
}
