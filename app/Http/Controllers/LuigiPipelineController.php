<?php
namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Log;

class LuigiPipelineController extends Controller
{
    private function executePipeline($pipelineName) {
        $command = "python3 /home/u830751002/domains/datagro-markets-tools.online/luigi/main.py --pipeline " . $pipelineName . " 2>&1";
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
    public function executeTestScript()
    {
        $scriptPath = "/home/u830751002/domains/datagro-markets-tools.online/luigi/test.py";
        $command = "/usr/bin/python3 " . $scriptPath;

        Log::info("Executing test script: " . $command);

        exec($command, $output, $return_var);

        if ($return_var == 0) {
            Log::info("Test script executed successfully.");
            return response()->json(['message' => 'Test script executed successfully.', 'output' => $output], 200);
        } else {
            Log::error("Test script execution failed. Output: " . implode("\n", $output));
            return response()->json(['error' => 'Test script execution failed.', 'output' => $output], 500);
        }
    }

}
