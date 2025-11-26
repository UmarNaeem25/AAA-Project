<?php

namespace App\Http\Controllers;

use App\Services\ProjectReportService;

class ReportController extends Controller
{
    public function downloadAlgorithmReport()
    {
        $reportService = new ProjectReportService();
        return $reportService->generateAlgorithmAnalysisReport();
    }
}