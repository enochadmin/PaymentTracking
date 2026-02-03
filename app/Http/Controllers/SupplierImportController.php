<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Log;

class SupplierImportController extends Controller
{
    public function template()
    {
        $csvHeader = ['Name', 'Tin Number', 'Contact Person', 'Email', 'Phone', 'Address', 'Supplier Type'];
        
        $callback = function() use ($csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            
            // Example row
            fputcsv($file, ['Example Supplier', '123-456-789', 'John Doe', 'john@example.com', '1234567890', '123 Main St', 'Supply only']);
            
            fclose($file);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="suppliers_template.csv"',
        ];

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        
        $header = fgetcsv($handle); // Skip header row
        
        // Basic mapping validation: check if header matches expected format if necessary
        // For simplicity, assuming strict column order: Name, Tin Number, Contact, Email, Phone, Address, Type

        $count = 0;
        
        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            try {
                // Determine supplier type (simple validation/fallback)
                $type = $row[6] ?? 'Supply only';
                $validTypes = ['Supply only', 'Subcontractor', 'Consultant'];
                if (!in_array($type, $validTypes)) {
                    $type = 'Supply only'; // Fallback
                }

                Supplier::create([
                    'name' => $row[0] ?? 'Unknown',
                    'tin_number' => $row[1] ?? null,
                    'contact_person' => $row[2] ?? null,
                    'email' => $row[3] ?? null,
                    'phone' => $row[4] ?? null,
                    'address' => $row[5] ?? null,
                    'supplier_type' => $type,
                ]);
                $count++;
            } catch (\Exception $e) {
                Log::error('Error importing supplier row: ' . json_encode($row) . ' Error: ' . $e->getMessage());
                // Continue to next row or halt based on requirement? 
                // Currently continuing, but logging could be improved.
            }
        }
        
        fclose($handle);

        return redirect()->route('suppliers.index')->with('success', "Imported {$count} suppliers successfully.");
    }

    public function export()
    {
        $suppliers = Supplier::all();
        $csvHeader = ['Name', 'Tin Number', 'Contact Person', 'Email', 'Phone', 'Address', 'Supplier Type'];
        
        $callback = function() use ($suppliers, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            
            foreach ($suppliers as $supplier) {
                fputcsv($file, [
                    $supplier->name,
                    $supplier->tin_number,
                    $supplier->contact_person,
                    $supplier->email,
                    $supplier->phone,
                    $supplier->address,
                    $supplier->supplier_type,
                ]);
            }
            
            fclose($file);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="suppliers_export_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream($callback, 200, $headers);
    }
}
