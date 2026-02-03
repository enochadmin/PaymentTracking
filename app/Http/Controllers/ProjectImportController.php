<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Discipline;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProjectImportController extends Controller
{
    public function template()
    {
        $csvHeader = ['Identifier', 'Name', 'Description', 'Discipline', 'Client Name', 'Project Manager', 'Start Date', 'End Date', 'Status'];
        
        $callback = function() use ($csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            
            // Example row
            fputcsv($file, ['PRJ-001', 'Office Complex', 'Construction of new office', 'Civil', 'Acme Corp', 'Alice Smith', '2024-01-01', '2024-12-31', 'Active']);
            
            fclose($file);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="projects_template.csv"',
        ];

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');
        
        $header = fgetcsv($handle); // Skip header

        $count = 0;
        $errors = 0;
        
        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            try {
                // Expected order: 
                // 0: Identifier, 1: Name, 2: Description, 3: Discipline, 4: Client, 5: PM, 6: Start, 7: End, 8: Status

                $identifier = $row[0] ?? null;
                $name = $row[1] ?? null;
                
                if (empty($identifier) || empty($name)) {
                    // Skip invalid rows?
                    Log::warning('Skipping project row due to missing ID or Name: ' . json_encode($row));
                    continue;
                }

                // Lookup Discipline
                $disciplineName = $row[3] ?? null;
                $disciplineId = null;
                if ($disciplineName) {
                    $discipline = Discipline::firstOrCreate(['name' => $disciplineName]);
                    $disciplineId = $discipline->id;
                }

                // Date parsing
                $startDate = $this->parseDate($row[6] ?? null);
                $endDate = $this->parseDate($row[7] ?? null);

                Project::create([
                    'project_identifier' => $identifier,
                    'name' => $name,
                    'description' => $row[2] ?? null,
                    'discipline_id' => $disciplineId,
                    'client_name' => $row[4] ?? null,
                    'project_manager' => $row[5] ?? null,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $row[8] ?? 'Active',
                ]);
                $count++;
            } catch (\Exception $e) {
                Log::error('Error importing project row: ' . json_encode($row) . ' Error: ' . $e->getMessage());
                $errors++;
            }
        }
        
        fclose($handle);

        $msg = "Imported {$count} projects successfully.";
        if ($errors > 0) {
            $msg .= " Failed to import {$errors} rows (check logs).";
        }

        return redirect()->route('projects.index')->with('success', $msg);
    }

    private function parseDate($dateStr)
    {
        if (empty($dateStr)) return null;
        try {
            return Carbon::parse($dateStr);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function export()
    {
        $projects = Project::with('discipline')->get();
        $csvHeader = ['Identifier', 'Name', 'Description', 'Discipline', 'Client Name', 'Project Manager', 'Start Date', 'End Date', 'Status'];
        
        $callback = function() use ($projects, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);
            
            foreach ($projects as $project) {
                fputcsv($file, [
                    $project->project_identifier,
                    $project->name,
                    $project->description,
                    $project->discipline ? $project->discipline->name : '',
                    $project->client_name,
                    $project->project_manager,
                    $project->start_date ? $project->start_date->format('Y-m-d') : '',
                    $project->end_date ? $project->end_date->format('Y-m-d') : '',
                    $project->status,
                ]);
            }
            
            fclose($file);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="projects_export_' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream($callback, 200, $headers);
    }
}
