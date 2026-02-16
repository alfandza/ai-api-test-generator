<?php

namespace App;

class CSVExporter
{
    public static function export(array $testCases): void
    {
        if (empty($testCases)) {
            throw new \Exception('No test cases to export');
        }

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="api_test_cases_' . date('Y-m-d_His') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write CSV header
        $headers = [
            'Test Case ID',
            'Scenario',
            'Label',
            'Steps',
            'Request Body',
            'Severity',
            'Priority'
        ];
        fputcsv($output, $headers);

        // Write test case rows
        foreach ($testCases as $testCase) {
            $row = [
                $testCase['id'] ?? '',
                $testCase['scenario'] ?? '',
                $testCase['label'] ?? '',
                $testCase['steps'] ?? '',
                $testCase['requestBody'] ?? 'N/A',
                $testCase['severity'] ?? '',
                $testCase['priority'] ?? ''
            ];
            fputcsv($output, $row);
        }

        fclose($output);
        exit; // Terminate script after download
    }
}
