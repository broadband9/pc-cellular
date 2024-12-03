<?php

namespace App\Http\Controllers;

use App\Models\Repair;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    public function generateRepairLabels($repairId)
    {
        // Retrieve the repair record by ID
        $repair = Repair::find($repairId);

        // Check if repair exists
        if (!$repair) {
            return redirect()->back()->with('error', 'Repair not found!');
        }

        // Generate the PDF for the labels
        $pdf = PDF::loadView('pdf.repair_labels', compact('repair'));

        // Download the PDF
        return $pdf->download("repair_labels_{$repair->repair_number}.pdf");
    }
}
