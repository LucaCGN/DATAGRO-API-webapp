<?php
namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Log; // Ensure you have the Log facade included

class LuigiPipelineController extends Controller
{
    private function executePipeline($pipelineName)
    {
        $command = "python3 /home/u830751002/domains/datagro-markets-tools.online/luigi/main.py --pipeline " . escapeshellarg($pipelineName) . " 2>&1";
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
        return $this->executePipeline('USDA');
    }

    public function triggerCOMEX()
    {
        return $this->executePipeline('COMEX');
    }

    public function triggerINDEC()
    {
        return $this->executePipeline('INDEC');
    }

    public function triggerAllPipelines()
    {
        return $this->executePipeline('ALL');
    }
}

