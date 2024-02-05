<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class LuigiPipelineController extends Controller
{
    public function triggerUSDA()
    {
        exec("python3 /path/to/your/luigi_app/main.py --pipeline USDA");
        return response()->json(['message' => 'USDA pipeline triggered successfully.'], 200);
    }

    public function triggerCOMEX()
    {
        exec("python3 /path/to/your/luigi_app/main.py --pipeline COMEX");
        return response()->json(['message' => 'COMEX pipeline triggered successfully.'], 200);
    }

    public function triggerINDEC()
    {
        exec("python3 /path/to/your/luigi_app/main.py --pipeline INDEC");
        return response()->json(['message' => 'INDEC pipeline triggered successfully.'], 200);
    }

    public function triggerAllPipelines()
    {
        exec("python3 /path/to/your/luigi_app/main.py --pipeline ALL");
        return response()->json(['message' => 'All pipelines triggered successfully.'], 200);
    }
}
