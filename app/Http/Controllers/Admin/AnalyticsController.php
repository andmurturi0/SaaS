<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Monthly Revenue & Orders Chart (Last 12 months)
        $revenueChart = [];
        $ordersChart = [];
        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M');
            $revenueChart[] = (float) Order::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');
            
            $ordersChart[] = Order::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
        }

        // ... existing stats calculation ...

        return Inertia::render('Admin/Analytics/Index', [
            'stats' => $stats,
            'topProducts' => $topProducts,
            'recentActivity' => $recentActivity,
            'liveStats' => $liveStats,
            'trafficSources' => $trafficSources,
            'chartLabels' => $labels
        ]);
    }

    private function calculateGrowthPercent($current, $previous): float
    {
        if ($previous == 0) return $current > 0 ? 100.0 : 0.0;
        return (float) round((($current - $previous) / $previous) * 100, 1);
    }
}
