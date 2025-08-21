<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the admin dashboard with statistics
     */
    public function dashboard()
    {
        // Get sales statistics
        $todaySales = Sale::whereDate('created_at', Carbon::today())->count();
        $totalSales = Sale::count();
        $totalRevenue = Sale::sum('total_amount');

        // Get product statistics
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock_quantity', '<=', 'alert_quantity')->count();

        // Get category statistics
        $totalCategories = Category::count();

        // Get recent sales
        $recentSales = Sale::with('user')->latest()->take(5)->get();

        // Get sales data for chart
        $salesData = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
        ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return view('dashboard', compact(
            'todaySales',
            'totalSales',
            'totalRevenue',
            'totalProducts',
            'lowStockProducts',
            'totalCategories',
            'recentSales',
            'salesData'
        ));
    }
}
