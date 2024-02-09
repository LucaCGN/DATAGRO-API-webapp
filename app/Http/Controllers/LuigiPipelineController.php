<?php
namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Log;

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
        // Set the path to your CSV file
        $filePath = storage_path('app/luigi/data/usda/processed/master_table.csv');

        // Check if the file exists
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        // Return the file as a download
        return response()->download($filePath, 'master_table.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

}
