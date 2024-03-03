<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function splitPdf(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'pdf' => 'required|file|mimes:pdf'
        ]);

        // Save the PDF to the 'tools/temp' directory instead of 'temp'
        $pdfPath = $request->file('pdf')->storeAs('tools/temp', 'invoice.pdf');
        $pdfFullPath = storage_path('app') . '/' . $pdfPath;

        // Path where the script is stored and the command to execute it
        $scriptPath = base_path('scripts/split_invoices.py');
        $command = "python3 {$scriptPath} '{$pdfFullPath}'";  // Added single quotes to handle spaces in path

        // Execute the Python script
        exec($command, $output, $returnVar);

        // Check if the script executed successfully
        if ($returnVar == 0) {
            // $output should contain the path to the ZIP file
            $zipPath = $output[0];

            // Ensure the ZIP path is within 'tools/temp' for security reasons
            if (strpos($zipPath, base_path('tools/temp')) === 0 && file_exists($zipPath)) {
                // Create a download link for the ZIP file
                return response()->download($zipPath)->deleteFileAfterSend(true);
            } else {
                // Log this incident, could be an attempt to access other files
                Log::warning("Invalid ZIP file path: " . $zipPath);
                return response()->json(['error' => 'An error occurred while processing the file.'], 500);
            }
        } else {
            // Handle the error accordingly
            return response()->json(['error' => 'Failed to split the PDF.'], 500);
        }
    }

    public function showSplitterForm()
    {
        Log::info('showSplitterForm method called');
        return view('tools.invoice_splitter'); // Make sure this view exists
    }


}
