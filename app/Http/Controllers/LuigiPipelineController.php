<?php
namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Log;

class LuigiPipelineController extends Controller
{
    private function executePipeline($pipelineName)
    {
        // Set the PATH environment variable to include the Python executable's directory
        $envVars = 'export PATH=/usr/local/bin:/bin:/usr/bin:/usr/bin/python3.6;';

        // Add Python's site-packages directories to PYTHONPATH
        $envVars .= 'export PYTHONPATH=/usr/local/lib64/python3.6/site-packages:/usr/local/lib/python3.6/site-packages:/usr/bin/../lib64/python3.6/site-packages:/usr/bin/../lib/python3.6/site-packages;';

        // Construct the command with the environment variables
        $command = $envVars . " python3 /home/u830751002/domains/datagro-markets-tools.online/luigi/main.py --pipeline " . escapeshellarg($pipelineName) . " 2>&1";

        // Log the command being executed
        Log::info("Executing command: " . $command);

        exec($command, $output, $return_var);

        if ($return_var == 0) {
            Log::info("Pipeline " . $pipelineName . " triggered successfully.");
            return response()->json(['message' => $pipelineName . ' pipeline triggered successfully.'], 200);
        } else {
            Log::error("Pipeline " . $pipelineName . " execution failed. Output: " . implode("\n", $output));
            return response()->json(['error' => $pipelineName . ' pipeline execution failed.', 'output' => $output], 500);
        }
    }

    public function triggerUSDA()
    {
        Log::info("USDA pipeline trigger initiated.");
        return $this->executePipeline('USDA');
    }

    public function triggerCOMEX()
    {
        Log::info("COMEX pipeline trigger initiated.");
        return $this->executePipeline('COMEX');
    }

    public function triggerINDEC()
    {
        Log::info("INDEC pipeline trigger initiated.");
        return $this->executePipeline('INDEC');
    }

    public function triggerAllPipelines()
    {
        Log::info("ALL pipelines trigger initiated.");
        return $this->executePipeline('ALL');
    }
}
