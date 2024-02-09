<?php
namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LuigiPipelineController extends Controller
{
    private function executePipeline($pipelineName) {
        // Adjust the path to the virtual environment's Python executable
        $pythonBinary = base_path("luigi/venv/bin/python");

        // Properly separate the script path and arguments
        $scriptPath = escapeshellarg(base_path("luigi/main.py"));
        $pipelineArgument = "--pipeline " . escapeshellarg($pipelineName);
        $command = "{$pythonBinary} " . $scriptPath . " " . $pipelineArgument . " 2>&1";

        Log::info("Executing command: " . $command);
        exec($command, $output, $return_var);

        $detailedOutput = implode("\n", $output);

        if ($return_var == 0) {
            Log::info("Pipeline " . $pipelineName . " triggered successfully.");
            return response()->json(['message' => $pipelineName . ' pipeline triggered successfully.', 'output' => $detailedOutput], 200);
        } else {
            Log::error("Pipeline " . $pipelineName . " execution failed. Detailed Output: " . $detailedOutput);
            return response()->json(['error' => $pipelineName . ' pipeline execution failed.', 'detailedOutput' => $detailedOutput], 500);
        }
    }


    public function triggerUSDA() {
        return $this->executePipeline('USDA');
    }

    public function triggerCOMEX() {
        return $this->executePipeline('COMEX');
    }

    public function triggerINDEC() {
        return $this->executePipeline('INDEC');
    }

    public function triggerAllPipelines() {
        return $this->executePipeline('ALL');
    }

    public function fetchUSDAall()
    {
        Log::info('fetchUSDAall: Function accessed');

        // Set the correct path to your CSV file
        $baseDir = base_path(); // Get the base path of the Laravel installation
        $filePath = $baseDir . '/luigi/data/usda/processed/master_table.csv'; // Correct file path
        Log::info('fetchUSDAall: File path - ' . $filePath);

        // Check if the file exists
        if (!file_exists($filePath)) {
            Log::error('fetchUSDAall: File not found at path - ' . $filePath);
            abort(404, 'File not found');
        }

        Log::info('fetchUSDAall: File exists, preparing download');

        // Return the file as a download
        return response()->download($filePath, 'master_table.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }


}
