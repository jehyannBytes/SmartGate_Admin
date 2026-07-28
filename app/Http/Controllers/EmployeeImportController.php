<?php
namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class EmployeeImportController extends Controller
{
    public function create(): View
    {
        return view('employees.import');
    }

    public function template(): Response
    {
        $headers = [
            'employee_code', 'last_name', 'first_name', 'middle_name',
            'department', 'employment_type', 'position',
        ];

        $sampleRows = [
            ['EMP-1001', 'Dela Cruz', 'Juan', '', 'College of Engineering', 'faculty', 'Instructor'],
            ['EMP-1002', 'Santos', 'Maria', 'R', 'Registrar Office', 'non-teaching', 'Records Officer'],
        ];

        $csv = implode(',', $headers) . "\n";
        foreach ($sampleRows as $row) {
            $csv .= implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employee_import_template.csv"',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return back()->withErrors(['csv_file' => 'Could not read the uploaded file.']);
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'The CSV file appears to be empty.']);
        }

        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        $requiredColumns = ['employee_code', 'last_name', 'first_name', 'department', 'employment_type', 'position'];
        $missingColumns  = array_diff($requiredColumns, $header);

        if (! empty($missingColumns)) {
            fclose($handle);
            return back()->withErrors([
                'csv_file' => 'Missing required column(s): ' . implode(', ', $missingColumns),
            ]);
        }

        $rowNumber = 1;
        $successCount = 0;
        $errors = [];
        $toInsert = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $record = array_combine($header, array_pad($row, count($header), null));

            $rowErrors = $this->validateRow($record, $rowNumber);
            if (! empty($rowErrors)) {
                $errors = array_merge($errors, $rowErrors);
                continue;
            }

            $toInsert[] = [
                'employee_code'   => trim($record['employee_code']),
                'last_name'       => trim($record['last_name']),
                'first_name'      => trim($record['first_name']),
                'middle_name'     => isset($record['middle_name']) ? trim($record['middle_name']) ?: null : null,
                'department'      => trim($record['department']),
                'employment_type' => strtolower(trim($record['employment_type'])),
                'position'        => trim($record['position']),
                'photo_url'       => null,
                'is_active'       => true,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        }

        fclose($handle);

        $codes = array_column($toInsert, 'employee_code');
        $duplicatesInFile = array_diff_assoc($codes, array_unique($codes));
        if (! empty($duplicatesInFile)) {
            $errors[] = 'Duplicate employee_code(s) within the file: ' . implode(', ', array_unique($duplicatesInFile));
            $seen = [];
            $toInsert = array_filter($toInsert, function ($row) use (&$seen) {
                if (in_array($row['employee_code'], $seen)) {
                    return false;
                }
                $seen[] = $row['employee_code'];
                return true;
            });
        }

        $existingCodes = Employee::whereIn('employee_code', $codes)->pluck('employee_code')->toArray();
        if (! empty($existingCodes)) {
            $errors[] = 'These employee_code(s) already exist and were skipped: ' . implode(', ', $existingCodes);
            $toInsert = array_filter($toInsert, fn ($row) => ! in_array($row['employee_code'], $existingCodes));
        }

        if (! empty($toInsert)) {
            DB::transaction(function () use ($toInsert) {
                foreach (array_chunk($toInsert, 100) as $chunk) {
                    Employee::insert($chunk);
                }
            });
            $successCount = count($toInsert);
        }

        if ($successCount === 0 && ! empty($errors)) {
            return back()->withErrors(['csv_file' => 'No employees were imported.'])
                         ->with('import_errors', $errors);
        }

        $message = "{$successCount} employee(s) imported successfully.";
        if (! empty($errors)) {
            $message .= ' Some rows had issues - see details below.';
        }

        return redirect()->route('employees.index')
                         ->with('success', $message)
                         ->with('import_errors', $errors);
    }

    private function validateRow(array $record, int $rowNumber): array
    {
        $errors = [];

        foreach (['employee_code', 'last_name', 'first_name', 'department', 'position'] as $field) {
            if (empty(trim((string) ($record[$field] ?? '')))) {
                $errors[] = "Row {$rowNumber}: '{$field}' is required.";
            }
        }

        $type = strtolower(trim((string) ($record['employment_type'] ?? '')));
        if (! in_array($type, ['faculty', 'non-teaching'])) {
            $errors[] = "Row {$rowNumber}: 'employment_type' must be 'faculty' or 'non-teaching' (got '{$record['employment_type']}').";
        }

        if (! empty($record['employee_code']) && strlen($record['employee_code']) > 50) {
            $errors[] = "Row {$rowNumber}: 'employee_code' exceeds 50 characters.";
        }

        return $errors;
    }
}