<?php

namespace App\Http\Controllers;

use App\Services\CupsService;
use Illuminate\Http\JsonResponse;

class PrintController extends Controller
{
    protected CupsService $cupsService;

    public function __construct(CupsService $cupsService)
    {
        $this->cupsService = $cupsService;
    }

    /**
     * Get the list of printers from the CUPS server.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $printers = $this->cupsService->listPrinters();

        if (empty($printers)) {
            return response()->json(['message' => 'No printers found.'], 404);
        }

        return response()->json($printers);
    }
}
