<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function sales(Request $request)
    {
        $dateRange = $request->input('date_range', 'today');
        $salesData = $this->reportService->getSalesReport($dateRange);

        return view('reports.sales', compact('salesData', 'dateRange'));
    }

    public function inventory()
    {
        $products = Product::with('category')->get();
        $lowStockProducts = Product::where('stock_quantity', '<=', 'alert_quantity')->get();

        return view('reports.inventory', compact('products', 'lowStockProducts'));
    }

    public function revenue(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $revenueData = $this->reportService->getRevenueReport($period);

        return view('reports.revenue', compact('revenueData', 'period'));
    }

    public function export(Request $request, $type)
    {
        $format = $request->input('format', 'pdf');

        // Here you would implement the actual export functionality
        // For now, we'll just return a response that simulates downloading a file

        $fileName = $type . '_report_' . date('Y-m-d') . '.' . $format;

        // In a real implementation, you would use a PDF or Excel generation library
        // For example, using Laravel Excel or DomPDF

        return response()->json([
            'success' => true,
            'message' => "Export of {$type} report as {$format} would be downloaded as {$fileName}"
        ]);

        // Example implementation with Laravel Excel would be:
        // return Excel::download(new SalesExport, 'sales_report.xlsx');
    }
}
