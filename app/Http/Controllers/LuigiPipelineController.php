<?php
namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Log;

class LuigiPipelineController extends Controller
{
    private function executePipeline($pipelineName) {
        // Properly separate the script path and arguments
        $scriptPath = escapeshellarg("/home/u830751002/domains/datagro-markets-tools.online/luigi/main.py");
        $pipelineArgument = "--pipeline " . escapeshellarg($pipelineName);
        $command = "/usr/bin/python3 " . $scriptPath . " " . $pipelineArgument . " 2>&1";

        Log::info("Executing command: " . $command);
        exec($command, $output, $return_var);

        $detailedOutput = implode("\n", $output);

        if ($return_var == 0) {
            Log::info("Pipeline " . $pipelineName . " triggered successfully.");
            return response()->json(['message' => $pipelineName . ' pipeline triggered successfully.'], 200);
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

    public function executeTestScriptPassthru() {
        $scriptPath = "/home/u830751002/domains/datagro-markets-tools.online/luigi/test4.py";
        $command = "/usr/bin/python3 " . escapeshellarg($scriptPath);

        Log::info("Executing test script with passthru: " . $command);

        ob_start(); // Start output buffering
        passthru($command, $return_var);
        $output = ob_get_clean(); // Get the buffer and then clean it

        if ($return_var == 0) {
            Log::info("Test script executed successfully with passthru.");
            return response()->json(['message' => 'Test script executed successfully.', 'output' => $output], 200);
        } else {
            Log::error("Test script execution failed with passthru. Output: " . $output);
            return response()->json(['error' => 'Test script execution failed.', 'output' => $output], 500);
        }
    }

    public function executeTestScript() {
        $scriptPath = "/home/u830751002/domains/datagro-markets-tools.online/luigi/test4.py";
        $command = "python3 " . escapeshellarg($scriptPath);

        Log::info("Executing test script: " . $command);

        // Using shell_exec
        $output = shell_exec($command);

        $return_var = (is_null($output) || $output === '') ? 1 : 0;

        if ($return_var == 0) {
            Log::info("Test script executed successfully with shell_exec.");
            return response()->json(['message' => 'Test script executed successfully.', 'output' => $output], 200);
        } else {
            Log::error("Test script execution failed with shell_exec. Output: " . $output);
            return response()->json(['error' => 'Test script execution failed.', 'output' => $output], 500);
        }
    }
}
