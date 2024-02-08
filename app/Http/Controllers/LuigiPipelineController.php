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

    public function executeTestScriptSystem() {
        $scriptPath = "/home/u830751002/domains/datagro-markets-tools.online/luigi/test3.py";
        $command = "/usr/bin/python3 " . escapeshellarg($scriptPath);

        Log::info("Executing test script with system: " . $command);

        ob_start();
        system($command, $return_var);
        $output = ob_get_clean();

        if ($return_var == 0) {
            Log::info("Test script executed successfully with system.");
            return response()->json(['message' => 'Test script executed successfully.', 'output' => $output], 200);
        } else {
            Log::error("Test script execution failed with system. Return code: $return_var. Output: " . $output);
            return response()->json(['error' => 'Test script execution failed.', 'return_code' => $return_var, 'output' => $output], 500);
        }
    }

    public function executeTestScriptProcOpen() {
        // Adjust the path to the virtual environment's Python executable
        $pythonBinary = base_path("luigi/venv/bin/python");

        // Adjust the path to your Python script within the Laravel project
        $scriptPath = base_path("luigi/test3.py");

        // Combine the Python executable and script path into one command
        $command = "{$pythonBinary} " . escapeshellarg($scriptPath);



        // Other environment variables can be set here as necessary

        $descriptorSpec = array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "w"),  // stdout
            2 => array("pipe", "w")   // stderr
        );

        Log::info("Executing test script with proc_open: " . $command);

        $process = proc_open($command, $descriptorSpec, $pipes);

        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);

            fclose($pipes[1]);
            fclose($pipes[2]);

            $return_var = proc_close($process);

            if ($return_var == 0) {
                Log::info("Test script executed successfully with proc_open.");
                return response()->json(['message' => 'Test script executed successfully.', 'output' => $output], 200);
            } else {
                Log::error("Test script execution failed with proc_open. Return code: $return_var. Error: " . $error);
                return response()->json(['error' => 'Test script execution failed.', 'return_code' => $return_var, 'output' => $output, 'error_output' => $error], 500);
            }
        } else {
            Log::error("Failed to start process with proc_open.");
            return response()->json(['error' => 'Failed to start process.'], 500);
        }
    }

}
